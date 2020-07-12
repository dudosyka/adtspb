<template>

    <div class="waving d-inline-flex justify-content-center align-items-center" v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'padding-top: 100px;'">

        <vue-headful title="Вход | Личный кабинет"/>

<!-- TODO: Раскоментить (когда будет функционал для педагога ребенка и учебного отдела) -->

<!--        <div class="top-panel" v-bind:class="(enoughSpaceForTopButtons()) ? 'd-flex' : ''">-->
<!--            <router-link to="/">-->
<!--                <b-button variant="dark" class="theme-alt top-panel-button"-->
<!--                          v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'width: 100% !important;'"><b-icon-chevron-double-left></b-icon-chevron-double-left> Назад</b-button>-->
<!--            </router-link>-->
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

                            name="E-mail"
                            :rules="{ required: true }"
                            v-slot="validationContext"
                        >
                            <b-form-group>
                                <b-form-input
                                    class="theme icon envelope"
                                    placeholder="E-mail"
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



                        <b-button style="background-color: #1862b6 !important;" class="custom-btn theme" type="submit" block :disabled="is_sending_request">Войти</b-button>

                        <p class="theme text-muted mx-auto lost-password" style="width: 100%; text-align: center;">Забыли <router-link class="theme" to="/login/restore-password">Пароль</router-link>?</p>

<!--                        <CenteredCaption>-->
<!--                            Или войти через-->
<!--                        </CenteredCaption>-->

<!--                        <div class="social-networks-list d-inline-flex justify-content-center">-->
<!--                            <FacebookButton class="mr-3"></FacebookButton>-->
<!--                            <GoogleButton></GoogleButton>-->
<!--                        </div>-->

                        <b-button class="custom-btn theme" style="margin-top: 15px;" block @click="gotoRegistration()">Создать аккаунт</b-button>

                        <!--
                        <p class="theme text-muted mx-auto register-account" style="width: 100%; text-align: center;">
                            <router-link class="theme" to="/register" style="color: green !important;">
                                Создать аккаунт <b-icon-arrow-right></b-icon-arrow-right>
                            </router-link>
                        </p>
                        -->

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

                graphql_errors: [],


                windowWidth: window.innerWidth
            };
        },

        mounted(){
            const _this = this;
            window.addEventListener('resize', () => {
                _this.windowWidth = window.innerWidth
            });

            if(this.$token != undefined){
                this.checkUps();

            }

        },

        methods: {

            enoughSpaceForTopButtons: function(){
                return this.windowWidth >= 765;
            },

            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            gotoRegistration(){
                this.$router.push({path: "/register/form"});
            },

            async checkUps() {
                let data = await this.$graphql_client.request(`query{viewer{email, status_email, hasAnyChildrenAdded, hasAnyProposals}}`, {});

                if (await this.hasAccess(13))
                {
                    this.$router.push({path: "/dashboard/"});
                    return;
                }

                // На шаг подтверждения e-mail
                if(data.viewer.status_email == "ожидание"){
                    this.$router.push({path: "/register/form?page=1&email="+data.viewer.email});
                    return;
                }

                // На шаг добавления детей
                if(!data.viewer.hasAnyChildrenAdded){
                    this.$router.push({path: "/register/form?page=2"});
                    return;
                }

                // На шаг регистрации детей в объединение
                if(!data.viewer.hasAnyProposals){
                    this.$router.push({path: "/register/form?page=3"});
                    return;
                }

                this.$router.push({path: "/register/form?page=4"});
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

                    _component.$nextTick(async function(){
                        _component.checkUps();
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

    .top-panel-button, .top-panel-button-darker{
        padding: 20px 40px !important;
        width: max-content;
        font-size: 16pt;
        height: 80px !important;
    }


    .top-panel-button{
        background-color: #1862b6 !important;
    }

    .top-panel-button-darker{
        background-color: #16529d !important;
    }

    .top-panel-button:focus, .top-panel-button-darker:focus {
        background-color: #12417c !important;
    }

    .top-panel-button:active, .top-panel-button-darker:active {
        background-color: #0f3061 !important;
    }



</style>
