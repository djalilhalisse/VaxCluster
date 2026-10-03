<?php require __DIR__.'/includes/bootstrap.php'; require_login(); require __DIR__.'/configuration/db_connection.php';
$id=$_GET['id']??''; if($id===''){header('Location: patients_list.php');exit;}
$st=$conn->prepare("SELECT Patient.*,GROUP_CONCAT(DISTINCT Maladie_Chronique.nom SEPARATOR '|') antecedents,GROUP_CONCAT(DISTINCT Effet_Secondaire.description SEPARATOR '|') side_effects,
 Vaccination.type_vaccin,Vaccination.date_vaccination,Vaccination.num_lot,Vaccination.date_peremption,Vaccination.lieu_vaccination,Vaccination.season,Vaccination.site,Vaccination.dose,Vaccination.heure_vaccination,Patient_Vaccination.date_apparition,Patient_Vaccination.duree_manifestation
 FROM Patient LEFT JOIN Patient_Maladie_Chronique ON Patient.id_patient=Patient_Maladie_Chronique.id_patient LEFT JOIN Maladie_Chronique ON Patient_Maladie_Chronique.id_maladie=Maladie_Chronique.id_maladie
 LEFT JOIN Patient_Vaccination ON Patient.id_patient=Patient_Vaccination.id_patient LEFT JOIN Vaccination ON Patient_Vaccination.id_vaccination=Vaccination.id_vaccination
 LEFT JOIN Vaccination_Effet_Secondaire ON Vaccination.id_vaccination=Vaccination_Effet_Secondaire.id_vaccination LEFT JOIN Effet_Secondaire ON Effet_Secondaire.id_effet=Vaccination_Effet_Secondaire.id_effet
 WHERE Patient.id_patient=? GROUP BY Patient.id_patient");
$st->bind_param('s',$id);$st->execute();$p=$st->get_result()->fetch_assoc();
if(!$p){open_page(t('nf_h'),'app','list');echo '<div class="card empty">'.h(t('nf_p',['id'=>$id])).' <a href="patients_list.php">'.h(t('back_patients')).'</a></div>';close_page('app');exit;}
$sec=['sec_patient'=>['id_nat'=>['id_patient',0],'f_gender'=>['sexe',1],'f_age'=>['age',0],'f_phone'=>['tel',0],'f_addr'=>['adresse',0],'th_added'=>['createdate',0]],
 'sec_vacc'=>['f_vaccine'=>['type_vaccin',1],'f_dose'=>['dose',1],'f_batch'=>['num_lot',0],'f_expiry'=>['date_peremption',0],'f_vacdate'=>['date_vaccination',0],'f_time'=>['heure_vaccination',0],'f_loc'=>['lieu_vaccination',0],'f_site'=>['site',1],'season'=>['season',1]]];
open_page($p['nom'],'app','list',$p['nom'],'<a class="btn sm ghost" href="patients_list.php">'.h(t('back')).'</a><a class="btn sm" href="patient_form.php?id='.urlencode($p['id_patient']).'">'.icon('clip').h(t('nav_report')).'</a>'); ?>
<div class="cols"><div>
<?php foreach($sec as $title=>$fields): ?><div class="card" style="margin-bottom:18px"><h3><?=h(t($title))?></h3><dl class="dgrid"><?php foreach($fields as $l=>[$k,$tr]): $v=$p[$k]; ?><div><dt><?=h(t($l))?></dt><dd><?=h($v===null||$v===''?'–':($tr?td($v):$v))?></dd></div><?php endforeach; ?></dl></div><?php endforeach; ?>
<div class="card"><h3><?=h(t('med_hist'))?></h3><?php foreach(explode('|',$p['antecedents']?:'No Background') as $a) echo '<span class="tag">'.h(td($a)).'</span>'; ?></div></div>
<div class="card"><h3><?=h(t('rep_fx'))?></h3>
<?php if($p['side_effects']): foreach(explode('|',$p['side_effects']) as $e) echo '<span class="tag fx">'.h(td($e)).'</span>'; ?>
<dl class="dgrid" style="margin-top:14px"><div><dt><?=h(t('started'))?></dt><dd><?=h($p['date_apparition']?:'–')?></dd></div><div><dt><?=h(t('duration'))?></dt><dd><?=h($p['duree_manifestation']?$p['duree_manifestation'].' '.t('days'):'–')?></dd></div></dl>
<?php else: ?><p class="muted"><?=h(t('nothing'))?></p><a class="btn sm" href="patient_form.php?id=<?=urlencode($p['id_patient'])?>"><?=h(t('nav_report'))?></a><?php endif; ?></div></div>
<?php close_page('app'); ?>
