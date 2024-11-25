<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>esercizio 2</title>
</head>
<body>
    <h1>esercizio 2</h1>
    <hr>
    <?php
    $today = new DateTime("now", new DateTimeZone('Europe/Rome'));
    echo $today ->format('h:i:s');
    $ora=$today ->format('h');
    echo "\nsono le $ora";
    ?>
</body>
</html>