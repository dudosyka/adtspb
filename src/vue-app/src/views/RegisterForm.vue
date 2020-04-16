<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Регистрация | Личный кабинет"/>

        <b-container class="form">
            <div>
                <h3 class="form-title">Регистрация</h3>
            </div>
            <validation-observer ref="observer" v-slot="{ passes }">


                <b-form @submit.stop.prevent="passes(onSubmit)">
                    <b-form-row>

                        <div class="social-networks-list d-inline-flex justify-content-center">
                            <FacebookButton class="mr-3"></FacebookButton>
                            <GoogleButton></GoogleButton>
                        </div>

                        <CenteredCaption>
                            Или
                        </CenteredCaption>

                        <validation-provider
                            style="width: 100%;"

                            name="Имя"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon person-lines-fill"
                                    v-model="name"
                                    placeholder="Имя"
                                    name="name"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="name-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Фамилия"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon person-lines-fill"
                                    v-model="surname"
                                    placeholder="Фамилия"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="surname-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="surname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Отчество"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon person-lines-fill"
                                    v-model="midname"
                                    placeholder="Отчество"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="midname-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="midname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="E-mail"
                            :rules="{ required: true, email: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon envelope"
                                    v-model="email"
                                    placeholder="E-mail"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="email-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Пароль"
                            :rules="{ required: true, password: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon lock-fill"
                                    v-model="password"
                                    placeholder="Пароль"
                                    type="password"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="email-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Подтверждение пароля"
                            :rules="{ required: true, password: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon lock-fill"
                                    v-model="password_matching"
                                    placeholder="Подтвердите пароль"
                                    type="password"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="password_matching-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="password_matching-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Мобильный телефон"
                            :rules="{ required: true, phone: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon phone"
                                    v-model="phone_number"
                                    placeholder="Мобильный телефон"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="phone_number-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="phone_number-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Пол"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-select
                                    placeholder="Пол"
                                    v-model="sex_options_selected"
                                    :options="sex_options"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="sex-live-feedback"
                                ></b-form-select>
                                <b-form-invalid-feedback id="sex-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Должность"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon briefcase-fill"
                                    v-model="job_position"
                                    placeholder="Должность"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="job_position-live-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="job_position-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Место работы"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="icon building"
                                    v-model="job_place"
                                    placeholder="Место работы"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="job_place-live-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="job_place-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <!-- TODO проверка валидации у адресов -->

                        <validation-provider
                            style="width: 100%;"

                            name="Адрес регистрации"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <AddressInput
                                    v-model="registration_address"
                                    placeholder="Адрес регистрации" />
                                <b-form-invalid-feedback id="registration_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <validation-provider
                            style="width: 100%;"

                            name="Адрес проживания"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <AddressInput
                                    v-model="residence_address"
                                    placeholder="Адрес проживания"

                                    @input="getValidationState(validationContext)"
                                    aria-describedby="residence_address-live-feedback" />
                                <b-form-invalid-feedback id="residence_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>






                        <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через 2 фона) -->

                        <validation-provider
                            style="width: 100%;"

                            name="Согласие"
                            :rules="{agreement: true}"
                            v-slot="validationContext"
                        >
                            <b-form-checkbox
                                v-model="status"
                                class="accept"

                                :state="getValidationState(validationContext)"
                                aria-describedby="agreement-live-feedback"
                            >
                                Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                            </b-form-checkbox>
                            <!-- Можно и не отображать -->
<!--                            <b-form-invalid-feedback id="agreement-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                        </validation-provider>

                        <b-button type="submit" block :disabled="is_sending_request">Создать аккаунт</b-button>

                    </b-form-row>
                </b-form>


            </validation-observer>


        </b-container>

        <!-- TODO: config -->
        <vue-particles class="particles" color="#dedede"></vue-particles>
        <div class="city-foreground"></div>
    </div>

</template>

<script>
    import CenteredCaption from "../components/CenteredCaption";
    import FacebookButton from "../components/social/FacebookButton";
    import GoogleButton from "../components/social/GoogleButton";
    import AddressInput from "../components/AddressInput";

    export default {
        name: "RegisterForm",
        components: {
            AddressInput,
            FacebookButton,
            CenteredCaption,
            GoogleButton
        },
        data: function(){
            return {
                status: false,
                sex_options_selected: null,
                sex_options: [
                    { value: null, text: 'Пол', disabled: true },
                    { value: "man", text: 'Мужской' },
                    { value: "woman", text: 'Женский' },
                ],

                name: null,
                surname: null,
                midname: null,
                email: null,
                password: null,
                password_matching: null,
                phone_number: null,
                sex: null,
                job_position: null,
                job_place: null,

                registration_address: null,
                residence_address: null,

                is_sending_request: false
            };
        },

        methods: {

            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            onSubmit(){
                //TODO: вывод ошибок graphql

                const request = `
                    mutation(
                        $name: String,
                        $surname: String,
                        $midname: String,
                        $email: String,
                        $password: String,
                        $phone_number: String,
                        $sex: String,
                        $job_position: String,
                        $job_place: String,
                        $registration_address: String,
                        $residence_address: String
                    ) {
                        register (
                            name: $name,
                            surname: $surname,
                            midname: $midname,
                            email: $email,
                            password: $password,
                            phone_number: $phone_number,
                            sex: $sex,
                            job_position: $job_position,
                            job_place: $job_place,
                            registration_address: $registration_address,
                            residence_address: $residence_address
                        )
                    }
                `;

                const data = JSON.stringify({
                    name:                   this.name,
                    surname:                this.surname,
                    midname:                this.midname,
                    email:                  this.email,
                    password:               this.password,
                    phone_number:           this.phone_number,
                    sex:                    this.sex,
                    job_position:           this.job_position,
                    job_place:              this.job_place,
                    registration_address:   this.registration_address,
                    residence_address:      this.residence_address
                });

                this.is_sending_request = true;

                this.$request(this.$request_endpoint, request, data).then(function(data){
                    this.is_sending_request = false;
                    console.log(data);
                }).catch(function(e){
                    this.is_sending_request = false;
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "./../assets/waving-form.scss";

    .form{
        width: 698px;
        padding: 70px 70px 70px 70px !important;
    }

    .social-networks-list{
        width: 100%;
    }


    .register-account {
        margin: 50px 0 0 0;
    }
    .lost-password{
        margin: 10px 0 0 0;
        font-size: 10pt;
    }

</style>
