<?php

namespace Muebleria\Http\Repositories;

use Muebleria\Http\Controllers\Controller;
use Muebleria\Product;
use Muebleria\Stock;

class ProductRepository extends Controller
{
	public function getActives($id_bussiness){
		$products = Product::Active($id_bussiness)->get();
        return $products;
	}
    public function getInactives($id_bussiness, $type){
        $products = Product::Inactive($id_bussiness)->get();
        return $products;
    }
    public function getWithStocks($id_bussiness, $category = null){

        $prodsFields = ['products.*'];
        $request = app()->make('request');
        $stocks = $request->get('stocks');
        
        $products = Product::with(['stocks' => function ($query) use ($id_bussiness, $stocks){
                if($stocks == 'simple') {
                    $query->select('id', 'existencia', 'business_id', 'product_id');
                } else {
                    $query->select('*');
                }
                if ($id_bussiness > 0) {
                    $query->where('business_id', $id_bussiness);
                }
                // $query->where('situacion', 'Activo');
            }]);
        // if the category is specified
        if (!is_null($category)){
            $products = $products->where('linea', $category);
        }
        return $products->get();
    }
    public function getProfileWithStocks($id, $id_bussiness){
        $prodsFields = ['products.*'];
        $request = app()->make('request');
        $stocks = $request->get('stocks');
        if($request->has('from_stocks')){
            $stock = Stock::find($id);
            $id = $stock->product_id;
        }
        $products = Product::with(['stocks' => function ($query) use ($stocks, $id_bussiness){
                if($stocks == "simple") {
                    $query->select('id', 'existencia', 'business_id', 'product_id', 'vendidos');

                } else if ($stocks == "full"){
                    $query->select('*');
                }
                if ($id_bussiness > 0) {
                    $query->where('business_id', $id_bussiness);
                }
                $query->where('situacion', 'Activo');
                // return $query;
            }])
            ->select($prodsFields)
            ->where('id', $id);
        return $products->get();
    }
    public function getOffers(){
        $products = Product::InOffer()->limit(8)->get();
        return $products;
    }
    public function getNew(){
        $products = Product::orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
        return $products;
    }
    public function getTopSell(){
        $products = Product::join('stocks', 'stocks.product_id', '=', 'products.id')
                    ->selectRaw('products.*, SUM(stocks.vendidos) as total_venta')
                    ->groupBy('products.id')
                    ->orderBy('total_venta', 'DESC')
                    ->limit(10)
                    ->get();
        return $products;
    }
}
