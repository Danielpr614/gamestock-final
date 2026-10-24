<?php
namespace App\Controllers;
use App\Core\{Auth,View,Validation as V};
use App\Models\{Usuario,Cliente,Game,Categoria};
use App\Services\AccountService;
use DomainException;
abstract class CrudController {
    protected string $entity;
    private function model() { return match($this->entity) { 'usuarios'=>new Usuario(),'clientes'=>new Cliente(),'games'=>new Game(),'categorias'=>new Categoria() }; }
    public function index(): void {
        Auth::requireAdmin();$q=trim(input($_GET,'q'));V::text(['busca'=>$q],'busca',100,false);
        $rows=match($this->entity) {
            'clientes'=>(new Cliente())->listarDetalhes(),
            'games'=>(new Game())->listarDetalhes(),
            default=>$this->model()->listar()
        };
        // A busca administrativa cobre as colunas visíveis, nunca senhas.
        $rows=array_filter($rows,fn($r)=>$q==='' || stripos(implode(' ',array_intersect_key($r,array_flip(['titulo','nome','email','plataforma','categoria','telefone']))),$q)!==false);
        View::render('admin/list',['title'=>ucfirst($this->entity),'entity'=>$this->entity,'rows'=>$rows,'q'=>$q]);
    }
    public function form(): void {
        Auth::requireAdmin();$id=isset($_GET['id'])?V::integer($_GET['id']):0;$data=$id?$this->model()->buscarPorId($id):[];
        if ($id && $this->entity==='clientes') $data=(new Cliente())->detalhes($id);
        if ($id && $this->entity==='usuarios') $data['telefone']=((new Cliente())->porUsuario($id)['telefone']??'');
        $data=array_replace($data,$_SESSION['old']??[]);unset($_SESSION['old']);
        View::render('admin/form',['title'=>($id?'Editar ':'Cadastrar ').$this->entity,'entity'=>$this->entity,'data'=>$data,'id'=>$id,'categories'=>(new Categoria())->listar()]);
    }
    public function save(): void {
        Auth::requireAdmin();Auth::csrf();$id=input($_POST,'id')===''?0:V::integer($_POST['id']);$d=$_POST;
        if (in_array($this->entity,['usuarios','clientes'],true)) (new AccountService())->save($d,$id,$this->entity==='clientes');
        elseif ($this->entity==='categorias') { $data=['nome'=>V::text($d,'nome',50),'descricao'=>V::text($d,'descricao',500,false)];if ($id) $this->model()->atualizar($id,$data);else $this->model()->cadastrar($data); }
        else {
            $data=['titulo'=>V::text($d,'titulo',100),'descricao'=>V::text($d,'descricao',5000),'preco'=>V::decimal(V::money(input($d,'preco'))),'estoque'=>V::integer($d['estoque']??'',0,1000000),'categoria_id'=>V::integer($d['categoria_id']??''),'plataforma'=>V::choice($d['plataforma']??'', ['PC','PlayStation','Xbox','Nintendo']),'sistemas'=>V::text($d,'sistemas',100),'loja'=>V::text($d,'loja',100,false),'link_loja'=>V::url($d),'ativo'=>V::integer($d['ativo']??'',0,1),'imagem'=>V::text($d,'imagem',255,false)];
            (new Categoria())->buscarPorId($data['categoria_id']);
            if ($data['imagem']!=='' && (!preg_match('/^[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/iD',$data['imagem']) || !is_file(ROOT.'/public/assets/images/'.$data['imagem']))) throw new DomainException('Selecione uma imagem existente na pasta public/assets/images.');
            if ($id) $this->model()->atualizar($id,$data);else $this->model()->cadastrar($data);
        }
        unset($_SESSION['old']);flash('Registro salvo com sucesso.');redirect('admin/'.$this->entity);
    }
    public function delete(): void {
        Auth::requireAdmin();Auth::csrf();$id=V::integer($_POST['id']??'');
        if (in_array($this->entity,['usuarios','clientes'],true)) (new AccountService())->delete($id,$this->entity==='clientes');
        else $this->model()->excluir($id);
        flash('Registro excluído.');redirect('admin/'.$this->entity);
    }
}
