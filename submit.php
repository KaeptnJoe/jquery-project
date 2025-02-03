<?php
/**
 * submit.php
 * Dieses Skript empfängt die POST-Daten, validiert sie (zusätzlich auf Serverseite)
 * und speichert die Nachricht in einer JSON-Datei.
 */

header('Content-Type: application/json');

// Nur POST-Anfragen zulassen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Ungültige Anforderungsmethode.']);
  exit;
}

// Eingehende Daten filtern
$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Prüfen, ob alle Felder ausgefüllt wurden
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
  echo json_encode(['success' => false, 'message' => 'Bitte alle Felder ausfüllen.']);
  exit;
}

// Zusätzliche Validierung für die E-Mail-Adresse
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success' => false, 'message' => 'Ungültige E-Mail-Adresse.']);
  exit;
}

// Daten für die Speicherung vorbereiten
$dataEntry = [
  'name'      => $name,
  'email'     => $email,
  'subject'   => $subject,
  'message'   => $message,
  'timestamp' => date('Y-m-d H:i:s')
];

// Pfad zur JSON-Datei (Stelle sicher, dass der Server Schreibrechte hat!)
$jsonFile = 'messages.json';

// Bestehende Nachrichten laden, falls die Datei existiert
if (file_exists($jsonFile)) {
  $jsonData = file_get_contents($jsonFile);
  $messages = json_decode($jsonData, true);
  if (!is_array($messages)) {
    $messages = [];
  }
} else {
  $messages = [];
}

// Neue Nachricht anhängen
$messages[] = $dataEntry;

// Nachrichten in die JSON-Datei schreiben
if (file_put_contents($jsonFile, json_encode($messages, JSON_PRETTY_PRINT))) {
  echo json_encode(['success' => true, 'message' => 'Nachricht wurde erfolgreich gesendet.']);
} else {
  echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern der Nachricht.']);
}
?>
