<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ValidateSignature;
use App\Models\products;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections=sections::all();
        $products=products::all();
        return view('products.products',compact('sections','products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
$validator=Validator::make($request->all(),[
    'Product_name'=>'required|string',
    'description'=>'required|string',
],[
    'Product_name.required'=>'يرجي ادخال اسم المنتج',
    'description.required'=>'يرجي ادخال البيانات'

]);
if($validator->fails()){
    return redirect()->back()->withErrors($validator)->withInput();
}

        products::create([
            'Product_name'=>$request->input('Product_name'),
            'description'=>$request->input('description'),
            'section_id'=>$request->input('section_id'),
        ]);
return redirect()->back()->with('success','تم اضافة المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(products $products)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, products $products)
    {
        $id=sections::where('section_name',$request->section_name)->first()->id;

        $validator=Validator::make($request->all(),[

            'Product_name'=>'required|string',
    'description'=>'required|string',
],[
    'Product_name.required'=>'يرجي ادخال اسم المنتج',
    'description.required'=>'يرجي ادخال البيانات'



        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $products=products::findOrFail($request->pro_id);
        $products->update([
'Product_name'=>$request->Product_name,
            'description'=>$request->description,
            'section_id'=>$id,

        ]);
return redirect()->back()->with('success','تم تعديل المنتج بنجاح');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $products=products::findOrFail($request->pro_id);
        $products->delete();
        return redirect()->back()->with('error','تم حذف المنتج بنجاح');
    }
}
