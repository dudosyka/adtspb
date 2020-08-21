<template>
    <div class="DashboardProposalHome">
        <b-table :busy="!proposalsLoaded" class="mt-3" outlined>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Загрузка...</strong>
                </div>
            </template>
        </b-table>
        <b-input :type="'search'" v-model="search" placeholder="Поиск заявления (по ФИО родителя \ ребенка)"></b-input>
        <b-table
            v-if="proposalsLoaded"
            :items="proposals_data"
            :fields="proposals_fields"
            :filter="search"
            :filter-included-fields="['parent_data', 'child_data']"
        >

            <template v-slot:cell(parent_data)="row">
                {{ row.value }}
                <b-button @click="editParent(row.item.getParent)">Изменить</b-button>
            </template>

            <template v-slot:cell(child_data)="row">
                {{ row.item.getChild.surname + " " + row.item.getChild.name + (row.item.getChild.midname != "" ? " " + row.item.getChild.midname : "") }}
                <b-button @click="editChild(row.item.getChild);">Изменить</b-button>
            </template>

            <template v-slot:cell(controls)="row">
                <b-button v-if="row.item.status_admin_id == 1 && row.item.status_parent_id != 3" @click="setBrought(row.item.id)">Принесено</b-button>
                <b-button v-if="row.item.status_admin_id == 1 && row.item.status_parent_id != 3" @click="setReject(row.item.id)">Отклонить</b-button>
                <b-button v-if="row.item.status_admin_id == 1 && row.item.status_parent_id != 3" @click="generateForm(row.item.getChild, row.item.getAssociation.id, row.item.getParent)">Печать</b-button>
            </template>
        </b-table>

        <b-modal v-model="editChildModal">
            <validation-observer ref="child_edit">
                <b-form-row>
                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.surname"
                                placeholder="Фамилия ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-surname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.name"
                                placeholder="Имя ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-name-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-name-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.midname"
                                placeholder="Отчество ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-midname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-midname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false, email: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon envelope"
                                v-model="child_data.email"
                                placeholder="E-mail ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-email-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-email-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false, phone: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon phone"
                                v-model="child_data.phone_number"
                                placeholder="Мобильный телефон ребенка"
                                v-mask="'+7(999)999-99-99'"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-phone_number-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-phone_number-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"

                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-select
                                placeholder="Пол ребенка"
                                v-model="child_data.sex"
                                :options="sex_options"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-sex-feedback'"
                            ></b-form-select>
                            <b-form-invalid-feedback :id="'cld-sex-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.registration_address"
                                placeholder="Адрес регистрации ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-registration-address-surname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-registration-address-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.registration_flat"
                                placeholder="Квартира по адресу регистрации ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-registration-flat-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-registration-flat-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.residence_address"
                                placeholder="Адрес проживания ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-residence-address-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-residence-address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.residence_flat"
                                placeholder="Квартира по адресу проживания ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-residence-flat-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-residence-flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                </b-form-row>
            </validation-observer>
            <template v-slot:modal-footer>
                <b-button @click="editConfirmation.visible = true;editConfirmation.type = 'c'">Сохранить</b-button>
            </template>
        </b-modal>

        <b-modal v-model="editParentModal">
            <validation-observer ref="child_edit">
                <b-form-row>
                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.surname"
                                placeholder="Фамилия родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-surname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.name"
                                placeholder="Имя родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-name-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-name-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.midname"
                                placeholder="Отчество родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-midname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-midname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false, email: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon envelope"
                                v-model="parent_data.email"
                                placeholder="E-mail родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-email-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-email-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false, phone: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon phone"
                                v-model="parent_data.phone_number"
                                placeholder="Мобильный телефон родителя"
                                v-mask="'+7(999)999-99-99'"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-phone_number-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-phone_number-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"

                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-select
                                placeholder="Пол родителя"
                                v-model="parent_data.sex"
                                :options="sex_options"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-sex-feedback'"
                            ></b-form-select>
                            <b-form-invalid-feedback :id="'cld-sex-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.registration_address"
                                placeholder="Адрес регистрации родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-registration-address-surname-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-registration-address-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.registration_flat"
                                placeholder="Квартира по адресу регистрации родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-registration-flat-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-registration-flat-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.residence_address"
                                placeholder="Адрес проживания родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-residence-address-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-residence-address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="parent_data.residence_flat"
                                placeholder="Квартира по адресу проживания родителя"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-residence-flat-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-residence-flat-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                </b-form-row>
            </validation-observer>
            <template v-slot:modal-footer>
                <b-button @click="editConfirmation.visible = true;editConfirmation.type = 'p'">Сохранить</b-button>
            </template>
        </b-modal>

        <b-modal v-model="editConfirmation.visible">
            Подтверждаете сохранение изменений? Отменить данное действие будет невозможно.
            <template v-slot:modal-footer>
                <b-button @click="closeEditing(editConfirmation.type)">Продолжить</b-button>
                <b-button @click="editConfirmation.visible = false;">Отмена</b-button>
            </template>
        </b-modal>

    </div>
