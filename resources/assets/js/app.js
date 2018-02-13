
import Vue from 'vue'
import DataViewer from './components/DataViewer.vue'
import Modal from './components/Modal.vue'
import Movements from './components/Movements.vue'
import SaleProfile from './components/sales/SaleProfile.vue'
import ClientProfile from './components/clients/Profile.vue'
import Vuetify from 'vuetify'

Vue.use(Vuetify)

var app = new Vue({
	el: "#app",
	components: { 
		DataViewer,
		Modal,
		Movements,
		SaleProfile,
		ClientProfile
	}
});
