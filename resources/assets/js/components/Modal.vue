<template id="modal-template">
	<div id="client-details" class="modal fade" ref="details">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<div class="modal-title">
							<h3 class="modal-title">Detalles de <span class="client-name">{{profile.nombre_completo}}</span></h3>

							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>	
					</div>

					<div class="modal-content">
						<div class="form-group">
							<label for="name">Nombre</label>
							<span class="form-label">{{profile.nombre_completo}}</span>
						</div>

						<div class="form-group">
							<label for="name">Dirección</label>
							<span class="form-label">
								{{profile.calle}} # {{profile.numero}}, Col. {{profile.colonia}} C.P. {{profile.cod_postal}} 
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
</template>
<script>

import Vue from 'vue'

	export default{
		props : ['show', 'client'],
		created(){
			this.loadProfile()
		},
		data(){
			return {
				profile: {
					nombre_completo: 'Alan sosa',
					calle: 'Plan de Guadalupe',
					numero: '6',
					colonia: 'Lazaro Cardenas',
					cod_postal: 60630
				}
			}
		},
		methods : {
			close : function(){
				this.show = false
			},
			savePost : function(){
				this.close()
			},
			loadProfile : function(){
									
			}
		},

	}
</script>