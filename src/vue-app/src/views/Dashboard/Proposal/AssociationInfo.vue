<template>
    <div class="DashboardProposalAssociationInfo mt-3 p-1">
        <b-input class="mt-4 mb-4" :type="'search'" v-model="search" placeholder="Поиск объединения"></b-input>
        <b-badge class="mr-1 p-1" variant="secondary">Запись приостановлена</b-badge>
        <b-table
            :fields="fields"
            :items="items"
            :filter="search"
            :filter-included-fields="['name']"
            :tbody-tr-class="rowStyler"
            responsive="true"
            class="mt-4"
        >
            <template v-slot:cell(fullness_percent)="row">
                {{ JSON.parse(row.item.statistic).fullness_percent }}
            </template>
            <template v-slot:cell(brought_percent)="row">
                {{ JSON.parse(row.item.statistic).brought_percent }}
            </template>
        </b-table>
    </div>
</template>

<script>
export default {
    name: "DashboardProposalAssociationInfo",
    data() {
        return {
            fields: [
                {
                    key: 'name',
                    label: 'Название'
                },
                {
                    key: 'description',
                    label: 'Описание'
                },
                {
                    key: 'max_age',
                    label: 'Макс. возр.',
                    sortable: true
                },
                {
                    key: 'min_age',
                    label: 'Мин. возр.',
                    sortable: true
                },
                {
                    key: 'study_hours_week',
                    label: 'Кол-во часов в неделю',
                    sortable: true
                },
                {
                    key: 'fullness_percent',
                    label: '% Наполненности',
                    sortable: true,
                },
                {
                    key: 'brought_percent',
                    label: '% принесенных заявлений',
                    sortable: true,
                },
            ],
            items: [],
            search: ""
        }
    },
    async mounted() {
        let data = await this.$graphql_client.request("query { adminLoadAssociationProposalInfo { name, max_age, min_age, description, study_hours_week, statistic, isClosed } }");
        this.items = data.adminLoadAssociationProposalInfo;
        console.log(this.items);
    },
    methods: {
        rowStyler(item, type)
        {
            if (!item || type !== 'row') return
            if (item['isClosed'] != 0) return 'table-secondary'
        }
    },
}
</script>

<style scoped>

</style>
