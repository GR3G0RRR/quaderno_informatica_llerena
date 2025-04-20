<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica se c'è un corso selezionato
if (!isset($_GET['id'])) {
    echo "Nessun corso selezionato.";
    exit;
}

$corso_id = intval($_GET['id']);

// Recupera dati del corso
$stmt = $conn->prepare("SELECT nome_corso, descrizione FROM corsi WHERE id = ?");
$stmt->bind_param("i", $corso_id);
$stmt->execute();
$stmt->bind_result($nome_corso, $descrizione);
if (!$stmt->fetch()) {
    echo "Corso non trovato.";
    exit;
}
$stmt->close();

// Recupera esercizi del corso
$esercizi = "";
$query = $conn->prepare("SELECT titolo, testo FROM esercizi WHERE corso_id = ?");
$query->bind_param("i", $corso_id);
$query->execute();
$query->bind_result($titolo, $testo);

while ($query->fetch()) {
    $esercizi .= "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>
                    <h4>" . htmlspecialchars($titolo) . "</h4>
                    <p>" . nl2br(htmlspecialchars($testo)) . "</p>
                 </div>";
}
$query->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nome_corso); ?></title>
</head>
<body>
    <h2>Corso: <?php echo htmlspecialchars($nome_corso); ?></h2>
    <p><?php echo htmlspecialchars($descrizione); ?></p>

    <h3>Esercizi</h3>
    <?php echo $esercizi ?: "<p>Non ci sono esercizi disponibili per questo corso.</p>"; ?>

    <br><a href="area-studente.php">Torna ai corsi</a>
</body>
</html>
