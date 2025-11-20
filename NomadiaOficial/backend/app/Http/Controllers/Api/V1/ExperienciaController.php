<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Experiencia;
use App\Services\ExperienciaService;

class ExperienciaController extends Controller
{
    protected $service;

    public function __construct(ExperienciaService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/v1/experiencias
     * Aplica filtros y devuelve las experiencias
     */
    public function index(Request $request)
    {
        $filtros = $request->only(['categoria','precio_min','precio_max','duracion','duracion_min','duracion_max']);
        $result = $this->service->getExperienciasFiltradas($filtros);
        return response()->json($result);
    }

    /**
     * GET /api/v1/experiencias/{id}
     */
    public function show($id)
    {
        $e = Experiencia::findOrFail($id);
        return response()->json($e);
    }

    /**
     * POST /api/v1/experiencias
     * Publicar experiencia (CU-06)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'nullable|string|max:100',
            'duracion' => 'nullable|integer|min:0',
        ]);

        $user = Auth::user();
        $guiaId = $user ? $user->id : null;

        $exp = $this->service->publicarExperiencia($data, $guiaId);
        return response()->json($exp, 201);
    }

    /**
     * POST /api/v1/experiencias/{id}/approve
     * Aprobar experiencia (CU-15)
     */
    public function approve(Request $request, $id)
    {
        $exp = $this->service->aprobarExperiencia($id);
        if (!$exp) return response()->json(['error' => 'not_found'], 404);
        return response()->json($exp);
    }
}
