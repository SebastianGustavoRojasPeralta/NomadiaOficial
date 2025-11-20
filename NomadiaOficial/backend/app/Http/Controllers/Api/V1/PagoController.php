<?php
namespace App\Http\Controllers\Api\V1;

use App\Services\PagoService;

class PagoController
{
    protected $service;
    public function __construct(PagoService $service = null)
    {
        $this->service = $service ?: new PagoService();
    }

    public function index()
    {
        $reservaId = isset($_GET['reserva_id']) ? (int)$_GET['reserva_id'] : null;
        if (!$reservaId) { echo json_encode([]); return; }
        $rows = $this->service->listByReserva($reservaId);
        echo json_encode($rows);
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $row = $this->service->create($input);
            echo json_encode(['pago'=>$row]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error'=>'insert_failed','message'=>$e->getMessage()]);
        }
    }
}
