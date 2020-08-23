<template>
    <div class="dashboard-associations-add">
        <b-alert variant="danger" v-bind:show="errors.length > 0" v-if="errors.length > 0"><p v-html="errors[0]"></p></b-alert>
        <b-alert variant="success" v-bind:show="success_add" v-if="success_add"><p>Объединение успешно добавлено</p></b-alert>
        <validation-observer ref="association_create">
            <b-form-row>
            <validation-provider
                style="width: 100%;"
                name="Название объединения"
                :rules="{ required: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="name"
                        placeholder="Название объединения"
                        :state="getValidationState(validationContext)"
                        aria-describedby="name-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Минимальный возраст ребенка"
                :rules="{ required: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="min_age"
                        placeholder="Минимальный возраст ребенка"
                        :state="getValidationState(validationContext)"
                        aria-describedby="min-age-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="min-age-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Максимальный возраст ребенка"
                :rules="{ required: true, integer: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="max_age"
                        placeholder="Максимальный возраст ребенка"
                        :state="getValidationState(validationContext)"
                        aria-describedby="max-age-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="max-age-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Кол-во лет обучения"
                :rules="{ required: true, integer: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        class=""
                        v-model="study_years"
                        placeholder="Кол-во лет обучения"
                        :state="getValidationState(validationContext)"
                        aria-describedby="study-years-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="study-years-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Общее кол-во часов в году"
                :rules="{ required: true, integer: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="study_hours"
                        placeholder="Общее кол-во часов в году"
                        :state="getValidationState(validationContext)"
                        aria-describedby="study-hours-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="study-hours-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Общее кол-во часов в неделе"
                :rules="{ required: true, integer: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="study_hours_week"
                        placeholder="Общее кол-во часов в неделе"
                        :state="getValidationState(validationContext)"
                        aria-describedby="study-hours-week-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="study-hours-week-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Кол-во групп"
                :rules="{ required: true, integer: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-input
                        v-model="group_count"
                        placeholder="Кол-во групп"
                        :state="getValidationState(validationContext)"
                        aria-describedby="group-count-feedback"
                    ></b-form-input>
                    <b-form-invalid-feedback id="group-count-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Описание объединения"
                :rules="{ required: true }"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-textarea
                        v-model="description"
                        placeholder="Описание объединения"
                        :state="getValidationState(validationContext)"
                        aria-describedby="description-feedback"
                    ></b-form-textarea>
                    <b-form-invalid-feedback id="description-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Закрыто для набора"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-checkbox
                        v-model="isClosed"
                        :state="getValidationState(validationContext)"
                        aria-describedby="is-closed-feedback"
                    >Закрыто для набора</b-form-checkbox>
                    <b-form-invalid-feedback id="is-closed-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>


            <validation-provider
                style="width: 100%;"
                name="Секретное объединение"
                v-slot="validationContext"
            >
                <b-form-group>
                    <b-form-checkbox
                        v-model="isHidden"
                        :state="getValidationState(validationContext)"
                        aria-describedby="is-hidden-feedback"
                    >Целевое объединение</b-form-checkbox>
                    <b-form-invalid-feedback id="is-hidden-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
            </validation-provider>

            <b-button @click="submitAssociationCreating()">Создать объединение</b-button>
        </b-form-row>
        </validation-observer>
    </div>
</template>

<script>
export default {
    name: "AddAssociation",
    data() {
        return {
            name: null,
            min_age: null,
            max_age: null,
            study_years: null,
            study_hours: null,
            study_hours_week: null,
            group_count: null,
            description: null,
            isClosed: false,
            isHidden: false,
            errors: [],
            success_add: false
        }
    },

    methods: {
        getValidationState({ dirty, validated, valid = null }) {
            return dirty || validated ? valid : null;
        },
        async submitAssociationCreating()
        {
            const isValid = await this.$refs.association_create.validate();

            if(!isValid) return false;

            this.errors = [];
            console.log(this.name, this.min_age, this.max_age, this.study_hours, this.study_years, this.study_hours_week, this.group_count, this.description, this.isClosed, this.isHidden);

            if (this.min_age > 18)
            {
                this.errors.push("Минимальный возраст не должен превышать 18");
                return;
            }
            if (this.max_age > 18)
            {
                this.errors.push("Максимальный возраст не должен превышать 18");
                return;
            }
            if (this.min_age > this.max_age)
            {
                this.errors.push("Максимальный возраст должен быть больше минимального");
                return;
            }

            let request = `
                mutation (
                    $name: String!
                    $min_age: Int!
                    $max_age: Int!
                    $study_hours: Int!
                    $study_years: Int!
                    $study_hours_week: Int!
                    $group_count: Int!
                    $description: String!
                    $isClosed: Int!
                    $isHidden: Int!
                ) {
                    adminCreateAssociation (
                        name: $name,
                        min_age: $min_age,
                        max_age: $max_age,
                        study_hours: $study_hours,
                        study_years: $study_years,
                        study_hours_week: $study_hours_week,
                        group_count: $group_count,
                        description: $description,
                        isClosed: $isClosed,
                        isHidden: $isHidden,
                    )
                }
            `;

            let data = {
                name: this.name,
                min_age: this.min_age,
                max_age: this.max_age,
                study_hours: this.study_hours,
                study_years: this.study_years,
                study_hours_week: this.study_hours_week,
                group_count: this.group_count,
                description: this.description,
                isClosed: this.isClosed ? 1 : 0,
                isHidden: this.isHidden ? 1 : 0
            };

            let _this = this;

            this.$request(this.$request_endpoint, request, data)
                .then(data => {
                    if (data.adminCreateAssociation)
                    {
                        _this.name = _this.min_age = _this.max_age = _this.group_count = _this.study_hours = _this.study_hours_week = _this.study_years = _this.description = null
                        _this.isCloned = _this.isHidden = false;
                        _this.success_add = true;
                    }
                    console.log(data);
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
