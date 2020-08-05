<template>
    <div class="dashboard-associations-hidden">

        <vue-headful title="Панель управления | Личный кабинет"/>

        <h5>Управление секретными объединениями</h5>
        <admin-association-table v-on:updatedata="updateData = !updateData" :key="updateData" type="hidden"></admin-association-table>
        <admin-association-table v-on:updatedata="updateData = !updateData" :key="updateData" type="common-hidden"></admin-association-table>
    </div>
</template>

<script>

import AdminAssociationTable from "@/components/AdminAssociationTable";
export default {
    name: "DashboardAssociationsHidden",

    data(){
        return {
            updateData: true
        };
    },

    methods: {
    },
    components: {AdminAssociationTable},
}
</script>

<style scoped>

</style>
