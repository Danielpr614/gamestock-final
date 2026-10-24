<?php
namespace App\Services;
use App\Core\Validation as V;
use App\Models\Usuario;
use App\Models\Cliente;
use DomainException;
final class AccountService {
    public function save(array $d,int $id=0,bool $customer=false,bool $public=false): int {
        $name=V::text($d,'nome',100);$email=V::email($d);$password=V::password($d,!$id);
        $perfil=$public || $customer ? 'cliente' : V::choice($d['perfil']??'', ['admin','cliente']);
        $ativo=$public ? 1 : V::integer($d['ativo']??'1',0,1);
        $phone=($customer || $public || $perfil==='cliente') ? V::phone($d) : null;
        if ($public && (!isset($d['confirmacao']) || !is_string($d['confirmacao']) || !hash_equals($password,$d['confirmacao']))) throw new DomainException('A confirmação da senha não corresponde.');
        db()->beginTransaction();
        try {
            // Serializar mudanças administrativas evita remover simultaneamente os últimos administradores.
            sql("SELECT id FROM usuarios WHERE perfil='admin' ORDER BY id FOR UPDATE")->fetchAll();
            if ($customer && $id) { $client=(new Cliente())->buscarPorId($id);$uid=(int)$client['usuario_id']; }
            else $uid=$id;
            if ($uid) {
                $old=(new Usuario())->buscarPorId($uid);
                if ($customer && $old['perfil']==='admin') throw new DomainException('Um administrador vinculado deve ser editado em Usuários.');
                if ($uid===(int)($_SESSION['uid']??0) && ($perfil!=='admin' || !$ativo)) throw new DomainException('Você não pode desativar sua conta nem remover sua própria permissão de administrador.');
                $this->ensureAdmin($old,$perfil,$ativo);
            }
            $data=['nome'=>$name,'email'=>$email,'perfil'=>$perfil,'ativo'=>$ativo];
            if ($password!=='') $data['senha']=password_hash($password,PASSWORD_DEFAULT);
            $m=new Usuario(); if ($uid) $m->atualizar($uid,$data); else $uid=$m->cadastrar($data);
            $client=sql('SELECT id FROM clientes WHERE usuario_id=?',[$uid])->fetch();
            if ($phone!==null) { if ($client) (new Cliente())->atualizar((int)$client['id'],['telefone'=>$phone]);else $client=['id'=>(new Cliente())->cadastrar(['usuario_id'=>$uid,'telefone'=>$phone])]; }
            db()->commit(); return $customer ? (int)$client['id'] : $uid;
        } catch (\Throwable $e) { if (db()->inTransaction()) db()->rollBack();throw $e; }
    }
    private function ensureAdmin(array $old,string $new,int $active): void {
        if ($old['perfil']==='admin' && $old['ativo'] && ($new!=='admin' || !$active) && (int)sql("SELECT COUNT(*) FROM usuarios WHERE perfil='admin' AND ativo=1")->fetchColumn()<=1) throw new DomainException('O sistema precisa manter pelo menos um administrador ativo.');
    }
    public function delete(int $id,bool $customer=false): void {
        db()->beginTransaction();try {
            sql("SELECT id FROM usuarios WHERE perfil='admin' ORDER BY id FOR UPDATE")->fetchAll();
            if ($customer) {
                $c=(new Cliente())->buscarPorId($id);$u=(new Usuario())->buscarPorId((int)$c['usuario_id']);
                if ($u['perfil']==='admin') throw new DomainException('Administrador vinculado: gerencie esta conta em Usuários.');
                if (sql('SELECT id FROM pedidos WHERE cliente_id=? LIMIT 1',[$id])->fetch()) throw new DomainException('Cliente com pedidos não pode ser excluído. Desative a conta em Usuários.');
                (new Cliente())->excluir($id);(new Usuario())->excluir((int)$u['id']);
            } else {
                $u=(new Usuario())->buscarPorId($id);
                if ($id===(int)($_SESSION['uid']??0)) throw new DomainException('Você não pode excluir sua própria conta.');
                $this->ensureAdmin($u,'cliente',0);
                if (sql('SELECT id FROM clientes WHERE usuario_id=?',[$id])->fetch()) throw new DomainException('Conta vinculada a cliente: exclua pela tela Clientes, se não houver pedidos.');
                (new Usuario())->excluir($id);
            }
            db()->commit();
        } catch (\Throwable $e) { if (db()->inTransaction()) db()->rollBack();throw $e; }
    }
}
