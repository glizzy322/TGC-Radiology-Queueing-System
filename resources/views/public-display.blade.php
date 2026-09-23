<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="/images/favicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radiology Queue Display</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/public-display.css') }}">
</head>
<body>
    <?php $pageData = include resource_path('views/data/public-display-data.php'); ?>
    @include('partials.public-display-content', ['pageData' => $pageData])
</body>
</html>
