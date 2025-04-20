<?php
session_start();

// Verifica che l'utente sia loggato e sia docente
if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

// Verifica che i dati siano inviati tramite POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica che i campi esistano
    if (!isset($_POST['nome_corso']) || !isset($_POST['descrizione'])) {
        die("Campi mancanti.");
    }

    $nome_corso = $_POST['nome_corso'];
    $descrizione = $_POST['descrizione'];
    $docente_id = $_SESSION['user_id'];

    // Connessione al database
    $conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
    if ($conn->connect_error) {
        die('Connessione fallita: ' . $conn->connect_error);
    }

    // Query per inserire il corso
    $stmt = $conn->prepare("INSERT INTO corsi (nome_corso, descrizione, docente_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nome_corso, $descrizione, $docente_id);

    if ($stmt->execute()) {
        header("Location: area-docente.php");
        exit;
    } else {
        echo "Errore nella creazione del corso: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Metodo non valido.";
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
    <h3>Crea un nuovo corso linguistico</h3>
    <form method="post" action="crea-corso.php">
    <label for="nome_corso">Nome del corso:</label><br>
    <input type="text" id="nome_corso" name="nome_corso" required><br><br>
    <label for="descrizione">Descrizione:</label><br>
    <textarea id="descrizione" name="descrizione" rows="4" cols="50"></textarea><br><br>
    <button type="submit">Crea corso</button>
</form>

</body>
</html>