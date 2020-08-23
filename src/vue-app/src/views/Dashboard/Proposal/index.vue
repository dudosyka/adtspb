<template>
    <div class="DashboardProposalHome p-3">
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2 mb-3" to="/dashboard/proposals/add">Добавить новое заявление</router-link>
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/proposals/">Выгрузка списка заявлений</router-link>
        <router-link class="text-dark bg-light rounded p-2 text-decoration-none ml-2" to="/dashboard/proposals/association-info">Информация по объединениям</router-link>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'DashboardProposalHome',
}
</script>

<style scoped>

</style>
