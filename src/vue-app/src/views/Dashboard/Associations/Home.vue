<template>
    <div class="dashboard-associations-home">

        <vue-headful title="Панель управления | Личный кабинет"/>

        <b-container class="mb-4">
            <h3 class="mb-3" style="text-align: center;">Управление объединениями</h3>
            <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/associations/closed">Управление закрытыми объединениями</router-link>
            <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/associations/hidden">Управление целевыми объединениями</router-link>
            <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/associations/add">Добавление новых объединений</router-link>
        </b-container>
        <hr>
        <b-container class="pl-4 pr-4">
            <router-view></router-view>
        </b-container>

    </div>
</template>

<script>

    export default {
        name: "DashboardAssociationsHome",

        data(){
            return {
            };
        },

        methods: {
        },
    }
</script>

<style scoped>

</style>
