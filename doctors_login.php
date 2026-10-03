<?php require __DIR__.'/includes/bootstrap.php'; if(!empty($_SESSION['username'])){header('Location: patients_list.php');exit;}
$err=msg(flash('error_message')); open_page(t('login_h')); ?>
<div class="wrap" style="max-width:460px;padding-top:80px"><div class="card"><h1 style="font-size:2rem"><?=h(t('login_h'))?></h1><p class="muted"><?=h(t('login_p'))?></p>
<?php if($err): ?><div class="alert" role="alert"><?=h($err)?></div><?php endif; ?>
<form action="login_action.php" method="post"><?=csrf_field()?>
<div class="field"><label for="username"><?=h(t('username'))?></label><input id="username" name="username" autocomplete="username" required autofocus></div>
<div class="field"><label for="password"><?=h(t('password'))?></label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
<button class="btn block" name="btn_login"><?=h(t('login_btn'))?></button></form></div></div>
<?php close_page(); ?>
