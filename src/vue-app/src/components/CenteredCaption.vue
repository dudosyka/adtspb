<template>
    <div class="caption d-inline-flex align-content-center justify-content-center align-items-center">

        <div class="flex-fill">
            <hr>
        </div>

        <div class="central-block">
            <slot class="text"></slot>
        </div>

        <div class="flex-fill">
            <hr>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Caption"
    }
</script>

<style scoped>
    .caption{
        width: 100% !important;
        margin-top: calc(43px / 4);
        margin-bottom: calc(43px / 4);
    }

    .central-block{
        margin: 0 10px 0 10px;
        font-size: 10pt;
        color: #6c757d;
    }

    hr, .text{
        opacity: 0.5;
    }

    hr{
        border-top: 2px solid rgba(0, 0, 0, 0.1);
    }
</style>
