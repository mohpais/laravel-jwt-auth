<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Validator;

class ProductController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest();

        return response()->json([
            'status' => 'success',
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand' => 'required|string',
            'description' => 'required',
        ]);

        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::create(array_merge(
            $validator->validated(),
            ['sku' => $this->generateCode()]
        ));

        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($sku_code)
    {
        $product = product::where('sku', $sku_code)->first();
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'product' => $product,
        ]);
    }

    protected function generateCode()
    {
        $data = Product::selectRaw('max(sku) as sku_code')->first();
        
        $countMax = (int) substr($data->sku_code, 3, 3);
        $countMax++;

        $uniqueCode = "AGR";
        $generate = $uniqueCode . sprintf("%03s", $countMax);

        return $generate;
    }
}
