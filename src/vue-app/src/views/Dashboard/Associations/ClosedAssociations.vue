<template>
    <div class="dashboard-associations-closed">

        <vue-headful title="Панель управления | Личный кабинет"/>

        <h5>Управление закрытыми объединениями</h5>
        <admin-association-table v-on:updatedata="updateData = !updateData" :key="updateData" type="closed"></admin-association-table>
        <admin-association-table v-on:updatedata="updateData = !updateData" :key="updateData" type="common-closed"></admin-association-table>
    </div>
</template>

<script>

import AdminAssociationTable from "@/components/AdminAssociationTable";
export default {
    name: "DashboardAssociationsClosed",
    data()
    {
        return {
            updateData: false,
        };
    },

    methods: {
    },
    components: {AdminAssociationTable},
}
</script>

<style scoped>

</style>
