<?php
namespace App\Controllers;
use App\Repositories\AdvertisementRepository;

class AdvertisementController
{
    private const PLAYBACK_FILE = __DIR__ . '/../../storage/ad_playback.json';

    private function requireAuth()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['administrator', 'receptionist', 'radiology_staff'], true)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    public function index()
    {
        header('Content-Type: application/json');
        try {
            $repo = new AdvertisementRepository();
            $ads = $repo->getAll();
            
            // Format for frontend
            $formatted = array_map(function($ad) {
                $isImg = preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $ad['filepath']);
                $isYt = (strpos($ad['filepath'], 'youtube.com') !== false || strpos($ad['filepath'], 'youtu.be') !== false);
                $type = $isYt ? 'youtube' : ($isImg ? 'image/jpeg' : 'video/mp4'); // basic estimation for frontend UI
                
                return [
                    'id' => $ad['id'],
                    'name' => basename($ad['filepath']),
                    'src' => $ad['filepath'],
                    'type' => $type,
                    'duration' => $ad['duration_seconds'],
                    'active' => (bool)$ad['is_active'],
                    'order' => $ad['display_order']
                ];
            }, $ads);

            $command = null;
            $commandFile = __DIR__ . '/../../storage/ad_command.json';
            if (file_exists($commandFile)) {
                $cmdData = json_decode(file_get_contents($commandFile), true);
                if ($cmdData && time() - $cmdData['timestamp'] < 60) { // Command valid for 60 seconds
                    $command = $cmdData;
                }
            }

            $settings = ['display_mode' => 'all']; // default
            $settingsFile = __DIR__ . '/../../storage/ad_settings.json';
            if (file_exists($settingsFile)) {
                $settingsData = json_decode(file_get_contents($settingsFile), true);
                if ($settingsData) {
                    $settings = $settingsData;
                }
            }

            echo json_encode(['status' => 'success', 'data' => $formatted, 'command' => $command, 'settings' => $settings]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function settings()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['display_mode'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid settings data']);
            return;
        }

        $settingsFile = __DIR__ . '/../../storage/ad_settings.json';
        file_put_contents($settingsFile, json_encode(['display_mode' => $input['display_mode']]));

        echo json_encode(['status' => 'success']);
    }

    public function command()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['action'])) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid command data']);
            return;
        }

        $cmdData = [
            'action' => $input['action'],
            'ad_id' => $input['ad_id'] ?? null,
            'timestamp' => microtime(true)
        ];
        
        $commandFile = __DIR__ . '/../../storage/ad_command.json';
        file_put_contents($commandFile, json_encode($cmdData));

        echo json_encode(['status' => 'success']);
    }

    public function getPlayback()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        $playback = null;
        if (file_exists(self::PLAYBACK_FILE)) {
            $data = json_decode((string) file_get_contents(self::PLAYBACK_FILE), true);
            if (is_array($data) && isset($data['ad_id'], $data['updated_at'])) {
                $playback = $data;
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => $playback,
            'server_time' => microtime(true)
        ]);
    }

    public function playback()
    {
        $this->requireAuth();
        if (empty($_SESSION['playback_token']) || !hash_equals($_SESSION['playback_token'], $_SERVER['HTTP_X_PLAYBACK_TOKEN'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized display writer']);
            return;
        }
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate');

        $input = json_decode((string) file_get_contents('php://input'), true);
        $adId = filter_var($input['ad_id'] ?? null, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1]
        ]);
        $position = filter_var($input['position'] ?? null, FILTER_VALIDATE_FLOAT);
        $mediaType = $input['media_type'] ?? '';

        if ($adId === false || $position === false || $position < 0 ||
            !in_array($mediaType, ['image', 'video', 'youtube'], true)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid playback data']);
            return;
        }

        $playback = [
            'ad_id' => $adId,
            'position' => round((float) $position, 3),
            'playing' => filter_var($input['playing'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'media_type' => $mediaType,
            'updated_at' => microtime(true)
        ];

        if (file_put_contents(self::PLAYBACK_FILE, json_encode($playback), LOCK_EX) === false) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Unable to save playback state']);
            return;
        }

        echo json_encode(['status' => 'success', 'data' => $playback]);
    }

    public function store()
    {
        $this->requireAuth();
        header('Content-Type: application/json');

        $youtubeUrl = $_POST['youtube_url'] ?? '';
        $publicUrl = '';
        $destPath = '';

        if (!empty($youtubeUrl)) {
            // Validate it's a basic URL
            if (!\App\Services\MediaValidation::youtubeUrl($youtubeUrl)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid YouTube URL']);
                return;
            }
            $publicUrl = $youtubeUrl;
        } else {
            if (!isset($_FILES['media']) || $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['error' => 'No file uploaded or YouTube link provided']);
                return;
            }

            $file = $_FILES['media'];
            
            // Basic validation
            if ($file['size'] > 50 * 1024 * 1024) { // 50MB max
                http_response_code(400);
                echo json_encode(['error' => 'File too large. Max 50MB.']);
                return;
            }
            
            try {
                $ext = \App\Services\MediaValidation::extension($file['tmp_name'], $file['name']);
            } catch (\InvalidArgumentException $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()]);
                return;
            }
            // Make sure upload dir exists
            $uploadDir = __DIR__ . '/../../public/storage/uploads/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique name
            $filename = 'ad_' . bin2hex(random_bytes(16)) . '.' . $ext;
            $destPath = $uploadDir . $filename;
            $publicUrl = '/storage/uploads/ads/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to move uploaded file']);
                return;
            }
        }

        $duration = (int)($_POST['duration'] ?? 10);
        $isActive = filter_var($_POST['active'] ?? true, FILTER_VALIDATE_BOOLEAN);
        
        try {
            $repo = new AdvertisementRepository();
            $ad = $repo->create($publicUrl, $duration, $isActive);
            echo json_encode(['status' => 'success', 'data' => $ad]);
        } catch (\Exception $e) {
            // cleanup if db fails and it's a file
            if ($destPath && file_exists($destPath)) {
                unlink($destPath);
            }
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function destroy()
    {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id = (int)($input['id'] ?? 0);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ID']);
            return;
        }

        try {
            $repo = new AdvertisementRepository();
            $ad = $repo->find($id);
            if ($ad) {
                $physicalPath = __DIR__ . '/../../public' . $ad['filepath'];
                if (file_exists($physicalPath)) {
                    unlink($physicalPath);
                }
                $repo->delete($id);
            }
            echo json_encode(['status' => 'success']);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
