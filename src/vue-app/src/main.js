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

    /*
    https://logaretm.github.io/vee-validate/guide/rules.html

    extend('required', {
  ...required,
  message: 'This field is required'
});
     */

});

// Кастомные типы данных для проверки:
extend("required",{ //Есои не надо выводить наименование поля
    ...vee_validate_rules.required,
    message: "Обязательно для заполнения"
});

extend("password",{
    message: "Пароль не должен содержать следующих символов: ` { } \[ \] : \" ; ' < > / ",
    validate: function(value){
        return value.match(/[`{}\[\]:";'<>\/]/) === null; //TODO: пофиксить проверку пароля
    }
});

extend("phone",{
    message: "Номер телефона должен быть заполнен в следующем формате: +7(XXX)XXX-XX-XX",
    validate: function(value){
        return value.match(/^\+7\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$/) !== null;
    }
});

extend("agreement",{
    message: "Требуется подтверждение согласия",
    validate: function(value){
        return value === true;
    }
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

Vue.prototype.$request_endpoint = "http://localhost:8085/api.php";
Vue.prototype.$request = request;

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
