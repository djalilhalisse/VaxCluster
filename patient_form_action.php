<?php
require __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['btn_submit'])) { header('Location: patient_form.php'); exit; }
csrf_check();
require __DIR__ . '/configuration/db_connection.php';
$back = 'patient_form.php';

$idnum = trim($_POST['idnum'] ?? '');
$st = $conn->prepare("SELECT 1 FROM Patient WHERE id_patient = ?");
$st->bind_param('s', $idnum); $st->execute();
if ($st->get_result()->num_rows === 0) { $_SESSION['error_message'] = 'err_id_missing'; header("Location: $back"); exit; }

$effects = array_unique(array_filter(array_map('trim', (array)($_POST['side_effects'] ?? [])), 'strlen'));
$dateAppar = $_POST['dateappar'] ?? ''; $duree = (int)($_POST['dureemanif'] ?? 0);

$st = $conn->prepare("SELECT id_vaccination FROM Patient_Vaccination WHERE id_patient = ? ORDER BY id_vaccination DESC LIMIT 1");
$st->bind_param('s', $idnum); $st->execute();
$row = $st->get_result()->fetch_assoc();
if (!$row) { $_SESSION['error_message'] = 'err_generic'; header("Location: $back"); exit; }
$vid = (int)$row['id_vaccination'];

$conn->begin_transaction();
try {
    $find = $conn->prepare("SELECT id_effet FROM Effet_Secondaire WHERE description = ?");
    $ins  = $conn->prepare("INSERT INTO Effet_Secondaire (description) VALUES (?)");
    $has  = $conn->prepare("SELECT 1 FROM Vaccination_Effet_Secondaire WHERE id_vaccination = ? AND id_effet = ?");
    $rel  = $conn->prepare("INSERT INTO Vaccination_Effet_Secondaire (id_vaccination, id_effet) VALUES (?, ?)");
    foreach ($effects as $e) {
        $find->bind_param('s', $e); $find->execute(); $r = $find->get_result()->fetch_assoc();
        if ($r) { $eid = (int)$r['id_effet']; } else { $ins->bind_param('s', $e); $ins->execute(); $eid = $conn->insert_id; }
        $has->bind_param('ii', $vid, $eid); $has->execute();
        if ($has->get_result()->num_rows === 0) { $rel->bind_param('ii', $vid, $eid); $rel->execute(); }   // no duplicates
    }
    if ($effects) {
        $up = $conn->prepare("UPDATE Patient_Vaccination SET date_apparition = ?, duree_manifestation = ? WHERE id_vaccination = ? AND id_patient = ?");
        $up->bind_param('siis', $dateAppar, $duree, $vid, $idnum); $up->execute();
    }
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback(); error_log('patient_form failed: ' . $e->getMessage());
    $_SESSION['error_message'] = 'err_generic'; header("Location: $back"); exit;
}
$_SESSION['success_message'] = 'ok_effects';
header("Location: $back"); exit;
