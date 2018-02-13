<template @keyup.esc="$emit('close')">
	<transition name="modal" v-if="clicked">
		<div class="modal-mask">
			<div class="modal-wrapper">
				<div class="modal-container responsive responsive-width responsive-height">
					<div class="modal-header">
						<slot name="header">
							Vista completa
							<button class="modal-default-button" @click="$emit('close')">
							  Cerrar
							</button>
						</slot>
					</div>

					<div class="modal-body">
						<slot name="body">
							<div class="avatar avatar-full">
								<img :src="avatar" alt="Foto de perfil a tamaño real." class="">
							</div>
						</slot>
					</div>
				</div>
			</div>
		</div>
	</transition>
</template>

<script>
	import Vue from 'vue'
	import axios from 'axios'
	import VueRouter from 'vue-router'

	Vue.use(VueRouter)
	// Vue.prototype.$http = axios

	module.exports = {
		name: 'avatar-full',
		props: ['avatar', 'clicked'],
		data() {
			return {

			}
		},
		methods: {

		}

		
	}
</script>
