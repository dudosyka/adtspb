<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Вход | Личный кабинет"/>

        <b-container class="form">
            <div>
                <h3 class="form-title">Авторизация</h3>
            </div>

            <validation-observer ref="observer" v-slot="{ passes }">
                <b-form @submit.stop.prevent="passes(onSubmit)">
                    <b-form-row>

                        <validation-provider
                            style="width: 100%;"

                            name="Отчество"
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

                        <validation-provider
                            style="width: 100%;"

                            name="Пароль"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="theme icon lock-fill"
                                    type="password"
                                    placeholder="Пароль"
                                    v-model="password"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="password-feedback"
                                ></b-form-input>
                                <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                            </b-form-group>
                        </validation-provider>

                        <b-button class="theme" type="submit" block :disabled="is_sending_request">Войти</b-button>

                        <p class="theme text-muted mx-auto lost-password">Забыли <router-link class="theme" to="/login/forgot-password">Логин / Пароль</router-link>?</p>

                        <CenteredCaption>
                            Или войти через
                        </CenteredCaption>

                        <div class="social-networks-list d-inline-flex justify-content-center">
                            <FacebookButton class="mr-3"></FacebookButton>
                            <GoogleButton></GoogleButton>
                        </div>

                        <p class="theme text-muted mx-auto register-account">
                            <router-link class="theme" to="/register">
                                Создать аккаунт <b-icon-arrow-right></b-icon-arrow-right>
                            </router-link>
                        </p>

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

    export default {
        name: "Login.vue",
        components: {
            FacebookButton,
            CenteredCaption,
            GoogleButton
        },
        data(){
            return {
                username: null,
                password: null,

                is_sending_request: false
            };
        },
        methods: {

            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            onSubmit(){

                const request = `
                    mutation(
                        $username: String!,
                        $password: String!
                    ) {
                        login (
                            username: $username,
                            password: $password,
                        )
                    }
                `;

                const data = {
                    username: this.username,
                    password: this.password
                };

                this.is_sending_request = true;

                const _component = this;

                this.$request(this.$request_endpoint, request, data).then(function(data){
                    _component.is_sending_request = false;
                    console.log(data);
                }).catch(function(e){
                    _component.is_sending_request = false;
                });

            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "./../assets/waving-form.scss";


    .form{
        padding: 30px 30px 10px 30px;
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
