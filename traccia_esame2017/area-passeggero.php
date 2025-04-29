<?php
session_start();

// Se l'utente non è loggato, reindirizzalo alla pagina di login
if (!isset($_SESSION['user_id'])) {
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

// Recupera il nome dell'utente
$stmt = $conn->prepare("SELECT nome FROM utenti WHERE id_utente = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$nome_utente = $user ? $user['nome'] : 'Utente';
$stmt->close();

// Se cliccano su "Prenota" un viaggio
if (isset($_GET['prenota']) && is_numeric($_GET['prenota'])) {
    $id_viaggio = intval($_GET['prenota']);
    
    // Controlla se l'utente ha già prenotato quel viaggio
    $check = $conn->prepare("SELECT * FROM prenotazioni WHERE id_utente = ? AND id_viaggio = ?");
    $check->bind_param("ii", $_SESSION['user_id'], $id_viaggio);
    $check->execute();
    $check_result = $check->get_result();
    
    if ($check_result->num_rows == 0) {
        // Crea la prenotazione
        $insert = $conn->prepare("INSERT INTO prenotazioni (id_viaggio, id_utente, stato) VALUES (?, ?, 'aperto')");
        $insert->bind_param("ii", $id_viaggio, $_SESSION['user_id']);
        $insert->execute();
        $insert->close();

        // Riduci i posti disponibili
        $update = $conn->prepare("UPDATE viaggi SET posti_disponibili = posti_disponibili - 1 WHERE id_viaggio = ? AND posti_disponibili > 0");
        $update->bind_param("i", $id_viaggio);
        $update->execute();
        $update->close();
    }
    
    header("Location: area-passeggero.php");
    exit();
}

// Recupera TUTTI i viaggi disponibili
$viaggi = $conn->query("SELECT * FROM viaggi WHERE stato = 'aperto' AND posti_disponibili > 0");

// Recupera tutte le prenotazioni dell'utente
$prenotazioni_utente = [];
$pstmt = $conn->prepare("SELECT id_viaggio FROM prenotazioni WHERE id_utente = ?");
$pstmt->bind_param("i", $_SESSION['user_id']);
$pstmt->execute();
$presult = $pstmt->get_result();
while ($row = $presult->fetch_assoc()) {
    $prenotazioni_utente[] = $row['id_viaggio'];
}
$pstmt->close();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Passeggero</title>
</head>
<body>
    <h1>Benvenuto <?php echo htmlspecialchars($nome_utente); ?>!</h1>

    <button><a href="gestione-prenotazioni.php" style="text-decoration: none;">📋 Gestione Prenotazioni</a></button>
    <button><a href="logout.php" style="text-decoration: none;">🚪 Logout</a></button>

    <h2>Viaggi disponibili da prenotare</h2>

    <?php if ($viaggi->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Posti Disponibili</th>
                <th>Azione</th>
            </tr>
            <?php while ($row = $viaggi->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['citta_partenza']); ?></td>
                <td><?php echo htmlspecialchars($row['citta_destinazione']); ?></td>
                <td><?php echo $row['data_partenza']; ?></td>
                <td><?php echo $row['ora_partenza']; ?></td>
                <td><?php echo $row['posti_disponibili']; ?></td>
                <td>
                    <?php if (in_array($row['id_viaggio'], $prenotazioni_utente)): ?>
                        ✅ Prenotato
                    <?php else: ?>
                        <a href="area-passeggero.php?prenota=<?php echo $row['id_viaggio']; ?>" onclick="return confirm('Sei sicuro di voler prenotare questo viaggio?');">🚌 Prenota</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Al momento non ci sono viaggi disponibili.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
