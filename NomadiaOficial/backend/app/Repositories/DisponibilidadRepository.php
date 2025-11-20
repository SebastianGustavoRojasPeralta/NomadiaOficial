<?php
namespace App\Repositories;

use App\Database\DB;

class DisponibilidadRepository
{
    public function listByExperiencia($experienciaId)
    {
        $mysqli = DB::getConnection();
        $stmt = $mysqli->prepare('SELECT * FROM disponibilidades WHERE experiencia_id = ? ORDER BY fecha ASC');
        $stmt->bind_param('i', $experienciaId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($r = $res->fetch_assoc()) $rows[] = $r;
        $stmt->close();
        $mysqli->close();
        return $rows;
    }

    public function create(array $data)
    {
        $mysqli = DB::getConnection();
        $stmt = $mysqli->prepare('INSERT INTO disponibilidades (experiencia_id, fecha, cupos, created_at, updated_at) VALUES (?,?,?,NOW(),NOW())');
        $stmt->bind_param('isi', $data['experiencia_id'], $data['fecha'], $data['cupos']);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $mysqli->close();
            throw new \Exception('insert_failed: ' . $err);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        $res = $mysqli->query('SELECT * FROM disponibilidades WHERE id = ' . (int)$id);
        $row = $res->fetch_assoc();
        $mysqli->close();
        return $row;
    }
}
