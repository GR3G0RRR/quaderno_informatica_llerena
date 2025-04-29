<?php
session_start();

// Se l'utente non è loggato, reindirizzalo alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Dati database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera i dati dell'utente loggato
$id_utente = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nome, cognome, ruolo FROM utenti WHERE id_utente = ?");
$stmt->bind_param("i", $id_utente);
$stmt->execute();
$result_utente = $stmt->get_result();
$dati_utente = $result_utente->fetch_assoc();

$nome = $dati_utente['nome'] ?? 'Utente';
$cognome = $dati_utente['cognome'] ?? '';
$ruolo = $dati_utente['ruolo'] ?? '';

// Se riceve richiesta di cancellazione
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_viaggio = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM viaggi WHERE id_viaggio = ? AND id_autista = ?");
    $stmt->bind_param("ii", $id_viaggio, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    header("Location: area-autista.php");
    exit();
}

// Recupera i viaggi dell'autista
$id_autista = $_SESSION['user_id'];
$sql = $conn->prepare("SELECT * FROM viaggi WHERE id_autista = ?");
$sql->bind_param("i", $id_autista);
$sql->execute();
$result = $sql->get_result();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Autista</title>
</head>
<body>
    <h1>Benvenuto <?php echo htmlspecialchars($nome . ' ' . $cognome); ?> ^_^</h1>

    <button><a href="crea-viaggio.php" style="text-decoration: none;">➕ Crea Viaggio</a></button>
    <button><a href="logout.php" style="text-decoration: none;">🚪 Logout</a></button>

    <h2>I tuoi viaggi creati</h2>

    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10">
            <tr>
                <th>Partenza</th>
                <th>Destinazione</th>
                <th>Data</th>
                <th>Ora</th>
                <th>Posti</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['citta_partenza']); ?></td>
                <td><?php echo htmlspecialchars($row['citta_destinazione']); ?></td>
                <td><?php echo $row['data_partenza']; ?></td>
                <td><?php echo $row['ora_partenza']; ?></td>
                <td><?php echo $row['posti_disponibili']; ?></td>
                <td><?php echo $row['stato']; ?></td>
                <td>
                    <a href="area-autista.php?delete=<?php echo $row['id_viaggio']; ?>" onclick="return confirm('Sei sicuro di voler eliminare questo viaggio?');">🗑 Elimina</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Non hai ancora creato viaggi.</p>
    <?php endif; ?>

    <?php
    $sql->close();
    $conn->close();
    ?>
</body>
</html>
        