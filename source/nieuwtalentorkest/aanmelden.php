<?php
// Verstuurt aanmeldingen voor het Nieuw Talent Orkest per mail. Er wordt niets opgeslagen.

$to = 'nto@togidohekelingen.nl';
$from = 'noreply@togidohekelingen.nl';
$instrumenten = ['trompet', 'trombone', 'bugel', 'bas', 'saxofoon', 'slagwerk', 'anders', 'weet ik nog niet', 'geen voorkeur'];

function fail(string $msg): never {
    http_response_code(400);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p>' . htmlspecialchars($msg) . '</p><p><a href="javascript:history.back()">Terug naar het formulier</a></p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /nieuwtalentorkest/');
    exit;
}

// Honeypot: bots vullen dit verborgen veld in, mensen niet.
if (!empty($_POST['website'])) {
    header('Location: /nieuwtalentorkest/bedankt.html');
    exit;
}

// Regeleindes eruit, zodat niets in de mailheaders kan lekken.
$field = fn(string $k) => trim(str_replace(["\r", "\n"], ' ', (string)($_POST[$k] ?? '')));

$naam = $field('naam');
$adres = $field('adres');
$telefoon = $field('telefoon');
$email = filter_var($field('email'), FILTER_VALIDATE_EMAIL);
$instrument = $field('instrument');

if ($naam === '' || $adres === '' || $telefoon === '') fail('Vul alle velden in.');
if ($email === false) fail('Vul een geldig e-mailadres in.');
if (!in_array($instrument, $instrumenten, true)) fail('Kies een instrument.');

$body = "Nieuwe aanmelding Nieuw Talent Orkest\n\n"
    . "Naam: $naam\n"
    . "Adres: $adres\n"
    . "Telefoon: $telefoon\n"
    . "E-mail: $email\n"
    . "Voorkeur instrument: $instrument\n";

$sent = mail(
    $to,
    '=?UTF-8?B?' . base64_encode("Aanmelding NTO: $naam") . '?=',
    $body,
    [
        'From' => $from,
        'Reply-To' => $email,
        'Content-Type' => 'text/plain; charset=utf-8',
    ]
);

if (!$sent) fail('Er ging iets mis bij het versturen. Probeer het later opnieuw of mail naar ' . $to . '.');

header('Location: /nieuwtalentorkest/bedankt.html');
