<template>
	<div>
		<v-layout row>
			<v-flex xs12>
			  <v-card>
			  	<v-card-media class="text-xs-center">
			  		<img :src="client.thumb" alt="" height="200px">
			  	</v-card-media>
			  </v-card>
			  <v-card-text>
			    <h3>{{ client.nombre_completo }}</h3>
			    <h4>{{ client.calle }} #{{ client.numero }}, Col. {{ client.col }}, C.P. {{ client.cod_postal }}, {{ client.ciudad }}</h4>
			    <p><b>Telefono(s) : </b> {{ client.telefono }} / {{ client.telefono2 }}</p>
			    <div class="row">
			    	<div class="col-md-6">
			    		<p><b>Situacion : </b> {{ client.situacion }}</p>
			    	</div>
			    	<div class="col-md-6">
			    		<p><b>Usuario : </b>{{ client.usuario }}</p>
			    	</div>
			    </div>
			  </v-card-text>
			</v-flex>
		</v-layout>
		
		<p><b>Comentarios : </b>{{ client.comentarios }}</p>
		<div class="row">
			<div class="col-md-6">
				<button class="btn btn-primary">Ver ventas</button>
			</div>
			<div class="col-md-6">
				<button class="btn btn-default">Editar</button>
			</div>
		</div>

		<avatar-full :avatar="client.avatar"
					 :clicked="showAvatarFull"
					 @close="showAvatarFull=false"></avatar-full>	
	</div>
	
</template>

<script>
	import Vue from 'vue'
	import axios from 'axios'
	import VueRouter from 'vue-router'
	import AvatarFull from './AvatarFull.vue'

	Vue.use(VueRouter)
	// Vue.prototype.$http = axios

	module.exports = {
		name: 'client-profile',
		props: ['type', 'client', 'show'],
		data() {
			return {
				showAvatarFull : false,
				avatar: '../../storage/noAvatar.svg'
			}
		},
		components: {
			'avatar-full' : AvatarFull
		},
		watch: {
			show: function(){
				if(this.show){
					this.avatar = this.client.avatar	
				}
			}
		}
	}
</script>