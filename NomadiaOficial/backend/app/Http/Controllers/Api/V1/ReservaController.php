<?php
namespace App\Http\Controllers\Api\V1;

use App\Services\ReservaService;

class ReservaController
{
    protected $service;
    public function __construct(ReservaService $service = null)
    {
        $this->service = $service ?: new ReservaService();
    }

    public function index()
    {
        $userId = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
        if (!$userId) {
            echo json_encode([]);
            return;
        }
        $rows = $this->service->listByUser($userId);
        echo json_encode($rows);
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $row = $this->service->create($input);
            echo json_encode(['reserva'=>$row]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error'=>'insert_failed','message'=>$e->getMessage()]);
        }
    }
}
