<template>
    <!-- TODO: поменять на FontAwesome -->
    <!-- TODO: исправить баг с анимацией -->
    <b-button class="social-button" variant="light"></b-button>
</template>

<script>
    export default {
        name: "GoogleButton"
    }
</script>

<style lang="scss" scoped>
    .social-button{
        width: 48px;
        height: 48px;
        background-size: 32px 32px;
        background-image: url("~@/assets/icons/social/google-brands.svg");
        background-position: center;
        background-repeat: no-repeat;

        background-color: #FFFFFF !important;
        box-shadow: 0 0 2px rgba(0,0,0,0.5) !important;
        border-radius: 5px !important;
    }
    .social-button:hover{
        background-color: #e4e4e4 !important;
        box-shadow: 0 0 4px rgba(0,0,0,0.5) !important;
    }

    .social-button:active{
        box-shadow: 0 0 8px rgba(0,0,0,0.5) !important;
    }

</style>
