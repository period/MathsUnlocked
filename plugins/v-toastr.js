import Vue from 'vue'
import VueToastr from '@deveodk/vue-toastr'

Vue.use(VueToastr, {
  defaultPosition: 'toast-top-right',
  defaultType: 'info',
  defaultTimeout: 5000
})
