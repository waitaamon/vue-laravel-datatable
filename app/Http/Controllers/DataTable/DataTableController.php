<?php

namespace App\Http\Controllers\DataTable;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    public function index(Request $request)
    {
        return response()->json([
            'data' => [
                'table' => $this->builder->getModel()->getTable(),
                'displayable' => array_values($this->getDisplayableColumns()),
                'updatable' => array_values($this->getUpdatableColumns() ),
                'records' => $this->getRecords($request)
            ]
        ]);
    }

    public function getDisplayableColumns()
    {
        return array_diff($this->getDatabaseColumnNames(), $this->builder->getModel()->getHidden());
    }

    public function getUpdatableColumns () {

        return $this->getDisplayableColumns();
    }

    protected function getDatabaseColumnNames()
    {
        return Schema::getColumnListing($this->builder->getModel()->getTable());
    }

    protected function getRecords(Request $request)
    {
        $builder = $this->builder;

        if($this->hasSearchQuery($request)) {
            $builder = $this->buildSearch($builder, $request);
        }

        try {
            return $builder->limit($request->limit)->orderBy('id', 'asc')->get($this->getDisplayableColumns());

        } catch (QueryException $e) {

            return [];
        }
    }

    protected function  hasSearchQuery(Request $request)
    {
        return count(array_filter($request->only(['column', 'operator', 'value']),'strlen')) === 3;
    }

    protected function buildSearch(Builder $builder, Request $request)
    {
        $queryParts = $this->resolveQueryParts($request->operator, $request->value);

        return $builder->where($request->column, $queryParts['operator'], $queryParts['value']);
    }

    protected function resolveQueryParts($operator, $value) {
        return Arr::get([
            'equals' => [
                'operator' => '=',
                'value' => $value
            ],
            'contains' => [
                'operator' => 'LIKE',
                'value' => "%{$value}%"
            ],
            'starts_with' => [
                'operator' => 'LIKE',
                'value' => "{$value}%"
            ],
            'ends_with' => [
                'operator' => 'LIKE',
                'value' => "%{$value}"
            ],
            'greater_than' => [
                'operator' => '>',
                'value' => $value
            ],
            'less_than' => [
                'operator' => '<',
                'value' => $value
            ],
        ], $operator);
    }
}
