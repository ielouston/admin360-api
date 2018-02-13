<?php

namespace Muebleria\Scopes;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Validator;

class SearchPaginateAndOrder implements Scope
{
    protected $operators = [
        'equal' => '=',
        'not_equal' => '<>',
        'less_than' => '<',
        'greater_than' => '>',
        'less_than_or_equal_to' => '<=',
        'greater_than_or_equal_to' => '>=',
        'in' => 'EN',
        'like' => 'COMO'
    ];
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {

        $query = $builder->getQuery();
        $request = app()->make('request');

        if($request->get('no_params') == 'true'){
    		return $query;
    	}

        $page = $request->get('page');
        $skip = ($page--) * $request->per_page;

        $v = Validator::make($request->only([
            'column' , 'direction', 'per_page', 'page', 'search_column', 'search_operator', 'search_input'
            ]),[
            'column' => 'required|alpha_dash|in:'.implode(',', $model::$columnas_tabla),
            'direction' => 'required|in:asc,desc',
            'per_page' => 'required|numeric|min:1',
            'page' => 'required|numeric|min:1',
            'search_column' => 'required|alpha_dash|in:'.implode(',', $model::$columnas_tabla),
            'search_operator' => 'required|alpha_dash|in:'.implode(',', array_keys($this->operators)),
            'search_input' => 'max:60',
            'search_filters' => 'max:255|string',
            'stocks' => 'max:6|string'
        ]);
        
        if($v->fails()){
            dd($v->messages());
        }
        
        return $query
            ->orderBy($request->column, $request->direction)
            ->where(function($query) use ($request, $model){

                if($request->has('search_input')){
                    if($request->search_operator == 'in'){
                        $query->whereIn($model::$table_name.'.'.$request->search_column, explode(',', $request->search_input));
                    }
                    else if($request->search_operator == 'like'){
                        $query->where($model::$table_name.'.'.$request->search_column, 'LIKE' ,'%'.$request->search_input.'%');
                    }
                    else{
                        $query->where($model::$table_name.'.'.$request->search_column, $this->operators[$request->search_operator], $request->search_input);
                    }
                }
            })
            ->paginate($request->per_page);
    }
}