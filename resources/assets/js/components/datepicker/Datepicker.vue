<template>
  <transition name="modal">
      <div class="modal-mask" v-if="show" @keyup.esc="$emit('close')">
        <div class="modal-wrapper">
          <div class="modal-container">

            <div class="modal-header">
              <slot name="header">
                Actualizar fecha de movimiento(s)
              </slot>
            </div>

            <div class="modal-body">
              <slot name="body">
                <div class="form-group">
                    <label for="date">Fecha : </label>
                    <input type="date" name="date" id="" class="form-control" v-model="date">
                </div>

                <div class="form-group">
                  <label for="hours">Hora : </label>
                  <div class="row">
                    <div class="col-md-4">
                      <input id="hours" type="number" step="01" value ="01" name="hours" class="form-control" v-model="hours"/>
                    </div>
                    <div class="col-md-1">
                      <p><b>:</b></p>
                    </div>
                    <div class="col-md-4">
                      <input id="mins" type="number" step="05" value ="00" name="mins" class="form-control" v-model="mins"/>
                    </div>
                    <div class="col-md-3">
                      <p><b>{{ day_time }}</b></p>
                    </div>
                  </div>
                </div>
                
                <button class="btn btn-block" @click="updateDates()">ACTUALIZAR FECHA</button>
              </slot>
            </div>

            <div class="modal-footer">
              <slot name="footer">
                <button class="modal-default-button" @click="$emit('close')">
                  Cerrar
                </button>
              </slot>
            </div>
          </div>
        </div>
      </div>
  </transition>
</template>

<script>
  import Vue from 'vue'
  import moment from 'moment'
  import axios from 'axios'

  export default {
    props : ['valueDate', 'formatDate', 'show', 'movs'],
    data(){
      return {
        source: '/api/movimientos',
        date: this.valueDate,
        hours: '01',
        mins: '00',
        day_time: 'a.m.',
        date_time: ''
      }
    },
    watch:{
      hours: 'checkHours',
      mins: 'checkMins'
    },
    methods: {
      updateDates(){
        
        var vm = this
        if(vm.day_time == 'p.m.'){
          vm.hours = eval(vm.hours) + 12 + '' 
        }
        vm.date_time = vm.date + " " + vm.hours + ':' + vm.mins
        
        var url = `${vm.source}/fecha`
        var body = {
          movs_list : vm.movs,
          fecha : vm.date_time
        }
        
        axios.post(url, body)
          .then(function(response){
            vm.$emit('reset')
            vm.$emit('close')
            vm.$emit('dateUpdated')
          })
          .catch(function(response){
            console.log(response)
          })
        
      },
      checkMins(){
        if(eval(this.mins) > 60 || this.mins.length == 0){
          this.mins = '00'
        }
        else if(eval(this.mins) < 0){
          this.mins = '60'
        }
      },
      checkHours(){
        
        if(this.hours.length == 0){
          this.hours = '01'
          return;
        }
        if(eval(this.hours) > 11 && eval(this.hours) < 24)
        {
          this.hours_24 = this.day_time == 'a.m.' ? eval(this.hours) + '' : eval(this.hours) + 12 +'' 
          this.hours = eval(this.hours) - 12 + ''
          this.day_time = this.day_time == 'p.m.' ? 'a.m.' : 'p.m.'
        }
        else if(eval(this.hours) > 24){
          this.hours = '01'
          this.hours_24 = this.hours + ''
          this.day_time = this.day_time == 'p.m.' ? 'a.m.' : 'p.m.'
        }
        else if(eval(this.hours) < 0){
          this.hours = '11'
          this.hours_24 = this.day_time == 'a.m.' ? eval(this.hours) + 12 + '' : this.hours + ''
          this.day_time = this.day_time == 'p.m.' ? 'a.m.' : 'p.m.'
        }
      }
    },
    computed: {
      date_formatted: function(){
        return moment(this.date).format('YYYY-DD-MM')
      }
    }
  }
</script>