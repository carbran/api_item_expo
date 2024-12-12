<?php

namespace App\Http\Controllers;

use App\Enum\StatusEnum;
use App\Models\Category;
use DB;
use Illuminate\Http\Request;
use Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = Category::where('status', StatusEnum::ACTIVE)->get();

            return response()->json($categories);

        } catch (\Exception $e) {
            Log::info('Erro ao consultar as categorias: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Erro ao consultar as categorias.'], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name'   => 'required|string|max:255',
                'status' => 'required|in:' . StatusEnum::ACTIVE . ',' . StatusEnum::INACTIVE,
            ]);

            $category = Category::create([
                'name'   => $request->name,
                'status' => $request->status,
            ]);

            DB::commit();

            return response()->json($category, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao criar categoria: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao criar categoria.'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Categoria não encontrada'], 404);
        }

        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'name'   => 'required|string|max:255',
                'status' => 'required|in:' . StatusEnum::ACTIVE . ',' . StatusEnum::INACTIVE,
            ]);

            $category = Category::find($id);

            $category->update([
                'name'   => $request->name,
                'status' => $request->status,
            ]);

            DB::commit();

            return response()->json($category);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao alterar a categoria: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao alterar a categoria.'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $category = Category::find($id);

            if (!$category) {
                return response()->json(['message' => 'Categoria não encontrada.'], 404);
            }

            $category->delete();

            DB::commit();

            return response()->json(['message' => 'Categora apagada com sucesso']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao apagar a categoria: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao apagar a categoria.'], 400);
        }
    }
}
