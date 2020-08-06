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
import { faHandPointUp } from '@fortawesome/free-solid-svg-icons';
import { dom } from '@fortawesome/fontawesome-svg-core'; //TODO import all icons
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';
import {GraphQLClient, request} from 'graphql-request';
import { ValidationObserver, ValidationProvider, extend, localize } from "vee-validate";
import VueScrollTo from 'vue-scrollto';
const VueInputMask = require('vue-inputmask').default;

Vue.use(VueInputMask);

// import YmapPlugin from 'vue-yandex-maps'
// import ymaps_settings from './ymaps'


dom.watch();

library.add(fab, faGoogle);
library.add(faHandPointUp);


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

extend("email",{ //Есои не надо выводить наименование поля
    ...vee_validate_rules.email,
    message: "E-mail должен быть действительным электронным адресом"
});

extend("max",{ //Есои не надо выводить наименование поля
    ...vee_validate_rules.max,
    message: "Поле содержит слишком большое количество символов"
});

extend("min",{ //Есои не надо выводить наименование поля
    ...vee_validate_rules.min,
    message: "Поле содержит слишком малое количество символов"
});

extend("password",{
    message: "Пароль не должен содержать следующих символов: ` { } \[ \] : \" ; ' < > /. Пароль должен быть длиннее 8 символов.",
    validate: function(value){
        return (value.match(/[`{}\[\]:";'<>\/]/) === null && value.length >= 8); //TODO: пофиксить проверку пароля
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

extend("date",{
    message: "Дата должна быть действительной (формат: ДД-ММ-ГГГГ)",
    validate: function(value){
        let input = value.match(/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/) !== null; // тоже поменять

        let __data = value.split("-");
        let year = parseInt(__data[2]); //0
        let month = parseInt(__data[1]); //1
        let day = parseInt(__data[0]); //2

        let validation1 = year >= 1000 && year <= 9999;
        let validation2 = month >= 1 && month <= 12;
        let validation3 = day >= 1 && day <= 31;

        let year_validation = ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0);

        let special = (month == 4 || month == 6 || month == 9 || month == 11) ? day <= 30 : true; // месяцы, где нет 31 дня
        let special1 = (!year_validation && month == 2) ? day <= 28 : true; // февраль
        let special2 = (year_validation && month == 2) ? day <= 29 : true; // високосный год

        return input && validation1 && validation2 && validation3 && special && special1 && special2;
    }
});

extend("kid_bdate",{
    message: "Дата рождения не соответствует возрастным ограничениям",
    validate: function(value){
        let input = value.match(/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/) !== null; // тоже поменять

        let __data = value.split("-");
        let year = parseInt(__data[2]); //0
        let month = parseInt(__data[1]); //1
        let day = parseInt(__data[0]); //2
        day = (day < 10) ? "0"+String(day) : day;
        month = (month < 10) ? "0"+String(month) : month;
        //
        // let validation1 = year >= 1000 && year <= 9999;
        // let validation2 = month >= 1 && month <= 12;
        // let validation3 = day >= 1 && day <= 31;
        //
        // let year_validation = ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0);
        //
        // let special = (month == 4 || month == 6 || month == 9 || month == 11) ? day <= 30 : true; // месяцы, где нет 31 дня
        // let special1 = (!year_validation && month == 2) ? day <= 28 : true; // февраль
        // let special2 = (year_validation && month == 2) ? day <= 29 : true; // високосный год
        //
        // if((input && validation1 && validation2 && validation3 && special && special1 && special2) == false)
        //     return false;
        //
        //
        // let getAge = function(date) {
        //     return ((new Date().getTime() - new Date(date)) / (24 * 3600 * 365.25 * 1000)) | 0;
        // };
        let getAge = date =>
        {
            let now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            now = now.toISOString().substr(0, 19).replace('T',' ');
            let age = now.substr(0, 4) - date.substr(0, 4);
            if(now.substr(5) < date.substr(5)) --age;
            return age;
        }
        let age = getAge(year+"-"+month+"-"+day+" 00:00:00");
        if(age >= 6 && age <= 18)
            return true;

        return false;
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


extend("valid_full_address",{
    message: "Адрес не найден или требует уточнения",
    validate: async function(value){
        // return true;
        await loadYmap({
            apiKey: '46740486-10c9-4828-9ffb-783dbdf451c6', //TODO: убрать дубликаты токена Яндекс Карт!
            // apiKey: '8ae15378-2641-415e-8789-239a9a7df87a',
            lang: 'ru_RU',
            coordorder: 'latlong',
            version: '2.1',
            debug: true
        });

        try
        {
            ymaps;
        } catch (ReferenceError)
        {
            return true;
        }

        let res = await ymaps.geocode(value);

        let obj = res.geoObjects.get(0),
            error;

        if (obj) {
            // Об оценке точности ответа геокодера можно прочитать тут: https://tech.yandex.ru/maps/doc/geocoder/desc/reference/precision-docpage/
            switch (obj.properties.get('metaDataProperty.GeocoderMetaData.precision')) {
                case 'exact':
                    break;
                case 'number':
                case 'near':
                case 'range':
                    // error = 'Неточный адрес, требуется уточнение';
                    error = true;
                    break;
                case 'street':
                    // error = 'Неполный адрес, требуется уточнение';
                    error = true;
                    break;
                case 'other':
                default:
                    // error = 'Неточный адрес, требуется уточнение';
                    error = true;
            }
        } else {
            // error = 'Адрес не найден';
            error = true;
        }

        // Если геокодер возвращает пустой массив или неточный результат, то показываем ошибку.
        if (error) {
            return false;
        }
        return true;
    }
});

extend("integer", {
    message: "Поле должно содержать только цифры",
    validate: value => {
        return (value.match(/^\D+$/u) == null);
    }
})

import vee_validate_ru from "vee-validate/dist/locale/ru.json";
import {loadYmap} from "vue-yandex-maps";
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

Vue.prototype.$request_endpoint = process.env.VUE_APP_REQUEST_ENDPOINT;
Vue.prototype.$request = request;






/* Токены */
//TODO: сделать подгрузку системы токенов в отдельном JS-файле (в директории globals)
//TODO: доделать глобализацию системы токенов
//https://stackoverflow.com/questions/58967829/how-watch-global-variable-in-component-vuejs

const $globals = Vue.observable({
    $token: {},
    $graphql_client: {},
    $file_upload: {},
    $action_list: null
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

Object.defineProperty(Vue.prototype, '$action_list', {
    get () {
        return $globals.$action_list
    },

    set (value) {
        $globals.$action_list = value
    }
});


async function hasAccess(action_id, action_list_id = 1)
{
    let request = `
                mutation {
                    getViewerRights(action_list_id: ` + action_list_id + `)
                }
            `;
    let result = false;
    try {
        await $globals.$graphql_client.request(request, {}).then(data => {
            $globals.$action_list = JSON.parse(data.getViewerRights).map(el => {
                return parseInt(el)
            });
            result = ($globals.$action_list.indexOf(action_id) > -1);
        }).catch(err => {
            console.log(err)
        });
    } catch (e) {}
    return result;
}

Vue.mixin({
    mounted(){
        this.$token = localStorage.$token;
    },
    methods: {
        hasAccess: hasAccess,
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

let config = [
    {regExp: /\/dashboard\/.*/,actionId: 13}
];

let requested;

let redirect;

router.beforeEach(async function(to, from, next) {
    requested = to;
    redirect = () => {
        router.push(from.path);
    };
    next();
})

new Vue({
    router,
    render: h => h(App),
    created: async function () {
        this.$token = localStorage.$token
        this.$recreateClient();
        let found = config.filter(el=>{return (requested.path.match(el.regExp))});
        if (found.length > 0)
            if (!await this.hasAccess(found[0].actionId))
                redirect();
    }
}).$mount('#app');
