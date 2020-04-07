import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VueParticles from 'vue-particles'

import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome'
import { fab, faGoogle } from '@fortawesome/free-brands-svg-icons' //TODO: выяснить, почему не подключается сразу все (почему не работает с fab)
//TODO import all icons
import { dom } from '@fortawesome/fontawesome-svg-core'

dom.watch()

library.add(fab, faGoogle)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)
Vue.component('font-awesome-layers-text', FontAwesomeLayersText)

// Install BootstrapVue
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.use(VueParticles)



Vue.config.productionTip = false

new Vue({
    router,
    render: h => h(App)
}).$mount('#app')
