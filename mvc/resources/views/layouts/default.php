<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Custom Shop</title>

    <!--
    Hier definieren wir eine HTML Base Url. Diese dient dazu, dass alle relativen URLs und includes im HTML relativ zu
    dieser URL berechnet werden.

    s. https://developer.mozilla.org/de/docs/Web/HTML/Element/base
    -->
    <base href="<?php echo \Core\Config::get('app.baseurl') ?>/">

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
</head>
<body>
<?php require_once __DIR__ . '/../partials/navbar.php'; ?>

<div class="container mt-5">
    <?php require_once $viewPath; ?>
</div>
<script src="js/app.js"></script>
</body>
</html>
