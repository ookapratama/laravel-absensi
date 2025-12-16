<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseCrudController extends Controller
{
    protected $service;


    public function index()
    {
        return response()->json($this->service->all());
    }


    public function store(Request $request)
    {
        return response()->json($this->service->store($request->all()));
    }


    public function show($id)
    {
        return response()->json($this->service->find($id));
    }


    public function update(Request $request, $id)
    {
        return response()->json($this->service->update($id, $request->all()));
    }


    public function destroy($id)
    {
        return response()->json($this->service->delete($id));
    }
}
