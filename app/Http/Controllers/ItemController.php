<?php

namespace App\Http\Controllers;

use App\Mail\UserDefinedItemCreated;
use App\Models\Inventory;
use App\Models\Item;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ItemController extends Controller
{
    use ApiResponser;

    private function checkIsAdmin()
    {
        $user = Auth::user();

        if($user->first_name != 'admin')
        {
            return $this->errorReponse('Unauthorized access', 403);
        } else {
            return true;
        }

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // check that user is admin
        if ($this->checkIsAdmin())
        {
            $items = Item::all();

            return $this->successReponse($items, 'Items fetched successfully');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // check that user is admin
        if($this->checkIsAdmin())
        {
            $barcode = $request->input('barcode');
            $name = $request->input('name');
    
            // create new item with unique barcode
            $existedModel = Item::where('barcode', $barcode)->first();

            if (!empty($existedModel))
            {
                return $this->errorReponse('Item already exists', 422);
            }

            $model = new Item();
            $model->barcode = $barcode;
            $model->name = $name;
    
            $model->save();
            
            return $this->successReponse($model, $model->name . " added successfully");
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($this->checkIsAdmin())
        {

            $model = Item::find($id);

            return $this->successReponse($model, '');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        if($this->checkIsAdmin())
        {
            //control incoming data so the correct fields are updated
            $requestData = $request->all();
            $data = array();

            if(array_key_exists('barcode', $requestData))
            {
                $data['barcode'] = $requestData['barcode'];
            }

            if(array_key_exists('name', $requestData))
            {
                $data['name'] = $requestData['name'];
            }

            
            $model = Item::find($id);
            $model->barcode = $data['barcode'];
            $model->name = $data['name'];

            // check if barcode exists
            $existedModels = Item::where('barcode', $data['barcode'])
                ->where('id','<>', $id)
                ->get();

            if($existedModels->count() > 0)
            {
                return $this->errorReponse('Barcode already exists', 422);
            }

            $model->save();

            return $this->successReponse($model, $model->name . " updated successfully");

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function verify($id)
    {
        if($this->checkIsAdmin())
        {

            $model = Item::find($id);
            if ($model->user_defined === 0) {
                return $this->errorReponse('Item is already verified', 422);
            }

            $model->user_defined = 0;
            $model->save();
            return $this->successReponse($model, $model->name . " verified successfully");
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->checkIsAdmin())
        {

            $model = Item::find($id);
            $model->delete();

            return $this->successReponse($model, $model->name . " deleted successfully.");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    static public function getItemByBarcode($barcode)
    {
        // check if item exists
        $model = Item::where('barcode', $barcode)->first();
        
        if (empty($model))
        {
            $model = new Item;
            $model->barcode = $barcode;
        }

        return $model;
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    static public function storeUserItem(Inventory $inventory)
    {
        $item = new Item();
        $item->barcode = $inventory->barcode;
        $item->name = $inventory->item;
            
        // store new item to the DB
        $item->save();
        
        // notify team
        Mail::to('smm.aws01@gmail.com')->send(new UserDefinedItemCreated($item));

        return;
        
    }
}
