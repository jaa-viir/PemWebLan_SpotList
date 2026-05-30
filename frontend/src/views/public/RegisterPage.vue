<template>
	<div class="auth-page">
		<div class="auth-card">
			<h1>Create Account</h1>
			<p>Already have an account? <router-link to="/login">Login here</router-link></p>

			<div class="form-group">
				<input v-model="name" type="text" placeholder="Full Name" />
				<input v-model="email" type="email" placeholder="Email Address" />
				<input v-model="password" type="password" placeholder="Password (min 6 characters)" @keyup.enter="submit" />
			</div>

			<p v-if="error" class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

			<button @click="submit" :disabled="loading" class="btn-submit">
				<span v-if="loading"><i class="fa-solid fa-spinner fa-spin"></i> Registering...</span>
				<span v-else>Create Account</span>
			</button>
		</div>
	</div>
</template>

<script>
import { useAuthStore } from "../../stores/auth.js";
import { useFlashStore } from "../../stores/flash.js";

export default {
	name: "RegisterPage",
	setup() {
		return { auth: useAuthStore(), flash: useFlashStore() };
	},
	data() {
		return { name: "", email: "", password: "", loading: false, error: null };
	},
	methods: {
		async submit() {
			this.error = null;
			this.loading = true;
			const res = await this.auth.register(this.name, this.email, this.password);
			this.loading = false;
			if (!res.success) {
				this.error = res.message || Object.values(res.errors || {})[0]?.[0] || "Registration failed.";
				return;
			}
			this.flash.success("Register berhasil! Silakan login.");
			this.$router.push("/login");
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
</style>
