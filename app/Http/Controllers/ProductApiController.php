<?php

namespace Muebleria\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Muebleria\Product;
use Muebleria\Stock;
use Muebleria\Queue;
use Muebleria\Http\Repositories\ProductRepository;

class ProductApiController extends Controller
{
    public function home(){
        $repo = new ProductRepository();
        $products_offer = $repo->getOffers();
        $products_new = $repo->getNew();
        $products_top_sell = $repo->getTopSell();
        $products = [
            'offers' => $products_offer,
            'new' => $products_new,
            'top_sell' => $products_top_sell
        ];
        return response()->json($products, 200);
    }
    public function store(Request $request){

        $cUser = new UserController;
        $user = $cUser->get();
         
    	$validator = Validator::make($request->all(), [
            'clave' => 'string|required',
            'claves_aux' => 'string|required',
    		'nombre' => 'string|required',
    		'precio_compra' => 'integer|required',
    		'stock' => 'integer|required',
            'business_id' => 'integer|required',
            'situacion' => 'string|required',
            'proveedor_id' => 'integer|required'
    	]);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}

        if($this->exists($request->get('clave')) > 0){
            return response()->json(0, 409);
        }
        
    	$prod = new Product;
    	$prod->clave = $request->get('clave');
    	$prod->claves_aux = $request->get('claves_aux');
    	$prod->nombre = $request->get('nombre');
    	$prod->descripcion = $request->get('descripcion');
    	$prod->precio_compra = $request->get('precio_compra');
    	$prod->precio_contado = $request->get('precio_contado');
    	$prod->precio_oferta = $request->get('precio_oferta');
    	$prod->precio_mayoreo = $request->get('precio_mayoreo');
    	$prod->iva = $request->get('iva');
    	$prod->linea = $request->get('linea');
        $prod->avatar = $request->get('avatar');
        $prod->thumb = $request->get('thumb');

