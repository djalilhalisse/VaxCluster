<?php require __DIR__.'/includes/bootstrap.php'; open_page(t('home_title')); ?>
<div class="wrap"><section class="hero"><div>
<h1><?=h(t('hero_h1'))?></h1><p class="lead"><?=h(t('hero_lead'))?></p>
<div class="row"><a class="btn" href="doctors_login.php"><?=h(t('cta_login'))?> <?=icon('arrow')?></a><a class="btn ghost" href="patient_form.php"><?=h(t('cta_report'))?></a></div></div>
<div class="viz"><canvas id="cluster" role="img" aria-label="<?=h(t('viz_alt'))?>"></canvas>
<div class="cap"><span><i style="background:#5b8cff"></i><?=h(t('cl_a'))?></span><span><i style="background:#ffb020"></i><?=h(t('cl_b'))?></span><span><i style="background:#2dd4a3"></i><?=h(t('cl_c'))?></span><span><i style="background:#fff"></i><?=h(t('viz_new'))?></span></div></div></section></div>
<section class="sec"><div class="wrap"><h2><?=h(t('how_h2'))?></h2><div class="grid">
<?php foreach([1,2,3] as $n): ?><div class="card"><div class="step-n"><?=$n?></div><h3><?=h(t("s{$n}_t"))?></h3><p class="muted"><?=h(t("s{$n}_p"))?></p></div><?php endforeach; ?></div></div></section>
<section class="sec" id="coronavirus"><div class="wrap"><h2><?=h(t('covid_h2'))?></h2><p class="muted"><?=h(t('covid_p'))?></p></div></section>
<section class="sec" id="prevention"><div class="wrap"><h2><?=h(t('prot_h2'))?></h2><div class="split">
<div class="card"><h3><?=h(t('do_h'))?></h3><ul class="dl"><?php for($i=1;$i<=6;$i++) echo '<li>'.h(t("do$i")).'</li>'; ?></ul></div>
<div class="card"><h3><?=h(t('avoid_h'))?></h3><ul class="dl dont"><?php for($i=1;$i<=5;$i++) echo '<li>'.h(t("av$i")).'</li>'; ?></ul></div></div></div></section>
<section class="sec" id="symptoms"><div class="wrap"><h2><?=h(t('sym_h2'))?></h2><p class="muted"><?=h(t('sym_p'))?></p><div class="grid">
<?php foreach([1=>'high-fever',2=>'cough',3=>'sore-troath',4=>'headache'] as $n=>$img): ?><div class="card sym"><img src="images/symptom_<?=$img?>.png" alt=""><div><h3><?=h(t("sym{$n}_t"))?></h3><p class="muted small"><?=h(t("sym{$n}_p"))?></p></div></div><?php endforeach; ?></div></div></section>
<script src="assets/js/cluster.js"></script>
<?php close_page(); ?>
