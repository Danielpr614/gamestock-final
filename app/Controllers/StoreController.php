<?php
namespace App\Controllers;
use App\Core\{Auth,View,Validation as V};
use App\Services\OrderService;
use App\Models\{Categoria,Game,Cliente,Pedido};
final class StoreController {
    public function home(): void {
        $q=V::text(['busca'=>input($_GET,'q')],'busca',100,false);$p=input($_GET,'platform');if ($p!=='') V::choice($p,['PC','PlayStation','Xbox','Nintendo']);
        $category=input($_GET,'category');if ($category!=='') V::integer($category);
        $sort=V::choice(input($_GET,'sort','title'),['title','price_asc','price_desc']);
        $perPage=12;$model=new Game();$total=$model->totalCatalogo($q,$p,$category);$pages=max(1,(int)ceil($total/$perPage));
        $page=min(V::integer(input($_GET,'page','1'),1,1000000),$pages);$games=$model->catalogo($q,$p,$category,$sort,$page,$perPage);
        View::render('home',['title'=>'Seu próximo universo','games'=>$games,'q'=>$q,'platform'=>$p,'category'=>$category,'sort'=>$sort,'total'=>$total,'page'=>$page,'pages'=>$pages,'perPage'=>$perPage,'featured'=>$model->destaques(),'categories'=>(new Categoria())->listar()]);
    }
    public function details(): void {
        $id=V::integer($_GET['id']??'');$g=(new Game())->detalhes($id);
        if (!$g) abort(404,'Jogo não encontrado.');View::render('details',['title'=>$g['titulo'],'g'=>$g]);
    }
    private function cartData(): array {
        $rows=[];$total=0;
        foreach ($_SESSION['cart']??[] as $id=>$qty) {
            $g=(new Game())->disponivel((int)$id);
            if (!$g) { unset($_SESSION['cart'][$id]);continue; }
            $g['quantity']=$qty;$subtotal=V::money($g['preco'])*$qty;$total+=$subtotal;$g['subtotal']=V::decimal($subtotal);$rows[]=$g;
        }
        return [$rows,V::decimal($total)];
    }
    public function cart(): void { [$rows,$total]=$this->cartData();$_SESSION['checkout_key']??=bin2hex(random_bytes(32));View::render('cart',['title'=>'Seu carrinho','rows'=>$rows,'total'=>$total]); }
    public function add(): void {
        Auth::csrf();$id=V::integer($_POST['id']??'');$qty=V::integer($_POST['quantity']??'1',1,99);$g=(new Game())->disponivel((int)$id);
        if (!$g) throw new \DomainException('Jogo indisponível.');$new=($_SESSION['cart'][$id]??0)+$qty;
        if ($new>99 || $new>$g['estoque']) throw new \DomainException('Quantidade superior ao estoque disponível ou ao limite de 99 por jogo.');
        if (empty($_SESSION['cart'])) $_SESSION['checkout_key']=bin2hex(random_bytes(32));
        if (!isset($_SESSION['cart'][$id]) && count($_SESSION['cart']??[])>=25) throw new \DomainException('O carrinho aceita até 25 jogos diferentes.');
        $_SESSION['cart'][$id]=$new;flash('Jogo adicionado ao carrinho.');redirect('cart');
    }
    public function updateCart(): void {
        Auth::csrf();$items=$_POST['itens']??[];if (!is_array($items) || count($items)>25) throw new \DomainException('Carrinho inválido.');$new=[];
        foreach ($items as $id=>$qty) { $id=V::integer($id);$qty=V::integer($qty,0,99);if (!isset($_SESSION['cart'][$id])) throw new \DomainException('Item inválido.');if (!$qty) continue;
            $g=(new Game())->disponivel($id);if (!$g || $qty>$g['estoque']) throw new \DomainException('Quantidade superior ao estoque de um dos jogos.');$new[$id]=$qty;
        }
        $_SESSION['cart']=$new;flash('Carrinho atualizado.');redirect('cart');
    }
    public function checkout(): void {
        $u=Auth::requireCustomer();Auth::csrf();$key=input($_POST,'chave');if (!hash_equals($_SESSION['checkout_key']??'',$key)) throw new \DomainException('Carrinho expirado. Atualize a página.');
        $c=(new Cliente())->porUsuario((int)$u['id']);if (!$c) throw new \DomainException('Sua conta não possui cadastro de cliente.');
        $id=(new OrderService())->save((int)$c['id'],$_SESSION['cart']??[],$key,0,true);$_SESSION['cart']=[];flash('Pedido confirmado! Nenhum pagamento real foi realizado.');redirect('order',['id'=>$id]);
    }
    public function orders(): void { $u=Auth::requireCustomer();$rows=(new Pedido())->porUsuario((int)$u['id']);View::render('orders',['title'=>'Meus pedidos','rows'=>$rows]); }
    public function order(): void {
        $u=Auth::requireLogin();$id=V::integer($_GET['id']??'');$p=(new Pedido())->detalhes($id);
        if (!$p || ($u['perfil']!=='admin' && (int)$p['usuario_id']!==(int)$u['id'])) abort(404,'Pedido não encontrado.');
        View::render('order',['title'=>'Pedido #'.$id,'p'=>$p,'items'=>(new Pedido())->itens($id)]);
    }
    public function cancel(): void { $u=Auth::requireCustomer();Auth::csrf();$c=(new Cliente())->porUsuario((int)$u['id']);if (!$c) abort(403,'Cliente não encontrado.');$id=V::integer($_POST['id']??'');(new OrderService())->transition($id,'cancelado',(int)$c['id']);flash('Pedido cancelado.');redirect('order',['id'=>$id]); }
    public function dashboard(): void {
        Auth::requireAdmin();$stats=(new Pedido())->resumo();
        View::render('admin/dashboard',['title'=>'Visão geral','stats'=>$stats,'low'=>(new Game())->estoqueBaixo()]);
    }
}
