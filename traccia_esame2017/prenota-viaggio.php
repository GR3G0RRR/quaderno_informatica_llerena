<?php
session_start();

// Se l'utente non è loggato, reindirizzalo alla pagina di login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Controlla che ci sia un id_viaggio nella richiesta
if (!isset($_GET['id_viaggio']) || !is_numeric($_GET['id_viaggio'])) {
    header("Location: area-passeggero.php");
    exit();
}

$id_viaggio = intval($_GET['id_viaggio']);
$id_utente = $_SESSION['user_id'];

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carpooling";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Controlla che il viaggio esista, sia aperto e abbia ancora posti disponibili
$stmt = $conn->prepare("SELECT posti_disponibili FROM viaggi WHERE id_viaggio = ? AND stato = 'aperto'");
$stmt->bind_param("i", $id_viaggio);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Nessun viaggio trovato oppure chiuso
    $stmt->close();
    $conn->close();
    header("Location: area-passeggero.php");
    exit();
}

$viaggio = $result->fetch_assoc();
$posti_disponibili = $viaggio['posti_disponibili'];

if ($posti_disponibili <= 0) {
    // Nessun posto disponibile
    $stmt->close();
    $conn->close();
    header("Location: area-passeggero.php?error=posti_esauriti");
    exit();
}

$stmt->close();

// Registra la prenotazione
$stmt = $conn->prepare("INSERT INTO prenotazioni (id_viaggio, id_utente, stato) VALUES (?, ?, 'aperto')");
$stmt->bind_param("ii", $id_viaggio, $id_utente);
if ($stmt->execute()) {
    // Diminuisci di 1 i posti disponibili
    $update = $conn->prepare("UPDATE viaggi SET posti_disponibili = posti_disponibili - 1 WHERE id_viaggio = ?");
    $update->bind_param("i", $id_viaggio);
    $update->execute();
    $update->close();
}

$stmt->close();
$conn->close();

// Torna all'area passeggero
header("Location: area-passeggero.php");
exit();
?>
