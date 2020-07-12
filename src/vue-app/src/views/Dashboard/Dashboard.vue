<template>
    <div class="dashboard">

        <vue-headful title="Панель управления | Личный кабинет"/>

        <router-view></router-view>

    </div>
</template>

<script>

    export default {
        name: "Dashboard",

        methods: {
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
