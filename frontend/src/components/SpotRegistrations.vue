<template>
	<div class="spot-registrations">
		<button @click="$emit('back')">← Back</button>
		<h2>Registrations for Spot #{{ spotId }}</h2>

		<p v-if="loading">Loading...</p>
		<p v-else-if="error">{{ error }}</p>
		<p v-else-if="registrations.length === 0">No registrations yet.</p>

		<table v-else class="reg-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Email</th>
					<th>Status</th>
					<th>Registered At</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(reg, i) in registrations" :key="reg.id">
					<td>{{ i + 1 }}</td>
					<td>{{ reg.user?.name }}</td>
					<td>{{ reg.user?.email }}</td>
					<td>
						<span :class="reg.status">{{ reg.status }}</span>
					</td>
					<td>{{ formatDate(reg.created_at) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</template>

<script>
export default {
	name: "SpotRegistrations",
	props: {
		spotId: { type: Number, required: true },
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
