<?php
namespace App\Controllers;
use App\Repositories\AdvertisementRepository;

class AdvertisementController
{
    private function requireAuth()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_role'])) {
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
            'timestamp' => time()
        ];
        
        $commandFile = __DIR__ . '/../../storage/ad_command.json';
        file_put_contents($commandFile, json_encode($cmdData));

        echo json_encode(['status' => 'success']);
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
            if (filter_var($youtubeUrl, FILTER_VALIDATE_URL) === false) {
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
            
            // Make sure upload dir exists
            $uploadDir = __DIR__ . '/../../public/storage/uploads/ads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique name
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('ad_') . '.' . $ext;
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
