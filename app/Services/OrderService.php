<?php
namespace App\Services;
use App\Core\Validation as V;
use App\Models\Pedido;
use DomainException;
final class OrderService {
    public function quantities(array $items): array {
        if (count($items)>100) throw new DomainException('Quantidade de itens inválida.');$out=[];
        foreach ($items as $id=>$qty) { $id=V::integer($id);$qty=V::integer($qty,0,99);if ($qty>0) $out[$id]=$qty; }
        if (!$out || count($out)>25) throw new DomainException('Selecione entre 1 e 25 jogos.');ksort($out,SORT_NUMERIC);return $out;
    }
    public function save(int $client,array $items,string $key,int $id=0,bool $confirm=false): int {
        if (!preg_match('/^[a-f0-9]{64}$/D',$key)) throw new DomainException('Identificador de pedido inválido.');
        db()->beginTransaction();try {
            $c=sql('SELECT c.id,u.ativo,u.perfil FROM clientes c JOIN usuarios u ON u.id=c.usuario_id WHERE c.id=? FOR UPDATE',[$client])->fetch();
            if (!$c || !$c['ativo'] || $c['perfil']!=='cliente') throw new DomainException('Selecione um cliente ativo.');
            if (!$id) { $existing=sql('SELECT id,cliente_id FROM pedidos WHERE chave=?',[$key])->fetch();
                if ($existing) { if ((int)$existing['cliente_id']!==$client) throw new DomainException('Pedido inválido.');db()->commit();return (int)$existing['id']; }
            }
            $quantities=$this->quantities($items);$m=new Pedido();
            if ($id) { $old=sql('SELECT * FROM pedidos WHERE id=? FOR UPDATE',[$id])->fetch();if (!$old || $old['status']!=='rascunho') throw new DomainException('Somente pedidos em rascunho podem ser editados.'); }
            $total=0;$rows=[];
            // Bloquear jogos em ordem de ID reduz conflitos e impede vender a mesma última unidade duas vezes.
            foreach ($quantities as $gid=>$qty) {
                $g=sql('SELECT * FROM games WHERE id=? FOR UPDATE',[$gid])->fetch();
                if (!$g || !$g['ativo']) throw new DomainException('Um jogo selecionado não está disponível.');
                if ($confirm && $qty>(int)$g['estoque']) throw new DomainException('Estoque insuficiente para '.$g['titulo'].'.');
                $price=V::money($g['preco']);$total+=$price*$qty;$rows[]=[$gid,$g['titulo'],$g['plataforma'],$qty,V::decimal($price)];
            }
            if ($id) { $m->atualizar($id,['cliente_id'=>$client,'total'=>V::decimal($total)]);sql('DELETE FROM itens_pedido WHERE pedido_id=?',[$id]); }
            else $id=$m->cadastrar(['cliente_id'=>$client,'total'=>V::decimal($total),'status'=>'rascunho','chave'=>$key]);
            foreach ($rows as $row) sql('INSERT INTO itens_pedido(pedido_id,game_id,titulo,plataforma,quantidade,preco_unitario) VALUES(?,?,?,?,?,?)',[$id,...$row]);
            if ($confirm) { foreach ($quantities as $gid=>$qty) sql('UPDATE games SET estoque=estoque-? WHERE id=?',[$qty,$gid]);$m->atualizar($id,['status'=>'confirmado']); }
            db()->commit();return $id;
        } catch (\Throwable $e) { if (db()->inTransaction()) db()->rollBack();throw $e; }
    }
    public function transition(int $id,string $target,?int $owner=null): void {
        V::choice($target,['confirmado','concluido','cancelado']);db()->beginTransaction();try {
            $p=sql('SELECT * FROM pedidos WHERE id=? FOR UPDATE',[$id])->fetch();
            if (!$p || ($owner!==null && (int)$p['cliente_id']!==$owner)) throw new DomainException('Pedido não encontrado.');
            if ($p['status']===$target) { db()->commit();return; }
            $allowed=['rascunho'=>['confirmado','cancelado'],'confirmado'=>['concluido','cancelado'],'concluido'=>[],'cancelado'=>[]];
            if (!in_array($target,$allowed[$p['status']],true) || ($owner!==null && $target!=='cancelado')) throw new DomainException('Esta mudança de situação não é permitida.');
            $items=sql('SELECT * FROM itens_pedido WHERE pedido_id=? ORDER BY game_id',[$id])->fetchAll();
            if ($target==='confirmado') {
                $client=sql('SELECT u.ativo,u.perfil FROM clientes c JOIN usuarios u ON u.id=c.usuario_id WHERE c.id=?',[$p['cliente_id']])->fetch();
                if (!$client || !$client['ativo'] || $client['perfil']!=='cliente' || !$items) throw new DomainException('Cliente ou itens indisponíveis.');
                foreach ($items as $i) { $g=sql('SELECT * FROM games WHERE id=? FOR UPDATE',[$i['game_id']])->fetch();
                    if (!$g || !$g['ativo'] || $g['estoque']<$i['quantidade']) throw new DomainException('Estoque insuficiente ou jogo indisponível.');
                    if (V::money($g['preco'])!==V::money($i['preco_unitario'])) throw new DomainException('O preço mudou. Edite e salve o rascunho antes de confirmar.');
                }
                foreach ($items as $i) sql('UPDATE games SET estoque=estoque-? WHERE id=?',[$i['quantidade'],$i['game_id']]);
            }
            if ($target==='cancelado' && $p['status']==='confirmado') foreach ($items as $i) {
                $g=sql('SELECT estoque FROM games WHERE id=? FOR UPDATE',[$i['game_id']])->fetch();
                if ((int)$g['estoque']+(int)$i['quantidade']>1000000) throw new DomainException('A devolução ultrapassa o limite de estoque. Ajuste o estoque antes de cancelar.');
                sql('UPDATE games SET estoque=estoque+? WHERE id=?',[$i['quantidade'],$i['game_id']]);
            }
            (new Pedido())->atualizar($id,['status'=>$target]);db()->commit();
        } catch (\Throwable $e) { if (db()->inTransaction()) db()->rollBack();throw $e; }
    }
    public function delete(int $id): void {
        db()->beginTransaction();try { $p=sql('SELECT status FROM pedidos WHERE id=? FOR UPDATE',[$id])->fetch();
            if (!$p || !in_array($p['status'],['rascunho','cancelado'],true)) throw new DomainException('Exclua apenas pedidos em rascunho ou cancelados.');
            (new Pedido())->excluir($id);db()->commit();
        } catch (\Throwable $e) { if (db()->inTransaction()) db()->rollBack();throw $e; }
    }
}
