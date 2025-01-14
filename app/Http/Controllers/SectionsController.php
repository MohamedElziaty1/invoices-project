<?php

namespace App\Http\Controllers;

use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections=sections::all();
        return view('sections.sections',compact('sections'));
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
            'section_name'=>'required|string|unique:sections,section_name',
            'description'=>'required',
            ],[

                'section_name.required'=>'يرجي ادخال اسم القسم',
            'section_name.unique'=>'اسم القسم مسجل مسبقا',
            'description.required'=>'يرجي ادخال البيان',

        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        sections::create([
            'section_name'=>$request->input('section_name'),
            'description'=>$request->input('description'),
            'Created_by'=>(Auth::user()->name),
        ]);
        return redirect()->route('sections.index')->with('success','تم اضافة القسم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
$id=$request->id;
        $validator=Validator::make($request->all(),[
            'section_name'=>'required|string|unique:sections,section_name,'.$id,
            'description'=>'required',
        ],[

            'section_name.required'=>'يرجي ادخال اسم القسم',
            'section_name.unique'=>'اسم القسم مسجل مسبقا',
            'description.required'=>'يرجي ادخال البيان',
        ]);
if($validator->fails()){

    return redirect()->back()->withErrors($validator)->withInput();
}

        $section=sections::findOrFail($id);

        $section->update([
            'section_name'=>$request->input('section_name'),
            'description'=>$request->input('description'),
        ]);
return redirect()->route('sections.index')->with('success','تم تعديل نجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id=$request->id;
        $section=sections::findorFail($id);
        $section->delete();

return redirect()->back()->with('warning','تم الحذف بنجاح');

    }
}
