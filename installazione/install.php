<?php
// Configura le credenziali del database
$servername = "localhost";
$username = "root";  // Usato per XAMPP
$password = "";      // Password per XAMPP
$db_name = "mio_database";  // Nome del nuovo database

// Crea la connessione
$conn = new mysqli($servername, $username, $password);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Crea il database
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql) === TRUE) {
    echo "Database creato con successo!<br>";
} else {
    echo "Errore nella creazione del database: " . $conn->error . "<br>";
}

// Seleziona il database appena creato
$conn->select_db($db_name);

// Crea una tabella utenti
$sql = "CREATE TABLE IF NOT EXISTS utenti (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    reg_date TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Tabella 'utenti' creata con successo!<br>";
} else {
    echo "Errore nella creazione della tabella utenti: " . $conn->error . "<br>";
}

// Popola la tabella con alcuni dati di esempio
$sql = "INSERT INTO utenti (nome, email)
    VALUES ('Mario Rossi', 'mario@example.com'),
           ('Giuseppe Verdi', 'giuseppe@example.com')";

if ($conn->query($sql) === TRUE) {
    echo "Dati di esempio inseriti nella tabella utenti!<br>";
} else {
    echo "Errore nell'inserimento dei dati: " . $conn->error . "<br>";
}

// Redirigi alla pagina di sommario dopo aver completato l'installazione
echo "<br><a href='sommario.php'>Vai al Sommario</a>";

// Chiudi la connessione
$conn->close();
?>
