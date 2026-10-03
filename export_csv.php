<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: doctors_login.php");
    exit();
}

$content = fopen('php://temp', 'r+');
$headers = ['National ID', 'Name', 'Gender', 'Age', 'Address', 'Phone Number', 'Vaccine Name', 'Batch Number', 'Expiration Date', 'Vaccination Location', 'Vaccination Date', 'Vaccination Time', 'Administration Site', 'Dose', 'Antecedents and Terrain', 'Side Effects', 'Date d\'ajout', 'Season of vaccination'];
fputcsv($content, $headers);

require('./configuration/db_connection.php');
$sql = "SELECT 
            Patient.id_patient, 
            Patient.nom, 
            Patient.sexe, 
            Patient.age, 
            Patient.adresse, 
            Patient.tel, 
            Vaccination.type_vaccin, 
            Vaccination.num_lot, 
            Vaccination.date_peremption, 
            Vaccination.lieu_vaccination, 
            Vaccination.date_vaccination, 
            Vaccination.heure_vaccination, 
            Vaccination.site, 
            Vaccination.dose, 
            GROUP_CONCAT(DISTINCT Maladie_Chronique.nom SEPARATOR ', ') AS antecedents, 
            GROUP_CONCAT(DISTINCT Effet_Secondaire.description SEPARATOR ', ') AS side_effects, 
            Patient.createdate, 
            Vaccination.season
        FROM Patient
        LEFT JOIN Patient_Maladie_Chronique ON Patient.id_patient = Patient_Maladie_Chronique.id_patient
        LEFT JOIN Maladie_Chronique ON Patient_Maladie_Chronique.id_maladie = Maladie_Chronique.id_maladie
        LEFT JOIN Patient_Vaccination ON Patient.id_patient = Patient_Vaccination.id_patient
        LEFT JOIN Vaccination ON Patient_Vaccination.id_vaccination = Vaccination.id_vaccination
        LEFT JOIN Vaccination_Effet_Secondaire ON Vaccination.id_vaccination = Vaccination_Effet_Secondaire.id_vaccination
        LEFT JOIN Effet_Secondaire ON Effet_Secondaire.id_effet = Vaccination_Effet_Secondaire.id_effet
        GROUP BY Patient.id_patient
        ORDER BY Patient.createdate DESC";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $rowData = [
            $row['id_patient'],
            $row['nom'],
            $row['sexe'],
            $row['age'],
            $row['adresse'],
            $row['tel'],
            $row['type_vaccin'],
            $row['num_lot'],
            $row['date_peremption'],
            $row['lieu_vaccination'],
            $row['date_vaccination'],
            $row['heure_vaccination'],
            $row['site'],
            $row['dose'],
            $row['antecedents'] ?: 'No Background',
            $row['side_effects'],
            $row['createdate'],
            $row['season'],
        ];
        fputcsv($content, $rowData);
    }
}

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="patients.csv"');

echo "\xEF\xBB\xBF";
rewind($content);
fpassthru($content);
fclose($content);
?>
