<?php
namespace App\Services;

use App\Repositories\PagoRepository;

class PagoService
{
    protected $repo;
    public function __construct(PagoRepository $repo = null)
    {
        $this->repo = $repo ?: new PagoRepository();
    }

    public function listByReserva($reservaId)
    {
        return $this->repo->listByReserva($reservaId);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }
}
