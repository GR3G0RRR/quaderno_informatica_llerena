<?php
session_start();

// Verifica che l'utente sia loggato e sia un autista
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'autista') {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Dati connessione
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "carpooling";

    // Connessione al database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Recupera l'id_autista corretto dalla tabella autisti
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT id_autista FROM autisti WHERE id_utente = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $autista = $result->fetch_assoc();
    $stmt->close();

    if (!$autista) {
        die("Non sei registrato come autista.");
    }

    $id_autista = $autista['id_autista'];

    // Recupera i dati dal form
    $citta_partenza = $_POST['citta_partenza'];
    $citta_destinazione = $_POST['citta_destinazione'];
    $data_partenza = $_POST['data_partenza'];
    $ora_partenza = $_POST['ora_partenza'];
    $contributo_economico = $_POST['contributo_economico'];
    $tempo_stimato = $_POST['tempo_stimato'];
    $posti_disponibili = $_POST['posti_disponibili'];
    $stato = "aperto"; // Di default

    // Query per inserire il viaggio
    $sql = $conn->prepare("INSERT INTO viaggi (id_autista, citta_partenza, citta_destinazione, data_partenza, ora_partenza, contributo_economico, tempo_stimato, posti_disponibili, stato) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("isssssiis", $id_autista, $citta_partenza, $citta_destinazione, $data_partenza, $ora_partenza, $contributo_economico, $tempo_stimato, $posti_disponibili, $stato);

    if ($sql->execute()) {
        $message = "Viaggio creato con successo!";
    } else {
        $message = "Errore nella creazione del viaggio: " . $sql->error;
    }

    $sql->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Viaggio</title>
    <script>
        <?php if (!empty($message)) : ?>
            alert(<?php echo json_encode($message); ?>);
            <?php if (strpos($message, "successo") !== false) : ?>
                window.location.href = "area-autista.php"; // Torna all'area autista se creazione avvenuta
            <?php endif; ?>
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Crea Viaggio</h1>

    <a href="area-autista.php">🔙 Torna all'Area Autista</a><br><br>

    <form action="crea-viaggio.php" method="POST">
        <p>Città di Partenza:</p>
        <input type="text" name="citta_partenza" required>

        <p>Città di Destinazione:</p>
        <input type="text" name="citta_destinazione" required>

        <p>Data di Partenza:</p>
        <input type="date" name="data_partenza" required>

        <p>Ora di Partenza:</p>
        <input type="time" name="ora_partenza" required>

        <p>Contributo Economico (€):</p>
        <input type="text" step="0.01" name="contributo_economico" required>

        <p>Tempo Stimato:</p>
        <input type="text" name="tempo_stimato" required>

        <p>Posti Disponibili:</p>
        <input type="number" name="posti_disponibili" required>

        <br><br>
        <input type="submit" value="Crea Viaggio">
    </form>
</body>
</html>
