<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return inventory based on user
        $user = Auth::user();

        $inventories = Inventory::where('user_id', $user->id)->get();
        
        return array(
            'data' => $inventories,
            'success' => true
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initInventory($barcode)
    {
        // Initialize inventory model based on barcode
        $model = new Inventory();
        $model->barcode = 0;
        $model->item = '';
        $model->quantity = 0;
        $model->expiry_date = date("Y-m-d");

        // check if item exists in DB
        $item = Item::where('barcode', $barcode)->first();
        if (!empty($item))
        {
            $model->barcode = $item->barcode;
            $model->item = $item->name;
        }

        return array(
            'data' => $model,
            'success' => true
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // extract request data
        $barcode = $request->input('barcode');
        $item = $request->input('item');
        $quantity = $request->input('quantity');
        $expiry_date = date($request->input('expiry_date'));
        
        // get user id
        $user = Auth::user();

        $model = new Inventory();
        $model->user_id = $user->id;
        $model->barcode = $barcode;
        $model->item = $item;
        $model->quantity = $quantity;
        $model->expiry_date = $expiry_date;

        // get item id
        $itemModel = Item::where('barcode', $barcode)->first();
        
        if (!empty($itemModel))
        {
            $model->barcode = $itemModel->barcode;
        }

        $model->save();

        return array(
            'data' => $model->id,
            'success' => true,
            'message' => $model->item . ' added successfully'
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Search by inventory ID and check that it belongs to the correct user
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
