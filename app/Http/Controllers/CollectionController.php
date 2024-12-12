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
            return response()->json(Collection::with('user')->get());

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
                'name'         => 'required|string|max:255',
                'user_id'      => 'required|exists:users,id',
                'categories'   => 'required|array|min:1',
                'categories.*' => 'exists:categories,id',
            ]);

            $collection = Collection::create([
                'name'    => $request->name,
                'user_id' => $request->user_id,
            ]);

            $collection->categories()->attach($request->categories);

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
        $collection = Collection::with('user')->find($id);

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
            $request->validate([
                'name'         => 'sometimes|required|string|max:255',
                'categories'   => 'required|array|min:1',
                'categories.*' => 'exists:categories,id',
            ]);

            $collection = Collection::find($id);

            if (!$collection) {
                return response()->json(['message' => 'Coleção não encontrada.'], 404);
            }

            $collection->update([
                'name' => $request->name,
            ]);

            $collection->categories()->sync($request->categories);

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
}