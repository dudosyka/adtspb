<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Вход | Личный кабинет"/>

<!--        <div class="top-panel d-flex">-->

<!--            <div>-->
<!--                <router-link class="theme" to="/">-->
<!--                    <b-button variant="dark" class="theme-alt top-panel-button selected"><b-icon-chevron-double-left></b-icon-chevron-double-left> Назад</b-button>-->
<!--                </router-link>-->
<!--            </div>-->

<!--        </div>-->

        <b-container class="form">
            <div>
                <h3 class="form-title">Авторизация</h3>
            </div>

            <validation-observer ref="observer" v-slot="{ passes }">
                <b-form @submit.stop.prevent="passes(onSubmit)">
                    <b-form-row>

                        <b-alert variant="danger" v-bind:show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" id="login_errors_container">
                            {{graphql_errors[0].message}}
                        </b-alert>

                        <validation-provider
                            style="width: 100%;"

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

                        <p class="theme text-muted mx-auto lost-password" style="width: 100%; text-align: center;">Забыли <router-link class="theme" to="/login/restore-password">Логин / Пароль</router-link>?</p>

<!--                        <CenteredCaption>-->
<!--                            Или войти через-->
<!--                        </CenteredCaption>-->

<!--                        <div class="social-networks-list d-inline-flex justify-content-center">-->
<!--                            <FacebookButton class="mr-3"></FacebookButton>-->
<!--                            <GoogleButton></GoogleButton>-->
<!--                        </div>-->

                        <p class="theme text-muted mx-auto register-account" style="width: 100%; text-align: center;">
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

                is_sending_request: false,

                graphql_errors: []
            };
        },
        methods: {

            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            onSubmit(){

                this.graphql_errors = [];

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
                    _component.$token = data.login[0];
                    _component.$nextTick(function(){
                        this.$router.push({path: "/dashboard"});
                    });
                }).catch(function(e){
                    let errors = e.response.errors;

                    _component.is_sending_request = false;
                    if(errors != undefined){
                        _component.graphql_errors = errors;
                        _component.$nextTick(function(){
                            _component.$scrollTo("#login_errors_container");
                        });
                    }
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


    /* TODO: объединить стили кнопки в одно место */

    .top-panel{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 100;
        padding: 10px 5%;
        /*pointer-events: none;*/
    }

    .top-panel-button{
        padding: 20px 40px !important;
        width: max-content;
        font-size: 16pt;
        height: 80px !important;
    }



</style>
