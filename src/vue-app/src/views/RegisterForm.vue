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

                    <b-form-input  class="theme icon person-lines-fill" placeholder="Имя"></b-form-input>
                    <b-form-input  class="theme icon person-lines-fill" placeholder="Фамилия"></b-form-input>
                    <b-form-input  class="theme icon person-lines-fill" placeholder="Отчество"></b-form-input>
                    <b-form-input  class="theme icon envelope" type="email" placeholder="E-mail"></b-form-input>
                    <b-form-input  class="theme icon lock-fill" type="password" placeholder="Пароль"></b-form-input>
                    <b-form-input  class="theme icon lock-fill" type="password" placeholder="Подтвердите пароль"></b-form-input>
                    <b-form-input  class="theme icon phone" placeholder="Мобильный телефон"></b-form-input>
                    <b-form-select class="theme" placeholder="Пол" v-model="sex_options_selected" :options="sex_options"></b-form-select>
                    <b-form-input  class="theme icon briefcase-fill" placeholder="Должность"></b-form-input>
                    <b-form-input  class="theme icon building" placeholder="Место работы"></b-form-input>

                    <!-- TODO: Компонент автодополнения адресов на базе Яндекс Карт -->
                    <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через ::before) -->
                    <b-form-input  class="theme select icon building" placeholder="Адрес регистрации"></b-form-input>
                    <b-form-input  class="theme select icon building" placeholder="Адрес проживания"></b-form-input>

                    <b-form-checkbox
                        v-model="status"
                        value="accepted"
                        unchecked-value="not_accepted"
                        class="theme accept"
                    >
                        Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                    </b-form-checkbox>

                    <b-button class="theme submit" block>Создать аккаунт</b-button>

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
