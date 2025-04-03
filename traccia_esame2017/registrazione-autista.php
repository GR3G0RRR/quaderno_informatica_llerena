<?php
$message = "";
$redirect = false; // Variabile per indicare se fare il redirect

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "carpooling";

    // connessione al database
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        //controllo che venga eseguita la connessione
        die("Connessione fallita: " . $conn->connect_error);
    }

    // recupera i dati dal form
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    $documento = $_POST['documento'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $ruolo = "autista";

    $numero_patente = $_POST['numero_patente'];
    $scadenza_patente = $_POST['scadenza_patente'];
    $veicolo = $_POST['veicolo'];
    $targa_veicolo = $_POST['targa_veicolo'];

    // Usa Prepared Statements per sicurezza
    $sql_utente = $conn->prepare("INSERT INTO utenti (nome, cognome, data_nascita, documento, email, telefono, ruolo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql_utente->bind_param("sssssss", $nome, $cognome, $data_nascita, $documento, $email, $telefono, $ruolo);

    if ($sql_utente->execute()) {
        $id_utente = $conn->insert_id;

        $sql_autista = $conn->prepare("INSERT INTO autisti (id_utente, numero_patente, scadenza_patente, veicolo, targa_veicolo) 
                                       VALUES (?, ?, ?, ?, ?)");
        $sql_autista->bind_param("issss", $id_utente, $numero_patente, $scadenza_patente, $veicolo, $targa_veicolo);

        if ($sql_autista->execute()) { //controllo che la query venga eseguita in maniera corretta
            $message = "Registrazione avvenuta con successo!";
            $redirect = true; //esegue il reindirizzamento alla pagina "login.html"
        } else {
            $message = "Errore nell'inserimento dell'autista: " . $sql_autista->error;
        }
        $sql_autista->close();
    } else {
        $message = "Errore nell'inserimento dell'utente: " . $sql_utente->error;
    }

    $sql_utente->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Autista</title>
    <script>
        // mostra un alert e lo indirizza se necessario
        <?php if (!empty($message)) : ?>
            alert(<?php echo json_encode($message); ?>);
            <?php if ($redirect) : ?>
                window.location.href = "login.html";
            <?php endif; ?>
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Registrazione Autista</h1>

    <form action="registrazione-autista.php" method="post">
        <p>Nome:</p>
        <input type="text" name="nome" required>
        <p>Cognome:</p>
        <input type="text" name="cognome" required>
        <p>Data di nascita:</p>
        <input type="date" name="data_nascita" required>
        <p>Documento:</p>
        <input type="text" name="documento" required>
        <p>Email:</p>
        <input type="email" name="email" required>
        <p>Telefono:</p>
        <input type="tel" name="telefono" required>
        <p>Numero patente:</p>
        <input type="text" name="numero_patente" required>
        <p>Scadenza patente:</p>
        <input type="date" name="scadenza_patente" required>
        <p>Veicolo:</p>
        <input type="text" name="veicolo" required>
        <p>Targa veicolo:</p>
        <input type="text" name="targa_veicolo" required>
        <br><br>
        <input type="submit" value="Registrati">
    </form>
</body>
</html>
