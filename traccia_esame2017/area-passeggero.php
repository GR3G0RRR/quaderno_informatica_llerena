<?php
session_start();

// Verifica login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connessione DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Ottieni nome utente
$stmt = $conn->prepare("SELECT nome FROM utenti WHERE id_utente = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$nome_utente = $user ? $user['nome'] : 'Utente';
$stmt->close();

// Annulla prenotazione
if (isset($_GET['annulla']) && is_numeric($_GET['annulla'])) {
    $id_prenotazione = intval($_GET['annulla']);

    $stmt = $conn->prepare("SELECT id_viaggio FROM prenotazioni WHERE id_prenotazione = ? AND id_utente = ?");
    $stmt->bind_param("ii", $id_prenotazione, $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id_viaggio = $row['id_viaggio'];

        $conn->query("DELETE FROM prenotazioni WHERE id_prenotazione = $id_prenotazione");
        $conn->query("UPDATE viaggi SET posti_disponibili = posti_disponibili + 1 WHERE id_viaggio = $id_viaggio");
    }
    header("Location: area-passeggero.php");
    exit();
}

// Placeholder modifica
if (isset($_GET['modifica']) && is_numeric($_GET['modifica'])) {
    echo "<script>alert('Funzionalità di modifica in sviluppo');</script>";
}

// Prenota viaggio
if (isset($_GET['prenota']) && is_numeric($_GET['prenota'])) {
    $id_viaggio = intval($_GET['prenota']);
    $check = $conn->prepare("SELECT * FROM prenotazioni WHERE id_utente = ? AND id_viaggio = ?");
    $check->bind_param("ii", $_SESSION['user_id'], $id_viaggio);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO prenotazioni (id_viaggio, id_utente, stato) VALUES (?, ?, 'aperto')");
        $insert->bind_param("ii", $id_viaggio, $_SESSION['user_id']);
        $insert->execute();
        $insert->close();

        $update = $conn->prepare("UPDATE viaggi SET posti_disponibili = posti_disponibili - 1 WHERE id_viaggio = ? AND posti_disponibili > 0");
        $update->bind_param("i", $id_viaggio);
        $update->execute();
        $update->close();
    }

    header("Location: area-passeggero.php");
    exit();
}

// Viaggi disponibili
$viaggi = $conn->query("SELECT * FROM viaggi WHERE stato = 'aperto' AND posti_disponibili > 0");

// Viaggi già prenotati
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
    <title>Area Passeggero</title>
</head>
<body>
    <h1>Benvenuto <?php echo htmlspecialchars($nome_utente); ?>!</h1>

    <button><a href="logout.php" style="text-decoration: none;">🚪 Logout</a></button>

    <h2>Viaggi disponibili</h2>
    <?php if ($viaggi->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Posti</th>
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
                            <a href="area-passeggero.php?prenota=<?php echo $row['id_viaggio']; ?>" onclick="return confirm('Prenotare questo viaggio?');"> Prenota</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Nessun viaggio disponibile al momento.</p>
    <?php endif; ?>

    <h2>Le tue prenotazioni</h2>
    <?php
    $stmt = $conn->prepare("SELECT p.id_prenotazione, v.citta_partenza, v.citta_destinazione, v.data_partenza, v.ora_partenza, v.stato
                            FROM prenotazioni p
                            JOIN viaggi v ON p.id_viaggio = v.id_viaggio
                            WHERE p.id_utente = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $prenotazioni = $stmt->get_result();
    ?>

    <?php if ($prenotazioni->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
            <?php while ($row = $prenotazioni->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['citta_partenza']); ?></td>
                    <td><?php echo htmlspecialchars($row['citta_destinazione']); ?></td>
                    <td><?php echo $row['data_partenza']; ?></td>
                    <td><?php echo $row['ora_partenza']; ?></td>
                    <td><?php echo htmlspecialchars($row['stato']); ?></td>
                    <td>
                        <a href="area-passeggero.php?modifica=<?php echo $row['id_prenotazione']; ?>">✏️ Modifica</a> |
                        <a href="area-passeggero.php?annulla=<?php echo $row['id_prenotazione']; ?>" onclick="return confirm('Vuoi annullare la prenotazione?');">🗑 Elimina</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Non hai prenotazioni attive.</p>
    <?php endif; ?>

    <?php
    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
