<template>
    <div class="dashboard-statistic">

        <vue-headful title="Панель управления | Выгрузка статистики"/>

        <h3 class="text-center">Выгрузка статистики: </h3>
        <b-container class="mb-5">
            <h5>По пользователям: </h5>
            <b-table
                :fields="user_statistic_table_fields"
                :items="user_statistic"
            >

            </b-table>
        </b-container>
        <hr>
        <b-container class="mt-5">
            <h5>По объединениям: </h5>
            <h6>Общее количество заявлений: {{ allProposalCount }}</h6>
            <b-input :type="'search'" v-model="associations_filter" placeholder="Поиск объединения"></b-input>
            <div class="mt-1 mb-2">
                <b-badge class="mr-1 p-1" variant="secondary">Запись приостановлена</b-badge>
                <b-badge class="mr-1 p-1" variant="primary">% наполненности <= 20</b-badge>
                <b-badge class="mr-1 p-1" variant="warning">% наполненности > 200 </b-badge>
                <b-badge class="mr-1 p-1" variant="danger">% наполненности > 300</b-badge>
            </div>
            <b-table
                :fields="association_statistic_table_fields"
                :filter="associations_filter"
                :filter-included-fields="['Название объединения']"
                :items="association_statistic"
                :tbody-tr-class="rowStyler"
            >

            </b-table>
        </b-container>
    </div>
</template>

<script>
export default {
    name: "DashboardStatistic",

    data(){
        return {
            association_statistic_table_fields: [{key: 'Название объединения', sortable: true}, {key: 'Количество групп', sortable: true}, {key: 'Плановые цифры', sortable: true}, {key: 'Фактические цифры', sortable: true}, {key: '% наполненности', sortable: true}],
            allProposalCount: 0,
            user_statistic_table_fields: ['Всего зарегистрировано детей', 'Всего зарегистрировано родителей'],
            association_statistic: [],
            user_statistic: [],
            associations_filter: null
        }
    },
    mounted() {
        let request = `
                mutation {
                    adminLoadStatistic
                }
            `;

        this.$graphql_client.request(request, {})
            .then(data=>{
                let statistic = JSON.parse(data.adminLoadStatistic);
                this.user_statistic.push({
                    'Всего зарегистрировано детей': statistic.child_statistic,
                    'Всего зарегистрировано родителей': statistic.parent_statistic,
                });
                statistic.proposal_statistic.map(el=>{
                    this.allProposalCount += parseInt(el['allProposalCount']);
                    this.association_statistic.push({
                        'Название объединения': el['Название объединения'],
                        'Количество групп': el['Количество групп'],
                        'Плановые цифры': el['Плановые цифры'],
                        'Фактические цифры': el['Фактические цифры'],
                        '% наполненности': el['% наполненности'],
                        'special': el['special']
                    });
                });
                console.log(statistic);
            })
            .catch(err=>{
                console.log(err);
            });
    },
    methods: {
        rowStyler(item, type) {
            if (!item || type !== 'row') return
            if (item['special'] != null) return 'table-secondary'
            if (item['% наполненности'] <= 20) return 'table-primary'
            if (item['% наполненности'] > 300) return 'table-danger'
            if (item['% наполненности'] > 200) return 'table-warning'
        }
    }

}
</script>

<style scoped>

</style>
