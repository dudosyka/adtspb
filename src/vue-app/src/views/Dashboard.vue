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
                this.$request(this.$request_endpoint, `mutation{ logout() }`).then(function(data){

                }).catch(function(e){

                });
            }
        },
        updated() {
            this.$nextTick(function(){
                //TODO: проверка на просроченный или неверный токен
                if(this.$token == null || this.$token == "" || this.$token == undefined ||
                    this.$graphql_client == null || this.$graphql_client == undefined){
                    this.$token = "";
                    this.$router.push({ path: '/login' });
                }
            });

        }
    }
</script>

<style scoped>

</style>
