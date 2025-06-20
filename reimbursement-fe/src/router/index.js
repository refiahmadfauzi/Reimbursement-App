import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/Login.vue'
import DashboardView from '../views/ReimbursementList.vue'
import UsersView from '../views/UserList.vue'
import CategoryView from '../views/CategoryList.vue'
import LogsView from '../views/Logs.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView,
    },
    {
      path: '/user',
      name: 'user',
      component: UsersView,
    },
    {
      path: '/category',
      name: 'category',
      component: CategoryView,
    },
    {
      path: '/logs',
      name: 'logs',
      component: LogsView,
    },
    
  ],
})

export default router
