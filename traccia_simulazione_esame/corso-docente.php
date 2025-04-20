<?php
session_start();

// Verifica se l'utente è loggato e docente
if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

$docente_id = $_SESSION['user_id'];

$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica se corso è passato nell'URL
if (!isset($_GET['id'])) {
    echo "Corso non selezionato.";
    exit;
}

$corso_id = intval($_GET['id']);

// Verifica che il corso appartenga al docente
$stmt = $conn->prepare("SELECT nome_corso, descrizione FROM corsi WHERE id = ? AND docente_id = ?");
$stmt->bind_param("ii", $corso_id, $docente_id);
$stmt->execute();
$stmt->bind_result($nome_corso, $descrizione);
if (!$stmt->fetch()) {
    echo "Corso non trovato o non autorizzato.";
    exit;
}
$stmt->close();

// Elimina esercizio se richiesto
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete = $conn->prepare("DELETE FROM esercizi WHERE id = ? AND corso_id = ?");
    $delete->bind_param("ii", $delete_id, $corso_id);
    $delete->execute();
    $delete->close();
    header("Location: corso-docente.php?id=$corso_id");
    exit;
}

// Recupera esercizi del corso
$esercizi = "";
$query = $conn->prepare("SELECT id, titolo, testo FROM esercizi WHERE corso_id = ?");
$query->bind_param("i", $corso_id);
$query->execute();
$query->bind_result($id, $titolo, $testo);

while ($query->fetch()) {
    $esercizi .= "
        <div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>
            <h4>" . htmlspecialchars($titolo) . "</h4>
            <p>" . nl2br(htmlspecialchars($testo)) . "</p>
            <a href='modifica-esercizio.php?id=$id&corso_id=$corso_id'>Modifica</a> |
            <a href='corso-docente.php?id=$corso_id&delete_id=$id' onclick='return confirm(\"Sei sicuro di voler eliminare questo esercizio?\")'>Elimina</a>
        </div>
    ";
}
$query->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Gestione Corso: <?php echo htmlspecialchars($nome_corso); ?></title>
</head>
<body>
    <h2>Corso: <?php echo htmlspecialchars($nome_corso); ?></h2>
    <p><?php echo htmlspecialchars($descrizione); ?></p>

    <h3>Esercizi</h3>
    <?php echo $esercizi ?: "<p>Nessun esercizio presente.</p>"; ?>

    <a href="inserisci-esercizio.php?corso_id=<?php echo $corso_id; ?>">➕ Aggiungi esercizio</a>
    <br><br>
    <a href="area-docente.php">🔙 Torna all'area docente</a>
</body>
</html>
