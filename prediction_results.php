<?php require __DIR__.'/includes/bootstrap.php'; require_login();
$err=flash('error'); $pred=$_SESSION['predicted_side_effects']??[]; unset($_SESSION['predicted_side_effects']); $p=$_SESSION['patient_info']??null;
$norm=[];$avg=[]; // each model is normalised to % of its own total, then averaged
foreach($pred as $a=>$fx){$sum=array_sum($fx)?:1;foreach($fx as $e=>$v)$norm[$e][$a]=round($v/$sum*100);}
foreach($norm as $e=>$v)$avg[$e]=round(array_sum($v)/count($pred)); arsort($avg); $avg=array_filter($avg,fn($v)=>$v>=1);
open_page(t('res_title'),'app','add'); ?>
<?php if($err): ?><div class="alert" role="alert"><?=h($err)?></div><?php endif; ?>
<?php if($avg): $first=array_key_first($avg); ?>
<div class="card top1"><b><?=$avg[$first]?>%</b><div><p><?=h($p?t('most_likely_for',['name'=>$p['name']]):t('most_likely'))?></p><h2 style="margin:0;color:#fff"><?=h(td($first))?></h2></div></div>
<div class="card"><ul class="rank"><?php foreach($avg as $e=>$v): $c=$v>=25?'hi':($v>=10?'mid':'lo'); ?>
<li class="<?=$c?>"><div><strong><?=h(td($e))?></strong><div class="small"><?php foreach($norm[$e] as $a=>$x) echo '<span class="tag">'.h($a).' '.$x.'%</span>'; ?></div></div>
<div class="meter" role="img" aria-label="<?=$v?>%"><i style="width:<?=min(100,$v*2)?>%"></i></div><div class="pct"><?=$v?>%</div></li><?php endforeach; ?></ul>
<p class="muted small" style="margin:14px 0 0"><?=h(t('res_note',['n'=>count($pred)]))?></p></div>
<?php else: ?><div class="card empty"><?=h(t('no_pred'))?></div><?php endif; ?>
<div class="actions" style="justify-content:flex-start;flex-wrap:wrap"><a class="btn" href="add_patient.php"><?=icon('plus')?><?=h(t('add_another'))?></a><a class="btn ghost" href="patients_list.php"><?=h(t('back_patients'))?></a></div>
<?php close_page('app'); ?>