</template>

<script>
export default {
    name: 'DashboardProposalHome',
    data: function () {
        return {
            proposals_data: Array,
            proposals_fields: [
                {
                    key: 'id',
                    thClass: 'd-none',
                    tdClass: 'd-none'
                },
                {
                    key: 'parent_data',
                    label: 'Родитель',
                    sortable: true
                },
                {
                    key: 'child_data',
                    label: 'Ребенок',
                    sortable: true
                },
                {
                    key: 'parentStatus',
                    label: 'Статус родителя',
                    sortable: true
                },
                {
                    key: 'adminStatus',
                    label: 'Статус администрации',
                    sortable: true
                },
                {
                    key: 'controls',
                    label: 'Управление',
                    sortable: false
                }
            ],

            child_data: Array,
            editChildModal: false,

            parent_data: Array,
            editParentModal: false,

            editConfirmation: {visible:false, type: false},
            sex_options: [
                { value: null, text: 'Пол', disabled: true },
                { value: "м", text: 'Мужской' },
                { value: "ж", text: 'Женский' },
            ],
            search: null,
            proposalsLoaded: false
        }
    },
    async mounted() {
        await this.loadProposals();
    },
    methods: {
        async setBrought(id) {
            console.log(await this.$graphql_client.request("mutation { adminChangeProposalStatus ( id: " + id + ", status_admin_id: 6) }"));
        },
        async setReject(id) {
            console.log(await this.$graphql_client.request("mutation { adminChangeProposalStatus ( id: " + id + ", status_admin_id: 7) }"));
        },
        generateForm(child, association_id, parent){
            const _this = this;

            //TODO: фетчинг не graphql файла через что-то цивильное?

            fetch(this.$request_endpoint+"?__module=ProposalGenerate&child_id="+child.id+"&association_id="+association_id+"&parent_id="+parent.id, {
                method: 'GET',
                headers: new Headers({
                    "Authorization": "Bearer " + this.$token
                })
            })
                .then(response => response.blob())
                .then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    // a.download = "Заявление "++"("+child.name+" "+child.surname+").pdf";
                    a.download = "Заявление("+child.surname + "_" + child.name + "_" + child.midname + ").pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });

        },
        async loadProposals()
        {
            this.proposalsLoaded = false;
            let data = await this.$graphql_client.request("query{ proposals { id, status_admin_id, status_parent_id, parentStatus, adminStatus, getAssociation { id, name }, getChild { id, name, surname, midname, email, phone_number, sex, registration_address, registration_flat, residence_address, residence_flat, birthday }, getParent { id, name, surname, midname, email, phone_number, sex, registration_address, registration_flat, residence_address, residence_flat, birthday } } }");
            console.log(data.proposals);
            this.proposals_data = data.proposals.map(el => {
                el.parent_data = el.getParent.surname + " " + el.getParent.name + (el.getParent.midname != "" ? " " + el.getParent.midname : "");
                el.child_data = el.getChild.surname + " " + el.getChild.name + (el.getChild.midname != "" ? " " + el.getChild.midname : "");
                return el;
            });
            this.proposalsLoaded = true;
        },
        editChild(child)
        {
            this.editChildModal = true;
            this.child_data = child;
        },
        editParent(parent)
        {
            this.editParentModal = true;
            this.parent_data = parent;
        },
        async closeEditing(type)
        {
            let data;
            if (type == 'c')
                data = this.child_data;
            else
                data = this.parent_data;

            await this.$graphql_client.request(`mutation {
                adminEditUserData (
                    id: ` + data.id + `,
                    name: "` + data.name + `",
                    surname: "` + data.surname + `",
                    ` + (data.midname != "" ? "midname:\"" + data.midname + "\"," : "") + `
                    email: "` + data.email + `",
                    phone_number: "` + data.phone_number + `",
                    sex: "` + data.sex + `",
                    registration_address: "` + data.registration_address + `",
                    registration_flat: "` + data.registration_flat + `",
                    residence_address: "` + data.residence_address + `",
                    residence_flat: "` + data.residence_flat + `",
                )
            }`);

            if (type == 'c')
                this.editChildModal = false;
            else
                this.editParentModal = false;

            this.editConfirmation.visible = false;
        },

        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
    }
}
</script>

<style scoped>

</style>
