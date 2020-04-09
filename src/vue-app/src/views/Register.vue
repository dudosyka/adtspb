<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Регистрация | Личный кабинет"/>

        <b-container class="form">
            <div>
                <h3 class="form-title">Регистрация</h3>
            </div>
            <b-form>
                <b-form-row>

                    <b-button class="theme submit" block @click="gotoFormClick">Я родитель</b-button>
                    <b-button class="theme submit info-block-button" block v-b-toggle.pupil-info>Я учащийся</b-button>
                    <b-collapse class="theme info-block mt-2" id="pupil-info">
                        <p class="theme info-block-text">
                            Привет!<br>
                            Сначала кто-то из родителей<br>
                            (законых представителей)<br>
                            должен зарегистрировать<br>
                            личный кабинет,<br>
                            после этого он сможет<br>
                            создать кабинет для тебя.</p>
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

</style>
