<?php require __DIR__.'/includes/bootstrap.php'; require_login(); require __DIR__.'/configuration/db_connection.php';
$err=msg(flash('error_message')); $hist=$conn->query("SELECT nom FROM Maladie_Chronique ORDER BY nom"); open_page(t('m_new'),'app','add'); ?>
<div class="card panel"><?php if($err): ?><div class="alert" role="alert"><?=h($err)?></div><?php endif; ?>
<ol class="stepper"><li class="on"><span><?=h(t('step_patient'))?></span></li><li><span><?=h(t('step_vacc'))?></span></li><li><span><?=h(t('step_hist'))?></span></li></ol>
<form id="f" action="add_patient_action.php" method="post" novalidate><?=csrf_field()?>
<div class="step on"><div class="field"><label for="idnum"><?=h(t('id_nat'))?></label><input id="idnum" name="idnum" inputmode="numeric" maxlength="20" pattern="[0-9]+" required></div>
<div class="field"><label for="name"><?=h(t('f_name'))?></label><input id="name" name="name" required></div>
<div class="two"><div class="field"><label for="gender"><?=h(t('f_gender'))?></label><select class="ui" id="gender" name="gender" required><option value=""><?=h(t('choose'))?></option><option value="Male"><?=h(t('male'))?></option><option value="Female"><?=h(t('female'))?></option></select></div>
<div class="field"><label for="age"><?=h(t('f_age'))?></label><input id="age" name="age" type="number" min="0" max="120" required></div></div>
<div class="field"><label for="adresse"><?=h(t('f_addr'))?></label><input id="adresse" name="adresse" required></div>
<div class="field"><label for="tel"><?=h(t('f_phone'))?></label><input id="tel" name="tel" type="tel" required></div></div>
<div class="step"><div class="two"><div class="field"><label for="vaccin"><?=h(t('f_vaccine'))?></label><select class="ui" id="vaccin" name="vaccin" required><option value=""><?=h(t('choose'))?></option><?php foreach(['Astra Zeneca','Sinopharm','Sputnik-v-','Sinovac'] as $v) echo '<option value="'.h($v).'">'.h(td($v)).'</option>'; ?></select></div>
<div class="field"><label for="dose"><?=h(t('f_dose'))?></label><select class="ui" id="dose" name="dose" required><option value=""><?=h(t('choose'))?></option><option value="1st"><?=h(t('dose1'))?></option><option value="2nd"><?=h(t('dose2'))?></option></select></div></div>
<div class="two"><div class="field"><label for="numlot"><?=h(t('f_batch'))?></label><input id="numlot" name="numlot" required></div>
<div class="field"><label for="dateperemp"><?=h(t('f_expiry'))?></label><input class="ui" id="dateperemp" name="dateperemp" type="date" required></div></div>
<div class="two"><div class="field"><label for="datevac"><?=h(t('f_vacdate'))?></label><input class="ui" id="datevac" name="datevac" type="date" value="<?=date('Y-m-d')?>" required></div>
<div class="field"><label for="timevac"><?=h(t('f_time'))?></label><input class="ui" id="timevac" name="timevac" type="time" required></div></div>
<div class="two"><div class="field"><label for="lieuvac"><?=h(t('f_loc'))?></label><input id="lieuvac" name="lieuvac" required></div>
<div class="field"><label for="Site"><?=h(t('f_site'))?></label><select class="ui" id="Site" name="Site" required><option value=""><?=h(t('choose'))?></option><option value="right"><?=h(t('right_arm'))?></option><option value="left"><?=h(t('left_arm'))?></option></select></div></div></div>
<div class="step"><p class="muted small"><?=h(t('hist_help'))?></p><div class="chips" id="hist"><?php while($r=$hist->fetch_assoc()): $n=$r['nom']; ?><label><input type="checkbox" name="antecedents[]" value="<?=h($n)?>" <?=$n==='No Background'?'checked':''?>><span><?=h(td($n))?></span></label><?php endwhile; ?></div>
<div class="field" style="margin-top:22px"><label for="na"><?=h(t('hist_other'))?></label><?=multi_input('new_antecedents[]',t('hist_other_ph'),'na')?></div></div>
<div class="actions"><button type="button" class="btn ghost" id="back"><?=h(t('back'))?></button><button type="button" class="btn" id="next"><?=h(t('cont'))?></button></div></form></div>
<script>
const S=[...document.querySelectorAll('.step')],P=[...document.querySelectorAll('.stepper li')],f=document.getElementById('f'),B=document.getElementById('back'),N=document.getElementById('next'),T={c:<?=json_encode(t('cont'),JSON_UNESCAPED_UNICODE)?>,s:<?=json_encode(t('save_cont'),JSON_UNESCAPED_UNICODE)?>};let i=0;
function show(){S.forEach((s,k)=>s.classList.toggle('on',k===i));P.forEach((p,k)=>{p.classList.toggle('on',k===i);p.classList.toggle('done',k<i)});B.style.visibility=i?'visible':'hidden';N.textContent=i===S.length-1?T.s:T.c}
N.onclick=()=>{const bad=[...S[i].querySelectorAll('[required]')].find(e=>!e.checkValidity());if(bad){bad.reportValidity();return}i<S.length-1?(i++,show()):f.submit()};B.onclick=()=>{i--;show()};
const bx=[...document.querySelectorAll('#hist input')],no=bx.find(b=>b.value==='No Background'),mu=document.querySelector('.multi');
const customs=()=>[...mu.querySelectorAll('input')].some(x=>x.value.trim());
function sync(src){if(src===no&&no.checked){bx.forEach(b=>{if(b!==no)b.checked=false});mu.querySelectorAll('.mrow.x').forEach(r=>r.remove());mu.querySelector('input').value='';return}
 no.checked=!(customs()||bx.some(x=>x!==no&&x.checked))}
bx.forEach(b=>b.addEventListener('change',()=>sync(b)));mu.addEventListener('input',()=>sync(mu));
f.addEventListener('keydown',e=>{if(e.key!=='Enter'||e.target.tagName!=='INPUT')return;e.preventDefault();const r=e.target.closest('.mrow');r?r.querySelector('.mpl').click():N.click()});
show();
</script>
<?php close_page('app'); ?>
