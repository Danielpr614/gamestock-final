<?php
namespace App\Core;
final class Auth {
    public static function user(): ?array {
        if (empty($_SESSION['uid'])) return null;
        $s=Database::connection()->prepare('SELECT id,nome,email,perfil,ativo FROM usuarios WHERE id=?');$s->execute([$_SESSION['uid']]);$u=$s->fetch();
        if (!$u || !$u['ativo'] || time()-($_SESSION['last_active']??0)>1800) { unset($_SESSION['uid']);return null; }
        $_SESSION['last_active']=time();return $u;
    }
    public static function requireLogin(): array { $u=self::user();if (!$u) { flash('Entre na sua conta para continuar.'); redirect('login'); } return $u; }
    public static function requireAdmin(): array { $u=self::requireLogin();if ($u['perfil']!=='admin') abort(403,'Acesso permitido somente ao administrador.');return $u; }
    public static function requireCustomer(): array { $u=self::requireLogin();if ($u['perfil']!=='cliente') abort(403,'Use uma conta de cliente para comprar.');return $u; }
    public static function csrf(): void { if (requestMethod()!=='POST' || !isset($_POST['csrf']) || !is_string($_POST['csrf']) || !hash_equals(csrfToken(),$_POST['csrf'])) abort(403,'Requisição inválida. Atualize a página e tente novamente.'); }
}
