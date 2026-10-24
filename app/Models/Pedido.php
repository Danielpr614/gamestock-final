<?php
namespace App\Models;
use App\Repositories\Repository;
final class Pedido extends Entity {
    public function __construct() { $this->repository=new Repository('pedidos',['cliente_id', 'status', 'total', 'chave']); }

    public function listarDetalhes(): array { return $this->repository->query('SELECT p.*,u.nome cliente FROM pedidos p JOIN clientes c ON c.id=p.cliente_id JOIN usuarios u ON u.id=c.usuario_id ORDER BY p.id DESC')->fetchAll(); }
    public function porUsuario(int $uid): array { return $this->repository->query('SELECT p.* FROM pedidos p JOIN clientes c ON c.id=p.cliente_id WHERE c.usuario_id=? ORDER BY p.id DESC',[$uid])->fetchAll(); }
    public function detalhes(int $id) { return $this->repository->query('SELECT p.*,u.nome cliente,c.usuario_id FROM pedidos p JOIN clientes c ON c.id=p.cliente_id JOIN usuarios u ON u.id=c.usuario_id WHERE p.id=?',[$id])->fetch(); }
    public function itens(int $id): array { return $this->repository->query('SELECT * FROM itens_pedido WHERE pedido_id=? ORDER BY id',[$id])->fetchAll(); }
    public function resumo(): array {
        return ['games'=>$this->repository->query('SELECT COUNT(*) FROM games')->fetchColumn(),'clients'=>$this->repository->query('SELECT COUNT(*) FROM clientes')->fetchColumn(),'orders'=>$this->repository->query('SELECT COUNT(*) FROM pedidos')->fetchColumn(),'sales'=>$this->repository->query("SELECT COALESCE(SUM(total),0) FROM pedidos WHERE status IN ('confirmado','concluido')")->fetchColumn()];
    }

}
