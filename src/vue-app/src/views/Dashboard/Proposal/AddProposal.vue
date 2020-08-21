<template>
    <div class="DashboardAddProposal mt-4">
        <b-alert variant="success" v-model="showNotice">Заявление успешно создано</b-alert>
        <validation-observer ref="create_proposal">
            <b-form-row>
                <validation-provider
                    style="width: 100%"
                    :rules="{ required: true }"
                    v-slot="validationContext"
                    >
                    <b-form-group>
                        ID ребенка:
                        <b-form-input
                            v-model="child_id"
                            placeholder="Начните вводить ФИО ребенка"
                            list="child_list"
                            :state="getValidationState(validationContext)"
                            :aria-describedby="'cld-child-feedback'"
                        ></b-form-input>
                        <b-form-datalist id="child_list" :options="children"></b-form-datalist>
                        <b-form-invalid-feedback :id="'cld-child-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                </validation-provider>
                <validation-provider
                    style="width: 100%"
                    :rules="{ required: true }"
                    v-slot="validationContext"
                    >
                    <b-form-group>
                        ID родителя:
                        <b-form-input
                            v-model="parent_id"
                            placeholder="Начните вводить ФИО родителя"
                            list="parents_list"
                            :state="getValidationState(validationContext)"
                            :aria-describedby="'cld-parent-feedback'"
                        ></b-form-input>
                        <b-form-datalist id="parents_list" :options="parents"></b-form-datalist>
                        <b-form-invalid-feedback :id="'cld-parent-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                </validation-provider>
                <validation-provider
                    style="width: 100%"
                    :rules="{ required: true }"
                    v-slot="validationContext"
                    >
                    <b-form-group>
                        ID объединения:
                        <b-form-input
                            v-model="association_id"
                            placeholder="Начните вводить название объединения"
                            list="associations_list"
                            :state="getValidationState(validationContext)"
                            :aria-describedby="'cld-association-feedback'"
                        ></b-form-input>
                        <b-form-datalist id="associations_list" :options="associations"></b-form-datalist>
                        <b-form-invalid-feedback :id="'cld-association-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                </validation-provider>

            </b-form-row>
        </validation-observer>
        <b-button @click="createProposal()">Создать</b-button>

        <b-modal v-model="showWarn">
            Загруженность ребенка превышает 10 часов, вы уверены, что  хотите продолжить?
            <template v-slot:modal-footer>
                <b-button @click="showWarn=false;ignored=true;createProposal()">Продолжить</b-button>
                <b-button @click="showWarn = false;">Отмена</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "DashboardAddProposal",
    data() {
        return {
            child_id: null,
            children: Array,
            parent_id: null,
            parents: Array,
            association_id: null,
            associations: Array,

            showNotice: false,
            ignored: false,
            showWarn: false
        }
    },
    methods: {
        async createProposal()
        {
            this.showNotice = false;
            const isValid = await this.$refs.create_proposal.validate();
            if(!isValid) return false;

            if (!this.ignored)
            {
                let hours = await this.$graphql_client.request("mutation { adminCheckChildLoad (child_id: "+this.child_id+",parent_id: "+this.parent_id+",association_id: "+this.association_id+") }");
                hours = hours.adminCheckChildLoad;
                if (hours >= 10)
                {
                    this.showWarn = true;
                    return;
                }
            }

            this.$graphql_client.request("mutation { adminSelectChildAssociations (child_id: "+this.child_id+",parent_id: "+this.parent_id+",association_id: "+this.association_id+",) }")
                .then(data => {
                    this.child_id = null;
                    this.parent_id = null;
                    this.association_id = null;
                    this.showNotice = true;
                    this.ignored = false;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
    },
    async mounted() {
        let data = await this.$graphql_client.request('query { getAllChildren { id, getFullname } }');
        this.children = data.getAllChildren.map(el => {
            return {
                value: el.id,
                text: el.getFullname
            };
        });
        data = await this.$graphql_client.request('query { getAllParents { id, getFullname } }');
        this.parents = data.getAllParents.map(el => {
            return {
                value: el.id,
                text: el.getFullname
            };
        });
        data = await this.$graphql_client.request('query { associations { id, name } }');
        this.associations = data.associations.map(el => {
            return {
                value: el.id,
                text: el.name
            };
        });
        console.log(this.children);
        console.log(this.parents);
        console.log(this.associations);
    }
}
</script>

<style scoped>

</style>
