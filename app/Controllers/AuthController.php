<?php
namespace App\Controllers;
use App\Core\{View,Auth,Validation as V};
use App\Services\AccountService;
use App\Models\Usuario;
final class AuthController {
    public function login(): void {
        if (requestMethod()==='POST') {
            Auth::csrf();$email=V::email($_POST);$pass=input($_POST,'senha');
            if (strlen($pass)>72) throw new \DomainException('E-mail ou senha inválidos.');
            if (($_SESSION['login_attempts']??0)>=8 && time()-($_SESSION['login_attempt_time']??0)<60) throw new \DomainException('Muitas tentativas. Aguarde um minuto e tente novamente.');
            if (time()-($_SESSION['login_attempt_time']??0)>=60) $_SESSION['login_attempts']=0;
            $u=(new Usuario())->porEmail($email);
            if (!$u || !password_verify($pass,$u['senha'])) { $_SESSION['login_attempts']=($_SESSION['login_attempts']??0)+1;$_SESSION['login_attempt_time']=time();throw new \DomainException('E-mail ou senha inválidos.'); }
            if (password_needs_rehash($u['senha'],PASSWORD_DEFAULT)) (new Usuario())->atualizar((int)$u['id'],['senha'=>password_hash($pass,PASSWORD_DEFAULT)]);
            session_regenerate_id(true);$_SESSION['uid']=(int)$u['id'];$_SESSION['last_active']=time();$_SESSION['csrf']=bin2hex(random_bytes(32));unset($_SESSION['old'],$_SESSION['login_attempts']);
            redirect($u['perfil']==='admin'?'admin':'home');
        }
        View::render('auth',['title'=>'Entrar','register'=>false]);
    }
    public function register(): void {
        if (requestMethod()==='POST') { Auth::csrf();(new AccountService())->save($_POST,0,true,true);unset($_SESSION['old']);flash('Conta criada! Entre com seu e-mail e senha.');redirect('login'); }
        View::render('auth',['title'=>'Criar conta','register'=>true]);
    }
    public function logout(): void { Auth::csrf();$_SESSION=[];session_regenerate_id(true);flash('Você saiu da sua conta.');redirect('home'); }
}
