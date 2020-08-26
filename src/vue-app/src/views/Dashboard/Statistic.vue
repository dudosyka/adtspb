<template>
    <div class="dashboard-statistic">

        <vue-headful title="Панель управления | Выгрузка статистики"/>

        <h3 class="text-center">Выгрузка статистики: </h3>
        <b-container class="mb-5" v-if="!isTeacher">
            <template>
                <h5>По пользователям: </h5>
                <b-table
                    :fields="user_statistic_table_fields"
                    :items="user_statistic"
                >
                </b-table>
            </template>
        </b-container>
        <hr>
        <b-container class="mt-5">
            <h5>По объединениям: </h5>
            <template v-if="!isTeacher">
                <h6>Общее количество заявлений: {{ allProposalCount }}</h6>
                <h6>Количество принесенных заявлений: {{ broughtProposalCount }}</h6>
            </template>
            <b-input :type="'search'" v-model="associations_filter" placeholder="Поиск объединения"></b-input>
            <div class="mt-1 mb-2">
                <b-badge class="mr-1 p-1" variant="secondary">Запись приостановлена</b-badge>
                <b-badge class="mr-1 p-1" variant="success">Целевое объединение</b-badge>
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
                responsive="true"
            >
                <template v-slot:cell(controls)="row">
                    <b-button @click="getAssociationDetails(row.item.id); row.toggleDetails">Показать</b-button>
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
                    {{ row.item.child_surname }} {{ row.item.child_name }} {{ row.item.child_midname }} ({{ row.item.child_birthday }})
                </template>
                <template v-slot:cell(child_contacts)="row">
                    <span>Email: {{ row.item.child_email }}</span><br><span>Мобильный: {{ row.item.child_phone }}</span>
                </template>
                <template v-slot:cell(parent_contacts)="row">
                    <span>Email: {{ row.item.parent_email }}</span><br><span>Мобильный: {{ row.item.parent_phone }}</span>
                </template>
                <template v-slot:cell(status_teacher)="row">
                    {{ row.value }}
                    <b-button v-if="row.item.status_teacher_id == 1 && (row.item.status_admin_id != 7) && (row.item.status_parent_id != 3)" @click="preSetReceived(row.item.id, row.item.association_id)">Принято</b-button>
                </template>
            </b-table>
        </b-modal>

        <b-modal v-model="showWarn">
            Подтверждаете сохранение изменений? Отменить данное действие будет невозможно.
            <template v-slot:modal-footer>
                <b-button @click="showWarn = false;setReceived()">Продолжить</b-button>
                <b-button @click="showWarn = false;">Отмена</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "DashboardStatistic",

    data(){
        return {
            association_statistic_table_fields: Array,
            allProposalCount: 0,
            user_statistic_table_fields: ['Всего зарегистрировано детей', 'Всего зарегистрировано родителей'],
            association_statistic: [],
            user_statistic: [],
            associations_filter: null,
            association_detail_stat: null,
            association_details: [],
            association_detail_stat_visible: false,
            broughtProposalCount: 0,
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
                    label: 'Данные ребенка',
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
                    label: 'Статус преподавателя',
                    sortable: true
                },
                {
                    key: 'status_admin',
                    label: 'Статус администратора',
                    sortable: true
                },
            ],
            proposalToReceived: null,
            showWarn: false,
            editedAssociation: null,
            isTeacher: true,
        }
    },
    async mounted() {
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
                    this.broughtProposalCount += parseInt(el['brought']);
                    this.association_statistic.push({
                        'id': el['id'],
                        'association_name': el['association_name'],
                        'association_group_count': el['association_group_count'],
                        'planned_numbers': el['planned_numbers'],
                        'fact_numbers': el['fact_numbers'],
                        'fullness_percent': el['fullness_percent'],
                        'isHidden': el['special'],
                        'isClosed': el['isClosed'],
                        'brought': el['brought'],
                        'controls': null

                    });
                });
                console.log(statistic);
            })
            .catch(err=>{
                console.log(err);
            });

        if (await this.hasAccess(12))
            this.isTeacher = false;

        this.association_statistic_table_fields = [
            {key: 'id', thClass: 'd-none', tdClass: 'd-none'},
                {key: 'association_name', sortable: true, label: "Название объединения"},
                {key: 'association_group_count', sortable: true, label: 'Кол-во групп'},
                (!this.isTeacher ? {key: 'planned_numbers', sortable: true, label: "Плановые цифры"} : {}),
                (!this.isTeacher ? {key: 'fact_numbers', sortable: true, label: "Фактические цифры"} : {}),
                (!this.isTeacher ? {key: 'fullness_percent', sortable: true, label: "% наполненности"} : {}),
                {key: 'brought', label: 'Заявлений принесено', sortable:  true},
                {key: 'controls', 'label': 'Подробнее'}
            ]
    },
    methods: {
        rowStyler(item, type)
        {
            if (!item || type !== 'row') return
            if (item['isHidden'] != 0) return 'table-success'
            if (item['isClosed'] != 0) return 'table-secondary'
            if (item['fullness_percent'] <= 20) return 'table-primary'
            if (item['fullness_percent'] > 300) return 'table-danger'
            if (item['fullness_percent'] > 200) return 'table-warning'
        },
        getAssociationDetails(id)
        {
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
                    console.log(this.association_detail_stat);
                })
                .catch(err => {
                    console.log(err);
                });
        },
        preSetReceived(id, association_id)
        {
            this.proposalToReceived = id;
            this.editedAssociation = association_id;
            this.showWarn = true;
        },
        async setReceived()
        {
            console.log(await this.$graphql_client.request("mutation { teacherChangeProposalStatus ( id: " + this.proposalToReceived + ", status: 2 ) }"));
            this.getAssociationDetails(this.editedAssociation);
            this.proposalToReceived = this.editedAssociation = null;
        }


    }

}
</script>

<style scoped>
</style>
