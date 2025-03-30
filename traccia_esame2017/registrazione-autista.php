<?php
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

    $sql_utente = "INSERT INTO utenti (nome, cognome, data_nascita, documento, email, telefono, ruolo) 
                   VALUES ('$nome', '$cognome', '$data_nascita', '$documento', '$email', '$telefono', '$ruolo')";

    if ($conn->query($sql_utente) === TRUE) {
        $id_utente = $conn->insert_id;

        $sql_autista = "INSERT INTO autista (id_utente, numero_patente, scadenza_patente, veicolo, targa_veicolo) 
                        VALUES ('$id_utente', '$numero_patente', '$scadenza_patente', '$veicolo', '$targa_veicolo')";

        if ($conn->query($sql_autista) === TRUE) {
            $message = "Registrazione avvenuta con successo!";
        } else {
            $message = "Errore nell'inserimento dell'autista: " . $conn->error;
        }
    } else {
        $message = "Errore nell'inserimento dell'utente: " . $conn->error;
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione Autista</title>
</head>
<body>
    <h1>Registrazione Autista</h1>

    <?php if (!empty($message)) : ?>
        <script>
            alert(<?php echo json_encode($message); ?>);
        </script>
    <?php endif; ?>

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
