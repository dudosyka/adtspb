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
        <b-pagination
            v-if="proposalsLoaded"
            v-model="currentPage"
            :total-rows="proposals_data.length"
            :per-page="perPage"
            aria-controls="proposalsTable"
        ></b-pagination>
        <b-container class="d-flex flex-row">
            <b-input :disabled="!proposalsLoaded" class="w-50" :type="'search'" v-model="search" placeholder="Поиск заявления (по ФИО родителя \ ребенка)"></b-input>
            <b-button :disabled="!proposalsLoaded" class="h-50 mt-2 ml-2" @click="loadProposals()">Поиск</b-button>
        </b-container>
        <b-table
            v-if="proposalsLoaded"
            id="proposalsTable"
            :items="proposals_data"
            :fields="proposals_fields"
            :per-page="perPage"
            :current-page="currentPage"
        >
            <template v-slot:cell(parentFullname)="row">
                {{ row.value }} ({{ row.item.parentPhone }})
                <b-button @click="editParent(row.item)">Изменить</b-button>
            </template>

            <template v-slot:cell(childFullname)="row">
                {{ row.value }} ({{ row.item.childBirthday }})
                <b-button @click="editChild(row.item);">Изменить</b-button>
                <b-button @click="generateResolutionForm(row.item)">Согласие</b-button>
            </template>

            <template v-slot:cell(controls)="row">
                <b-button v-if="row.item.status_admin_id == 1 && row.item.status_parent_id != 3" @click="preSetBrought(row.item.id)">Принесено</b-button>
                <b-button v-if="row.item.status_admin_id == 1 && row.item.status_parent_id != 3" @click="rejectProposal = true; rejectProposalId = row.item.id">Отклонить</b-button>
                <b-button v-if="(row.item.status_admin_id == 1 || row.item.status_admin_id == 6) && row.item.status_parent_id != 3" @click="generateForm(row.item)">Заявление</b-button>
                <b-button v-if="row.item.status_admin_id != 1" @click="preSetWaiting(row.item.id)">Ожидание</b-button>
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
                            Фамилия ребенка
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
                            Имя ребенка
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
                            Отчестве ребенка
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
                        :rules="{ required: false }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            Дата рождения ребенка
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.birthday"
                                placeholder="Дата рождения ребенка"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-birthday-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-birthday-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false, email: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            E-mail ребенка (необязательное)
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
                            Мобильный телефон ребенка
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
                            Пол ребенка
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
                            Адрес регистрации ребенка
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
                            Квартира по адресу регистрации ребенка
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
                            Адрес проживания ребенка
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
                            Квартира по адресу проживания ребенка
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

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            Место обучения
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.study_place"
                                placeholder="Место обучения"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-study-place-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-study-place-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: false }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            Класс обучения
                            <b-form-input
                                class="icon person-lines-fill"
                                v-model="child_data.study_class"
                                placeholder="Класс обучения"
                                :state="getValidationState(validationContext)"
                                :aria-describedby="'cld-study-class-feedback'"
                            ></b-form-input>
                            <b-form-invalid-feedback :id="'cld-study-class-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>
                </b-form-row>
            </validation-observer>
            <template v-slot:modal-footer>
                <b-button @click="editConfirmation.visible = true;editConfirmation.function = closeEditingChild">Сохранить</b-button>
            </template>
        </b-modal>

        <b-modal v-model="editParentModal">
            <validation-observer ref="parent_edit">
                <b-form-row>
                    <validation-provider
                        style="width: 100%;"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            Фамилия родителя
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
                            Имя родителя
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
                            Отчество родителя
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
                            E-mail родителя
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
                            Мобильный телефон родителя
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
                            Пол родителя
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
                            Адрес регистрации родителя
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
                            Квартира по адресу регистрации родителя
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
                            Адрес проживания родителя
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
                            Квартира по адресу проживания родителя
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
                <b-button @click="editConfirmation.visible = true;editConfirmation.function = closeEditingParent">Сохранить</b-button>
            </template>
        </b-modal>

        <b-modal v-model="editConfirmation.visible">
            Подтверждаете сохранение изменений? Отменить данное действие будет невозможно.
            <template v-slot:modal-footer>
                <b-button @click="editConfirmation.visible = false;editConfirmation.function()">Продолжить</b-button>
                <b-button @click="editConfirmation.visible = false;">Отмена</b-button>
            </template>
        </b-modal>

        <b-modal v-model="rejectProposal">
            Введите причину отклонения:
            <b-input v-model="rejectReason"></b-input>
            <template v-slot:modal-footer>
                <b-button @click="preSetReject()">Продолжить</b-button>
                <b-button @click="rejectProposal = false; rejectProposalId = null; rejectReason = '';">Отмена</b-button>
            </template>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: 'DashboardProposalHome',
    data: function () {
        return {
            proposals_data: [],
            proposals_fields: [
                {
                    key: 'id',
                    thClass: 'd-none',
                    tdClass: 'd-none'
                },
                {
                    key: 'associationName',
                    label: 'Название объединения',
                    sortable: true
                },
                {
                    key: 'parentFullname',
                    label: 'Родитель',
                    sortable: true
                },
                {
                    key: 'childFullname',
                    label: 'Ребенок',
                    sortable: true
                },
                {
                    key: 'statusParentName',
                    label: 'Статус родителя',
                    sortable: true
                },
                {
                    key: 'statusAdminName',
                    label: 'Статус администрации',
                    sortable: true
                },
                {
                    key: 'controls',
                    label: 'Управление',
                    sortable: false
                }
            ],
            proposal_id: null,

            child_data: Array,
            editChildModal: false,

            parent_data: Array,
            editParentModal: false,

            editConfirmation: {visible:false, function: false},
            sex_options: [
                { value: null, text: 'Пол', disabled: true },
                { value: "м", text: 'Мужской' },
                { value: "ж", text: 'Женский' },
            ],
            search: null,
            proposalsLoaded: true,
            currentPage: 1,
            perPage: 10,

            rejectProposal: false,
            rejectProposalId: null,
            rejectReason: ""
        }
    },
    mounted() {
        window.onkeydown = e => {
            console.log(e);
            if (e.key == "Enter" && this.proposalsLoaded)
            {
                this.loadProposals();
            }
        }
    },
    methods: {
        async setBrought()
        {
            await this.$graphql_client.request("mutation { adminChangeProposalStatus ( id: " + this.proposal_id + ", status_admin_id: 6) }");
            await this.loadProposals();
            this.editConfirmation = {visible:false, function: false};
        },
        preSetBrought(id)
        {
            this.proposal_id = id;
            this.editConfirmation.visible = true;
            this.editConfirmation.function = this.setBrought;
        },
        async setWaiting()
        {
            await this.$graphql_client.request("mutation { adminChangeProposalStatus ( id: " + this.proposal_id + ", status_admin_id: 1) }");
            await this.loadProposals();
            this.editConfirmation = {visible:false, function: false};
        },
        preSetWaiting(id)
        {
            this.proposal_id = id;
            this.editConfirmation.visible = true;
            this.editConfirmation.function = this.setWaiting;
        },
        async setReject()
        {
            await this.$graphql_client.request("mutation { adminChangeProposalStatus ( id: " + this.proposal_id + ", status_admin_id: 7, comment: \"" + this.rejectReason + "\") }");
            await this.loadProposals();
            this.editConfirmation = {visible:false, function: false};
            this.rejectReason = "";
        },
        preSetReject()
        {
            if (this.rejectReason == "")
                return;
            this.proposal_id = this.rejectProposalId;
            this.rejectProposal = false;
            this.rejectProposalId = null;
            this.editConfirmation.visible = true;
            this.editConfirmation.function = this.setReject;
        },
        async loadProposals()
        {
            if (this.search == "")
                return;
            this.proposalsLoaded = false;
            let data = await this.$graphql_client.request("query{ proposals (search: \"" + this.search + "\") }");
            this.proposals_data = JSON.parse(data.proposals);
            this.proposalsLoaded = true;
        },
        async editUserData(userData)
        {
            let data = userData;

            let birthday = data.birthday ? data.birthday : "";
            let study_class = data.study_class ? data.study_class : "";
            let study_place = data.study_place ? data.study_place : "";

            let request = `mutation {
                adminEditUserData (
                    id: ` + data.id + `,
                    name: "` + data.name + `",
                    surname: "` + data.surname + `",
                    ` + (data.midname != "" ? "midname:\"" + data.midname + "\"," : "") + `
                    birthday: "` + birthday + `",
                    email: "` + data.email + `",
                    phone_number: "` + data.phone_number + `",
                    sex: "` + data.sex + `",
                    registration_address: "` + data.registration_address + `",
                    registration_flat: "` + data.registration_flat + `",
                    residence_address: "` + data.residence_address + `",
                    residence_flat: "` + data.residence_flat + `",
                    study_class: "` + study_class + `",
                    study_place: "` + study_place + `"
                )
            }`;

            await this.$graphql_client.request(request);
            await this.loadProposals();
        },
        editChild(proposal)
        {
            this.editChildModal = true;
            this.child_data = {
                id: proposal.child_id,
                surname: proposal.childSurname,
                name: proposal.childName,
                midname: proposal.childMidname,
                birthday: proposal.childBirthday,
                email: proposal.childEmail,
                phone_number: proposal.childPhone,
                sex: proposal.childSex,
                registration_address: proposal.childRegistrationAddress,
                registration_flat: proposal.childRegistrationFlat,
                residence_address: proposal.childResidenceAddress,
                residence_flat: proposal.childResidenceFlat,
                study_place: proposal.childStudyPlace,
                study_class: proposal.childStudyClass
            };
            console.log(this.child_data);
        },
        editParent(proposal)
        {
            console.log(proposal);
            this.editParentModal = true;
            this.parent_data = {
                id: proposal.parent_id,
                surname: proposal.parentSurname,
                name: proposal.parentName,
                midname: proposal.parentMidname,
                birthday: false,
                email: proposal.parentEmail,
                phone_number: proposal.parentPhone,
                sex: proposal.parentSex,
                registration_address: proposal.parentRegistrationAddress,
                registration_flat: proposal.parentRegistrationFlat,
                residence_address: proposal.parentResidenceAddress,
                residence_flat: proposal.parentResidenceFlat,
                study_place: false,
                study_class: false
            };
            console.log(this.parent_data);
        },
        async closeEditingParent()
        {
            await this.editUserData(this.parent_data);
            this.editParentModal = false;
            this.editConfirmation.visible = false;
        },
        async closeEditingChild()
        {
            await this.editUserData(this.child_data);
            this.editChildModal = false;
            this.editConfirmation.visible = false;
        },
        generateForm(proposal)
        {
            const _this = this;

            //TODO: фетчинг не graphql файла через что-то цивильное?

            fetch(this.$request_endpoint+"?__module=ProposalGenerate&child_id="+proposal.child_id+"&parent_id="+proposal.parent_id+"&association_id="+proposal.association_id, {
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
                    a.download = "Заявление("+proposal.childSurname + "_" + proposal.childName + "_" + proposal.childMidname + ").pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });

        },
        generateResolutionForm(proposal)
        {
            fetch(this.$request_endpoint+"?__module=ResolutionGenerate&child_id="+proposal.child_id+"&parent_id="+proposal.parent_id, {
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
                    a.download = "Согласие_"+proposal.childSurname + "_" + proposal.childName + "_" + proposal.childMidname + ".pdf";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                });
        },
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
    }
}
</script>

<style scoped>

</style>

