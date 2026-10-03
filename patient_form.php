<?php require __DIR__.'/includes/bootstrap.php'; require __DIR__.'/configuration/db_connection.php';
$mode=empty($_SESSION['username'])?'public':'app'; $ok=msg(flash('success_message')); $err=msg(flash('error_message'));
$fx=$conn->query("SELECT description FROM effet_secondaire ORDER BY description");
open_page(t('rep_h'),$mode,'report'); echo $mode==='public'?'<div class="wrap" style="padding-top:48px"><h1 style="font-size:2.4rem">'.h(t('rep_h')).'</h1>':'<div>'; ?>
<div class="card panel"><?php if($ok): ?><div class="alert ok" role="status"><?=h($ok)?></div><?php endif; ?><?php if($err): ?><div class="alert" role="alert"><?=h($err)?></div><?php endif; ?>
<form action="patient_form_action.php" method="post"><?=csrf_field()?>
<div class="field"><label for="idnum"><?=h(t('id_nat'))?></label><input id="idnum" name="idnum" inputmode="numeric" maxlength="20" pattern="[0-9]+" value="<?=h($_GET['id']??'')?>" required></div>
<div class="field"><span class="lbl"><?=h(t('fx_label'))?></span><div class="chips"><?php while($r=$fx->fetch_assoc()): ?><label><input type="checkbox" name="side_effects[]" value="<?=h($r['description'])?>"><span><?=h(td($r['description']))?></span></label><?php endwhile; ?></div></div>
<div class="field"><label for="other"><?=h(t('fx_other'))?></label><?=multi_input('side_effects[]',t('fx_other_ph'),'other')?></div>
<div class="two"><div class="field"><label for="dateappar"><?=h(t('d_start'))?></label><input class="ui" id="dateappar" name="dateappar" type="date" max="<?=date('Y-m-d')?>" required></div>
<div class="field"><label for="dureemanif"><?=h(t('d_dur'))?></label><input id="dureemanif" name="dureemanif" type="number" min="1" required></div></div>
<button class="btn" name="btn_submit"><?=h(t('save_report'))?></button></form></div></div>
<?php close_page($mode); ?>
