<?php
namespace App\Services;

use App\Repositories\DisponibilidadRepository;

class DisponibilidadService
{
    protected $repo;
    public function __construct(DisponibilidadRepository $repo = null)
    {
        $this->repo = $repo ?: new DisponibilidadRepository();
    }

    public function listByExperiencia($experienciaId)
    {
        return $this->repo->listByExperiencia($experienciaId);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }
}
