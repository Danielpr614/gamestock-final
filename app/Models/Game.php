<?php
namespace App\Models;
use App\Repositories\Repository;
final class Game extends Entity {
    public function __construct() { $this->repository=new Repository('games',['titulo', 'descricao', 'preco', 'estoque', 'categoria_id', 'imagem', 'plataforma', 'sistemas', 'loja', 'link_loja', 'ativo']); }

    private function filtrosCatalogo(string $q,string $platform,string $category): array {
        $where='g.ativo=1';$args=[];
        if ($q!=='') { $where.=" AND g.titulo LIKE ? ESCAPE '='";$args[]='%'.str_replace(['=','%','_'],['==','=%','=_'],$q).'%'; }
        if ($platform!=='') { $where.=' AND g.plataforma=?';$args[]=$platform; }
        if ($category!=='') { $where.=' AND g.categoria_id=?';$args[]=$category; }
        return [$where,$args];
    }
    public function totalCatalogo(string $q,string $platform,string $category): int {
        [$where,$args]=$this->filtrosCatalogo($q,$platform,$category);
        return (int)$this->repository->query('SELECT COUNT(*) FROM games g JOIN categorias c ON c.id=g.categoria_id WHERE '.$where,$args)->fetchColumn();
    }
    public function catalogo(string $q,string $platform,string $category,string $sort,int $page=1,int $perPage=12): array {
        $orders=['title'=>'g.titulo ASC,g.id ASC','price_asc'=>'g.preco ASC,g.id ASC','price_desc'=>'g.preco DESC,g.id ASC'];
        if (!isset($orders[$sort])) throw new \DomainException('Ordenação inválida.');
        if ($page<1 || $perPage<1 || $perPage>100) throw new \DomainException('Página inválida.');
        [$where,$args]=$this->filtrosCatalogo($q,$platform,$category);$offset=($page-1)*$perPage;
        // LIMIT/OFFSET são inteiros definidos pelo sistema; filtros usam parâmetros PDO.
        return $this->repository->query('SELECT g.*,c.nome categoria FROM games g JOIN categorias c ON c.id=g.categoria_id WHERE '.$where.' ORDER BY '.$orders[$sort].' LIMIT '.$perPage.' OFFSET '.$offset,$args)->fetchAll();
    }
    public function destaques(): array {
        // Uma seleção por plataforma. Se o preferido estiver inativo, usar outro disponível.
        $preferred=['PC'=>'Clair Obscur: Expedition 33','PlayStation'=>'God of War Ragnarök','Xbox'=>'Forza Horizon 5 — Edição Padrão','Nintendo'=>'Mario Kart World'];
        $rows=[];
        foreach ($preferred as $platform=>$title) {
            $game=$this->repository->query('SELECT g.*,c.nome categoria FROM games g JOIN categorias c ON c.id=g.categoria_id WHERE g.ativo=1 AND g.plataforma=? ORDER BY (g.titulo=?) DESC,g.titulo,g.id LIMIT 1',[$platform,$title])->fetch();
            if ($game) $rows[]=$game;
        }
        return $rows;
    }
    public function detalhes(int $id) { return $this->repository->query('SELECT g.*,c.nome categoria FROM games g JOIN categorias c ON c.id=g.categoria_id WHERE g.id=? AND g.ativo=1',[$id])->fetch(); }
    public function disponivel(int $id) { return $this->repository->query('SELECT * FROM games WHERE id=? AND ativo=1',[$id])->fetch(); }
    public function listarDetalhes(): array { return $this->repository->query('SELECT g.*,c.nome categoria FROM games g JOIN categorias c ON c.id=g.categoria_id ORDER BY g.titulo')->fetchAll(); }
    public function estoqueBaixo(): array { return $this->repository->query('SELECT * FROM games WHERE ativo=1 AND estoque<5 ORDER BY estoque,titulo')->fetchAll(); }
    public function ativos(): array { return $this->repository->query('SELECT * FROM games WHERE ativo=1 ORDER BY titulo')->fetchAll(); }

}
