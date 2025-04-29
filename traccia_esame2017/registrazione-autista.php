<?php
$message = "";
$redirect = false;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "carpooling";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Dati dal form
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    $documento = $_POST['documento'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ruolo = "autista";

    $numero_patente = $_POST['numero_patente'];
    $scadenza_patente = $_POST['scadenza_patente'];
    $veicolo = $_POST['veicolo'];
    $targa_veicolo = $_POST['targa_veicolo'];

    // Query utenti
    $sql_utente = $conn->prepare("INSERT INTO utenti (nome, cognome, data_nascita, documento, email, telefono, password, ruolo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$sql_utente) {
        die("Errore query utenti: " . $conn->error);
    }

    $sql_utente->bind_param("ssssssss", $nome, $cognome, $data_nascita, $documento, $email, $telefono, $password, $ruolo);

    if ($sql_utente->execute()) {
        $id_utente = $conn->insert_id;

        // Query autista
        $sql_autista = $conn->prepare("INSERT INTO autisti (id_utente, numero_patente, scadenza_patente, veicolo, targa_veicolo) 
                                       VALUES (?, ?, ?, ?, ?)");
        if (!$sql_autista) {
            die("Errore query autista: " . $conn->error);
        }

        $sql_autista->bind_param("issss", $id_utente, $numero_patente, $scadenza_patente, $veicolo, $targa_veicolo);

        if ($sql_autista->execute()) {
            $message = "Registrazione avvenuta con successo!";
            $redirect = true;
        } else {
            $message = "Errore inserimento autista: " . $sql_autista->error;
        }

        $sql_autista->close();
    } else {
        $message = "Errore inserimento utente: " . $sql_utente->error;
    }

    $sql_utente->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Registrazione Autista</title>
</head>
<body>
    <h1>Registrazione Autista</h1>

    <form action="registrazione-autista.php" method="post">
        <p>Nome: <input type="text" name="nome" required></p>
        <p>Cognome: <input type="text" name="cognome" required></p>
        <p>Data di nascita: <input type="date" name="data_nascita" required></p>
        <p>Documento: <input type="text" name="documento" required></p>
        <p>Email: <input type="email" name="email" required></p>
        <p>Telefono: <input type="tel" name="telefono" required></p>
        <p>Password: <input type="password" name="password" required></p>
        <p>Numero patente: <input type="text" name="numero_patente" required></p>
        <p>Scadenza patente: <input type="date" name="scadenza_patente" required></p>
        <p>Veicolo: <input type="text" name="veicolo" required></p>
        <p>Targa veicolo: <input type="text" name="targa_veicolo" required></p>
        <p>sei un passeggero? <a href="registrazione-passeggero.php">registrati come passeggero</a></p>
        <p>hai un account? <a href="login.php">accedi</a></p>
        <p><input type="submit" value="Registrati"></p>
    </form>

    <?php if (!empty($message)): ?>
        <script>
            alert(<?php echo json_encode($message); ?>);
            <?php if ($redirect): ?>
                window.location.href = "login.php";
            <?php endif; ?>
        </script>
    <?php endif; ?>
</body>
</html>
