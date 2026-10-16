import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/contacts',
    name: 'contacts',
    component: () => import( '../views/Contact.vue')
  },
  {
    path: '/student_crud',
    name: 'student_crud',
    component: () => import( '../views/student_crud.vue')
  }
]


const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
