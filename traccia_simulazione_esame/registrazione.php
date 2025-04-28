<?php
//avvio una sessione o riprendo la sessione già aperta
session_start();
//connesione al database
//nome del server:
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
    //dichiaro username e prendo dalla pagina html il valore nel campo name = "username" che poi verrà memorizzato in $username;
    $username = $_POST['username'];
    //dichiaro email e prendo dalla pagina html il valore del campo name = "email" che poi verrà memorizzato in $email;
    $email = $_POST['email'];
    
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_teacher = isset($_POST['is_teacher']) ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO utenti (username,email, password, is_teacher) VALUES (?,?, ?, ?)");
    $stmt->bind_param("sssi", $username,$email, $password, $is_teacher);

    if ($stmt->execute()) {
        echo "Registrazione completata. <a href='login.php'>Vai al login</a>";
    } else {
        echo "Errore: " . $stmt->error;
    }
}
?>

<h2>Registrati</h2>
<form method="post">
    <p>username</p>
    <input type="text" name="username" required><br>
    <p>Email:</p>
    <input type="email" name="email" required><br>
    <p>Password:</p> 
    <input type="password" name="password" required>
    <br><input type="checkbox" name="is_teacher"><p>Sei un docente?</p>
    <p><a href="login.php">hai un account? accedi</a></p>
    <button type="submit">Registrati</button>

</form>
