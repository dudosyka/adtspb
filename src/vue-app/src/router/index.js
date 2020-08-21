import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'

Vue.use(VueRouter)
const routes = [
    {
        path: '/',
        name: 'Login',
        //TODO: Вернуть (когда будет функционал для педагога ребенка и учебного отдела)

        // name: 'Home',
        // component: Home
        component: () => import('../views/Login.vue'),
        right: 1,
    },
    {
        path: '/about',
        name: 'About',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Login.vue')
    },
    {
        path: '/register',
        name: 'Register',
        component: () => import('../views/Register.vue')
    },
    {
        path: '/register/form',
        name: 'Register form',
        component: () => import('../views/RegisterForm.vue')
    },
    // {
    //     path: '/dashboard',
    //     name: 'Dashboard',
    //     component: () => import('../views/Dashboard.vue')
    // },
    {
        path: '/login/restore-password',
        name: 'Restore password',
        component: () => import('../views/RestorePassword.vue')
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue'),
        children: [
            {
                path: "/dashboard",
                component: () => import('../views/Dashboard/Home')
            },
            {
                path: "/dashboard/statistic",
                component: () => import('../views/Dashboard/Statistic')
            },
            {
                path: "/dashboard/associations",
                component: () => import('../views/Dashboard/Associations/Home'),
                children: [
                    {
                        path: "/dashboard/associations/hidden",
                        component: () => import('../views/Dashboard/Associations/HiddenAssociations'),
                    },
                    {
                        path: "/dashboard/associations/closed",
                        component: () => import('../views/Dashboard/Associations/ClosedAssociations'),
                    },
                    {
                        path: "/dashboard/associations/add",
                        component: () => import('../views/Dashboard/Associations/AddAssociation'),
                    }
                ]
            },
            {
                path: "/dashboard/proposals",
                component: () => import('../views/Dashboard/Proposal/Home'),
                children: [
                    {
                        path: '/dashboard/proposals/add',
                        component: () => import("../views/Dashboard/Proposal/AddProposal")
                    }
                ]
            }
        ],
    },

]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes

})

export default router
