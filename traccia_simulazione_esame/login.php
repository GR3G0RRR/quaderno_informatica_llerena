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

//verifico se è in metodo post
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $passwordInserita = $_POST['password'];
    //utilizzo le query preparate perchè sono importanti per la sicurezza (ad esempio per evitare attacchi sql injection).
    $stmt = $conn->prepare("SELECT id, password, is_teacher, username FROM utenti WHERE email = ?");
    //questo comando è un metodo che lega i paramteri ai segnaposti ? nella query preparata.
    $stmt->bind_param("s", $email);
    //eseguo la query che avevo preparato.
    $stmt->execute();
    //questo comando memorizza i dati ottenuti dalla query nel buffer di sistema.
    $stmt->store_result();

    //controllo quante righe sono state restituite dalla query eseguita.
    if ($stmt->num_rows === 1) {
        //associa i colonnati dei risultati della query alle variabili php.
        $stmt->bind_result($id, $hashed_password, $is_teacher, $username);
        //estraggo i dati dalla query
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
    <!--messaggio di errore-->
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
