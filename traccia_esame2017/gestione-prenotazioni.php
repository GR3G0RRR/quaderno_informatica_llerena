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

// Recupera le prenotazioni dell'utente
$id_utente = $_SESSION['user_id'];
$sql = $conn->prepare("SELECT p.id_prenotazione, v.citta_partenza, v.citta_destinazione, v.data_partenza, v.ora_partenza, p.stato 
                       FROM prenotazioni p
                       JOIN viaggi v ON p.id_viaggio = v.id_viaggio
                       WHERE p.id_utente = ?");
$sql->bind_param("i", $id_utente);
$sql->execute();
$result = $sql->get_result();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Prenotazioni</title>
</head>
<body>
    <h1>Le tue Prenotazioni</h1>
    <a href="area-passeggero.php">🔙 Torna all'Area Passeggero</a><br><br>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Stato</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['citta_partenza']); ?></td>
                <td><?php echo htmlspecialchars($row['citta_destinazione']); ?></td>
                <td><?php echo $row['data_partenza']; ?></td>
                <td><?php echo $row['ora_partenza']; ?></td>
                <td><?php echo $row['stato']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Non hai ancora prenotato nessun viaggio.</p>
    <?php endif; ?>

    <?php
    $sql->close();
    $conn->close();
    ?>
</body>
</html>
