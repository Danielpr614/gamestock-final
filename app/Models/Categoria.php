<?php
namespace App\Models;
use App\Repositories\Repository;
final class Categoria extends Entity {
    public function __construct() { $this->repository=new Repository('categorias',['nome', 'descricao']); }
}
