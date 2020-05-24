<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Восстановление пароля | Личный кабинет"/>

        <b-container class="form">
            <div>
                <h3 class="form-title">Восстановление пароля</h3>
            </div>


            <b-alert variant="success" v-bind:show="succeeded_restore" v-if="succeeded_restore" id="succeeded_restore">
                Пароль успешно изменен.
            </b-alert>

            <vue-good-wizard
                v-bind:show="!succeeded_restore"
                v-if="!succeeded_restore"

                ref="wizard"
                :steps="steps"
                :onNext="nextClicked"
                :onBack="backClicked"
                :nextStepLabel="'Далее'"
                :previousStepLabel="'Назад'"
                :finalStepLabel="'Изменить пароль'">
                <div slot="page1">
                    <h4>Ввод информации об аккаунте</h4>
                    <p>Введите e-mail, номер телефона или логин аккаунта, доступ к которому требуется восстановить. На Вашу электронную почту будет выслано письмо со ссылкой для восстановления пароля.</p>

                    <validation-observer ref="observer" v-slot="{ passes }">
                        <b-form @submit.stop.prevent="passes(onSubmit)">
                            <b-form-row>

                                <b-alert variant="danger" v-bind:show="using_account_graphql_errors.length > 0" v-if="using_account_graphql_errors.length > 0" id="using_account_error_container">
                                    {{using_account_graphql_errors[0].message}}
                                </b-alert>

                                <validation-provider
                                    style="width: 100%;"

                                    ref="username_obs"
                                    name="E-mail, номер телефона или логин"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="theme icon envelope"
                                            placeholder="E-mail, номер телефона или логин"
                                            v-model="username"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="crd-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="crd-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                                <!--                        <b-button class="theme" type="submit" block :disabled="is_sending_request">Войти</b-button>-->
                            </b-form-row>
                        </b-form>
                    </validation-observer>

                </div>
                <div slot="page2">
                    <h4>Подтверждение доступа</h4>
                    <p>Введите код, который был выслан на электронную почту.</p>

                    <b-alert variant="danger" v-bind:show="incorrect_restore_code" v-if="incorrect_restore_code" id="incorrect_restore_code_message">
                        Неверный код подтверждения. Пожалуйста, проверьте введённый код.
                    </b-alert>

                    <validation-provider
                        style="width: 100%;"

                        ref="key_code"
                        name="Код доступа"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="theme icon envelope"
                                placeholder="Код доступа"
                                v-model="key_code"

                                :state="getValidationState(validationContext)"
                                aria-describedby="crd-feedback"
                            ></b-form-input>
                            <b-form-invalid-feedback id="crd-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>

                </div>
                <div slot="page3">
                    <h4>Введите новый пароль</h4>
                    <p>Введите новый пароль. Повторите введенный пароль.</p>

                    <b-alert variant="danger" v-bind:show="restore_graphql_errors.length > 0" v-if="restore_graphql_errors.length > 0" id="restore_account_errors_container">
                        {{restore_graphql_errors[0].message}}
                    </b-alert>

                    <validation-provider
                        style="width: 100%;"

                        ref="password"
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

                        ref="password2"
                        name="Подтверждение пароля"
                        :rules="{ required: true, password: true, password_match: password }"
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

                </div>
            </vue-good-wizard>

        </b-container>

        <!-- TODO: config -->
        <vue-particles class="particles" color="#dedede"></vue-particles>
        <div class="city-foreground"></div>
    </div>

</template>

<!-- TODO реализовать переход к нужному шагу через ссылки -->

