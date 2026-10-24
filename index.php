<?php
require __DIR__.'/app/bootstrap.php';
use App\Controllers\{AuthController,StoreController,UsuarioController,ClienteController,GameController,CategoriaController,PedidoController};
$route='home';
try {
    $route=input($_GET,'r','home');$method=requestMethod();
    $routes=[
        'home'=>[StoreController::class,'home',['GET']], 'game'=>[StoreController::class,'details',['GET']],
        'login'=>[AuthController::class,'login',['GET','POST']], 'register'=>[AuthController::class,'register',['GET','POST']], 'logout'=>[AuthController::class,'logout',['POST']],
        'cart'=>[StoreController::class,'cart',['GET']], 'cart/add'=>[StoreController::class,'add',['POST']], 'cart/update'=>[StoreController::class,'updateCart',['POST']], 'checkout'=>[StoreController::class,'checkout',['POST']],
        'orders'=>[StoreController::class,'orders',['GET']], 'order'=>[StoreController::class,'order',['GET']], 'order/cancel'=>[StoreController::class,'cancel',['POST']],
        'admin'=>[StoreController::class,'dashboard',['GET']]
    ];
    foreach (['usuarios'=>UsuarioController::class,'clientes'=>ClienteController::class,'games'=>GameController::class,'categorias'=>CategoriaController::class,'pedidos'=>PedidoController::class] as $name=>$class) {
        foreach ([''=>'index','/form'=>'form','/save'=>'save','/delete'=>'delete'] as $suffix=>$action) $routes['admin/'.$name.$suffix]=[$class,$action,[in_array($action,['save','delete'])?'POST':'GET']];
    }
    $routes['admin/pedidos/status']=[PedidoController::class,'status',['POST']];
    if (!isset($routes[$route])) abort(404,'Página não encontrada.');
    [$class,$action,$methods]=$routes[$route];if (!in_array($method,$methods,true)) { header('Allow: '.implode(', ',$methods));abort(405,'Método de requisição não permitido.'); }
    (new $class())->$action();
} catch (InvalidArgumentException|DomainException $e) {
    if (requestMethod()==='POST') {
        remember($_POST);flash($e->getMessage());
        if (preg_match('#^admin/(usuarios|clientes|games|categorias|pedidos)/save$#',$route,$m)) { $id=is_string($_POST['id']??null)&&ctype_digit($_POST['id'])?(int)$_POST['id']:0;redirect('admin/'.$m[1].'/form',$id?['id'=>$id]:[]); }
        if (str_starts_with($route,'admin/')) { $parts=explode('/',$route);redirect('admin/'.$parts[1]); }
        redirect(in_array($route,['login','register'],true)?$route:'cart');
    }
    http_response_code(400);App\Core\View::render('error',['title'=>'Confira os dados','message'=>$e->getMessage()]);
} catch (PDOException $e) {
    error_log($e->getMessage());$constraint=$e->getCode()==='23000';
    $message=$constraint ? (($e->errorInfo[1]??0)===1062 ? 'Já existe um registro com esse e-mail, categoria ou combinação de título e plataforma.' : 'Este registro possui vínculos. Remova os vínculos permitidos ou mantenha o registro desativado.') : 'Não foi possível acessar o banco. Confira o XAMPP, database/instalar.sql e config/local.php.';
    if (requestMethod()==='POST' && $constraint) {
        remember($_POST);flash($message);
        if (preg_match('#^admin/(usuarios|clientes|games|categorias)/save$#',$route,$m)) { $id=is_string($_POST['id']??null)&&ctype_digit($_POST['id'])?(int)$_POST['id']:0;redirect('admin/'.$m[1].'/form',$id?['id'=>$id]:[]); }
        if (str_starts_with($route,'admin/')) { $parts=explode('/',$route);redirect('admin/'.$parts[1]); }
        redirect($route==='register'?'register':'home');
    }
    http_response_code(503);echo '<!doctype html><html lang="pt-BR"><meta charset="UTF-8"><title>Conexão</title><h1>Não foi possível continuar</h1><p>'.e($message).'</p></html>';
} catch (Throwable $e) { error_log($e->getMessage());http_response_code(500);echo '<!doctype html><html lang="pt-BR"><meta charset="UTF-8"><h1>Não foi possível concluir</h1><p>Atualize a página e tente novamente.</p></html>'; }
