<?php
namespace App\Models;
use App\Repositories\Repository;
final class Cliente extends Entity {
    public function __construct() { $this->repository=new Repository('clientes',['usuario_id', 'telefone']); }

    public function porUsuario(int $uid) { return $this->repository->query('SELECT * FROM clientes WHERE usuario_id=?',[$uid])->fetch(); }
    public function listarDetalhes(): array { return $this->repository->query('SELECT c.id,c.telefone,u.nome,u.email,u.ativo,u.perfil FROM clientes c JOIN usuarios u ON u.id=c.usuario_id ORDER BY c.id DESC')->fetchAll(); }
    public function detalhes(int $id) { return $this->repository->query('SELECT c.id,c.telefone,u.nome,u.email,u.ativo,u.perfil FROM clientes c JOIN usuarios u ON u.id=c.usuario_id WHERE c.id=?',[$id])->fetch(); }
    public function ativos(): array { return $this->repository->query("SELECT c.id,u.nome,u.email FROM clientes c JOIN usuarios u ON u.id=c.usuario_id WHERE u.ativo=1 AND u.perfil='cliente' ORDER BY u.nome")->fetchAll(); }

}
