<?php
session_start();

// DEBUG: Visualizza user_id e ruolo per controllo
echo "User ID: " . ($_SESSION['user_id'] ?? 'Non impostato') . "<br>";
echo "Ruolo: " . ($_SESSION['ruolo'] ?? 'Non impostato') . "<br>";

// Verifica che l'utente sia loggato e sia un autista
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] != 'autista') {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "carpooling";

    // Connessione al database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Recupera i dati dal form
    $id_autista = $_SESSION['user_id'];  // L'ID autista loggato
    $citta_partenza = $_POST['citta_partenza'];
    $citta_destinazione = $_POST['citta_destinazione'];
    $data_partenza = $_POST['data_partenza'];
    $ora_partenza = $_POST['ora_partenza'];
    $contributo_economico = $_POST['contributo_economico'];
    $tempo_stimato = $_POST['tempo_stimato'];
    $posti_disponibili = $_POST['posti_disponibili'];
    $stato = "aperto"; // Di default, il viaggio è "aperto"

    // Usa prepared statement per evitare SQL injection
    $sql = $conn->prepare("INSERT INTO viaggi (id_autista, citta_partenza, citta_destinazione, data_partenza, ora_partenza, contributo_economico, tempo_stimato, posti_disponibili, stato) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("isssssiis", $id_autista, $citta_partenza, $citta_destinazione, $data_partenza, $ora_partenza, $contributo_economico, $tempo_stimato, $posti_disponibili, $stato);

    if ($sql->execute()) {
        // Viaggio creato con successo, fai il redirect all'area autista
        header("Location: area-autista.php");
        exit();
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
