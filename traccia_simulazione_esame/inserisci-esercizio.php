<?php
session_start();

// Controlla se sei loggato e sei un docente
if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

// Connessione al database
$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica che sia stato passato l'ID del corso
if (!isset($_GET['corso_id'])) {
    echo "ID del corso mancante.";
    exit;
}

$corso_id = intval($_GET['corso_id']);

// Se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'];
    $testo = $_POST['testo'];

    $stmt = $conn->prepare("INSERT INTO esercizi (corso_id, titolo, testo) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $corso_id, $titolo, $testo);

    if ($stmt->execute()) {
        header("Location: corso-docente.php?id=$corso_id");
        exit;
    } else {
        echo "Errore durante l'inserimento dell'esercizio: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Inserisci Esercizio</title>
</head>
<body>
    <h2>Nuovo Esercizio</h2>
    <form method="post">
        <p>Titolo:</p>
        <input type="text" name="titolo" required><br>
        <p>Testo:</p>
        <textarea name="testo" rows="5" cols="40" required></textarea><br><br>
        <button type="submit">Aggiungi Esercizio</button>
    </form>
    <br>
    <a href="corso-docente.php?id=<?php echo $corso_id; ?>">🔙 Torna al corso</a>
</body>
</html>
