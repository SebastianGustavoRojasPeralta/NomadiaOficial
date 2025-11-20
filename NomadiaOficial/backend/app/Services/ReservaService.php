<?php
namespace App\Services;

use App\Repositories\ReservaRepository;

class ReservaService
{
    protected $repo;

    public function __construct(ReservaRepository $repo = null)
    {
        $this->repo = $repo ?: new ReservaRepository();
    }

    public function listByUser($userId)
    {
        return $this->repo->allByUser($userId);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }
}
