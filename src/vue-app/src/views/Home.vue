<template>
    <div class="home">
    <div class="content">
        <div class="top-panel d-flex">

            <div>
                <b-button variant="dark" class="theme-alt top-panel-button"><b-icon-chevron-double-left></b-icon-chevron-double-left> Вернуться на сайт</b-button>
            </div>

            <div class="spacer"></div>

            <div>
                <b-button variant="dark" class="theme-alt top-panel-button">К личному кабинету <b-icon-chevron-double-right></b-icon-chevron-double-right></b-button>
            </div>


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

        <div class="form advantage-form d-inline-flex justify-content-around align-items-start">
            <div class="advantage">
                <h2>Родителям</h2>
                <div class="advantage-image-container">
                    <img class="advantage-image" src="../assets/parents.png" alt="For parents">
                </div>

                <!-- TODO текст, редактируемый из БД -->

                <div class="advantage-comment">
                    Подача заявления
                </div>

                <div class="advantage-comment">
                    Отслеживание статуса заявления
                </div>

                <div class="advantage-comment">
                    Отслеживание расписания
                </div>

                <div class="advantage-comment">
                    Подбор курса исходя из потребностей школьника*
                </div>

                <div class="advantage-comment">
                    Получение уведомлений
                </div>

            </div>
            <div class="advantage">
                <h2>Обущающимся</h2>
                <div class="advantage-image-container">
                    <img class="advantage-image" src="../assets/pupils.png" alt="For pupils">
                </div>

                <!-- TODO текст, редактируемый из БД -->
                <div class="advantage-comment">
                    Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн.
                </div>
            </div>
            <div class="advantage">
                <h2>Педагогам</h2>
                <div class="advantage-image-container">
                    <img class="advantage-image" src="../assets/teachers.png" alt="For teachers">
                </div>

                <!-- TODO текст, редактируемый из БД -->
                <div class="advantage-comment">
                    Lorem Ipsum - это текст-"рыба", часто используемый в печати и вэб-дизайне. Lorem Ipsum является стандартной "рыбой" для текстов на латинице с начала XVI века. В то время некий безымянный печатник создал большую коллекцию размеров и форм шрифтов, используя Lorem Ipsum для распечатки образцов. Lorem Ipsum не только успешно пережил без заметных изменений пять веков, но и перешагнул в электронный дизайн.
                </div>
            </div>
        </div>

        <div class="socials d-inline-flex justify-content-center align-items-center">
            <div class="social">
                <i class="fab fa-vk"></i>
            </div>
            <div class="social">
                <i class="fab fa-instagram"></i>
            </div>
            <div class="social">
                <i class="fab fa-facebook-f"></i>
            </div>
            <div class="social">
                <i class="fab fa-twitter"></i>
            </div>
        </div>
    </div>

    <!-- TODO ссылки -->
    <div class="footer-navigation d-inline-flex justify-content-around align-items-center">
        <div class="navigation-page">
            <a href="#">Главная</a>
        </div>
        <div class="navigation-page">
            <a href="#">Об академии</a>
        </div>
        <div class="navigation-page selected">
            <a href="#">Личный кабинет</a>
        </div>
        <div class="navigation-page">
            <a href="#">Новости</a>
        </div>
        <div class="navigation-page">
            <a href="#">Контакты</a>
        </div>
    </div>


    <div class="copyright">
        &copy; 2019 ГБНОУ Академия Цифровых Технологий Санкт-Петербурга
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
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 100;
        padding: 10px;
    }

    .top-panel-button{
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .welcome-panel{
        margin-top: 40px;
        margin-bottom: 70px;
        position: relative;
        height: 602px;
        width: 100%;
        z-index: 2;
    }

    .logo-flex{
        height: 602px;
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
