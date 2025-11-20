<?php
namespace App\Services;

use App\Repositories\CalificacionRepository;

class CalificacionService
{
    protected $repo;
    public function __construct(CalificacionRepository $repo = null)
    {
        $this->repo = $repo ?: new CalificacionRepository();
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
