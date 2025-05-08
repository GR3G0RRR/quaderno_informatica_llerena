<?php
session_start();

// Verifica se l'utente è loggato e è un passeggero
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'passeggero') {
    header("Location: login.php");
    exit();
}

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Verifica se l'ID della prenotazione è stato passato
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_prenotazione = intval($_GET['id']);

    // Recupera le informazioni sulla prenotazione
    $stmt = $conn->prepare("SELECT p.id_viaggio, v.citta_partenza, v.citta_destinazione, v.data_partenza, v.ora_partenza
                            FROM prenotazioni p
                            JOIN viaggi v ON p.id_viaggio = v.id_viaggio
                            WHERE p.id_prenotazione = ? AND p.id_utente = ?");
    $stmt->bind_param("ii", $id_prenotazione, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Prenotazione non trovata.";
        exit();
    }

    $prenotazione = $result->fetch_assoc();
    $id_viaggio_attuale = $prenotazione['id_viaggio'];
    $citta_partenza = $prenotazione['citta_partenza'];
    $citta_destinazione = $prenotazione['citta_destinazione'];
    $data_partenza = $prenotazione['data_partenza'];
    $ora_partenza = $prenotazione['ora_partenza'];

    // Recupera i viaggi disponibili per la modifica
    $viaggi = $conn->query("SELECT * FROM viaggi WHERE stato = 'aperto' AND posti_disponibili > 0");
} else {
    echo "ID prenotazione non valido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Prenotazione</title>
</head>
<body>
    <h1>Modifica la tua prenotazione</h1>
    <p>Attualmente hai prenotato un viaggio da <?php echo htmlspecialchars($citta_partenza); ?> a <?php echo htmlspecialchars($citta_destinazione); ?> il <?php echo $data_partenza; ?> alle <?php echo $ora_partenza; ?>.</p>

    <h2>Scegli un nuovo viaggio</h2>

    <form method="POST" action="modifica-prenotazione.php">
        <label for="nuovo_viaggio">Scegli un nuovo viaggio:</label>
        <select name="nuovo_viaggio" id="nuovo_viaggio" required>
            <?php while ($row = $viaggi->fetch_assoc()): ?>
                <option value="<?php echo $row['id_viaggio']; ?>"><?php echo htmlspecialchars($row['citta_partenza']); ?> - <?php echo htmlspecialchars($row['citta_destinazione']); ?> (<?php echo $row['data_partenza']; ?>)</option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Modifica Prenotazione</button>
    </form>

    <?php
    // Gestisci la modifica della prenotazione
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nuovo_viaggio'])) {
        $id_nuovo_viaggio = intval($_POST['nuovo_viaggio']);

        // Cancella la prenotazione esistente
        $conn->query("DELETE FROM prenotazioni WHERE id_prenotazione = $id_prenotazione");

        // Aggiungi la nuova prenotazione
        $conn->query("INSERT INTO prenotazioni (id_viaggio, id_utente, stato) VALUES ($id_nuovo_viaggio, $user_id, 'aperto')");

        // Aggiorna i posti disponibili per il vecchio e il nuovo viaggio
        $conn->query("UPDATE viaggi SET posti_disponibili = posti_disponibili + 1 WHERE id_viaggio = $id_viaggio_attuale");
        $conn->query("UPDATE viaggi SET posti_disponibili = posti_disponibili - 1 WHERE id_viaggio = $id_nuovo_viaggio");

        header("Location: gestione-prenotazioni.php");
        exit();
    }
    ?>

</body>
</html>
