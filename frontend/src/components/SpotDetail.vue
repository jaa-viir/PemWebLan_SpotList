<template>
	<div class="spot-detail">
		<button @click="$emit('back')">← Back</button>

		<div v-if="loading">Loading...</div>
		<div v-else-if="error">{{ error }}</div>

		<div v-else-if="spot">
			<img v-if="spot.thumbnail" :src="spot.thumbnail" :alt="spot.title" class="detail-thumbnail" />

			<h1>{{ spot.title }}</h1>
			<span class="spot-category">{{ spot.category }}</span>
			<span class="spot-status" :class="spot.status">{{ spot.status }}</span>

			<p style="margin: 16px 0">{{ spot.description }}</p>

			<table class="detail-table">
				<tr>
					<td>📍 Location</td>
					<td>{{ spot.location }}</td>
				</tr>
				<tr>
					<td>📅 Date</td>
					<td>{{ formatDate(spot.event_date) }}</td>
				</tr>
				<tr>
					<td>🏢 Organizer</td>
					<td>{{ spot.organizer }}</td>
				</tr>
				<tr>
					<td>💰 Price</td>
					<td>{{ spot.price === 0 ? "Free" : "Rp" + spot.price.toLocaleString() }}</td>
				</tr>
				<tr>
					<td>👥 Capacity</td>
					<td>{{ spot.participant_limit ?? "Unlimited" }}</td>
				</tr>
				<tr v-if="spot.registration_link">
					<td>🔗 Link</td>
					<td>
						<a :href="spot.registration_link" target="_blank">{{ spot.registration_link }}</a>
					</td>
				</tr>
			</table>

			<!-- Member actions -->
			<div v-if="isLoggedIn && !isAdmin" class="member-actions">
				<div v-if="spot.status === 'open'">
					<button v-if="!alreadyRegistered" @click="$emit('register', spot.id)">Register for this Event</button>
					<div v-else>
						<p>✅ You are registered ({{ registrationStatus }})</p>
						<button v-if="registrationStatus !== 'cancelled'" @click="$emit('cancel', spot.id)" class="btn-danger">Cancel Registration</button>
					</div>
				</div>
				<p v-else>Registration is {{ spot.status }}.</p>
			</div>

			<!-- Admin actions -->
			<div v-if="isAdmin" style="margin-top: 20px">
				<button @click="$emit('view-registrations', spot.id)">View All Registrations</button>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: "SpotDetail",
	props: {
		spot: { type: Object, default: null },
		loading: { type: Boolean, default: false },
		error: { type: String, default: null },
		isLoggedIn: { type: Boolean, default: false },
		isAdmin: { type: Boolean, default: false },
		alreadyRegistered: { type: Boolean, default: false },
		registrationStatus: { type: String, default: null },
	},
	methods: {
		formatDate(dt) {
			if (!dt) return "-";
			return new Date(dt).toLocaleString("id-ID", { dateStyle: "long", timeStyle: "short" });
		},
	},
};
</script>
