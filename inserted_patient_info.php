<?php require __DIR__.'/includes/bootstrap.php'; require_login();
$p=$_SESSION['patient_info']??null; if(!$p){header('Location: add_patient.php');exit;} $ok=msg(flash('success_message'));
$rows=['id_nat'=>['idnum',0],'f_gender'=>['gender',1],'f_age'=>['age',0],'f_addr'=>['adresse',0],'f_phone'=>['tel',0],'f_vaccine'=>['vaccin',1],'f_dose'=>['dose',1],'f_batch'=>['lot',0],'f_expiry'=>['peremption',0],'f_loc'=>['lieuvac',0],'f_vacdate'=>['datevac',0],'f_time'=>['heurevac',0],'f_site'=>['site',1],'season'=>['season',1]];
open_page(t('added_h',['name'=>$p['name']]),'app','add'); ?>
<?php if($ok): ?><div class="alert ok" role="status"><?=h($ok)?></div><?php endif; ?>
<div class="card panel"><dl class="dgrid"><?php foreach($rows as $l=>[$k,$tr]): ?><div><dt><?=h(t($l))?></dt><dd><?=h($tr?td($p[$k]):$p[$k])?></dd></div><?php endforeach; ?></dl>
<p class="lbl" style="margin-top:22px"><?=h(t('med_hist'))?></p><?php foreach($p['antecedents'] as $a) echo '<span class="tag">'.h(td($a)).'</span>'; ?>
<div class="actions" style="justify-content:flex-start;flex-wrap:wrap"><a class="btn" href="predicts.php"><?=h(t('predict_btn'))?> <?=icon('arrow')?></a><a class="btn ghost" href="add_patient.php"><?=h(t('add_another'))?></a></div></div>
<?php close_page('app'); ?>
