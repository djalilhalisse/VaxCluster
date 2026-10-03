<?php
require __DIR__ . '/includes/bootstrap.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: add_patient.php'); exit; }
csrf_check();
require __DIR__ . '/configuration/db_connection.php';

$f = fn($k) => trim($_POST[$k] ?? '');
$idnum = $f('idnum'); $name = $f('name'); $gender = $f('gender'); $age = $f('age'); $adresse = $f('adresse'); $tel = $f('tel');
$vaccin = $f('vaccin'); $lot = $f('numlot'); $peremption = $f('dateperemp'); $lieuvac = $f('lieuvac'); $datevac = $f('datevac');
$heurevac = $f('timevac'); $site = $f('Site'); $dose = $f('dose');
$antecedents = array_values(array_filter((array)($_POST['antecedents'] ?? []), 'strlen'));
$new_antecedents = array_values(array_filter(array_map('trim', (array)($_POST['new_antecedents'] ?? [])), 'strlen'));

if (in_array('', [$idnum, $name, $gender, $age, $adresse, $tel, $vaccin, $lot, $peremption, $lieuvac, $datevac, $heurevac, $site, $dose], true)) {
    $_SESSION['error_message'] = 'err_required'; header('Location: add_patient.php'); exit;
}
$season = getSeason($datevac);

$st = $conn->prepare("SELECT id_patient FROM Patient WHERE id_patient = ?");
$st->bind_param('s', $idnum); $st->execute(); $st->store_result();
if ($st->num_rows > 0) { $_SESSION['error_message'] = 'err_id_exists'; header('Location: add_patient.php'); exit; }
$st->close();

$conn->begin_transaction();
try {
    $st = $conn->prepare("INSERT INTO Patient (id_patient, nom, age, sexe, adresse, tel) VALUES (?, ?, ?, ?, ?, ?)");
    $st->bind_param('ssisss', $idnum, $name, $age, $gender, $adresse, $tel); $st->execute();

    $st = $conn->prepare("INSERT INTO Vaccination (type_vaccin, num_lot, date_peremption, lieu_vaccination, date_vaccination, heure_vaccination, site, dose, season) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $st->bind_param('sssssssss', $vaccin, $lot, $peremption, $lieuvac, $datevac, $heurevac, $site, $dose, $season); $st->execute();
    $vaccination_id = $conn->insert_id;

    $st = $conn->prepare("INSERT INTO Patient_Vaccination (id_patient, id_vaccination) VALUES (?, ?)");
    $st->bind_param('si', $idnum, $vaccination_id); $st->execute();

    $link = $conn->prepare("INSERT INTO Patient_Maladie_Chronique (id_patient, id_maladie) VALUES (?, ?)");
    $find = $conn->prepare("SELECT id_maladie FROM Maladie_Chronique WHERE nom = ?");
    $ins  = $conn->prepare("INSERT INTO Maladie_Chronique (nom) VALUES (?)");
    $done = [];
    foreach (array_merge($antecedents, $new_antecedents) as $nom) {       // selected + newly typed conditions
        if (isset($done[mb_strtolower($nom)])) continue; $done[mb_strtolower($nom)] = 1;
        $find->bind_param('s', $nom); $find->execute(); $r = $find->get_result()->fetch_assoc();
        if ($r) { $mid = (int)$r['id_maladie']; }
        else { $ins->bind_param('s', $nom); $ins->execute(); $mid = $conn->insert_id; }
        $link->bind_param('si', $idnum, $mid); $link->execute();           // patient id is a string, condition id an int
    }
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log('add_patient failed: ' . $e->getMessage());
    $_SESSION['error_message'] = 'err_generic'; header('Location: add_patient.php'); exit;
}

$_SESSION['patient_info'] = ['idnum'=>$idnum,'name'=>$name,'gender'=>$gender,'age'=>$age,'adresse'=>$adresse,'tel'=>$tel,'vaccin'=>$vaccin,'lot'=>$lot,
    'peremption'=>$peremption,'lieuvac'=>$lieuvac,'datevac'=>$datevac,'heurevac'=>$heurevac,'site'=>$site,'dose'=>$dose,
    'antecedents'=>array_values(array_unique(array_merge($antecedents, $new_antecedents))),'atc'=>implode(', ', array_unique(array_merge($antecedents, $new_antecedents))),'season'=>$season];
$_SESSION['success_message'] = 'ok_added';
header('Location: inserted_patient_info.php'); exit;

function getSeason($date) {
    $m = (int)date('n', strtotime($date));
    return $m >= 3 && $m <= 5 ? 'Spring' : ($m >= 6 && $m <= 8 ? 'Summer' : ($m >= 9 && $m <= 11 ? 'Autumn' : 'Winter'));
}
