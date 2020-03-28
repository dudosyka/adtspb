<template>

    <div class="login d-inline-flex justify-content-center align-items-center">

        <b-container class="form">
            <div>
                <h3 class="form-title">Авторизация</h3>
            </div>
            <b-form>
                <b-form-row>
                    <b-form-input class="theme-input" type="email" placeholder="E-mail, номер телефона или логин"></b-form-input>
                </b-form-row>
            </b-form>

        </b-container>

        <!-- TODO: config -->
        <vue-particles class="particles" color="#dedede"></vue-particles>
        <div class="city-foreground"></div>
    </div>

</template>

<script>
    export default {
        name: "Login.vue"
    }
</script>

<style lang="scss" scoped>

    /*
        TODO: передвинуть в отдельный scss файл
     */
    @function asset($path) {
        @return url("~@/assets/#{$path}");
    }

    .login{
        width: 100vw;
        height: 100vh;
    }

    .form{
        background-color: #ffffff;
        border-radius: 10px;
        z-index: 10;
        padding: 30px;
        width: 410px;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }

    .form-title{
        /*
            TODO: font
        */
        /*
            TODO: color
        */

        padding: 10px 0 10px 0;
        font-weight: bold;
    }

    .particles, .city-foreground{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .particles{
        z-index: 0;
        /*
            TODO: исправить баг со скроллом при 100%
        */
        height: calc(100% - 10px);
    }

    .city-foreground{
        z-index: 1;

        height: 100%;

        background-image: asset('/bg-spb.png');
        background-repeat: no-repeat;
        background-position: bottom;
        background-size: 100%;
    }

</style>
