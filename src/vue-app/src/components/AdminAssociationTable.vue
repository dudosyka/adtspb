<template>
    <div class="admin-association-table">
        <b-table :busy="!associationsLoaded" class="mt-3" outlined>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Загрузка...</strong>
                </div>
            </template>
        </b-table>

        <b-form-input :type="'search'" v-model="filter"  placeholder="Поиск по названию"></b-form-input>
        <b-table
            v-if="associationsLoaded"
            :fields="fields"
            :items="items"
            :filter="filter"
            :filter-included-fields="['name']"
        >
            <template v-slot:cell(controls)="row">
                <button v-if="table_type == 'hidden'" @click="switchHidden(row.item.id)">Убрать из секретных</button>
                <button v-if="table_type == 'common-hidden'" @click="switchHidden(row.item.id)">Добавить в секретные</button>
                <button v-if="table_type == 'common-closed'" @click="switchClosed(row.item.id)">Добавить в закрытые</button>
                <button v-if="table_type == 'closed'" @click="switchClosed(row.item.id)">Убрать из закрытых</button>
            </template>
        </b-table>
    </div>
</template>

<script>
export default {
    name: "AdminAssociationTable",
    data() {
        return {
            fields: [
                {
                    key: 'id',
                    label: 'Id',
                    sortable: true
                },
                {
                    key: 'name',
                    label: 'Название объединения',
                    sortable: true
                },
                {
                    key: 'controls',
                    label: 'Управление'
                }
            ],
            items: Array,
            associationsLoaded: false,
            table_type: String,
            filter: null
        }
    },
    methods: {
        loadAssociations()
        {
            this.associationsLoaded = false;
            let request = `
                query {
                    associations`;
            switch (this.table_type)
            {
                case 'hidden':
                    request += '(hidden: 1)';
                    break;
                case 'closed':
                    request += '(closed: 1)';
                    break;
                default:
                    request += '(hidden: 0, closed: 0)';
                    break;
            }

            request += " {id, name, min_age, max_age, study_years, study_hours, study_hours_week, group_count, description, isClosed, isHidden} }";

            console.log(request);

            this.$graphql_client.request(request, {})
                .then(data => {
                    console.log(data);
                    this.items = data.associations;
                    this.associationsLoaded = true;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        updateData()
        {
            this.$emit("updatedata");
        },
        switchAssociationState(state, id)
        {
            let method = "adminSwitchAssociationHidden";
            if (state == 'closed')
            {
                method = "adminSwitchAssociationClosed";
            }
            let request = `
                mutation{
                    `+method+`(id: `+id+`)
                }
            `;
            let _this = this;
            this.$graphql_client.request(request, {})
                .then(data => {
                    if (data[method])
                        _this.updateData();
                })
                .catch(err => {
                    console.log(err);
                });
        },
        switchHidden(id)
        {
            this.switchAssociationState('hidden', id);
        },
        switchClosed(id)
        {
            this.switchAssociationState('closed', id);
        }
    },
    mounted() {
        this.table_type = this.$attrs.type;
        this.loadAssociations();
    }
}
</script>

<style scoped>

</style>
