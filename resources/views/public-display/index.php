<?php
$pageData = include __DIR__ . '/../data/public-display-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>Radiology Queue Display</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/public-display.css">
    <script src="/js/background-playback.js"></script>
    <script src="/js/playback-sync.js"></script>
</head>
<body>
    <?php include __DIR__ . '/../partials/public-display-content.php'; ?>
</body>
</html>
