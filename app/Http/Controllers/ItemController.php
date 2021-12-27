<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    private function checkIsAdmin()
    {
        $user = Auth::user();

        if($user->name != 'admin')
        {
            return false;
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

            return array(
                'data' => $items,
                'success' => true,
            );
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
                return array(
                    'data'=> null,
                    'success' => false,
                    'message' => 'Error: Duplicated Barcode, please try again.');
            }

            $model = new Item();
            $model->barcode = $barcode;
            $model->name = $name;
    
            $model->save();
            
    
            return array(
                'data' => $model,
                'success' => true,
                'message' => $model->name ." added successfully"    
            );
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
    
            return array(
                'data' => $model,
                'success' => true    
            );
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
                return array(
                    'data' => null,
                    'sucess' => false,
                    'message' => 'Error: Duplicated barcode. Please try again'
                );
            }

            $model->save();

            return array(
                'data' => $model,
                'sucess' => true,
                'message' => $model->name ." updated successfully"
            );

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
    
            return array(
                'data' => $model,
                'success' => true,    
                'message' => $model->name ." deleted successfully"    
            );
        }
    }
}
