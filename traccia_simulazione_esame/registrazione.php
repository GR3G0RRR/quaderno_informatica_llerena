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
    //in caso di errore interrompe l'esecuzione dello script
    die('Connessione fallita: ' . $conn->connect_error);
}
//controllo che la richiestia sia in post
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];  
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $is_teacher = isset($_POST['is_teacher']) ? 1 : 0;
    //uso una query preparata (statement)
    $stmt = $conn->prepare("INSERT INTO utenti (username,email, password, is_teacher) VALUES (?,?, ?, ?)");
    $stmt->bind_param("sssi", $username,$email, $password, $is_teacher);
    //controllo che venga eseguita
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
