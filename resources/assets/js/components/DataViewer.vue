<template>
	<div class="dv container">
		<div class="dv-header">
			<div class="dv-header-title">
				{{title}}
			</div>
			
			<div class="dv-header-filters">
				<div class="dv-header-filters-types">
					<select name="" id="" class="dv-header-select" @change="fetchClientData" v-model="query.type">
						<option v-for="(value, key) in types" :value="key" >{{value}}</option>
					</select>
				</div>
				<div class="dv-header-filters-columns">
					<select class="dv-header-select" v-model="query.search_column">
						<option v-for="column in columns_cond" :value="column">{{column}}</option>
					</select>

					<select class="dv-header-operators" v-model="query.search_operator">
						<option v-for="(value, key) in operators" :value="key">{{value}}</option>
					</select>
				</div>
			</div>
			
			<div class="dv-header-search">
				<input type="text" class="dv-header-search-input" v-model="query.search_input" @keyup.enter="fetchClientData()">
				<button class="dv-header-btn" @click="fetchClientData()">Buscar</button>
			</div>
		</div>
		
		<div class="dv-body">
			<table class="dv-body-table table table-collapse">
				<thead>
					<tr>
						<th v-for="column in columns" @click="toggleOrder(column)">
							<div class="block">
								<div class="span1">
									{{column}}
									<div class="span1 pull-right" v-if="column == query.column">
										<span v-show="query.direction == 'asc'">&uarr;</span>
										<span v-show="query.direction == 'desc'">&darr;</span>	
									</div>	
								</div>
								
							</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="row in model" @click="showDetails(row.id)" class="link">
						<td v-for="(value, key) in row" v-if="inColumns(key)">
							{{isNull(value)}}
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="dv-footer">
			

			<div class="dv-footer-item">
				<div class="dv-footer-sub">
					<input type="text" class="dv-footer-sub-input" v-model="query.per_page" @keyup.enter="fetchClientData()"><span> por página</span>	
				</div>

				<div class="dv-footer-sub">
					<span> | Página : </span>
					
					<input type="text" class="dv-footer-sub-input" v-model="query.page" @keyup.enter="fetchClientData()">	
				</div>
			</div>
		</div>
		<!-- modal vue js -->
			<transition name="modal">
			    <div class="modal-mask" v-if="showModal">
			      <div class="modal-wrapper">
			        <div class="modal-container">

			          <div class="modal-header">
			            <slot name="header">
			              Detalles del movimiento
			              <v-spacer></v-spacer>
			              <button class="modal-default-button" @click="shutdown">
			                Cerrar
			              </button>
			            </slot>
			          </div>

			          <div class="modal-body">
			            <slot name="body">
			              <div class="mov-refs">
			              	<section class="mov-ref-sale" v-if="showSaleProfile">
			              		<sale-profile :saleProfile="query.sale"></sale-profile>
			              	</section>

			              	<section class="mov-ref-sale" v-else-if="showClientProfile">
			              		<client-profile :client="query.client" :show="showModal"></client-profile>
			              	</section>

			              	<section class="mov-ref-prod" v-else-if="showProductProfile">
			              		<product-profile :productProfile="query.product"></product-profile>
			              	</section>

			              	<section class="mov-ref-buy" v-else-if="showBuyProfile">
			              		<buy-profile :buyProfile="query.buy"></buy-profile>
			              	</section>

			              	<section class="mov-ref-pay">
			              		
			              	</section>
			              </div>
			            </slot>
			          </div>
			        </div>
			      </div>
			    </div>
			</transition>
	</div>
</template>

