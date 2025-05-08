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

// Annulla prenotazione
if (isset($_GET['annulla']) && is_numeric($_GET['annulla'])) {
    $id_prenotazione = intval($_GET['annulla']);

    // Recupera l'id_viaggio associato per aggiornare i posti
    $stmt = $conn->prepare("SELECT id_viaggio FROM prenotazioni WHERE id_prenotazione = ? AND id_utente = ?");
    $stmt->bind_param("ii", $id_prenotazione, $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id_viaggio = $row['id_viaggio'];

        // Cancella la prenotazione
        $conn->query("DELETE FROM prenotazioni WHERE id_prenotazione = $id_prenotazione");

        // Incrementa i posti disponibili
        $conn->query("UPDATE viaggi SET posti_disponibili = posti_disponibili + 1 WHERE id_viaggio = $id_viaggio");
    }
    $stmt->close();
    header("Location: gestione-prenotazioni.php");
    exit();
}

// Recupera le prenotazioni dell'utente
$stmt = $conn->prepare("SELECT p.id_prenotazione, v.citta_partenza, v.citta_destinazione, v.data_partenza, v.ora_partenza, v.stato
                         FROM prenotazioni p
                         JOIN viaggi v ON p.id_viaggio = v.id_viaggio
                         WHERE p.id_utente = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le tue prenotazioni</title>
</head>
<body>
    <h1>Le tue prenotazioni</h1>
    <a href="area-passeggero.php">🔙 Torna all'area passeggero</a>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['citta_partenza']); ?></td>
                    <td><?php echo htmlspecialchars($row['citta_destinazione']); ?></td>
                    <td><?php echo $row['data_partenza']; ?></td>
                    <td><?php echo $row['ora_partenza']; ?></td>
                    <td><?php echo $row['stato']; ?></td>
                    <td>
                        <a href="gestione-prenotazioni.php?annulla=<?php echo $row['id_prenotazione']; ?>" onclick="return confirm('Vuoi annullare la prenotazione?');">Annulla</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Non hai effettuato prenotazioni.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
