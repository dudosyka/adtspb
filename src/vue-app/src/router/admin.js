import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter);

const router = new VueRouter({
  mode: 'abstract',
  routes: [
      { path: '/', name: 'admin dashboard', component: () => import('../views/Dashboard.vue') }
  ]
})

// router.beforeEach((to, from, next) => {
//     console.log("beforeEach", to, from, next);
//     next();
// })

const adminRouter = {
    router: router,
    template: () => import('../App.vue')
};

export default adminRouter
