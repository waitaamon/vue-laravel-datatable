<?php

namespace App\Http\Controllers\DataTable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

abstract class DataTableController extends Controller
{
    protected $builder;

    abstract public function builder();

    public function __construct()
    {
        $builder = $this->builder();

        if(!$builder instanceof Builder) {
            throw new Exception('Entity builder not an instance of builder');
        }
        $this->builder = $builder;
    }

    public function index()
    {
        return response()->json([
            'data' => [
                'displayable' => $this->getDisplayableColumns(),
                'records' => $this->getRecords()
            ]
        ]);
    }

    public function getDisplayableColumns()
    {
        return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
    }

    protected function getDatabaseColumnNames()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    protected function getRecords()
    {
        return $this->builder->get();
    }
}
