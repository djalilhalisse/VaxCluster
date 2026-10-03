<?php require __DIR__.'/includes/bootstrap.php'; require_login(); require __DIR__.'/configuration/db_connection.php';
$rows=$conn->query("SELECT p.id_patient,p.nom,p.sexe,p.age,p.createdate,v.type_vaccin,v.dose,
 (SELECT GROUP_CONCAT(es.description SEPARATOR '|') FROM Vaccination_Effet_Secondaire ves JOIN Effet_Secondaire es ON es.id_effet=ves.id_effet WHERE ves.id_vaccination=v.id_vaccination) AS effects
 FROM Patient p LEFT JOIN Patient_Vaccination pv ON pv.id_patient=p.id_patient LEFT JOIN Vaccination v ON v.id_vaccination=pv.id_vaccination ORDER BY p.createdate DESC")->fetch_all(MYSQLI_ASSOC);
$top=$conn->query("SELECT es.description d,COUNT(*) c FROM Vaccination_Effet_Secondaire ves JOIN Effet_Secondaire es ON es.id_effet=ves.id_effet GROUP BY es.description ORDER BY c DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$vac=[];$rep=0;foreach($rows as $r){$k=$r['type_vaccin']?:'';$vac[$k]=($vac[$k]??0)+1;if($r['effects'])$rep++;}
$total=count($rows);$max=max(array_column($top,'c')?:[1]);$best=$vac?array_search(max($vac),$vac):'';
open_page(t('pat_title'),'app','list',t('pat_title'),'<a class="btn sm ghost" href="export_csv.php">'.icon('down').h(t('m_export')).'</a><a class="btn sm" href="add_patient.php">'.icon('plus').h(t('m_new')).'</a>'); ?>
<div class="kpis"><div class="card kpi"><b><?=$total?></b><span><?=h(t('kpi_total'))?></span></div><div class="card kpi"><b><?=$rep?></b><span><?=h(t('kpi_reported'))?></span></div>
<div class="card kpi"><b><?=$total?round($rep/$total*100):0?>%</b><span><?=h(t('kpi_follow'))?></span></div><div class="card kpi"><b><?=h($best===''?'–':td($best))?></b><span><?=h(t('kpi_vaccine'))?></span></div></div>
<div class="cols"><div class="card"><div class="tools"><input id="q" type="search" placeholder="<?=h(t('search_ph'))?>" aria-label="<?=h(t('search_ph'))?>">
<select class="ui" id="vf" aria-label="<?=h(t('all_vaccines'))?>"><option value=""><?=h(t('all_vaccines'))?></option><?php foreach(array_keys($vac) as $v) if($v!=='') echo '<option value="'.h($v).'">'.h(td($v)).'</option>'; ?></select></div>
<div class="tw"><table id="pt"><thead><tr><th><?=h(t('th_patient'))?></th><th><?=h(t('th_age'))?></th><th><?=h(t('th_vaccine'))?></th><th><?=h(t('th_fx'))?></th><th><?=h(t('th_added'))?></th></tr></thead><tbody>
<?php foreach($rows as $r): $fx=$r['effects']?explode('|',$r['effects']):[]; $vn=$r['type_vaccin']?:''; ?>
<tr data-v="<?=h($vn)?>" data-s="<?=h(mb_strtolower($r['id_patient'].' '.$r['nom'].' '.implode(' ',$fx).' '.implode(' ',array_map('td',$fx))))?>">
<td><a class="id" href="patient_details.php?id=<?=urlencode($r['id_patient'])?>"><?=h($r['nom'])?></a><div class="muted small"><?=h($r['id_patient'])?> · <?=h(td($r['sexe']))?></div></td><td><?=h($r['age'])?></td>
<td><?=h($vn===''?t('unknown'):td($vn))?><div class="muted small"><?=h(td($r['dose']))?></div></td>
<td><?php if($fx) foreach($fx as $f) echo '<span class="tag fx">'.h(td($f)).'</span>'; else echo '<span class="muted small">'.h(t('not_reported')).'</span>'; ?></td><td class="muted small" dir="ltr" style="white-space:nowrap"><?=h(substr($r['createdate'],0,10))?></td></tr>
<?php endforeach; ?></tbody></table></div>
<div class="empty" id="none" <?=$total?'hidden':''?>><?=h(t('no_patients'))?> <a href="add_patient.php"><?=h(t('add_first'))?></a></div>
<p class="muted small" style="margin:12px 0 0"><span id="count"><?=$total?></span> <?=h(t('shown'))?></p></div>
<div class="card"><h3><?=h(t('top_fx'))?></h3><?php if($top): ?><ul class="bars"><?php foreach($top as $x): ?><li><span><?=h(td($x['d']))?></span><span class="t"><i style="width:<?=round($x['c']/$max*100)?>%"></i></span><b><?=$x['c']?></b></li><?php endforeach; ?></ul>
<?php else: ?><p class="muted small"><?=h(t('chart_empty'))?></p><?php endif; ?></div></div>
<script src="assets/js/app.js"></script>
<?php close_page('app'); ?>
