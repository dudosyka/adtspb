<template>
    <div class="dashboard-statistic">

        <vue-headful title="Панель управления | Выгрузка статистики"/>

        <router-link class="text-dark bg-light rounded position-relative p-2 text-decoration-none" style="top: 1em; left: 1em;" to="/dashboard">Назад</router-link>

        <b-container class="w-75 min-vw-75 bg-light mt-2 rounded p-2 pt-4">
            <h3 class="text-center">Выгрузка статистики: </h3>

            <b-container class="mb-5">
                <h5>По объединениям: </h5>
                <b-table
                    :sticky-header="true"
                    :fields="association_statistic_table_fields"
                    :items="association_statistic"
                    :tbody-tr-class="rowStyler"
                >

                </b-table>
            </b-container>
            <hr>
            <b-container class="mt-5">
                <h5>По пользователям: </h5>
                <b-table
                    :fields="user_statistic_table_fields"
                    :items="user_statistic"
                >

                </b-table>
            </b-container>

        </b-container>

    </div>
</template>

<script>
    export default {
        name: "DashboardStatistic",

        data(){
            return {
                association_statistic_table_fields: [{key: 'Название объединения', sortable: true}, {key: 'Количество групп', sortable: true}, {key: 'Плановые цифры', sortable: true}, {key: 'Фактические цифры', sortable: true}, {key: '% наполненности', sortable: true}],
                user_statistic_table_fields: ['Всего зарегистрировано детей', 'Всего зарегистрировано родителей'],
                association_statistic: [],
                user_statistic: []
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
                    this.association_statistic.push({
                        'Название объединения': el['Название объединения'],
                        'Количество групп': el['Количество групп'],
                        'Плановые цифры': el['Плановые цифры'],
                        'Фактические цифры': el['Фактические цифры'],
                        '% наполненности': el['% наполненности']
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
                if (item['% наполненности'] <= 100) return 'table-success'
                if (item['% наполненности'] > 100 && item['% наполненности'] <= 200) return 'table-warning'
                if (item['% наполненности'] > 200) return 'table-danger'
            }
        }

    }
</script>

<style scoped>

</style>
