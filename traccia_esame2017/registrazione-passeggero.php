<?php
$message = "";
$redirect = false; // Variabile per il redirect

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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash della password
    $ruolo = "passeggero"; // Ruolo specifico per il passeggero

    // Usa Prepared Statements per maggiore sicurezza
    $sql_utente = $conn->prepare("INSERT INTO utenti (nome, cognome, data_nascita, documento, email, telefono, password, ruolo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $sql_utente->bind_param("ssssssss", $nome, $cognome, $data_nascita, $documento, $email, $telefono, $password, $ruolo);

    if ($sql_utente->execute()) {
        $message = "Registrazione avvenuta con successo!";
        $redirect = true; // Flag per il redirect
    } else {
        $message = "Errore nella registrazione: " . $sql_utente->error;
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
    <title>Registrazione Passeggero</title>
    <script>
        // Mostra l'alert e reindirizza se necessario
        <?php if (!empty($message)) : ?>
            alert(<?php echo json_encode($message); ?>);
            <?php if ($redirect) : ?>
                window.location.href = "login.html";
            <?php endif; ?>
        <?php endif; ?>
    </script>
</head>
<body>
    <h1>Registrazione Passeggero</h1>

    <form action="registrazione-passeggero.php" method="post">
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

        <p>Password:</p>
        <input type="password" name="password" required>

        <br><br>
        <input type="submit" value="Registrati">
    </form>
</body>
</html>
