<?php
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_teacher']) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['corso_id'])) {
    $corso_id = intval($_POST['corso_id']);
    $docente_id = $_SESSION['user_id'];

    $conn = new mysqli('localhost', 'root', '', 'corsi_linguistici');
    if ($conn->connect_error) {
        die("Errore di connessione: " . $conn->connect_error);
    }

    // Elimina esercizi e iscrizioni prima del corso (se non hai ON DELETE CASCADE)
    $stmt1 = $conn->prepare("DELETE FROM esercizi WHERE corso_id = ?");
    $stmt1->bind_param("i", $corso_id);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("DELETE FROM iscrizioni WHERE corso_id = ?");
    $stmt2->bind_param("i", $corso_id);
    $stmt2->execute();
    $stmt2->close();

    // Elimina il corso solo se è del docente loggato
    $stmt3 = $conn->prepare("DELETE FROM corsi WHERE id = ? AND docente_id = ?");
    $stmt3->bind_param("ii", $corso_id, $docente_id);
    if ($stmt3->execute()) {
        $stmt3->close();
        $conn->close();
        header("Location: area-docente.php");
        exit;
    } else {
        echo "Errore durante l'eliminazione.";
    }
} else {
    echo "Richiesta non valida.";
}
?>
