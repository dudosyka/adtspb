<template>
<!--    <b-form-input class="select icon" :id="id"></b-form-input>-->

    <!-- TODO: отобразить значок выпадающего списка -->
    <!-- TODO: реализовать передачу аргументов на компонент (скопировать и вставить содержимое компонента в этот модуль) -->
    <vue-bootstrap-typeahead
        v-bind="$attrs"
        class="autofill-container"
        :data="addresses"
        v-model="addressSearch"
        :value="value"
        :placeholder="placeholder"
        style="width: 100%;"
        @input="handleInput"
        :state="state"
    />



    <!--        :input.required="is_required"-->
<!--    {{is_required}}-->
    <!-- TODO: полная поддержка v-model (на перезапись значения) -->

</template>


<script>
    import {loadYmap} from "vue-yandex-maps";

    export default {
        name: "AddressInput",
        props:{
            placeholder: String,
            is_required: Boolean,
            state: String,
            aria_describedby: String,

            value: String
        },
        model: {
            prop: 'value',
            event: 'input'
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
            },
            handleInput (e) {
                this.$emit('input', this.addressSearch)
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
