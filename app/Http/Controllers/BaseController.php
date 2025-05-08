<?php

namespace App\Http\Controllers;

use App\Models\Base;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
     * Model data.
     *
     * @return array
     */
    public function model(): Base
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $model = new $class_name();
        return $model;
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): Builder
    {
        $base_name = $this->GetBaseName();
        $class_name = "\App\Models\\" . $base_name;
        $model = new $class_name();
        $model = $model->where('status', 1);
        $model = $model->orderBy('id', 'desc');
        return $model;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->expectsJson()) {
            $model = $this->query();
            return response()->json($model->paginate());
        } else {
            $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
            return Inertia::render($base_name . '/List');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (request()->has('recommendations')) {
            return $this->recommendations($id);
        }
        if (request()->expectsJson()) {
            $model = $this->query();
            $item = $model->where('id', $id)->first();
            if (isset($item)) {
                return response()->json($item);
            } else {
                abort(404);
            }
        } else {
            $base_name = Str::ucfirst(Str::plural($this->GetBaseName()));
            return Inertia::render($base_name . '/Show');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(403);
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

    /**
     * Recommendations
     */
    public function recommendations(string $id)
    {
        $model = $this->query();
        $item = $model->where('id', $id)->first();
        if (isset($item)) {
            $model = $this->query();
            $model = $model->where('id', '<>', $id);
            $keys = array_unique(array_merge($item->categories, $item->tags));
            $key = implode(" ", $keys);
            if (in_array(\App\Traits\HasFullTextSearch::class, class_uses($this->model()))) {
                $model = $model->search($key);
            }
            $recommendations = $model->take(3)->get();
            $count = count($recommendations);
            if ($count <= 3) {
                $ids = collect($recommendations)->map(fn($recommendation) => $recommendation->id . '')->toArray();
                array_push($ids, $id);
                $model = $this->query();
                $model = $model->whereNotIn('id', $ids);
                $extents = $model->take(3 - $count)->get();
                $recommendations = $recommendations->merge($extents);
            }
            return response()->json($recommendations);
        } else {
            abort(404);
        }
    }
}
