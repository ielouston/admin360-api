<template>
	<div>
		<div class="row">
			<div class="col-md-6">
				<button class="btn btn-primary btn-block">Ver Movimientos</button>
			</div>
			<div class="col-md-6">
				<button class="btn btn-secondary btn-block">Editar</button>
			</div>
		</div>
		<div class="form-group">
			<v-card>
				<v-card-media>
					<img :src="productProfile.avatar" alt="Foto del producto" height="300px">
				</v-card-media>
			</v-card>
			<h4><b>{{productProfile.clave}} : {{productProfile.claves_aux}}</b></h4>
			<h3>{{productProfile.nombre}}</h3>
			
			<p><b>Descripcion : </b> {{productProfile.descripcion}}</p>
			<div class="row">
				<div class="col-md-6">
					<p><b>Precio compra : </b> {{productProfile.precio_compra}}</p>
				</div>
				<div class="col-md-6">
					<p><b>Precio contado : </b> {{productProfile.precio_contado}}</p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-6">
					<p><b>Precio oferta : </b> {{productProfile.precio_oferta}}</p>
				</div>
				<div class="col-md-6">
					<p><b>Precio mayoreo : </b> {{productProfile.precio_mayoreo}}</p>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-4">
					<p><b>Iva : </b> {{productProfile.iva}}</p>
				</div>
			</div>
						              			
			<p><b>Linea : </b>{{productProfile.linea}}</p>
			
			<div class="row">
				<div class="col-md-6">
					<p><b>Comprados : </b> {{productProfile.comprados}}</p>
				</div>

				<div class="col-md-6">
					<p><b>Vendidos : </b> {{productProfile.vendidos}}</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<p><b>Situación : </b> {{productProfile.situacion}}</p>
				</div>

				<div class="col-md-6">
					<p><b>Usuario : </b>{{productProfile.usuario}}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		props: ['productProfile']
	}
</script>