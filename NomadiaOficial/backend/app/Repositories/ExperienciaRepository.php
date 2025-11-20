<?php
namespace App\Repositories;

use App\Models\Experiencia;

class ExperienciaRepository
{
    /**
     * Buscar experiencias aplicando filtros RF-04
     * Filtros soportados:
     * - categoria => string
     * - precio_min, precio_max => decimals
     * - duracion => exact value OR duracion_min, duracion_max
     *
     * @param array $filtros
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function buscar(array $filtros)
    {
        $query = Experiencia::query();

        // filtrar por categoría
        if (!empty($filtros['categoria'])) {
            $query->where('categoria', $filtros['categoria']);
        }

        // rango de precio
        if (isset($filtros['precio_min']) && isset($filtros['precio_max'])) {
            $query->whereBetween('precio', [(float)$filtros['precio_min'], (float)$filtros['precio_max']]);
        } else {
            if (isset($filtros['precio_min'])) {
                $query->where('precio', '>=', (float)$filtros['precio_min']);
            }
            if (isset($filtros['precio_max'])) {
                $query->where('precio', '<=', (float)$filtros['precio_max']);
            }
        }

        // duracion exacta o rango
        if (isset($filtros['duracion'])) {
            $query->where('duracion', $filtros['duracion']);
        } else {
            if (isset($filtros['duracion_min']) && isset($filtros['duracion_max'])) {
                $query->whereBetween('duracion', [(int)$filtros['duracion_min'], (int)$filtros['duracion_max']]);
            } else {
                if (isset($filtros['duracion_min'])) {
                    $query->where('duracion', '>=', (int)$filtros['duracion_min']);
                }
                if (isset($filtros['duracion_max'])) {
                    $query->where('duracion', '<=', (int)$filtros['duracion_max']);
                }
            }
        }

        // Orden por fecha de creación descendente por defecto
        $query->orderBy('created_at', 'desc');

        return $query->get();
    }

    /**
     * Crear una nueva experiencia
     * @param array $data
     * @return Experiencia
     */
    public function create(array $data)
    {
        return Experiencia::create($data);
    }

    /**
     * Actualiza el estado de una experiencia
     * @param mixed $id
     * @param string $status
     * @return Experiencia|null
     */
    public function updateStatus($id, $status)
    {
        $exp = Experiencia::find($id);
        if (!$exp) return null;
        $exp->estado = $status;
        $exp->save();
        return $exp;
    }

    /**
     * Buscar por id
     * @param mixed $id
     * @return Experiencia|null
     */
    public function findById($id)
    {
        return Experiencia::find($id);
    }
}
