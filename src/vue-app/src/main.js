import _ from 'lodash'
import Vue from 'vue'
import App from './App.vue'
import router from './router'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import VueParticles from 'vue-particles'
import VueHeadful from 'vue-headful'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome'
import { fab, faGoogle } from '@fortawesome/free-brands-svg-icons' //TODO: выяснить, почему не подключается сразу все (почему не работает с fab)
import { dom } from '@fortawesome/fontawesome-svg-core' //TODO import all icons
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead'
// import YmapPlugin from 'vue-yandex-maps'
// import ymaps_settings from './ymaps'

dom.watch()

library.add(fab, faGoogle)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)
Vue.component('font-awesome-layers-text', FontAwesomeLayersText)


Vue.use(BootstrapVue) // Install BootstrapVue
Vue.use(IconsPlugin) // Optionally install the BootstrapVue icon components plugin // TODO: возможно стоит очистить bootstrap-icons
Vue.use(VueParticles)
Vue.use(_)
// Vue.use(YmapPlugin);

Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead)
Vue.component('vue-headful', VueHeadful)

// Vue.mixin(ymaps_settings);


Vue.config.productionTip = false

Vue.directive('scroll', {
    inserted: function (el, binding) {
        let f = function (evt) {
            if (binding.value(evt, el)) {
                window.removeEventListener('scroll', f)
            }
        }
        window.addEventListener('scroll', f)
    }
})

new Vue({
    router,
    render: h => h(App)
}).$mount('#app')
