<template>
	
</template>
	
<script>
	import Vue from 'vue'
	import axios from 'axios'
	import VueRouter from 'vue-router'

	Vue.use(VueRouter)
	Vue.prototype.$http = axios

	export default {
		props : ['business_id'],
		data(){
			return {
				prod_list : {}

			}
		},
		created(){

		},
		methods: {
			fetchProducts(){
				var url = `${this.source}`
				
			}
		}
	}
</script>