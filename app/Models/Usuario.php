<?php
namespace App\Models;
use App\Repositories\Repository;
final class Usuario extends Entity {
    public function __construct() { $this->repository=new Repository('usuarios',['nome', 'email', 'senha', 'perfil', 'ativo']); }

    public function porEmail(string $email) { return $this->repository->query('SELECT * FROM usuarios WHERE email=? AND ativo=1',[$email])->fetch(); }

}
