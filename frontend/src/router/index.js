import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "../stores/auth.js";

// ─── PUBLIC VIEWS ─────────────────────────────────────────────────
import SpotListPage from "../views/public/SpotListPage.vue";
import SpotDetailPage from "../views/public/SpotDetailPage.vue";
import GuidesPage from "../views/public/GuidesPage.vue";
import GuideDetailPage from "../views/public/GuideDetailPage.vue";

// ─── AUTH VIEWS ───────────────────────────────────────────────────
import LoginPage from "../views/public/LoginPage.vue";
import RegisterPage from "../views/public/RegisterPage.vue";

// ─── MEMBER VIEWS ─────────────────────────────────────────────────
import MyRegistrationsPage from "../views/member/MyRegistrationsPage.vue";

// ─── ADMIN VIEWS ──────────────────────────────────────────────────
import AdminSpotsPage from "../views/admin/AdminSpotsPage.vue";
import AdminSpotCreatePage from "../views/admin/AdminSpotCreatePage.vue";
import AdminSpotEditPage from "../views/admin/AdminSpotEditPage.vue";
import AdminSpotRegsPage from "../views/admin/AdminSpotRegsPage.vue";
import AdminGuidesPage from "../views/admin/guides/AdminGuidesPage.vue";
import AdminGuideCreatePage from "../views/admin/guides/AdminGuideCreatePage.vue";
import AdminGuideEditPage from "../views/admin/guides/AdminGuideEditPage.vue";

const routes = [
  // ── Public Spots ────────────────────────────────────────────────
  {
    path: "/",
    name: "home",
    component: SpotListPage,
  },
  {
    path: "/spots/:id",
    name: "spot-detail",
    component: SpotDetailPage,
  },

  // ── Public Guides ───────────────────────────────────────────────
  {
    path: "/guides",
    name: "guides",
    component: GuidesPage,
  },
  {
    path: "/guides/:id",
    name: "guide-detail",
    component: GuideDetailPage,
  },

  // ── Auth ────────────────────────────────────────────────────────
  {
    path: "/login",
    name: "login",
    component: LoginPage,
    meta: { guestOnly: true }, // Mengalihkan pengguna jika sudah masuk/login
  },
  {
    path: "/register",
    name: "register",
    component: RegisterPage,
    meta: { guestOnly: true },
  },

  // ── Member ──────────────────────────────────────────────────────
  {
    path: "/my-registrations",
    name: "my-registrations",
    component: MyRegistrationsPage,
    meta: { requiresAuth: true },
  },

  // ── Admin Spots ─────────────────────────────────────────────────
  {
    path: "/admin/spots",
    name: "admin-spots",
    component: AdminSpotsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/spots/create",
    name: "admin-spot-create",
    component: AdminSpotCreatePage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/spots/:id/edit",
    name: "admin-spot-edit",
    component: AdminSpotEditPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/spots/:id/registrations",
    name: "admin-spot-registrations",
    component: AdminSpotRegsPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },

  // ── Admin Guides ────────────────────────────────────────────────
  {
    path: "/admin/guides",
    name: "admin-guides",
    component: AdminGuidesPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/guides/create",
    name: "admin-guide-create",
    component: AdminGuideCreatePage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },
  {
    path: "/admin/guides/:id/edit",
    name: "admin-guide-edit",
    component: AdminGuideEditPage,
    meta: { requiresAuth: true, requiresAdmin: true },
  },

  // ── Catch-all Fallback ──────────────────────────────────────────
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// ─── NAVIGATION GUARDS ────────────────────────────────────────────
router.beforeEach((to, from) => {
  const auth = useAuthStore();

  // Guest-only pages (login/register) redirect logged-in users away
  if (to.meta.guestOnly && auth.isLoggedIn) {
    return { name: "home" };
  }

  // Auth-required pages redirect guests to login
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    return { name: "login" };
  }

  // Admin-required pages redirect non-admins away
  if (to.meta.requiresAdmin && !auth.isAdmin) {
    return { name: "home" };
  }

  // Jika semua kondisi terpenuhi, izinkan navigasi berlanjut secara implisit (tanpa return apa-apa)
});

export default router;
