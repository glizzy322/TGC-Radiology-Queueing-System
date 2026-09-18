<?php
namespace App\Services;

final class MediaValidation
{
    public static function extension(string $path, string $name): string
    {
        $allowed = [
            'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'],
            'png' => ['image/png'], 'gif' => ['image/gif'], 'webp' => ['image/webp'],
            'mp4' => ['video/mp4'], 'webm' => ['video/webm'],
        ];
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($path);
        if (!isset($allowed[$extension]) || !in_array($mime, $allowed[$extension], true)) {
            throw new \InvalidArgumentException('Unsupported media or file content does not match extension');
        }
        if (str_starts_with($mime, 'image/') && @getimagesize($path) === false) {
            throw new \InvalidArgumentException('Invalid image content');
        }
        return $extension;
    }

    public static function youtubeUrl(string $url): bool
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) return false;
        $parts = parse_url($url);
        return ($parts['scheme'] ?? '') === 'https'
            && !isset($parts['user']) && !isset($parts['pass'])
            && (!isset($parts['port']) || $parts['port'] === 443)
            && in_array(strtolower($parts['host'] ?? ''), ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtu.be'], true);
    }
}
