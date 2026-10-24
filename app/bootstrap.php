<?php
// Front controller, autoload e helpers compartilhados. Não contém regras de negócio.
define('ROOT',dirname(__DIR__));
spl_autoload_register(function(string $class): void { if (str_starts_with($class,'App\\')) { $p=ROOT.'/app/'.str_replace('\\','/',substr($class,4)).'.php';if (is_file($p)) require $p; } });
ini_set('display_errors','0');ini_set('log_errors','1');
if (session_status()!==PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');
    session_name('gamestock_final');session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax','path'=>'/']);session_start();
}
header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');header('Cache-Control: no-store');
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; form-action 'self'; base-uri 'self'; frame-ancestors 'none'");
function baseUrl(): string { $path=str_replace('\\','/',dirname($_SERVER['SCRIPT_NAME']??'/index.php'));return $path==='/' || $path==='.' ? '' : $path; }
function url(string $route='home',array $params=[]): string { return baseUrl().'/index.php?'.http_build_query(['r'=>$route]+$params); }
function asset(string $path): string { return baseUrl().'/public/assets/'.$path; }
function e($v): string { return htmlspecialchars(is_scalar($v)?(string)$v:'',ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function csrfToken(): string { return $_SESSION['csrf']??=bin2hex(random_bytes(32)); }
function csrfField(): string { return '<input type="hidden" name="csrf" value="'.e(csrfToken()).'">'; }
function flash(string $msg): void { $_SESSION['flash']=$msg; }
function redirect(string $r,array $params=[]): never { header('Location: '.url($r,$params),true,303);exit; }
function abort(int $code,string $msg): never { http_response_code($code);App\Core\View::render('error',['title'=>'Não foi possível continuar','message'=>$msg]);exit; }
function requestMethod(): string { return $_SERVER['REQUEST_METHOD']??'GET'; }
function input(array $d,string $k,string $default=''): string { if (!isset($d[$k])) return $default; if (!is_string($d[$k])) throw new InvalidArgumentException('Formato inválido de parâmetro.'); return $d[$k]; }
function money($v): string { return 'R$ '.number_format((float)$v,2,',','.'); }
function db(): PDO { return App\Core\Database::connection(); }
function sql(string $q,array $p=[]): PDOStatement { $s=db()->prepare($q);$s->execute($p);return $s; }
function old(string $k,$default='') { return $_SESSION['old'][$k]??$default; }
function remember(array $d): void { unset($d['senha'],$d['confirmacao'],$d['csrf']);$_SESSION['old']=$d; }

// Arte horizontal para o cartão; capas cadastradas permanecem nas telas de detalhes.
function cardImage(string $filename): string {
    $filename=basename($filename);$landscape='cards/'.pathinfo($filename,PATHINFO_FILENAME).'.jpg';
    return is_file(ROOT.'/public/assets/images/'.$landscape)?$landscape:$filename;
}

// Preferir uma arte local de maior definição quando ela já existe no pacote.
// A moldura acompanha a proporção do arquivo escolhido, sem recortar a imagem.
function detailArtwork(string $filename): ?array {
    $filename=basename($filename);$candidates=[$filename,'cards/'.pathinfo($filename,PATHINFO_FILENAME).'.jpg'];$best=null;
    foreach ($candidates as $path) {
        $file=ROOT.'/public/assets/images/'.$path;if (!is_file($file)) continue;
        $size=@getimagesize($file);if (!$size || !$size[0] || !$size[1]) continue;
        $area=$size[0]*$size[1];
        if (!$best || ($area>$best['width']*$best['height']*1.5 && $size[0]>=$best['width'])) $best=['path'=>$path,'width'=>$size[0],'height'=>$size[1]];
    }
    return $best;
}
