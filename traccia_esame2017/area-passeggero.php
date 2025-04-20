<?php
session_start();

// Se l'utente non è loggato, reindirizzalo alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Benvenuto <?php echo $_SESSION['SELECT nome FROM utenti where user_id']; ?>!</h1>
    <button><a href="prenotazione">prenota viaggio</a></button>
    <a href="logout.php">Logout</a>
</body>
</html>