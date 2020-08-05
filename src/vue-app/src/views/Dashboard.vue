<template>
    <div class="dashboard">

        <vue-headful title="Панель управления | Личный кабинет"/>


        <a class="text-dark bg-light rounded position-relative p-2 text-decoration-none" style="cursor:pointer;top: 1em; left: 1em;" @click="$router.go(-1)">Назад</a>

        <b-button @click="logout" class="float-right position-relative" style="top: 0.5em; right: 1em;">
            Выйти
        </b-button>

        <b-container class="w-75 min-vw-75 bg-light mt-2 rounded p-2 pt-4 pb-4">
            <router-view></router-view>
        </b-container>

    </div>
</template>

<script>

    export default {
        name: "Dashboard",

        methods: {
            logout(){

                this.$graphql_client.request(`mutation{ logout }`)
                    .then(this.clearToken)
                    .catch(this.clearToken);

            },
            clearToken(){
                this.$token = "";
                this.$router.push({ path: '/login' });
            },
        },

        mounted() {
            this.$nextTick(function(){
                //TODO: проверка на просроченный или неверный токен
                if(this.$token == null || this.$token == "" || this.$token == undefined ||
                    this.$graphql_client == null || this.$graphql_client == undefined){
                    this.clearToken();
                }
            });
        },
        components: {
        }
    }
</script>

<style scoped>

</style>
