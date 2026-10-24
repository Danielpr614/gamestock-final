<?php
namespace App\Repositories;
use App\Core\Database;
use DomainException;
class Repository {
    protected string $table;
    protected array $fields;
    public function __construct(string $table,array $fields) { $this->table=$table;$this->fields=$fields; }
    public function query(string $query,array $params=[]): \PDOStatement { $s=Database::connection()->prepare($query);$s->execute($params);return $s; }
    public function all(): array { return Database::connection()->query('SELECT * FROM '.$this->table.' ORDER BY id DESC')->fetchAll(); }
    public function find(int $id): array { $s=Database::connection()->prepare('SELECT * FROM '.$this->table.' WHERE id=?');$s->execute([$id]);$r=$s->fetch();if (!$r) throw new DomainException('Registro não encontrado.');return $r; }
    public function create(array $d): int { $d=array_intersect_key($d,array_flip($this->fields));if (!$d) throw new DomainException('Dados ausentes.');$keys=array_keys($d);$s=Database::connection()->prepare('INSERT INTO '.$this->table.' ('.implode(',',$keys).') VALUES ('.implode(',',array_fill(0,count($keys),'?')).')');$s->execute(array_values($d));return (int)Database::connection()->lastInsertId(); }
    public function update(int $id,array $d): void { $this->find($id);$d=array_intersect_key($d,array_flip($this->fields));if (!$d) return;$sets=array_map(fn($k)=>$k.'=?',array_keys($d));Database::connection()->prepare('UPDATE '.$this->table.' SET '.implode(',',$sets).' WHERE id=?')->execute([...array_values($d),$id]); }
    public function delete(int $id): void { $this->find($id);$s=Database::connection()->prepare('DELETE FROM '.$this->table.' WHERE id=?');$s->execute([$id]); }
}
