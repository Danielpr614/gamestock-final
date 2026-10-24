<?php
namespace App\Controllers;
use App\Core\{View,Auth,Validation as V};
use App\Services\OrderService;
use App\Models\{Pedido,Cliente,Game};
final class PedidoController {
    public function index(): void { Auth::requireAdmin();$rows=(new Pedido())->listarDetalhes();View::render('admin/orders',['title'=>'Pedidos','rows'=>$rows]); }
    public function form(): void {
        Auth::requireAdmin();$id=isset($_GET['id'])?V::integer($_GET['id']):0;$data=$id?(new Pedido())->buscarPorId($id):[];
        if ($id && $data['status']!=='rascunho') throw new \DomainException('Somente rascunhos podem ser editados.');
        $items=$id?(new Pedido())->itens($id):[];$quantities=[];foreach ($items as $i) $quantities[$i['game_id']]=$i['quantidade'];
        $old=$_SESSION['old']??[];unset($_SESSION['old']);$data=array_replace($data,$old);if (isset($old['itens']) && is_array($old['itens'])) $quantities=$old['itens'];
        $_SESSION['admin_order_key']??=bin2hex(random_bytes(32));
        View::render('admin/order_form',['title'=>$id?'Editar pedido':'Criar pedido','id'=>$id,'data'=>$data,'quantities'=>$quantities,'clients'=>(new Cliente())->ativos(),'games'=>(new Game())->ativos()]);
    }
    public function save(): void {
        Auth::requireAdmin();Auth::csrf();$id=input($_POST,'id')===''?0:V::integer($_POST['id']);$key=input($_POST,'chave');
        if (!hash_equals($_SESSION['admin_order_key']??'',$key)) throw new \DomainException('Formulário expirado. Abra um novo pedido.');
        if (!is_array($_POST['itens']??null)) throw new \DomainException('Selecione os itens do pedido.');
        $id=(new OrderService())->save(V::integer($_POST['cliente_id']??''),$_POST['itens'],$key,$id);
        unset($_SESSION['old'],$_SESSION['admin_order_key']);flash('Rascunho salvo. Confirme o pedido para baixar o estoque.');redirect('admin/pedidos');
    }
    public function status(): void { Auth::requireAdmin();Auth::csrf();(new OrderService())->transition(V::integer($_POST['id']??''),input($_POST,'status'));flash('Situação atualizada.');redirect('admin/pedidos'); }
    public function delete(): void { Auth::requireAdmin();Auth::csrf();(new OrderService())->delete(V::integer($_POST['id']??''));flash('Pedido excluído.');redirect('admin/pedidos'); }
}
