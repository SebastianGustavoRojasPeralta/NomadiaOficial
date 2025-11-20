<?php
namespace App\Repositories;

use App\Database\DB;

class PagoRepository
{
    public function listByReserva($reservaId)
    {
        $mysqli = DB::getConnection();
        $stmt = $mysqli->prepare('SELECT * FROM pagos WHERE reserva_id = ? ORDER BY created_at DESC');
        $stmt->bind_param('i', $reservaId);
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
        $stmt = $mysqli->prepare('INSERT INTO pagos (reserva_id, amount, method, status, created_at, updated_at) VALUES (?,?,?,"pending",NOW(),NOW())');
        $stmt->bind_param('ids', $data['reserva_id'], $data['amount'], $data['method']);
        if (!$stmt->execute()) {
            $err = $stmt->error;
            $stmt->close();
            $mysqli->close();
            throw new \Exception('insert_failed: ' . $err);
        }
        $id = $stmt->insert_id;
        $stmt->close();
        $res = $mysqli->query('SELECT * FROM pagos WHERE id = ' . (int)$id);
        $row = $res->fetch_assoc();
        $mysqli->close();
        return $row;
    }
}
