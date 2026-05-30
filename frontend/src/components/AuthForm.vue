<template>
	<div class="auth-form">
		<div class="auth-tabs">
			<button :class="{ active: mode === 'login' }" @click="mode = 'login'">Login</button>
			<button :class="{ active: mode === 'register' }" @click="mode = 'register'">Register</button>
		</div>

		<div v-if="mode === 'login'">
			<h2>Login</h2>
			<input v-model="loginEmail" type="email" placeholder="Email" />
			<input v-model="loginPassword" type="password" placeholder="Password" />
			<button @click="$emit('login', loginEmail, loginPassword)">Login</button>
		</div>

		<div v-else>
			<h2>Register</h2>
			<input v-model="registerName" type="text" placeholder="Name" />
			<input v-model="registerEmail" type="email" placeholder="Email" />
			<input v-model="registerPassword" type="password" placeholder="Password (min 6)" />
			<button @click="$emit('register', registerName, registerEmail, registerPassword)">Register</button>
		</div>
	</div>
</template>

<script>
export default {
	name: "AuthForm",
	data() {
		return {
			mode: "login",
			loginEmail: "",
			loginPassword: "",
			registerName: "",
			registerEmail: "",
			registerPassword: "",
		};
	},
};
</script>
