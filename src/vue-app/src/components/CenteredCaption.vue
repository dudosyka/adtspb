<template>
    <div class="caption d-inline-flex align-content-center justify-content-center align-items-center">

        <div class="flex-fill">
            <hr>
        </div>

        <div class="central-block">
            <slot></slot>
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
    }

    .central-block{
        margin: 0 10px 0 10px;
        font-size: 10pt;
    }

    hr{
        border-top: 2px solid rgba(0, 0, 0, 0.1);
    }
</style>
