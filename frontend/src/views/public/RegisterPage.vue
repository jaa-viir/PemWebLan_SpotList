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

			<button @click="submit" :disabled="loading" class="btn-submit btn-primary">
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
/* 1. Background Image and Layout */
.auth-page {
	position: relative;
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: calc(100vh - 70px);
	padding: 40px 20px;
	overflow: hidden;
}

/* 2. The Blurred Background Layer */
.auth-page::before {
	content: "";
	position: absolute;
	top: -20px;
	left: -20px;
	right: -20px;
	bottom: -20px;
	background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("@/assets/auth-bg.jpg");
	background-size: cover;
	background-position: center;
	background-attachment: fixed;
	filter: blur(8px);
	z-index: 0;
}

/* 3. The Card */
.auth-card {
	position: relative;
	z-index: 1;
	width: 100%;
	max-width: 420px;
	background: var(--surface);
	border-radius: 16px;
	padding: 40px;
	box-shadow:
		0 20px 25px -5px rgba(0, 0, 0, 0.1),
		0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.auth-card h1 {
	font-size: 1.75rem;
	font-weight: 800;
	margin-bottom: 8px;
	color: var(--text-main);
}

.auth-card p {
	margin-bottom: 24px;
	font-size: 0.95rem;
	color: var(--text-muted);
}

.auth-card a {
	color: var(--primary);
	font-weight: 700;
}

/* 4. Form Spacing */
.form-group {
	display: flex;
	flex-direction: column;
	gap: 16px;
	margin-bottom: 24px;
}

.form-error {
	color: #991b1b;
	background: #fee2e2;
	padding: 12px;
	border-radius: var(--radius);
	font-size: 0.85rem;
	margin-bottom: 20px;
	display: flex;
	align-items: center;
	gap: 8px;
}

.btn-submit {
	width: 100%;
	padding: 0.85rem;
	font-size: 1rem;
	font-weight: 600;
	background: var(--primary);
	border: none;
	border-radius: var(--radius);
	color: white;
	cursor: pointer;
}

.btn-submit:hover {
	background: var(--primary-hover);
}

@media (max-width: 640px) {
	.auth-card {
		padding: 32px 24px;
	}
}
</style>
