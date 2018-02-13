<template>
  <div>
    <div class="row">
    	<div class="col-md-6">
    		<button class="btn btn-primary btn-block">Ver Pagos</button>
    	</div>
    	<div class="col-md-6">
    		<button class="btn btn-secondary btn-block" @click="regenerateMovs">Regenerar movs.</button>
    	</div>
    </div>
    <div class="form-group">
    	<h4><b>{{saleProfile.nota}} : {{saleProfile.tipo_venta}}</b></h4>
    	<h3>{{saleProfile.cliente}}</h3>
    	<div class="row">
    		<div class="col-md-4">
    			<p><b>Subtotal : </b> {{saleProfile.subtotal}}</p>
    		</div>
    		<div class="col-md-4">
    			<p><b>Descuento : </b> {{saleProfile.descuento}}</p>
    		</div>
    		<div class="col-md-4">
    			<p><b>Total : </b> {{saleProfile.total}}</p>
    		</div>
    	</div>
    	
    	<div class="row">
    		<div class="col-md-4">
    			<p><b>Anticipo : </b> {{saleProfile.anticipo}}</p>
    		</div>

    		<div class="col-md-4">
    			<p><b>Pagado : </b> {{saleProfile.pagado}}</p>
    		</div>

    		<div class="col-md-4">
    			<p><b>Saldo : </b> {{saleProfile.saldo_actual}}</p>
    		</div>
    	</div>
    	
    	<div class="row">
    		<div class="col-md-6">
    			<p><b>Inversión : </b> {{saleProfile.inversion}}</p>
    		</div>

    		<div class="col-md-6">
    			<p><b>Ganancias : </b> {{saleProfile.pagado - saleProfile.inversion}}</p>
    		</div>
    	</div>
    </div>
  </div>  
</template>

<script>
	export default {
		props: ['saleProfile']
	}
</script>