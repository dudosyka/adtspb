<template>
    <div class="home" v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'padding-top: 180px;'"> <!--  v-scroll="handleScroll"-->

        <vue-headful title="Главная страница | Личный кабинет"/>

        <div class="content">
        <div class="top-panel" v-bind:class="(enoughSpaceForTopButtons()) ? 'd-flex' : ''">


            <a href="https://adtspb.ru/" target="_blank">
                <!-- v-bind:class="(topScroll > 0) ? 'selected' : ''" -->
                <b-button variant="dark" class="theme-alt top-panel-button"
                          v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'width: 100% !important;'"><b-icon-chevron-double-left></b-icon-chevron-double-left> Вернуться на сайт</b-button>
            </a>

            <div class="spacer" v-if="enoughSpaceForTopButtons()"></div>

            <router-link to="/login">
                <!-- v-bind:class="(topScroll > 0) ? 'selected' : ''" -->
                <b-button variant="dark" class="theme-alt top-panel-button-darker"
                          v-bind:style="(enoughSpaceForTopButtons()) ? '' : 'width: 100% !important;'">К личному кабинету <b-icon-chevron-double-right></b-icon-chevron-double-right></b-button>
            </router-link>


        </div>

        <div class="welcome-panel">
            <!-- TODO подумать над логикой исчезновения частиц за пределы контейнера (изменить фоновый цвет(?)) -->
            <vue-particles class="particles" color="#dedede" :hoverEffect="false" hoverMode="grab" :clickEffect="false"></vue-particles>

            <div class="logo-flex d-flex justify-content-center align-items-center">
                <div class="logo">
                    <h1 class="logo-title">Личный кабинет пользователя</h1>
                    <img class="logo-image" src="../assets/logo.png" alt="Logo">
                </div>
            </div>

        </div>

        <!-- TODO адаптация блока плюсов под мобильные устройства -->

        <div class="form advantage-form justify-content-around align-items-start" v-bind:class="(enoughSpace()) ? 'd-inline-flex' : ''">
            <div v-bind:class="(enoughSpace()) ? 'advantage' : 'advantage-mobile'">
                <h2>Родителям</h2>
                <div class="advantage-info-block">
                    <div class="advantage-image-container">
                        <img class="advantage-image" src="../assets/parents.png" alt="For parents">
                    </div>

                    <!-- TODO текст, редактируемый из БД -->

                    <div class="advantage-comment">
                        Подача заявления
                    </div>

                    <div class="advantage-comment">
                        Отслеживание статуса приема в Академию
                    </div>

                    <div class="advantage-comment">
                        Доступ к расписанию детей
                    </div>
                </div>
            </div>

            <div v-bind:class="(enoughSpace()) ? 'advantage-separator' : 'advantage-separator-mobile'"></div>

            <div v-bind:class="(enoughSpace()) ? 'advantage' : 'advantage-mobile'">
                <h2>Обущающимся</h2>
                <div class="advantage-info-block">
                    <div class="advantage-image-container">
                        <img class="advantage-image" src="../assets/pupils.png" alt="For pupils">
                    </div>

                    <!-- TODO текст, редактируемый из БД -->
                    <div class="advantage-comment">
                        Возможность мониторинга процедуры зачисления в выбранные объединения
                    </div>

                    <div class="advantage-comment">
                        Знакомство с графиком посещения занятий
                    </div>
                </div>

            </div>

            <div v-bind:class="(enoughSpace()) ? 'advantage-separator' : 'advantage-separator-mobile'"></div>

            <div v-bind:class="(enoughSpace()) ? 'advantage' : 'advantage-mobile'">
                <h2>Педагогам</h2>
                <div class="advantage-info-block">
                    <div class="advantage-image-container">
                        <img class="advantage-image" src="../assets/teachers.png" alt="For teachers">
                    </div>

                    <!-- TODO текст, редактируемый из БД -->
                    <div class="advantage-comment">
                        Наблюдение за ходом приема учащихся в свои объединения
                    </div>

                    <div class="advantage-comment">
                        Просмотр расписания
                    </div>

                </div>

            </div>
        </div>

        <!-- TODO замена ссылок из бд (не особо важно, месяца через 2) -->
        <div class="socials d-inline-flex justify-content-center align-items-center">
            <a class="social" href="https://vk.com/nabor_adtspb" target="_blank">
                <i class="fab fa-vk"></i>
            </a>
            <a class="social" href="https://www.instagram.com/adtspb/" target="_blank">
                <i class="fab fa-instagram"></i>
            </a>
            <a class="social" href="https://www.facebook.com/adtspb" target="_blank">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a class="social" href="https://twitter.com/adtspb" target="_blank">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    </div>

        <!-- TODO ссылки из бд (добавление, редактирование) (не особо важно, месяца через 2) -->
        <div class="footer-navigation justify-content-around align-items-center" v-bind:class="(enoughSpace()) ? 'd-inline-flex' : ''">
            <div class="navigation-page" v-bind:class="(enoughSpace()) ? '' : 'mobile'">
                <a href="https://adtspb.ru/" target="_blank">Главная</a>
            </div>
            <div class="navigation-page" v-bind:class="(enoughSpace()) ? '' : 'mobile'">
                <a href="https://adtspb.ru/about/" target="_blank">Об академии</a>
            </div>
            <div class="navigation-page selected" v-bind:class="(enoughSpace()) ? '' : 'mobile'">
                <a>Личный кабинет</a>
            </div>
            <div class="navigation-page" v-bind:class="(enoughSpace()) ? '' : 'mobile'">
                <a href="https://adtspb.ru/about/doc/" target="_blank">Документы</a>
            </div>
            <div class="navigation-page" v-bind:class="(enoughSpace()) ? '' : 'mobile'">
                <a href="https://adtspb.ru/contact/" target="_blank">Контакты</a>
            </div>
        </div>


        <div class="copyright">
            &copy; 2019 - {{year}} ГБНОУ Академия Цифровых Технологий Санкт-Петербурга
        </div>


