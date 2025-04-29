<?php
$message = ""; //dichiaro un messaggio vuoto momentaneamente
$redirect = false; // Variabile per il redirect
//verifico che il form sia in metodo post
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //nome server:
    $servername = "localhost";
    //nome utente dell'account con privilegi
    $username = "root";
    //password dell'account con privilegi
    $password = "";
    //nome del database
    $dbname = "carpooling";

    // Connessione al database coi dati scritti prima
    $conn = new mysqli($servername, $username, $password, $dbname);
    //se la connessione falisce
    if ($conn->connect_error) {
        //risulato del database:
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Recupera i dati dal form
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $data_nascita = $_POST['data_nascita'];
    $documento = $_POST['documento'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    //password hash è un metodo per crittografare la password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash della password
    $ruolo = "passeggero"; // Ruolo specifico per il passeggero

    // Uso degli Statements per maggiore sicurezza, mi preparo la query
    $sql_utente = $conn->prepare("INSERT INTO utenti (nome, cognome, data_nascita, documento, email, telefono, password, ruolo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    //? = wild mask ovvero copro i valori.
    $sql_utente->bind_param("ssssssss", $nome, $cognome, $data_nascita, $documento, $email, $telefono, $password, $ruolo);
    //verifico che la query venga eseguita
    if ($sql_utente->execute()) {
        $message = "Registrazione avvenuta con successo!";
        $redirect = true; // Flag per il redirect
    } else {
        $message = "Errore nella registrazione: " . $sql_utente->error;
    }
    //chiudo l'esecuzione della query
    $sql_utente->close();
    //chiudo la connessione
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
        // verifico se il messaggio iniziale non è vuoto
        <?php if (!empty($message)) : ?>
            //stampo il messaggio tramite pop-up
            alert(<?php echo json_encode($message); ?>);
            //
            <?php if ($redirect) : ?>
                window.location.href = "login.php";
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

        <br>
        <p>sei un autista? <a href="registrazione-autista.php">registrati come autista</a></p>
        <p>hai un account? <a href="login.php">accedi</a></p>
        <input type="submit" value="Registrati">
    </form>
</body>
</html>
