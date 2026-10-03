<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/lang.php';

// Language switch: ?lang=en|fr|ar is remembered, then removed from the URL.
if (isset($_GET['lang']) && isset($LANGS[$_GET['lang']])) {
    $_SESSION['lang'] = $_GET['lang'];
    setcookie('lang', $_GET['lang'], time() + 31536000, '/');
    $q = $_GET; unset($q['lang']);
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . ($q ? '?' . http_build_query($q) : '')); exit;
}
$LANG = $_SESSION['lang'] ?? ($_COOKIE['lang'] ?? 'en');
if (!isset($LANGS[$LANG])) $LANG = 'en';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function t($k, $r = []){ global $S, $LI, $LANG; $s = isset($S[$k]) ? $S[$k][$LI[$LANG]] : $k; foreach ($r as $a => $b) $s = str_replace(':' . $a, $b, $s); return $s; }
function norm($s){ return strtr(mb_strtolower(trim((string)$s), 'UTF-8'), ['é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','à'=>'a','â'=>'a','ô'=>'o','î'=>'i','ï'=>'i','ç'=>'c','û'=>'u','ù'=>'u']); }
// Translate a stored value (side effect, condition, vaccine, gender...) for display only.
function td($s){ global $D, $LANG; $e = $D[norm($s)] ?? null; if (!$e) return (string)$s; if ($LANG === 'en') return $e[0]; if ($LANG === 'ar') return $e[2]; return $e[1] ?? (string)$s; }
function flash($k){ $v = $_SESSION[$k] ?? null; unset($_SESSION[$k]); return $v; }
function msg($m){ global $S; return $m === null ? null : (isset($S[$m]) ? t($m) : $m); } // session messages hold a translation key (or raw text)
function require_login(){ if (empty($_SESSION['username'])) { header('Location: doctors_login.php'); exit; } }
function csrf_field(){ if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return '<input type="hidden" name="_csrf" value="' . h($_SESSION['csrf']) . '">'; }
function csrf_check(){ if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['_csrf'] ?? '')) { http_response_code(400); exit('Invalid or expired form. Go back, reload the page and try again.'); } }
function multi_input($name, $placeholder = '', $id = ''){ // repeatable text input: JS (ui.js) adds/removes rows
  return '<div class="multi" data-rm="' . h(t('rm_row')) . '"><div class="mrow"><input' . ($id ? ' id="' . h($id) . '"' : '') . ' name="' . h($name) . '" placeholder="' . h($placeholder) . '" autocomplete="off"><button type="button" class="mpl" aria-label="' . h(t('add_row')) . '" title="' . h(t('add_row')) . '">+</button></div></div>'; }
function icon($n){
  $p = ['users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>','plus'=>'<path d="M12 5v14M5 12h14"/>',
  'clip'=>'<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M9 12h6M9 16h4"/>','down'=>'<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
  'arrow'=>'<path d="M5 12h14M13 6l6 6-6 6"/>'];
  return '<svg class="i' . ($n === 'arrow' ? ' flip' : '') . '" viewBox="0 0 24 24" aria-hidden="true">' . ($p[$n] ?? '') . '</svg>';
}
function logo(){ return '<a class="logo" href="index.php"><svg width="28" height="28" viewBox="0 0 28 28" aria-hidden="true"><circle cx="9" cy="10" r="5" fill="#5b8cff"/><circle cx="19" cy="9" r="4" fill="#ffb020"/><circle cx="15" cy="20" r="5" fill="#2dd4a3"/></svg>VaxCluster</a>'; }
function lang_switch(){ global $LANGS, $LANG; $o = '<div class="lang" role="group" aria-label="' . h(t('language')) . '">';
  foreach ($LANGS as $k => $l) $o .= '<a href="?' . h(http_build_query(array_merge($_GET, ['lang' => $k]))) . '" lang="' . $k . '"' . ($k === $LANG ? ' aria-current="true"' : '') . '>' . $l . '</a>'; return $o . '</div>'; }
function open_page($title, $mode = 'public', $active = '', $heading = '', $actions = ''){ global $LANG, $LOCALE, $WEEKSTART;
  $js = ['locale'=>$LOCALE[$LANG],'weekStart'=>$WEEKSTART[$LANG]]; foreach (['today','clear','now','done','hour','minute','pick_date','pick_time'] as $k) $js[$k] = t($k); ?>
<!DOCTYPE html><html lang="<?= $LANG ?>" dir="<?= $LANG === 'ar' ? 'rtl' : 'ltr' ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= h($title) ?> · VaxCluster</title><link rel="icon" href="favicon.png" type="image/png"><meta name="color-scheme" content="light dark">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/app.css"><script>window.I18N=<?= json_encode($js, JSON_UNESCAPED_UNICODE) ?>;</script></head><body>
<?php if ($mode === 'app'): $nav = ['list'=>['patients_list.php','users','m_patients'],'add'=>['add_patient.php','plus','m_new'],'report'=>['patient_form.php','clip','nav_report'],'csv'=>['export_csv.php','down','m_export']]; ?>
<div class="shell"><aside class="side"><?= logo() ?>
<?php foreach ($nav as $k => $n): ?><a class="n <?= $active === $k ? 'on' : '' ?>" href="<?= $n[0] ?>"><?= icon($n[1]) ?><?= h(t($n[2])) ?></a><?php endforeach; ?>
<div class="who"><div><span class="small"><?= h(t('signed_in')) ?></span><b><?= h($_SESSION['username'] ?? '') ?></b></div><?= lang_switch() ?><a class="small" href="logout.php"><?= h(t('logout')) ?></a></div></aside>
<div class="main"><header><h1><?= h($heading ?: $title) ?></h1><div><?= $actions ?></div></header>
<?php else: ?>
<nav class="top" aria-label="Main"><div><?= logo() ?>
<a class="l" href="index.php#coronavirus"><?= h(t('nav_what')) ?></a><a class="l" href="index.php#prevention"><?= h(t('nav_prev')) ?></a><a class="l" href="index.php#symptoms"><?= h(t('nav_sym')) ?></a><a class="l" href="patient_form.php"><?= h(t('nav_report')) ?></a><?= lang_switch() ?>
<?php if (!empty($_SESSION['username'])): ?><a class="btn sm" href="patients_list.php"><?= h(t('nav_dash')) ?></a><?php else: ?><a class="btn sm" href="doctors_login.php"><?= h(t('nav_login')) ?></a><?php endif; ?></div></nav>
<?php endif; }
function close_page($mode = 'public'){
  if ($mode === 'app') echo '</div></div>';
  else { ?><footer class="foot"><div class="wrap"><img src="images/Badji_Mokhtar_-_Annaba_University_Logo.png" alt="Badji Mokhtar Annaba University"><img src="images/Logo_LRI.png" alt="LRI laboratory">
<p><?= h(t('foot')) ?></p><a href="https://www.univ-annaba.dz/" target="_blank" rel="noopener"><?= h(t('foot_site')) ?></a></div></footer><?php }
  echo '<script src="assets/js/ui.js"></script></body></html>'; }
