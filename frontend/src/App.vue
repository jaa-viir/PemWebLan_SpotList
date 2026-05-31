<template>
  <div id="app">
    <header class="navbar-wrapper">
      <nav class="navbar">
        <router-link to="/" class="brand">
          <i class="fa-solid fa-calendar-check"></i> SpotList
        </router-link>

        <div class="nav-links">
          <router-link to="/">
            <i class="fa-solid fa-magnifying-glass"></i> Spots
          </router-link>
          <router-link to="/guides">
            <i class="fa-solid fa-book-open"></i> Guides
          </router-link>
          <router-link v-if="auth.isLoggedIn" to="/my-registrations">
            <i class="fa-solid fa-ticket"></i> My Registrations
          </router-link>
          <router-link v-if="auth.isAdmin" to="/admin/spots">
            <i class="fa-solid fa-database"></i> All Spots
          </router-link>
          <router-link v-if="auth.isAdmin" to="/admin/guides">
            <i class="fa-solid fa-book-bookmark"></i> All Guides
          </router-link>
        </div>

        <div class="nav-user">
          <template v-if="auth.isLoggedIn">
            <div class="user-profile">
              <i class="fa-solid fa-circle-user profile-icon"></i>
              <div class="user-meta">
                <span class="user-name">{{ auth.user.name }}</span>
                <span class="user-role">{{ auth.user.role }}</span>
              </div>
            </div>
            <button class="btn-secondary" @click="logout">
              <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
          </template>

          <template v-else>
            <router-link to="/login" class="btn-nav-outline">
              <i class="fa-solid fa-right-to-bracket"></i> Login
            </router-link>
            <router-link to="/register" class="btn-nav-primary">
              <i class="fa-solid fa-user-plus"></i> Register
            </router-link>
          </template>
        </div>
      </nav>
    </header>

    <div v-if="flash.message" :class="['flash', flash.type]">
      {{ flash.message }}
    </div>

    <main :class="['page-content', { 'no-padding': isAuthPage }]">
      <router-view />
    </main>
  </div>
</template>

<script>
import { useAuthStore } from "./stores/auth.js";
import { useFlashStore } from "./stores/flash.js";
import { useRoute } from "vue-router";
import { computed } from "vue";

export default {
  name: "App",
  setup() {
    const auth = useAuthStore();
    const flash = useFlashStore();
    const route = useRoute();

    // Check if the current path is login or register
    const isAuthPage = computed(() => {
      return route.path === "/login" || route.path === "/register";
    });

    return { auth, flash, isAuthPage };
  },
  methods: {
    async logout() {
      await this.auth.logout();
      this.$router.push("/login");
    },
  },
};
</script>

<style>
/* 1. Define the palette */
:root {
  --primary: #2563eb;
  --primary-hover: #1d4ed8;
  --bg: #f8fafc;
  --surface: #ffffff;
  --text-main: #0f172a;
  --text-muted: #475569;
  --border: #e2e8f0;
  --radius: 12px;
  --max-width: 1100px;
}

/* 2. Apply it globally */
*,
*::before,
*::after {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family:
    "Inter",
    system-ui,
    -apple-system,
    sans-serif;
  background: var(--bg);
  color: var(--text-main);
  line-height: 1.5;
}

a {
  color: inherit;
  text-decoration: none;
}

/* 3. Layout structure */
.navbar-wrapper {
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
  position: sticky;
  top: 0;
  z-index: 50;
}

.navbar {
  display: flex;
  align-items: center;
  padding: 1rem 2rem;
  gap: 24px;
  width: 100%;
  max-width: var(--max-width);
  margin: 0 auto;
}

.navbar .brand {
  font-weight: 800;
  font-size: 1.5rem;
  color: var(--primary);
  margin-right: auto;
  letter-spacing: -0.02em;
}

.nav-links {
  display: flex;
  gap: 2rem;
}
.nav-links a {
  color: var(--text-muted);
  font-weight: 500;
  font-size: 0.95rem;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}
.nav-links a:hover,
.nav-links a.router-link-active {
  color: var(--primary);
}

.nav-links a i,
.nav-user i {
  margin-right: 8px;
  font-size: 0.9em;
}

.nav-user {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}
.nav-user .user-info {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 4px 12px;
  background: #f1f5f9;
  border-radius: 999px;
  margin-right: 8px;
}

.profile-icon {
  font-size: 1.25rem;
  color: var(--primary);
}

.user-meta {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.user-name {
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--text-main);
}

.user-role {
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
  color: var(--text-muted);
  letter-spacing: 0.02em;
}

.nav-user button {
  margin-left: 4px;
}

.btn-nav-outline {
  padding: 0.5rem 1rem;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
}
.btn-nav-outline:hover {
  background: #f1f5f9;
}

.btn-nav-primary {
  padding: 0.5rem 1rem;
  background: var(--primary);
  color: white;
  border-radius: var(--radius);
  font-weight: 600;
  font-size: 0.9rem;
  transition: background 0.2s;
}
.btn-nav-primary:hover {
  background: var(--primary-hover);
}

/* 4. Page Content */
.page-content {
  width: 100%;
  max-width: var(--max-width);
  margin: 0 auto;
  padding: 2rem;
}

.page-content.no-padding {
  padding: 0;
  max-width: 100%;
}

/* 5. Modern Buttons */
button {
  padding: 0.6rem 1.2rem;
  border: none;
  border-radius: var(--radius);
  background: var(--text-main);
  color: #fff;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.9rem;
  transition: all 0.2s;
}
button:hover {
  background: #334155;
}

.btn-secondary {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-main);
}
.btn-secondary:hover {
  background: #f1f5f9;
}

/* 6. Inputs */
input,
textarea,
select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  background: var(--surface);
  font-family: inherit;
  font-size: 0.95rem;
}
input:focus {
  border-color: var(--primary);
  outline: none;
}

/* 7. Flash & Badges */
.flash {
  padding: 1rem 2rem;
  text-align: center;
  font-weight: 500;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
}
.flash.success {
  background: #dcfce7;
  color: #166534;
}
.flash.error {
  background: #fee2e2;
  color: #991b1b;
}

.badge {
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
}
.badge.open {
  background: #dcfce7;
  color: #166534;
}
.badge.closed {
  background: #fee2e2;
  color: #991b1b;
}
.badge.draft {
  background: #fef3c7;
  color: #92400e;
}
</style>
