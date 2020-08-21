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
                <template v-slot:cell(controls)="row">
                    <button @click="getAssociationDetails(row.item.id); row.toggleDetails">Показать</button>
                </template>
            </b-table>
        </b-container>
        <b-modal hide-footer size="xl" v-model="association_detail_stat_visible">
            <b-table
                :fields="association_detail_stat_fields"
                :items="association_detail_stat"
            >
                <template v-slot:cell(parent_fullname)="row">
                    {{ row.item.parent_surname }} {{ row.item.parent_name }} {{ row.item.parent_midname }}
                </template>
                <template v-slot:cell(child_fullname)="row">
                    {{ row.item.child_surname }} {{ row.item.child_name }} {{ row.item.child_midname }}
                </template>
                <template v-slot:cell(child_contacts)="row">
                    <span>Email: {{ row.item.child_email }}</span><br><span>Мобильный: {{ row.item.child_phone }}</span>
                </template>
                <template v-slot:cell(parent_contacts)="row">
                    <span>Email: {{ row.item.parent_email }}</span><br><span>Мобильный: {{ row.item.parent_phone }}</span>
                </template>
            </b-table>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "DashboardStatistic",

    data(){
        return {
            association_statistic_table_fields: [{key: 'id', thClass: 'd-none', tdClass: 'd-none'}, {key: 'Название объединения', sortable: true}, {key: 'Количество групп', sortable: true}, {key: 'Плановые цифры', sortable: true}, {key: 'Фактические цифры', sortable: true}, {key: '% наполненности', sortable: true}, {key: 'controls', 'label': 'Подробнее'}],
            allProposalCount: 0,
            user_statistic_table_fields: ['Всего зарегистрировано детей', 'Всего зарегистрировано родителей'],
            association_statistic: [],
            user_statistic: [],
            associations_filter: null,
            association_detail_stat: null,
            association_details: [],
            association_detail_stat_visible: false,
            association_detail_stat_fields: [
                // {
                //     key: 'association_name',
                //     label: 'Название объединения',
                //     sortable: true
                // },
                {
                    key: 'parent_fullname',
                    label: 'ФИО родителя',
                    sortable: true
                },
                {
                    key: 'parent_contacts',
                    label: 'Контакты родителя',
                    sortable: true
                },
                {
                    key: 'child_fullname',
                    label: 'ФИО ребенка',
                    sortable: true
                },
                {
                    key: 'child_contacts',
                    label: 'Контакты ребенка',
                    sortable: true
                },
                {
                    key: 'timestamp',
                    label: 'Дата подачи',
                    sortable: true
                },
                {
                    key: 'status_parent',
                    label: 'Статус родителя',
                    sortable: true
                },
                {
                    key: 'status_teacher',
                    label: 'Статус преподователя',
                    sortable: true
                },
                {
                    key: 'status_admin',
                    label: 'Статус администратора',
                    sortable: true
                },
            ],
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
                        'id': el['id'],
                        'Название объединения': el['Название объединения'],
                        'Количество групп': el['Количество групп'],
                        'Плановые цифры': el['Плановые цифры'],
                        'Фактические цифры': el['Фактические цифры'],
                        '% наполненности': el['% наполненности'],
                        'special': el['special'],
                        'controls': null

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
            if (item['isHidden'] != null) return 'table-secondary'
            if (item['% наполненности'] <= 20) return 'table-primary'
            if (item['% наполненности'] > 300) return 'table-danger'
            if (item['% наполненности'] > 200) return 'table-warning'
        },
        getAssociationDetails(id) {
            this.association_detail_stat_visible = true;

            let request = `
                mutation {
                    getAssociationDetails(id: `+id+`)
                }
            `;
            this.$graphql_client.request(request, {})
                .then(data => {
                    this.association_details.push(JSON.parse(data.getAssociationDetails));
                    this.association_detail_stat = JSON.parse(data.getAssociationDetails);
                })
                .catch(err => {
                    console.log(err);
                });
        }

    }

}
</script>

<style scoped>
</style>
