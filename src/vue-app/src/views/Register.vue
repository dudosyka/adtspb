<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Регистрация | Личный кабинет"/>

<!--        <div class="top-panel d-flex">-->

<!--            <div>-->
<!--                <router-link class="theme" to="/login">-->
<!--                    <b-button variant="dark" class="theme-alt top-panel-button selected"><b-icon-chevron-double-left></b-icon-chevron-double-left> Назад</b-button>-->
<!--                </router-link>-->
<!--            </div>-->

<!--        </div>-->

        <b-container class="form">
            <div>
                <h3 class="form-title">Регистрация</h3>
            </div>
            <b-form>
                <b-form-row>

                    <b-button class="submit" block @click="gotoFormClick">Я родитель</b-button>
                    <b-button class="submit info-block-button" block v-b-toggle.pupil-info>Я учащийся</b-button>
                    <b-collapse class="info-block mt-2" id="pupil-info">
                        <p class="info-block-text">
                            Привет!<br>
                            Сначала кто-то из родителей<br>
                            (законных представителей)<br>
                            должен зарегистрировать<br>
                            личный кабинет,<br>
                            после этого он сможет<br>
                            создать кабинет для тебя.
                        </p>
                    </b-collapse>

                </b-form-row>
            </b-form>

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
        name: "RegisterForm",
        components: {
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
                ]
            };
        },
        methods: {

            gotoFormClick(){
                this.$router.push({path: "/register/form"});
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "./../assets/waving-form.scss";

    .form{
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
