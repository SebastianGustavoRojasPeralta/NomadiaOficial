<?php
namespace App\Repositories;

use App\Database\DB;

class CalificacionRepository
{
    public function listByExperiencia($experienciaId)
    {
        $mysqli = DB::getConnection();
        $stmt = $mysqli->prepare('SELECT * FROM calificaciones WHERE experiencia_id = ? ORDER BY created_at DESC');
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
        $stmt = $mysqli->prepare('INSERT INTO calificaciones (experiencia_id, usuario_id, rating, comentario, created_at, updated_at) VALUES (?,?,?,?,NOW(),NOW())');
        $stmt->bind_param('iiis', $data['experiencia_id'], $data['usuario_id'], $data['rating'], $data['comentario']);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $mysqli->close();
            throw new \Exception('insert_failed: ' . $err);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        $res = $mysqli->query('SELECT * FROM calificaciones WHERE id = ' . (int)$id);
        $row = $res->fetch_assoc();
        $mysqli->close();
        return $row;
    }
}
