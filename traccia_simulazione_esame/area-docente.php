<?php
session_start();

// Verifica se l'utente è loggato e docente
if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

// Variabili utente
$username = $_SESSION['username'];
$docente_id = $_SESSION['user_id'];

// Connessione al DB
$conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

// Recupera i corsi del docente
$stmt = $conn->prepare("SELECT id, nome_corso, descrizione, data_creazione FROM corsi WHERE docente_id = ?");
$stmt->bind_param("i", $docente_id);
$stmt->execute();
$result = $stmt->get_result();

// Costruisci la tabella HTML (in una variabile)
$corsi_table = "";
if ($result->num_rows > 0) {
    $corsi_table .= "<table border='1' cellpadding='8' cellspacing='0'>";
    $corsi_table .= "<tr><th>Nome Corso</th><th>Descrizione</th><th>Data Creazione</th><th>Azioni</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $corso_id = $row['id'];
        $corsi_table .= "<tr>";
        $corsi_table .= "<td><a href='corso-docente.php?id=$corso_id'>" . htmlspecialchars($row['nome_corso']) . "</a></td>";
        //serve a proteggere il tuo sito da problemi di sicurezza e visualizzazione, convertendo caratteri speciali in entità HTML sicure.
        $corsi_table .= "<td>" . htmlspecialchars($row['descrizione']) . "</td>";
        $corsi_table .= "<td>" . htmlspecialchars($row['data_creazione']) . "</td>";
        $corsi_table .= "<td>
            <form method='POST' action='elimina-corso.php' onsubmit=\"return confirm('Sei sicuro di voler eliminare questo corso?');\" style='display:inline;'>
                <input type='hidden' name='corso_id' value='$corso_id'>
                <button type='submit'>Elimina</button>
            </form>
        </td>";
        $corsi_table .= "</tr>";
    }
    $corsi_table .= "</table>";
} else {
    $corsi_table = "<p>Non hai ancora creato corsi.</p>";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Docente</title>
</head>
<body>
    <h2>Ciao, <?php echo htmlspecialchars($username); ?>!</h2>
    <p>Benvenuto nella tua area docente.</p>
    <hr>
    <h3>I tuoi corsi creati:</h3>
    <button><a href="crea-corso.php">Crea nuovo corso</a></button>
    <br><br>
    <?php echo $corsi_table; ?>
    <br><a href="logout.php">Logout</a>
</body>
</html>
