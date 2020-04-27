<template>
    <!-- TODO: поменять на FontAwesome -->
    <b-button class="social-button" variant="primary"></b-button>
</template>

<script>
    export default {
        name: "FacebookButton"
    }
</script>

<style lang="scss" scoped>
    .social-button{
        width: 48px;
        height: 48px;
        background-size: 32px 32px;
        background-image: url("~@/assets/icons/social/facebook-square-brands.svg");
        background-position: center;
        background-repeat: no-repeat;

        background-color: #3b5997 !important;
        border-color: #3b5997 !important;
        box-shadow: 0 0 2px rgba(0,0,0,0.5) !important;
        border-radius: 5px !important;
    }

    .social-button:hover{
        background-color: #344e84 !important;
        box-shadow: 0 0 4px rgba(0,0,0,0.5) !important;
    }

    .social-button:active{
        box-shadow: 0 0 8px rgba(0,0,0,0.5) !important;
    }

</style>
