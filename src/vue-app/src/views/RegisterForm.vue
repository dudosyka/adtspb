<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <b-container class="form">
            <div>
                <h3 class="form-title">Регистрация</h3>
            </div>
            <b-form>
                <b-form-row>

                    <div class="social-networks-list d-inline-flex justify-content-center">
                        <FacebookButton class="mr-3"></FacebookButton>
                        <GoogleButton></GoogleButton>
                    </div>

                    <CenteredCaption>
                        Или
                    </CenteredCaption>

                    <b-form-input class="theme icon envelope" type="email" placeholder="Имя"></b-form-input>
                    <b-form-input class="theme icon envelope" type="email" placeholder="Фамилия"></b-form-input>
                    <b-form-input class="theme icon envelope" type="email" placeholder="Отчество"></b-form-input>
                    <b-form-input class="theme icon envelope" type="email" placeholder="E-mail"></b-form-input>
                    <b-form-input class="theme icon lock-fill" type="password" placeholder="Пароль"></b-form-input>
                    <b-form-input class="theme icon lock-fill" type="password" placeholder="Подтвердите пароль"></b-form-input>

                    <!-- TODO: дизайн по теме -->
                    <b-form-checkbox
                        v-model="status"
                        name="checkbox-1"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="theme"
                    >
                        Я согласен(-а) на обработку персональных данных
                        в соответствии с п.4 ст.9 Федерального закона
                        от 27.07.2006 №152-ФЗ "О персональных данных"
                    </b-form-checkbox>

                    <b-button class="theme" block>Создать аккаунт</b-button>

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
                status: false
            };
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
