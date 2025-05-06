<?php

namespace App\Http\Controllers\Api;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ReflectionClass;

class BaseController
{
    /**
     * Get base object name
     */
    public function GetBaseName()
    {
        $base_name = (new ReflectionClass($this))->getShortName();
        $base_name = Str::replace("Controller", "", $base_name);
        return $base_name;
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function model(): Builder
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $model = new $class_name();
        $model = $model->where('status', 1);
        return $model;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model = $this->model();
        return response()->json($model->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(403);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $model = $this->model();
        return response()->json($model->where('id', $id)->first());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(403);
    }
}