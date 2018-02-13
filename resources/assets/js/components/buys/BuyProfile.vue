<template>
	<div>
		<div class="row">
			<div class="col-md-6">
				<button class="btn btn-primary btn-block">Ver Proveedor</button>
			</div>
			<div class="col-md-6">
				<button class="btn btn-danger btn-block">Cancelar</button>
			</div>
		</div>

		<div class="form-group">
			<h3><b>{{buyProfile.clave}}</b></h3>
			<h4><b>{{buyProfile.nota}} : {{buyProfile.tipo_compra}}</b></h4>
			<p><b>Total : </b> {{buyProfile.total}}</p>
			<p><b>Usuario : </b> {{buyProfile.usuario}}</p>
			<p><b>Situación : </b> {{buyProfile.situacion}}</p>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['buyProfile']
	}
</script>