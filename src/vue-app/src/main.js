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
import {GraphQLClient, request} from 'graphql-request';
import { ValidationObserver, ValidationProvider, extend, localize } from "vee-validate";
import VueScrollTo from 'vue-scrollto';
import VueGoodWizard from 'vue-good-wizard';


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
Vue.use(VueScrollTo, {
    container: "body",
    duration: 500,
    easing: "ease",
    offset: -10,
    force: true,
    cancelable: true,
    onStart: false,
    onDone: false,
    onCancel: false,
    x: false,
    y: true
});
Vue.use(VueGoodWizard);

/* Vee-validate */
//TODO: сделать подгрузку системы валидации в отдельном JS-файле (в директории globals)

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

// Совпадение паролей
extend('password_match', {
    params: ['target'],
    validate(value, { target }) {
        return value === target;
    },
    message: 'Пароли не совпадают'
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






/* Токены */
//TODO: сделать подгрузку системы токенов в отдельном JS-файле (в директории globals)
//TODO: доделать глобализацию системы токенов
//https://stackoverflow.com/questions/58967829/how-watch-global-variable-in-component-vuejs

const $globals = Vue.observable({
    $token: {},
    $graphql_client: {},
    $file_upload: {}
});

Object.defineProperty(Vue.prototype, '$token', {
    get () {
        return $globals.$token
    },

    set (value) {
        $globals.$token = value
    }
});

Object.defineProperty(Vue.prototype, '$graphql_client', {
    get () {
        return $globals.$graphql_client
    },

    set (value) {
        $globals.$graphql_client = value
    }
});

Object.defineProperty(Vue.prototype, '$file_upload', {
    get () {
        return $globals.$file_upload
    },

    set (value) {
        $globals.$file_upload = value
    }
});


Vue.mixin({
    mounted(){
        this.$token = localStorage.$token;
    },
    methods: {
        $recreateClient(){
            if(this.$token){
                this.$graphql_client = new GraphQLClient(this.$request_endpoint, {
                    headers: {
                        authorization: 'Bearer '+this.$token,
                        // "Content-Type": "multipart/form-data"
                    },
                });
                this.$file_upload = async function(query, variables, files){

                    let h = {
                        "Authorization": 'Bearer '+this.$token,
                    };

                    let b = new FormData();
                    b.append("variables", variables);
                    b.append("query", query);

                    for(let i in files){
                        b.append("file"+i, files[i]);
                    }


                    return fetch(this.$request_endpoint, {
                        method: 'POST',
                        headers: h,
                        body: b
                    });
                };
            }
        }
    },

    watch: {
        $token(newToken) {
            localStorage.$token = newToken;
            this.$recreateClient();
        }
    },
});

new Vue({
    router,
    render: h => h(App)
}).$mount('#app');
