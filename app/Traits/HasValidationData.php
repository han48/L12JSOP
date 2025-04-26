<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait HasValidationData
{
    public function getColumnNames()
    {
        return Schema::getColumnListing($this->getTable());
    }

    public function validationData()
    {
        $columns = $this->getColumnNames();
        foreach (array_keys($this->attributes) as $key) {
            Log::debug($key);
            if (!in_array($key, $columns)) {
                Log::debug("    Unset: " . $key);
                unset($this->attributes[$key]);
            }
        }
        return $this;
    }
}
