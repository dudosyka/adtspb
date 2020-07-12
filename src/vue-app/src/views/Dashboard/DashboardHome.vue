<template>
    <div class="dashboard-home">

        <vue-headful title="Панель управления | Личный кабинет"/>

        Вы авторизованы
        <b-button @click="logout">
            Выйти
        </b-button>
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/statistic">Выгрузка статистики</router-link>


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

        <input type="file" @change="uploadTeachersList">

        <p>{{last_upload_status_uploadTeachersList}}</p>

        <p>
            Загрузка списка объедений:
        </p>

        <input type="file" @change="adminUploadAssociations">

        <p>{{last_upload_status_adminUploadAssociations}}</p>

        <p>
            Загрузка списка административных сотрудников:
        </p>

        <input type="file">


    </div>
</template>

<script>
    import AddressInput from "../../components/AddressInput";

    export default {
        name: "DashboardHome",

        data: function(){
            return {
                last_upload_status_uploadTeachersList: "",
                last_upload_status_adminUploadAssociations: "",
            };
        },

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

                this.last_upload_status_uploadTeachersList = "";

                const _ = this;

                this.$file_upload(`mutation{ adminUploadTeachersList }`, "", [target.files[0]])
                    // В текст
                    .then(function(response) {
                        return response.json();
                    })
                    // в данные
                    .then(function(data){
                        _.last_upload_status_uploadTeachersList = data.data.adminUploadTeachersList;
                    })

                    .catch(function(e){
                        console.log(e);
                    });
            },
            adminUploadAssociations({target}){

                this.last_upload_status_adminUploadAssociations = "";

                const _ = this;

                this.$file_upload(`mutation{ adminUploadAssociations }`, "", [target.files[0]])
                // В текст
                    .then(function(response) {
                        return response.json();
                    })
                    // в данные
                    .then(function(data){
                        _.last_upload_status_adminUploadAssociations = data.data.adminUploadAssociations;
                    })

                    .catch(function(e){
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
