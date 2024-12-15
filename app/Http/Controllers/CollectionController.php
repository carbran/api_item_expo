<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            $collections = Collection::where("user_id", $user->id)->get();

            if ($collections->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($collections);

        } catch (\Exception $e) {
            Log::info('Erro ao consultar as coleções: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Erro ao consultar as coleções.'], 400);
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
                'name'           => 'required|string|max:255',
                'user_id'        => 'required|exists:users,id',
                'categories'     => 'required|array|min:1',
                'categories.*.0' => 'exists:category,id',
            ]);

            $collection = Collection::create([
                'name'    => $request->name,
                'user_id' => $request->user_id,
            ]);

            $categoryIds = collect($request->input('categories'))
                ->pluck('id') // Extrai apenas os IDs das categorias
                ->toArray();

            $collection->categories()->sync($categoryIds);

            DB::commit();

            return response()->json($collection, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao criar coleção: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao criar coleção.'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $collection = Collection::with('user', 'categories')->find($id);

        if (!$collection) {
            return response()->json(['message' => 'Coleção não encontrada'], 404);
        }

        return response()->json($collection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $categoryIds = collect($request->categories)->pluck('id')->toArray();

            $request->validate([
                'name'            => 'sometimes|required|string|max:255',
                'categories'      => 'required|array|min:1',
                'categories.*.id' => 'exists:category,id',
            ]);

            $collection = Collection::find($id);

            if (!$collection) {
                return response()->json(['message' => 'Coleção não encontrada.'], 404);
            }

            $collection->update([
                'name' => $request->name,
            ]);

            $collection->categories()->sync($categoryIds);

            DB::commit();

            return response()->json($collection);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao alterar a coleção: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao alterar a coleção.'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $collection = Collection::find($id);

            if (!$collection) {
                return response()->json(['message' => 'Collection not found'], 404);
            }

            $collection->delete();

            DB::commit();

            return response()->json(['message' => 'Collection deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao apagar a coleção: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao apagar a coleção.'], 400);
        }
    }

    public function getFirstItemImage(string $collectionId)
    {
        try {
            $collection = Collection::with(['items' => function ($query) {
                $query->with('pictures')->orderBy('id')->limit(1);
            }])->find($collectionId);

            if (!$collection) {
                return response()->json(['message' => 'Coleção não encontrada.'], 404);
            }

            $firstItem = $collection->items->first();

            $imageData = $firstItem ? $firstItem->pictures->first()->image_data : null;

            $imageData = $firstItem->pictures->first()->image_data;

            return response()->json(['image_data' => $imageData], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao obter imagem do primeiro item: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao processar a requisição.'], 500);
        }
    }
}
