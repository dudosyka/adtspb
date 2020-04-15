import Vue from 'vue';
import _ from 'lodash';
import App from './App.vue';
import router from './router';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueParticles from 'vue-particles';
import VueHeadful from 'vue-headful';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon, FontAwesomeLayers, FontAwesomeLayersText } from '@fortawesome/vue-fontawesome';
import { fab, faGoogle } from '@fortawesome/free-brands-svg-icons'; //TODO: выяснить, почему не подключается сразу все (почему не работает с fab)
import { dom } from '@fortawesome/fontawesome-svg-core'; //TODO import all icons
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';
import { request } from 'graphql-request';
import {
    ValidationObserver,
    ValidationProvider,
    extend,
    localize
} from "vee-validate";
// import YmapPlugin from 'vue-yandex-maps'
// import ymaps_settings from './ymaps'


dom.watch();

library.add(fab, faGoogle);

Vue.component('font-awesome-icon', FontAwesomeIcon);
Vue.component('font-awesome-layers', FontAwesomeLayers);
Vue.component('font-awesome-layers-text', FontAwesomeLayersText);


Vue.use(BootstrapVue); // Install BootstrapVue
Vue.use(IconsPlugin); // Optionally install the BootstrapVue icon components plugin // TODO: возможно стоит очистить bootstrap-icons
Vue.use(VueParticles);
Vue.use(_);






//Vee-validate

// Install VeeValidate rules and localization
import * as vee_validate_rules from "vee-validate/dist/rules";
Object.keys(vee_validate_rules).forEach(rule => {
    extend(rule, vee_validate_rules[rule]);
});

import vee_validate_ru from "vee-validate/dist/locale/ru.json";
localize("ru", vee_validate_ru);

// Install VeeValidate components globally
Vue.component("ValidationObserver", ValidationObserver);
Vue.component("ValidationProvider", ValidationProvider);








// Vue.use(YmapPlugin);

Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead);
Vue.component('vue-headful', VueHeadful);

// Vue.mixin(ymaps_settings);


Vue.config.productionTip = false;

Vue.directive('scroll', {
    inserted: function (el, binding) {
        let f = function (evt) {
            if (binding.value(evt, el)) {
                window.removeEventListener('scroll', f)
            }
        };
        window.addEventListener('scroll', f)
    }
});

Vue.request_endpoint = "http://localhost:8085/api.php";
Vue.request = request;

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
