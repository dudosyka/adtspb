<template>
    <div class="dashboard">

        <vue-headful title="Панель управления | Личный кабинет"/>

        Вы авторизованы
        <b-button @click="logout">
            Выйти
        </b-button>

        <p>
            Форма для добавления ребенка:
        </p>

        <AddressInput placeholder="Фамилия"/>
        <AddressInput placeholder="Имя"/>
        <AddressInput placeholder="Отчество"/>
        <AddressInput placeholder="Дата рождения"/>
        <AddressInput placeholder="Школа"/>
        <AddressInput placeholder="Класс"/>

        <b-btn>Добавить</b-btn>

        <p>
            Загрузка списка педагогов:
        </p>

        <input type="file" accept="image/*" @change="uploadTeachersList">

    </div>
</template>

<script>
    import AddressInput from "../components/AddressInput";

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
            uploadTeachersList({ target }){
                console.log(target);
                // target.files[0]

                this.$graphql_client.request(`mutation($file: Upload!){ uploadTeachersList(file: $file) }`, data).then(function(data){
                    console.log(data);
                }).catch(function(e){
                    console.log(e);
                });
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

        },
        components: {
            AddressInput
        }
    }
</script>

<style scoped>

</style>
