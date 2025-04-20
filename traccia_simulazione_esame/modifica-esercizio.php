<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if (!isset($_GET['id']) || !isset($_GET['corso_id'])) {
    echo "Dati mancanti.";
    exit;
}

$esercizio_id = intval($_GET['id']);
$corso_id = intval($_GET['corso_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titolo = $_POST['titolo'];
    $testo = $_POST['testo'];

    $update = $conn->prepare("UPDATE esercizi SET titolo = ?, testo = ? WHERE id = ? AND corso_id = ?");
    $update->bind_param("ssii", $titolo, $testo, $esercizio_id, $corso_id);
    $update->execute();
    $update->close();

    header("Location: corso-docente.php?id=$corso_id");
    exit;
}

// Precompila dati
$stmt = $conn->prepare("SELECT titolo, testo FROM esercizi WHERE id = ? AND corso_id = ?");
$stmt->bind_param("ii", $esercizio_id, $corso_id);
$stmt->execute();
$stmt->bind_result($titolo, $testo);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Esercizio</title>
</head>
<body>
    <h2>Modifica Esercizio</h2>
    <form method="post">
        <p>Titolo:</p>
        <input type="text" name="titolo" value="<?php echo htmlspecialchars($titolo); ?>" required><br>
        <p>Testo:</p>
        <textarea name="testo" required><?php echo htmlspecialchars($testo); ?></textarea><br><br>
        <button type="submit">Salva modifiche</button>
    </form>
    <br>
    <a href="corso-docente.php?id=<?php echo $corso_id; ?>">🔙 Torna al corso</a>
</body>
</html>
