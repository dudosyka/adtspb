<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <b-container class="form">
            <div>
                <h3 class="form-title">Авторизация</h3>
            </div>
            <b-form>
                <b-form-row>
                    <b-form-input class="theme icon envelope" type="email" placeholder="E-mail, номер телефона или логин"></b-form-input>
                    <b-form-input class="theme icon lock-fill" type="password" placeholder="Пароль"></b-form-input>

                    <b-button class="theme" block>Войти</b-button>

                    <p class="theme text-muted mx-auto lost-password">Забыли <b-link class="theme" href="/login/forgot-password">Логин / Пароль</b-link>?</p>

                    <CenteredCaption>
                        Или войти через
                    </CenteredCaption>

                    <div class="social-networks-list d-inline-flex justify-content-center">
                        <FacebookButton class="mr-3"></FacebookButton>
                        <GoogleButton></GoogleButton>
                    </div>

                    <p class="theme text-muted mx-auto register-account"><b-link class="theme" href="/register">Создать аккаунт</b-link> <b-icon-arrow-right></b-icon-arrow-right></p>

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
        name: "Login.vue",
        components: {
            FacebookButton,
            CenteredCaption,
            GoogleButton
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
