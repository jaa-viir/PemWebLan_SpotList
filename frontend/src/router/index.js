import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

// ─── PUBLIC VIEWS ─────────────────────────────────────────────────
import SpotListPage    from '../views/public/SpotListPage.vue'
import SpotDetailPage  from '../views/public/SpotDetailPage.vue'

// ─── AUTH VIEWS ───────────────────────────────────────────────────
import LoginPage       from '../views/public/LoginPage.vue'
import RegisterPage    from '../views/public/RegisterPage.vue'

// ─── MEMBER VIEWS ─────────────────────────────────────────────────
import MyRegistrationsPage from '../views/member/MyRegistrationsPage.vue'

// ─── ADMIN VIEWS ──────────────────────────────────────────────────
import AdminSpotsPage      from '../views/admin/AdminSpotsPage.vue'
import AdminSpotCreatePage from '../views/admin/AdminSpotCreatePage.vue'
import AdminSpotEditPage   from '../views/admin/AdminSpotEditPage.vue'
import AdminSpotRegsPage   from '../views/admin/AdminSpotRegsPage.vue'

const routes = [
  // ── Public ──────────────────────────────────────────────────────
  {
    path: '/',
    name: 'home',
    component: SpotListPage,
  },
  {
    path: '/spots/:id',
    name: 'spot-detail',
    component: SpotDetailPage,
  },

  // ── Auth ────────────────────────────────────────────────────────
  {
    path: '/login',
    name: 'login',
    component: LoginPage,
    meta: { guestOnly: true }, // redirect away if already logged in
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterPage,
    meta: { guestOnly: true },
  },

  // ── Member ──────────────────────────────────────────────────────
  {
    path: '/my-registrations',
    name: 'my-registrations',
    component: MyRegistrationsPage,
    meta: { requiresAuth: true },
  },

  // ── Admin ───────────────────────────────────────────────────────
  {
    path: '/admin/spots',
    name: 'admin-spots',
    component: AdminSpotsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/spots/create',
    name: 'admin-spot-create',
    component: AdminSpotCreatePage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/spots/:id/edit',
    name: 'admin-spot-edit',
    component: AdminSpotEditPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/admin/spots/:id/registrations',
    name: 'admin-spot-registrations',
    component: AdminSpotRegsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },

  // ── Catch-all ───────────────────────────────────────────────────
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// ─── NAVIGATION GUARDS ────────────────────────────────────────────
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // Guest-only pages (login/register) redirect logged-in users away
  if (to.meta.guestOnly && auth.isLoggedIn) {
    return next({ name: 'home' })
  }

  // Auth-required pages redirect guests to login
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return next({ name: 'login' })
  }

  // Admin-required pages redirect non-admins away
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return next({ name: 'home' })
  }

  next()
})

export default router
