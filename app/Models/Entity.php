<?php
namespace App\Models;
use App\Repositories\Repository;
abstract class Entity {
    protected Repository $repository;
    public function listar(): array { return $this->repository->all(); }
    public function buscarPorId(int $id): array { return $this->repository->find($id); }
    public function cadastrar(array $data): int { return $this->repository->create($data); }
    public function atualizar(int $id,array $data): void { $this->repository->update($id,$data); }
    public function excluir(int $id): void { $this->repository->delete($id); }
}
