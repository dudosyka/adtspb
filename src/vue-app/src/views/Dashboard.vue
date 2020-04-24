<template>
    <div class="dashboard">
        Вы авторизованы
        <b-button @click="logout">
            Выйти
        </b-button>
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
            }
        },
        mounted() {
            this.$nextTick(function(){
                //TODO: проверка на просроченный или неверный токен
                if(this.$token == null || this.$token == "" || this.$token == undefined ||
                    this.$graphql_client == null || this.$graphql_client == undefined){
                    this.clearToken();
                }
            });

        }
    }
</script>

<style scoped>

</style>