<script>
    import CenteredCaption from "../components/CenteredCaption";
    import FacebookButton from "../components/social/FacebookButton";
    import GoogleButton from "../components/social/GoogleButton";
    import { GoodWizard } from 'vue-good-wizard';

    export default {
        name: "RestorePassword.vue",
        components: {
            FacebookButton,
            CenteredCaption,
            GoogleButton,
            'vue-good-wizard': GoodWizard,
        },
        data(){
            return {
                is_sending_request: false,
                username: "",
                key_code: "",

                // TODO: оптимизироватьсделать один массив ошибок на все уведомления с ошибками
                using_account_graphql_errors: [],
                incorrect_restore_code: null,
                restore_graphql_errors: [],

                password: "",
                password_matching: "",


                succeeded_restore: false,

                steps: [
                    {
                        label: 'Шаг №1',
                        slot: 'page1',
                    },
                    {
                        label: 'Шаг №2',
                        slot: 'page2'
                    },
                    {
                        label: 'Шаг №3',
                        slot: 'page3'
                    }
                ],

            };
        },
        methods: {

            nextClicked(currentPage) {
                // console.log('next clicked', currentPage);
                if (currentPage == 0) {
                    this.sendRestoreRequest();
                    return false;
                }

                if(currentPage == 1){
                    this.validateKeyCode();
                    return false;
                }

                if(currentPage == 2){
                    this.sendNewPassword();
                    return false;
                }

                return true; //return false if you want to prevent moving to next page
            },

            backClicked(currentPage) {
                // console.log('back clicked', currentPage);
                return true; //return false if you want to prevent moving to previous page
            },

            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            /* Шаг 3 */
            async sendNewPassword(){
                const isValid = await this.$refs.password.validate();
                if(!isValid.valid) return false;

                const isValid2 = await this.$refs.password2.validate();
                if(!isValid2.valid) return false;

                const request = `
                    mutation(
                        $key_code: String!,
                        $new_password: Password!
                    ) {
                        restorePasswordSaveNew (
                            key_code: $key_code,
                            new_password: $new_password
                        )
                    }
                `;

                const data = {
                    key_code: this.key_code,
                    new_password: this.password
                };

                this.is_sending_request = true;
                this.restore_graphql_errors = [];

                const _component = this;

                this.succeeded_restore = false;

                this.$request(this.$request_endpoint, request, data)
                    .then(function(data){
                        _component.is_sending_request = false;
                        _component.succeeded_restore = true;
                    })
                    .catch(function(e){
                        let errors = e.response.errors;

                        _component.is_sending_request = false;
                        if(errors != undefined){
                            _component.restore_graphql_errors = errors;
                            _component.$nextTick(function(){
                                _component.$scrollTo("#restore_account_errors_container");
                            });
                        }
                    });

            },

            /* Шаг 2 */
            async validateKeyCode(){
                const isValid = await this.$refs.key_code.validate();
                if(!isValid.valid) return false;


                const request = `
                    mutation(
                        $key_code: String!
                    ) {
                        validateCode (
                            key_code: $key_code
                        )
                    }
                `;

                const data = {
                    key_code: this.key_code
                };

                this.is_sending_request = true;
                this.incorrect_restore_code = null;

                const _component = this;

                this.$request(this.$request_endpoint, request, data)
                    .then(function(data){
                        _component.is_sending_request = false;
                        if(data.validateCode){
                            _component.$refs.wizard.currentStep++;
                        } else {
                            _component.incorrect_restore_code = true;
                            _component.$nextTick(function(){
                                _component.$scrollTo("#incorrect_restore_code_message");
                            });
                        }
                    })
                    .catch(function(e){
                        // let errors = e.response.errors;
                        _component.is_sending_request = false;
                    });



                return false;
            },

            /* Шаг 1 */
            async sendRestoreRequest(){
                this.using_account_graphql_errors = [];

                const isValid = await this.$refs.username_obs.validate();
                if(!isValid.valid) return false;


                const request = `
                    mutation(
                        $username: String!
                    ) {
                        restorePasswordRequest (
                            username: $username
                        )
                    }
                `;

                const data = {
                    username: this.username
                };

                this.is_sending_request = true;

                const _component = this;

                this.$request(this.$request_endpoint, request, data)
                    .then(function(data){
                        _component.is_sending_request = false;
                        if(data.restorePasswordRequest){
                            _component.$refs.wizard.currentStep++;
                        }
                    })
                    .catch(function(e){
                        let errors = e.response.errors;

                        _component.is_sending_request = false;
                        if(errors != undefined){
                            _component.using_account_graphql_errors = errors;
                            _component.$nextTick(function(){
                                _component.$scrollTo("#using_account_error_container");
                            });
                        }
                    });



                return false;
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "./../assets/waving-form.scss";


    .form{
        width: 698px;
        /*padding: 70px 70px 70px 70px !important;*/
    }

    .social-networks-list, .alert{
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