<script>
	import Vue from 'vue'
	import axios from 'axios'
	import VueRouter from 'vue-router'
	import ClientProfile from './clients/Profile.vue'
	import ProductProfile from './products/profile.vue'
	import SaleProfile from './sales/SaleProfile.vue'
	import BuyProfile from './buys/BuyProfile.vue'

	Vue.use(VueRouter);
	Vue.prototype.$http = axios;

	module.exports = {
		props: ['source', 'title', 'type', 'col', 'token', 'business_id'],
		data(){
			return {
				model: {},
				columns: {},
				columns_cond:{},
				query: {
					page: 1,
					column: this.col,
					direction: 'desc',
					per_page: 50,
					search_column: 'updated_at',
					search_operator: 'less_than_or_equal_to',
					search_input: '',
					type: this.type,
					token: this.token,
					business_id: this.business_id,
					details_source: '',
					client: { },
					product: { },
					sale: {	},
					buy: { }
				},
				showModal : false,
				showProductProfile: false,
				showSaleProfile: false,
				showBuyProfile: false,
				showClientProfile: false,
				column: 'nombre_completo',
				operators: {
					equal: '=',
					not_equal: '<>',
					less_than: '<',
					greater_than: '>',
					less_than_or_equal_to: '<=',
					greater_than_or_equal_to: '>=',
					in: 'EN',
					like: 'COMO'
				},
				types:{
					Activos:'Activos',
					ConCredito:'Credito Activo',
					SinCredito:'Credito Inactivo',
					Inactivos:'Inactivos'
				},
				filters:{}
			}
		}
		,
		created(){
			this.setDate(),
			this.setToken(),
			this.fetchClientData()
		},
		components:{
			'client-profile' : ClientProfile,
			'product-profile': ProductProfile,
			'sale-profile': SaleProfile,
			'buy-profile': BuyProfile
		},
		methods: {
			shutdown(){
				this.showSaleProfile = false
				this.showProductProfile = false
				this.showBuyProfile = false
				this.showModal = false
			},
			loadProfile(data, type){
				var vm = this
				switch(type){
					case "productos":
						vm.query.product = data[0]
						vm.showProductProfile = true
						break;
					case "clientes":
						vm.query.client = data
						vm.showClientProfile = true
						break;
					case "ventas":
						vm.query.sale = data
						vm.showSaleProfile = true
						break;
					case "compras":
						vm.query.buy = data
						vm.showSaleProfile = true
						break;
				}
				vm.showModal = true
			},
			showDetails(id){
				var vm = this
				let uri_splitted = this.source.split('/api/')
				let uri_type = uri_splitted[1].split('/')

				switch(uri_type[0]){
					case "productos":
						vm.query.details_source = '/api/productos/' + id + '/' + vm.query.business_id
						break;
					case "clientes":
						vm.query.details_source = "/api/clientes/" + id
						break;
					case "ventas":
						vm.query.details_source = '/api/ventas/' + id
						break;
				}
				
				var url =  `${this.query.details_source}?no_params=true` 
				
				var body = {
					'no_params': true,
					'id': id
				}

				axios.get(url, body)
					.then(function(response){
						vm.loadProfile(response.data, uri_type[0]);
					})
					.catch(function(response){
						console.log(response)
					})
			},
			next(){
				if(this.model.next_page_url){
					this.query.page++
					this.fetchClientData()
				}
			},
			prev(){
				if(this.model.prev_page_url){
					this.query.page--
					this.fetchClientData()
				}
			},
			setToken(){
				axios.defaults.headers.common = {
					'Accept': 'application/json',
					'Authorization' : 'Bearer ' +this.token
				};
			},
			fetchClientData(){
				var url = `${this.source}/tabla?column=${this.query.column}&direction=${this.query.direction}&page=${this.query.page}&per_page=${this.query.per_page}&search_column=${this.query.search_column}&search_operator=${this.query.search_operator}&search_input=${this.query.search_input}&type=${this.query.type}&business_id=${this.business_id}`

				var vm = this
				
				axios.get(url)
					.then(function(response){
						
						Vue.set(vm.$data, 'model', response.data.data)
						Vue.set(vm.$data, 'columns', response.data.columnas_tabla)
						Vue.set(vm.$data, 'columns_cond', response.data.columnas_cond)
						Vue.set(vm.$data, 'filters', response.data.filtros)
						Vue.set(vm.$data, 'types', response.data.tipos)

					})
					.catch(function(response){
						console.log(response)
					})
			},
			toggleOrder(col){
				if(col === this.query.column){
					this.query.direction = this.query.direction === 'asc' ? 'desc' : 'asc'
				}
				else{
					this.query.column = col
					this.query.direction = 'desc'
				}
				this.fetchClientData()
			},
			inColumns(val){
				return this.columns.indexOf(val) > -1 ? true : false
			},
			isNull(val){
				return val == null || val == '' || val == ' ' ? 'N/A' : val 
			},
			setDate(){
				var moment = require('moment');
				var date = moment().format();
				
				this.query.search_input = date;
			},
			updateSource(){
				var source = this.source
				var index = source.indexOf('/');
				
				var new_source = source.substring(0, index);
				
				//this.source = new_source + this.query.type;
			},

			updateFilters(filter){
				var filter = explode(',', filter);
				this.search_filters.push({
					column: filter[0],
					operator: filter[1],
					input: filter[2],
				});
			},
			hideClient(){
				this.showClientProfile = false
			}
		},
		computed: {
		    colKey:{
		    	get: function(){
		    		return this.col
		    	},
		    	set: function (val) {
		      		this.col = val		
		    	}
		    },

		    	
		}
	}
</script>