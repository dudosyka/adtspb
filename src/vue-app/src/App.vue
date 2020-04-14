<template>
  <div id="app">
      <!-- TODO: исправить мерцание частиц при переходе на другую страницу (добавить о общий компонент слоя) -->
<!--    <div id="nav">-->
<!--      <router-link to="/">Home</router-link> |-->
<!--      <router-link to="/about">About</router-link>-->
<!--    </div>-->
    <router-view/>
  </div>
</template>

<style lang="scss">
    @import "./assets/global.scss";
</style>
