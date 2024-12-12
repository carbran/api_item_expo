<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemPicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(Item::with('collection')->get());

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
            $validatedData = $request->validate([
                'collection_id'    => 'required|exists:collection,id',
                'title'            => 'required|string|max:255',
                'subtitle'         => 'nullable|string|max:255',
                'author'           => 'nullable|string|max:255',
                'acquisition_date' => 'required|date',
                'condition'        => 'nullable|integer',
                'size'             => 'nullable|string|max:255',
                'size_type'        => 'nullable|integer',
                'amount'           => 'nullable|integer',
                'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $item = Item::create([
                'collection_id'    => $validatedData['collection_id'],
                'title'            => $validatedData['title'],
                'subtitle'         => $validatedData['subtitle'],
                'author'           => $validatedData['author'],
                'acquisition_date' => $validatedData['acquisition_date'],
                'condition'        => $validatedData['condition'],
                'size'             => $validatedData['size'],
                'size_type'        => $validatedData['size_type'],
                'amount'           => $validatedData['amount'],
            ]);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');

                $imageData = base64_encode(file_get_contents(storage_path('app/public/' . $imagePath)));

                ItemPicture::create([
                    'item_id'    => $item->id,
                    'image_data' => $imageData,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Item criado com sucesso.',
                'item'    => $item,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao criar item: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao criar item.'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $item = Item::with('collection', 'itemPicture')->findOrFail($id);

            return response()->json($item);

        } catch (\Exception $e) {
            Log::info('Item não encontrado: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro buscar o item.'], 400);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validate([
                'collection_id'    => 'required|exists:collection,id',
                'title'            => 'required|string|max:255',
                'subtitle'         => 'nullable|string|max:255',
                'author'           => 'nullable|string|max:255',
                'acquisition_date' => 'required|date',
                'condition'        => 'nullable|integer',
                'size'             => 'nullable|string|max:255',
                'size_type'        => 'nullable|integer',
                'amount'           => 'nullable|integer',
                'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $item = Item::findOrFail($id);

            $item->update($validatedData);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                $imageData = base64_encode(file_get_contents(storage_path('app/public/' . $imagePath)));

                ItemPicture::updateOrCreate(
                    ['item_id' => $item->id],
                    ['image_data' => $imageData]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Item alterado com sucesso.',
                'item'    => $item,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao alterar a item: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao alterar a item.'], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($id);
            $item->delete();

            $itemPicture = ItemPicture::where('item_id', $item->id)->first();
            if ($itemPicture) {
                $itemPicture->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Item deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info('Erro ao apagar a item: ' . json_encode($e->getMessage()));
            return response()->json(['message' => 'Ocorreu um erro ao apagar a item.'], 400);
        }
    }
}
