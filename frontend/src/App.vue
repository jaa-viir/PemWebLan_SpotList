<template>
	<div id="app">
		<header class="navbar-wrapper">
			<nav class="navbar">
				<router-link to="/" class="brand">SpotList</router-link>

				<div class="nav-links">
					<router-link to="/">Spots</router-link>
					<router-link v-if="auth.isLoggedIn" to="/my-registrations">My Registrations</router-link>
					<router-link v-if="auth.isAdmin" to="/admin/spots">Admin Panel</router-link>
				</div>

				<div class="nav-user">
					<template v-if="auth.isLoggedIn">
						<span class="user-info">{{ auth.user.name }} · {{ auth.user.role }}</span>
						<button class="btn-secondary" @click="logout">Logout</button>
					</template>
					<template v-else>
						<router-link to="/login">Login</router-link>
						<router-link to="/register" class="btn-primary-link">Register</router-link>
					</template>
				</div>
			</nav>
		</header>

		<!-- Flash message — any page can trigger this via the flash store -->
		<div v-if="flash.message" :class="['flash', flash.type]">
			{{ flash.message }}
		</div>

		<!-- All pages render here -->
		<main class="page-content">
			<router-view />
		</main>
	</div>
</template>

<script>
import { useAuthStore } from "./stores/auth.js";
import { useFlashStore } from "./stores/flash.js";

export default {
	name: "App",
	setup() {
		const auth = useAuthStore();
		const flash = useFlashStore();
		return { auth, flash };
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
	transition: color 0.2s;
}
.nav-links a:hover,
.nav-links a.router-link-active {
	color: var(--primary);
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

/* 4. Page Content */
.page-content {
	width: 100%;
	max-width: var(--max-width);
	margin: 0 auto;
	padding: 2rem;
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
