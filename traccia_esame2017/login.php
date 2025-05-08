<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT id_utente, nome, password, ruolo FROM utenti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password_input, $user['password'])) {
            // Login riuscito: salvataggio dati nella sessione
            $_SESSION['user_id'] = $user['id_utente'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['ruolo'] = $user['ruolo'];

            // Reindirizza in base al ruolo
            if ($user['ruolo'] === 'autista') {
                header("Location: area-autista.php");
            } else {
                header("Location: area-passeggero.php");
            }
            exit();
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

<!-- HTML semplice per il login -->
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h2>Accedi</h2>
    <?php if (isset($errore)) echo "<p style='color: red;'>$errore</p>"; ?>
    <form method="post">
        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
