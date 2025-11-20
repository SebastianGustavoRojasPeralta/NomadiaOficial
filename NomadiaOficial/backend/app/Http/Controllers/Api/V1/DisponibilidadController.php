<?php
namespace App\Http\Controllers\Api\V1;

use App\Services\DisponibilidadService;

class DisponibilidadController
{
    protected $service;
    public function __construct(DisponibilidadService $service = null)
    {
        $this->service = $service ?: new DisponibilidadService();
    }

    public function index()
    {
        $expId = isset($_GET['experiencia_id']) ? (int)$_GET['experiencia_id'] : null;
        if (!$expId) {
            echo json_encode([]);
            return;
        }
        $rows = $this->service->listByExperiencia($expId);
        echo json_encode($rows);
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        try {
            $row = $this->service->create($input);
            echo json_encode(['disponibilidad'=>$row]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error'=>'insert_failed','message'=>$e->getMessage()]);
        }
    }
}
