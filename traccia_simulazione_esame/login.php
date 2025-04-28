<?php
session_start();

// Connessione al database:

//nome del server
$servername = 'localhost';
//username dell'utente che ho su xampp
$username = 'root';
//password dell'utente che ho su xampp
$password = '';
//nome del database che ho su xampp
$dbname = 'corsi_linguistici';

//eseguo la connesione al database con questo comando:
$conn = new mysqli($servername, $username, $password, $dbname);

// verifico se ce un errore con la connessione al database
if ($conn->connect_error) {
    //in caso di errore 'die" interrompe l'esecuzione dello script mostrando il messaggio specifico dal database
    die('Connessione fallita: ' . $conn->connect_error);
}

//$_server[request_method] è una variabile speciale che ti dice con quale metodo http è stata fatta la richiesta di invio del form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //dichiaro email e prendo dalla pagina html il valore del campo name = "email" che poi verrà memorizzato in $email;
    $email = $_POST['email'];
    //dichiaro $password inserita e prendo dalla pagina html il valore del campo name = "password" che poi verrà memorizzato in $password;
    $passwordInserita = $_POST['password'];

    //utilizzo le query preparate perchè sono importanti per la sicurezza (ad esempio per evitare attacchi sql injection).
    //$conn-> prepare(...) questo comando prepara una query per essere eseguita in modo sicuro.
    //la query prende id, password, is_teacher, username dalla tabella utente dove l'email corrisponde a un certo valore.
    $stmt = $conn->prepare("SELECT id, password, is_teacher, username FROM utenti WHERE email = ?");
    //questo comando è un metodo che lega i paramteri ai segnaposti ? nella query preparata.
    //"s" indica il tipo di dato che sto passando, in questo caso s sta per stringa.
    $stmt->bind_param("s", $email);
    //eseguo la query che avevo preparato.
    $stmt->execute();
    //questo comando memorizza i dati ottenuti dalla query nel buffer di sistema.
    $stmt->store_result();

    //controllo quante righe sono state restituite dalla query eseguita.
    if ($stmt->num_rows === 1) {
        //associa i colonnati dei risultati della query alle variabili php.
        $stmt->bind_result($id, $hashed_password, $is_teacher, $username);
        //fetch() è utilizzato per estrarre i dati dalla query e popolare le variabili che ho precedemente legato tramite bind_result(...)
        $stmt->fetch();

        
        if (password_verify($passwordInserita, $hashed_password)) {
            // Salvo i dati nella sessione
            $_SESSION['user_id'] = $id;
            $_SESSION['is_teacher'] = $is_teacher;
            $_SESSION['username'] = $username;

            // Reindirizzo all'area corretta
            if ($is_teacher) {
                //se sono docente mi porta nella pagina del docente
                header("Location: area-docente.php");
            } else {
                //in caso contrario mi porta nella pagina dello studente
                header("Location: area-studente.php");
            }
            exit;
        } else {
            $errore = "Password errata.";
        }
    } else {
        $errore = "Utente non trovato.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($errore)): ?>
        <p style="color: red;"><?php echo $errore; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <p>Non sei registrato? <a href="registrazione.php">Registrati</a></p>
</body>
</html>