    	if($prod->save()){
            $stock = new Stock;
            $stock->nombre = $request->get('nombre');
            $stock->descripcion = $request->get('descripcion');
            $stock->comprados = $request->get('comprados');
            $stock->vendidos = $request->get('vendidos');
            $stock->stock = $request->get('stock');
            $stock->product_id = $prod->id;
            $stock->business_id = $request->get('business_id');
            $stock->situacion = $request->get('situacion');
            $stock->proveedor_id = $request->get('proveedor_id');

            if($stock->save()){
                return response()->json($stock->id, 200);    
            }
            return response()->json(0, 500);
    	}
    	return response()->json(0, 500);
    }
    public function update(Request $request, $id){
        
        $cUser = new UserController;
        $user = $cUser->get();

    	$validator = Validator::make($request->all(), [
            'clave' => 'string|required',
            'claves_aux' => 'string|required',
    		'nombre' => 'string|required',
    		'precio_compra' => 'integer|required',
            'situacion' => 'string|required'
    	]);
        
        $stock = Stock::find($id);

    	if($validator->fails()){
    		return response()->json(0, 400);
    	}
        
        $stock->descripcion = $request->get('descripcion');
        $stock->stock = $request->get('stock');
        $stock->situacion = $request->get('situacion');
        $stock->proveedor_id = $request->get('proveedor_id');
    	$prod = Product::find($stock->product_id);

    	if($prod->clave != $request->get('clave')){

            $prod->clave = $request->get('clave');
            $prod->claves_aux = $request->get('claves_aux');    
        }
        
    	$prod->nombre = $request->get('nombre');
    	$prod->descripcion = $request->get('descripcion');
    	$prod->precio_compra = $request->get('precio_compra');
        $prod->precio_contado = $request->get('precio_contado');
        $prod->precio_oferta = $request->get('precio_oferta');
        $prod->precio_mayoreo = $request->get('precio_mayoreo');
    	$prod->iva = $request->get('iva');
    	$prod->linea = $request->get('linea');
        $prod->avatar = $request->get('avatar');
        $prod->thumb = $request->get('thumb');
        $prod->oferta = $request->get('oferta');
        $prod->descuento = $request->get('descuento');
        
        if($this->changesInProduct($stock, $request->all())){
            $this->reflectInOtherBusiness($prod, "update", $request->get('device'));
        }
    	if($prod->update() && $stock->update()){
    		return response()->json($stock->id, 200);
    	}
    	return response()->json(0, 500);	
    }
    public function exists($prod_key){
        $products = Product::where('claves_aux', 'LIKE', $prod_key .'%')
                            ->orWhere('claves_aux', 'LIKE','%,'. $prod_key .'%')
                            ->get(['id']);
        
        if($products->isEmpty()){
            return 0;
        }
        return $products[0]['id'];
    }
    private function changesInProduct(Stock $stock, $prod_a){
        $prod = Product::find($stock->product_id);

        if($prod->clave != $prod_a['clave']){
            return true;
        }
        else if($prod->precio_compra != $prod_a['precio_compra']){
            return true;
        }
        else if($prod->precio_contado != $prod_a['precio_contado']){
            return true;
        }
        else if($prod->precio_oferta != $prod_a['precio_oferta']){
            return true;
        }
        else if($prod->precio_mayoreo != $prod_a['precio_mayoreo']){
            return true;
        }
        else if($prod->iva != $prod_a['iva']){
            return true;
        }
        return false;
    }
    public function reflectInOtherBusiness(Product $prod, $action, $device){
        $queue = new Queue;
        $queue->action = $action;
        $queue->model = "product";
        $queue->data = $prod;
        $queue->status = "Pendiente";
        $queue->devices = $device;
        $queue->business_id = 0;

        if(!$queue->save()){
            return response()->json($queue, 500);
        }
    }
    public function find($id, $business_id){
        $repo = new ProductRepository();
        $product = $repo->getProfileWithStocks($id, $business_id);
    	return response()->json($product, 200);
    }
    /*
    	return Product with stock by prod_key and business_id
     */
    public function getBy($prod_key){
        $prod = Product::where('claves_aux', 'LIKE', $prod_key .'%')
                        ->orWhere('claves_aux', 'LIKE', '%,'. $prod_key .'%')
                        ->first();
        
        if(is_null($prod)){
            return false;
        }
        return $prod;
    }
    /**
     * Get products with stocks from the business_id gave it
     * @param int $id_business
     * @param Array $options
     * @return Collection<Product<Stocks>> $products
     */
    public function get($id_business){
        $repo = new ProductRepository;
        $products = $repo->getWithStocks($id_business);
        return response()->json($products, 200);
    }
    public function getTable(Request $req){
        $repo = new ProductRepository;
        $id_business = $req->get('business_id');
        $products = $repo->getWithStocks($id_business);

        return response()->json([
            'columnas_cond' => Product::$columnas_cond,
            'columnas_tabla' => Product::$columnas_tabla,
            'data' => $products,
            'tipos' => Product::$tipos
            ], 200);
    }
    public function getByCategory($category, $id_business){
        $repo = new ProductRepository();
        $products = $repo->getWithStocks($id_business, $category);

        return response()->json($products, 200);
    }
    public function import(Request $request){
        $products = json_decode($request->get('productos'), true);
        $model = new Product;
        $response = array();

        foreach ($products as $product) {
            $client_id = $product['ID'];
            
            $validator = Validator::make($product, [
                'clave' => 'string|required',
                'claves_aux' => 'string|required',
                'nombre' => 'string|required',
                'precio_compra' => 'integer|required',
                'stock' => 'integer|required',
                'business_id' => 'integer|required',
                'situacion' => 'string|required',
                'proveedor_id' => 'integer|required'
            ]);

            if($validator->fails()){
                $model->id = 0;
            }
		    $model->id =  $this->exists($product['clave']);
            if($model->id > 0){
                $stock = new Stock;
                $stock->nombre = $product['nombre'];
                $stock->descripcion = $product['descripcion'];
                $stock->existencia = $product['existencia'];
                $stock->proveedor_id = $product['proveedor_id'];
                $stock->comprados = $product['comprados'];
                $stock->vendidos = $product['vendidos'];
                $stock->product_id = $model->id;
                $stock->business_id = $product['business_id'];
                $stock->stock = $product['stock'];
                $stock->situacion = $product['situacion'];
                $stock->save();
            }
            else{
                $model = Product::create($product);    
                $stock = new Stock;
                $stock->nombre = $model->nombre;
                $stock->descripcion = $model->descripcion;
                $stock->existencia = $product['existencia'];
                $stock->proveedor_id = $product['proveedor_id'];
                $stock->comprados = $product['comprados'];
                $stock->vendidos = $product['vendidos'];
                $stock->product_id = $model->id;
                $stock->business_id = $product['business_id'];
                $stock->stock = $product['stock'];
                $stock->situacion = $product['situacion'];
                $stock->save();
            }
            $response[$client_id] = $model->id <= 0 ? $model->id : $stock->id;
        }
        return response()->json($response, 200);
    }
    public function getCategories(){
        $categories = Product::distinct()->select('linea')->get();
        return response()->json($categories, 200);
    }
    public function searchInModel(Request $req){
        $query = $req->get('query');
        $products = Product::where('nombre', 'like', '%' . $query . '%')
                    ->limit(5)
                    ->get(['id','nombre']);

        return response()->json($products, 200);
    }
}
