<?php
namespace App\Repositories;

use App\Database\DB;

class ReservaRepository
{
    public function allByUser($userId)
    {
        $mysqli = DB::getConnection();
        $stmt = $mysqli->prepare('SELECT * FROM reservas WHERE usuario_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $userId);
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
        $stmt = $mysqli->prepare('INSERT INTO reservas (experiencia_id, usuario_id, fecha_reserva, cantidad, total, status, created_at, updated_at) VALUES (?,?,?,?,?,"pending",NOW(),NOW())');
        $stmt->bind_param('iisid', $data['experiencia_id'], $data['usuario_id'], $data['fecha_reserva'], $data['cantidad'], $data['total']);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $mysqli->close();
            throw new \Exception('insert_failed: ' . $err);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        $res = $mysqli->query('SELECT * FROM reservas WHERE id = ' . (int)$id);
        $row = $res->fetch_assoc();
        $mysqli->close();
        return $row;
    }
}
