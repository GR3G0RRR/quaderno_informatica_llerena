<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    // Filtra e valida i dati in ingresso
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Indirizzo email non valido.";
        header("Location: Signup.php"); // Ritorna alla pagina di registrazione
        exit();
    }

    // Crittografia della password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Verifica se l'username o l'email esistono già
    $check_sql = "SELECT id FROM utenti_registrati WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Username o email già in uso.";
        header("Location: errore.html"); // Ritorna alla pagina di registrazione
        echo "<h1>l'utente già esiste</h1>";
        exit();
    }

    // Query per inserire i dati nella tabella
    $sql = "INSERT INTO utenti_registrati (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrazione completata! Puoi accedere ora.";
        header("Location: signin.php"); // Redirect alla pagina di login
        exit();
    } else {
        $_SESSION['error'] = "Errore durante la registrazione. Riprova.";
        header("Location: Signup.php"); // Ritorna alla pagina di registrazione
        exit();
    }

    // Chiudi gli statement
    $check_stmt->close();
    $stmt->close();
}

// Chiudi la connessione al database
$conn->close();
?>
