<?php 
namespace Muebleria\Helper;
use Validator;
use Muebleria\Scopes\SearchPaginateAndOrder;

trait DataViewer{
	
	public static function bootDataViewer(){
		static::addGlobalScope(new SearchPaginateAndOrder);
	}
}
 ?>	
