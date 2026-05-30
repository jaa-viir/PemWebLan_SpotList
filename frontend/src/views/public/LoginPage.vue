<template>
	<div class="auth-page">
		<div class="auth-card">
			<h1>Login</h1>
			<p>Don't have an account? <router-link to="/register">Register here</router-link></p>

			<div class="form-group">
				<input v-model="email" type="email" placeholder="Email Address" />
				<input v-model="password" type="password" placeholder="Password" @keyup.enter="submit" />
			</div>

			<p v-if="error" class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

			<button @click="submit" :disabled="loading" class="btn-submit">
				<span v-if="loading"><i class="fa-solid fa-spinner fa-spin"></i> Logging in...</span>
				<span v-else>Login</span>
			</button>

			<div class="dev-shortcuts">
				<p class="dev-label">Dev Shortcuts</p>
				<div class="shortcut-row">
					<button @click="autoLogin('admin@spotlist.com', 'password123')" class="btn-dev">Admin</button>
					<button @click="autoLogin('Usman.isk03@spotlist.com', 'password123')" class="btn-dev">User</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { useAuthStore } from "../../stores/auth.js";
import { useFlashStore } from "../../stores/flash.js";

export default {
	name: "LoginPage",
	setup() {
		return { auth: useAuthStore(), flash: useFlashStore() };
	},
	data() {
		return { email: "", password: "", loading: false, error: null };
	},
	methods: {
		async submit() {
			this.error = null;
			this.loading = true;
			const res = await this.auth.login(this.email, this.password);
			this.loading = false;
			if (!res.success) {
				this.error = res.message;
				return;
			}
			this.flash.success("Login berhasil!");
			this.$router.push("/");
		},
		async autoLogin(email, password) {
			this.email = email;
			this.password = password;
			await this.submit();
		},
	},
};
</script>

<style scoped>
.auth-page {
	display: flex;
	justify-content: center;
	padding: 60px 20px;
}

.auth-card {
	width: 100%;
	max-width: 400px;
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: var(--radius);
	padding: 32px;
	box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.auth-card h1 {
	font-size: 1.5rem;
	margin-bottom: 8px;
	color: var(--text-main);
}

.auth-card p {
	margin-bottom: 24px;
	font-size: 0.9rem;
	color: var(--text-muted);
}

.auth-card a {
	color: var(--primary);
	font-weight: 600;
}

.form-group {
	margin-bottom: 16px;
}

.form-error {
	color: #991b1b;
	background: #fee2e2;
	padding: 10px;
	border-radius: var(--radius);
	font-size: 0.85rem;
	margin-bottom: 16px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.btn-submit {
	width: 100%;
	padding: 0.75rem;
	font-weight: 600;
	background: var(--primary);
}

.btn-submit:hover {
	background: var(--primary-hover);
}

.dev-shortcuts {
	margin-top: 32px;
	padding-top: 20px;
	border-top: 1px dashed var(--border);
	text-align: center;
}

.dev-label {
	font-size: 0.75rem !important;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	color: #94a3b8 !important;
	margin-bottom: 12px !important;
}

.shortcut-row {
	display: flex;
	gap: 10px;
}

.btn-dev {
	flex: 1;
	background: #f1f5f9;
	color: #475569;
	font-size: 0.8rem;
	padding: 8px;
}

.btn-dev:hover {
	background: #e2e8f0;
	color: #1e293b;
}
</style>
