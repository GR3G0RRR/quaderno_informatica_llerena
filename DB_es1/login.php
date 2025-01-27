<?php
session_start();

// Configurazione del database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "utenti";

// Crea connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controlla connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Controlla se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Query per verificare le credenziali
    $sql = "SELECT password FROM utenti_registrati WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if ($hashed_password && password_verify($password, $hashed_password)) {
        $_SESSION['username'] = $username;
        header("Location: messaggio1.html"); // Redirect alla dashboard
        exit();
    } else {
        $_SESSION['error'] = "Credenziali non valide.";
        header("Location: signin.php"); // Torna alla pagina di login
        exit();
    }

    $stmt->close();
}

// Chiudi la connessione al database
$conn->close();
?>
