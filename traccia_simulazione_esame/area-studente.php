<?php
session_start();

// Controllo accesso come studente
if (!isset($_SESSION['user_id']) || $_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

$studente_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Connessione al database
$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Se ha cliccato su "Iscriviti"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['corso_id'])) {
    $corso_id = intval($_POST['corso_id']);

    // Verifica se già iscritto
    $check = $conn->prepare("SELECT * FROM iscrizioni WHERE studente_id = ? AND corso_id = ?");
    $check->bind_param("ii", $studente_id, $corso_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO iscrizioni (studente_id, corso_id) VALUES (?, ?)");
        $insert->bind_param("ii", $studente_id, $corso_id);
        $insert->execute();
        $insert->close();
    }
    $check->close();
}

// Corsi disponibili
$query = "SELECT corsi.id, corsi.nome_corso, corsi.descrizione, corsi.data_creazione, utenti.username AS docente 
          FROM corsi 
          JOIN utenti ON corsi.docente_id = utenti.id";
$result = $conn->query($query);

// Corsi a cui lo studente è iscritto
$iscrizioni = [];
$check_iscrizioni = $conn->prepare("SELECT corso_id FROM iscrizioni WHERE studente_id = ?");
$check_iscrizioni->bind_param("i", $studente_id);
$check_iscrizioni->execute();
$check_iscrizioni->bind_result($cid);
while ($check_iscrizioni->fetch()) {
    $iscrizioni[] = $cid;
}
$check_iscrizioni->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Area Studente</title>
</head>
<body>
    <h2>Ciao, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Benvenuto nella tua area studente.</p>

    <h3>Corsi disponibili:</h3>

    <form method="post">
        <table border="1" cellpadding="5">
            <tr>
                <th>Corso</th>
                <th>Descrizione</th>
                <th>Data</th>
                <th>Docente</th>
                <th>Azione</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php
                        if (in_array($row['id'], $iscrizioni)) {
                            echo "<a href='corso.php?id=" . $row['id'] . "'>" . htmlspecialchars($row['nome_corso']) . "</a>";
                        } else {
                            echo htmlspecialchars($row['nome_corso']);
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['descrizione']); ?></td>
                    <td><?php echo htmlspecialchars($row['data_creazione']); ?></td>
                    <td><?php echo htmlspecialchars($row['docente']); ?></td>
                    <td>
                        <?php if (in_array($row['id'], $iscrizioni)): ?>
                            <button type="button" disabled>Iscritto</button>
                        <?php else: ?>
                            <button type="submit" name="corso_id" value="<?php echo $row['id']; ?>">Iscriviti</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </form>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
