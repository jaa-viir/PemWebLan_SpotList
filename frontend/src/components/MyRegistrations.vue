<template>
	<div class="my-registrations">
		<h2>My Registrations</h2>

		<p v-if="loading">Loading...</p>
		<p v-else-if="error">{{ error }}</p>
		<p v-else-if="registrations.length === 0">You haven't registered for any events yet.</p>

		<div v-else>
			<div v-for="reg in registrations" :key="reg.id" class="reg-card">
				<h3>{{ reg.spot?.title }}</h3>
				<p>📍 {{ reg.spot?.location }}</p>
				<p>📅 {{ formatDate(reg.spot?.event_date) }}</p>
				<p>
					Status: <span :class="reg.status">{{ reg.status }}</span>
				</p>

				<button v-if="reg.status !== 'cancelled'" @click="$emit('cancel', reg.spot_id)" class="btn-danger">Cancel</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: "MyRegistrations",
	props: {
		registrations: { type: Array, required: true },
		loading: { type: Boolean, default: false },
		error: { type: String, default: null },
	},
	methods: {
		formatDate(dt) {
			if (!dt) return "-";
			return new Date(dt).toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" });
		},
	},
};
</script>
