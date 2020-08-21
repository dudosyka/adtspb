<template>
    <div class="dashboard-home">

        <vue-headful title="Панель управления | Личный кабинет"/>

        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/statistic">Выгрузка статистики</router-link>
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/associations">Управление объединениями</router-link>
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/proposals">Управление заявлениями</router-link>

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
