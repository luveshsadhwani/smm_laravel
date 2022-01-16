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
        $user = Auth::user();

        $item = ItemController::getItemByBarcode($barcode);
        // Initialize inventory model based on barcode and copy data from item
        // The model fields are manually set here. They can also be manually set under attributes in the model
        $model = new Inventory();
        $model->barcode = $item->barcode;
        $model->item = $item->name;
        $model->desc = '';
        $model->quantity = 0;
        $model->expiry_date = date("Y-m-d");
        $model->entry_date = date("Y-m-d");
        $model->notification_id = 0;

        return $this->successReponse($model, '');
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
        $item = ItemController::getItemByBarcode($inventoryData['barcode']);
        $user_defined = $item->user_defined;

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
        $model->notification_id = 0;


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
        $user = Auth::user();
        // Search by inventory ID
        $model = Inventory::find($id);
        return $this->successReponse($model, $model->item . " fetched");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $user = Auth::user();

        $inventoryData = $request->post('data');
        $model = Inventory::find($id);
        if (empty($model)){
            return $this->errorReponse('Item not found', 422);
        }

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
        $model->notification_id = 0;
        $model->save();
        return $this->successReponse($model->id, $model->item . " updated successfully");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $model = Inventory::find($id);
        if (empty($model)) {
            return $this->errorReponse('Item not found', 422);
        }
        $model->delete();

        return $this->successReponse($model, $model->item . ' deleted successfully');
    }
}
