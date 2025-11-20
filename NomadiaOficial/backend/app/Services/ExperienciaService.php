<?php
namespace App\Services;

use App\Repositories\ExperienciaRepository;
use App\Models\Experiencia;

class ExperienciaService
{
    protected $repo;

    public function __construct(ExperienciaRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Publicar una nueva experiencia (CU-06)
     * - setea estado a 'Pendiente'
     * - asigna guia_id
     */
    public function publicarExperiencia(array $data, $guiaId)
    {
        $data['estado'] = 'Pendiente';
        $data['guia_id'] = $guiaId;
        return $this->repo->create($data);
    }

    /**
     * Aprobar una experiencia (CU-15)
     */
    public function aprobarExperiencia($experienciaId)
    {
        return $this->repo->updateStatus($experienciaId, 'Aprobada');
    }

    /**
     * Obtener experiencias filtradas
     */
    public function getExperienciasFiltradas(array $filtros)
    {
        return $this->repo->buscar($filtros);
    }
}
