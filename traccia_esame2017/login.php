<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connessione al DB
    $conn = new mysqli("localhost", "root", "", "carpooling");

    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Controlla l'utente con quella email
    $stmt = $conn->prepare("SELECT id_utente, password, ruolo FROM utenti WHERE email = ?");
    if (!$stmt) {
        die("Errore nella query: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Bind dei risultati
    $stmt->bind_result($id_utente, $hashed_password, $ruolo);

    if ($stmt->fetch()) {
        // Verifica la password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id_utente;
            $_SESSION['ruolo'] = $ruolo;

            // Reindirizzamento in base al ruolo
            if ($ruolo === 'autista') {
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
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    <form method="POST" action="login.php">
        <p>Email:</p>
        <input type="email" name="email" required>

        <p>Password:</p>
        <input type="password" name="password" required>

        <br><br>
        <input type="submit" value="Accedi">
    </form>

    <?php if (isset($errore)) : ?>
        <script>alert("<?= $errore ?>");</script>
    <?php endif; ?>
</body>
</html>