<!--    <img alt="Vue logo" src="../assets/logo.png">-->
<!--    <HelloWorld msg="Welcome to Your Vue.js App"/>-->
<!--      <div>-->
<!--          <router-link to="/login/">Вход</router-link>-->
<!--          <router-link to="/register/form">Регистрация</router-link>-->
<!--      </div>-->


    </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue'

export default {
    name: 'Home',
    components: {
        // HelloWorld
    },
    data: function(){
        return {
            year: new Date().getFullYear(),
            topScroll: 0,
            windowWidth: window.innerWidth
        };
    },
    methods: {
        handleScroll: function (evt, el) {
            this.topScroll = window.scrollY;
        },
        enoughSpace: function(){
            return this.windowWidth >= 1273;
        },
        enoughSpaceForTopButtons: function(){
            return this.windowWidth >= 765;
        }
    },

    mounted() {
        const _this = this;
        window.addEventListener('resize', () => {
            _this.windowWidth = window.innerWidth
        });
    }

}
</script>

<style scoped>

    /* TODO: отредактировать цвета до импорта bootstrap */

    @import "./../assets/waving-form.scss";

    .content{
        margin-left: 5%;
        margin-right: 5%;
    }

    .spacer{
        margin: auto;
    }

    .particles{
        height: 602px;
        width: 100%;

        position: absolute;
        top: 0;
        left: 0;
        /*box-shadow: inset 0 0 100px rgba(24,98,182, 1) !important;*/
        z-index: 0;
    }

    .top-panel{
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 100;
        padding: 10px 5%;
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


    .welcome-panel{
        margin-top: 40px;
        margin-bottom: 70px;
        position: relative;
        height: 602px;
        width: 100%;
    }

    .logo-flex{
        height: 602px;
        z-index: 1;
        position: relative
    }

    .logo{
        margin: auto;
        text-align: center;
    }

    .logo-title{
        margin-bottom: 60px;
        color: #b7bfc5;
    }

    .logo-image{
        width: 320px;
        height: 320px;
    }

    h1{
        color: white;
        text-transform: uppercase;
        font-weight: lighter;
    }

    h2{
        text-transform: uppercase;
        letter-spacing: 5px;
        color: black;
        text-align: center;
    }

    .form{
        width: 100%;
    }

    .advantage-form{
        padding-top: 90px;
        padding-bottom: 90px;
    }

    .advantage{
        /*width: min-content;*/
        width: 33.3333%;
        font-size: 14pt;

        /* отсутп */
        flex: 1 1 auto;
        margin: 0 65px;
    }

    .advantage-mobile{
        /*width: min-content;*/
        width: 100%;
        font-size: 14pt;
    }

    .advantage-separator{
        border-left: 1px solid #ccc;
        height: 700px;
        width: 1px;
    }

    .advantage-separator-mobile{
        border-bottom: 1px solid #ccc;
        /*width: 700px;*/
        width: 100%;
        height: 1px;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .advantage-image-container, .advantage-image{
        height: 192px;
    }

    .advantage-image-container{
        text-align: center;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .advantage-comment{
        color: #79796a;
        margin-bottom: 15px;
    }


    .socials{
        width: 100%;
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .social{
        color: #cacad9;
        border-radius: 50%;
        border: #cacad9 2px solid;
        width: 54px;
        height: 54px;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24pt;

        margin-right: 15px;
    }

    .social:last-child{
        margin-right: 0;
    }


    .footer-navigation{
        background-color: white;
        width: 100%;
        padding: 40px 0;
    }

    .navigation-page{
        font-weight: bold;
        text-align: center;
        width: 350px;
        font-size: 24pt;
        text-transform: uppercase;
    }

    .navigation-page.mobile{
        width: 100% !important;
    }

    .navigation-page:not(.selected) > a{
        color: #aaaaaa !important;
    }

    .navigation-page.selected > a{
        color: black
    }

    .copyright{
        margin: 40px 0;
        text-align: center;
        color: #aaaaaa;
        font-size: 16pt;
    }

</style>
