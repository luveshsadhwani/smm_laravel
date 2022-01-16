<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    use ApiResponser;
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

        return $this->successReponse($inventories, 'Inventories fetched successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function initInventory($barcode)
    {
        $item = ItemController::getItemByBarcode($barcode);
        // Initialize inventory model based on barcode and copy data from item
        $model = new Inventory();

        $model->barcode = $item->barcode;
        $model->item = $item->name;
        $model->expiry_date = date("Y-m-d");

        return $this->successReponse(array('data' => $model, 'user_defined' => $item->user_defined), '');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get user id
        $user = Auth::user();

        // get post body data, must be in json
        $inventoryData = $request->post('data');

        // check if the user enters a new item
        $user_defined = $request->post('user_defined');

        $model = new Inventory();
        // process data
        foreach($inventoryData as $fieldName => $value)
        {
            if(is_null($value))
            {
                $value = '';
            }
            $model->{$fieldName} = $value;
        }
        $model->user_id = $user->id;


        if ($user_defined == 1) {
            ItemController::storeUserItem($model);
        }

        $model->save();
        return $this->successReponse($model->id, $model->item . " added successfully");
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
