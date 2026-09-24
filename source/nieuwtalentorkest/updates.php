<?php
// Verstuurt een e-mailadres voor NTO-updates per mail. Er wordt niets opgeslagen.

$to = 'nto@togidohekelingen.nl';
$from = 'noreply@togidohekelingen.nl';

function fail(string $msg): never {
    http_response_code(400);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>' . htmlspecialchars($msg) . '</p><p><a href="javascript:history.back()">Terug naar het formulier</a></p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /nieuwtalentorkest/updates/');
    exit;
}

// Honeypot: bots vullen dit verborgen veld in, mensen niet.
if (!empty($_POST['website'])) {
    header('Location: /nieuwtalentorkest/updates/bedankt.html');
    exit;
}

$email = filter_var(trim(str_replace(["\r", "\n"], '', (string)($_POST['email'] ?? ''))), FILTER_VALIDATE_EMAIL);
if ($email === false) fail('Vul een geldig e-mailadres in.');

$sent = mail(
    $to,
    '=?UTF-8?B?' . base64_encode("Updates NTO: $email") . '?=',
    "Iemand wil op de hoogte blijven van het Nieuw Talent Orkest.\n\nE-mail: $email\n",
    [
        // Geen Reply-To met het adres van de aanmelder: een freemail-Reply-To laat de spamscore van TransIP oplopen.
        'From' => "TOGIDO website <$from>",
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=utf-8',
        'Content-Transfer-Encoding' => '8bit',
    ]
);

if (!$sent) fail('Er ging iets mis bij het versturen. Probeer het later opnieuw of mail naar ' . $to . '.');

header('Location: /nieuwtalentorkest/updates/bedankt.html');
