<template>
<!--    <b-form-input class="select icon" :id="id"></b-form-input>-->

    <!-- TODO: отобразить значок выпадающего списка -->

    <vue-bootstrap-typeahead
        class="autofill-container"
        :data="addresses"
        v-model="addressSearch"
        :placeholder="placeholder"
        style="width: 100%;"
    />



    <!--TODO-->
    <!--
    https://tech.yandex.ru/maps/jsbox/2.1/direct_geocode
    https://tech.yandex.ru/maps/jsapi/doc/2.1/dg/concepts/geocoding/suggest-docpage/
    https://tech.yandex.ru/maps/jsapi/doc/2.1/dg/concepts/geocoding/geocode-docpage/

    https://tech.yandex.ru/maps/jsapi/?from=mapsapi
    -->

</template>

<script>
    import {loadYmap} from "vue-yandex-maps";

    const API_URL = 'https://api-url-here.com?query=:query'

    export default {
        name: "AddressInput",
        props:{
            placeholder: String
        },
        data() {
            return {
                addresses: [],
                addressSearch: '',
                selectedAddress: null,
                id: 'y_maps_input_'+Math.random(),

                yandex_maps_inited: false
            }
        },

        async mounted() {
            // const res = await fetch(API_URL.replace(':query', query))
            // const suggestions = await res.json()
            // this.addresses = suggestions.suggestions
            // let test = await loadYmap();

            const baseComponent = this;

            //TODO: сделать конфиги Яндекс Карт глобавльными

            await loadYmap({
                apiKey: '46740486-10c9-4828-9ffb-783dbdf451c6',
                lang: 'ru_RU',
                coordorder: 'latlong',
                version: '2.1',
                debug: true
            }).then(function(){
                // Только после then будет доступен ymaps
                // console.log(test);
                baseComponent.yandex_maps_inited = true;
            });


        },

        methods:{
            getAddresses(addr){
                if(this.yandex_maps_inited){

                    const baseComponent = this;

                    ymaps.suggest(addr).then(function(addrs){
                        //TODO: оптимизировать переобразование массива адресов в нужный формат
                        let suggested = [];
                        for(let i in addrs){
                            let current = addrs[i];
                            suggested.push(current.value);
                        }
                        baseComponent.addresses = suggested;
                    });
                }
            }
        },

        watch: {
            addressSearch: _.debounce(function(addr) { this.getAddresses(addr) }, 500)
        }
    }
</script>

<style scoped>
    /* TODO: напрямую задать свойства у инпута */
    div.autofill-container > div.input-group > input.form-control{
        padding-right: 50px;
        background-image: url("data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMTYgMTYiIHdpZHRoPSIxZW0iIGhlaWdodD0iMWVtIiBmb2N1c2FibGU9ImZhbHNlIiByb2xlPSJpbWciIGFsdD0iaWNvbiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiBmaWxsPSIjNzU3NTc1IiBjbGFzcz0iYmktY2FyZXQtZG93bi1maWxsIG14LWF1dG8gYi1pY29uIGJpIj48ZyBkYXRhLXYtODg5MmI5MjQ9IiI+PHBhdGggZD0iTTcuMjQ3IDExLjE0TDIuNDUxIDUuNjU4QzEuODg1IDUuMDEzIDIuMzQ1IDQgMy4yMDQgNGg5LjU5MmExIDEgMCAwMS43NTMgMS42NTlsLTQuNzk2IDUuNDhhMSAxIDAgMDEtMS41MDYgMHoiPjwvcGF0aD48L2c+PC9zdmc+");
        background-repeat: no-repeat;
        background-size: 8px 10px;
        background-position: right 2rem center;
    }
</style>
