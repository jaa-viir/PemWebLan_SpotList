<template>
	<div class="spot-card">
		<img v-if="spot.thumbnail" :src="spot.thumbnail" :alt="spot.title" class="spot-thumbnail" />

		<div class="spot-info">
			<span class="spot-category">{{ spot.category }}</span>
			<h3>{{ spot.title }}</h3>
			<p>📍 {{ spot.location }}</p>
			<p>📅 {{ formatDate(spot.event_date) }}</p>
			<p>💰 {{ spot.price === 0 ? "Free" : "Rp" + spot.price.toLocaleString() }}</p>
			<span class="spot-status" :class="spot.status">{{ spot.status }}</span>
			<p v-if="spot.participant_limit !== undefined">👥 Limit: {{ spot.participant_limit ?? "Unlimited" }}</p>
		</div>

		<div class="spot-actions">
			<button @click="$emit('view', spot.id)">View Detail</button>

			<template v-if="isAdmin">
				<button @click="$emit('edit', spot)">Edit</button>
				<button @click="$emit('delete', spot.id)" class="btn-danger">Delete</button>
				<button @click="$emit('view-registrations', spot.id)">Registrations</button>
			</template>
		</div>
	</div>
</template>

<script>
export default {
	name: "SpotCard",
	props: {
		spot: { type: Object, required: true },
		isAdmin: { type: Boolean, default: false },
	},
	methods: {
		formatDate(dt) {
			if (!dt) return "-";
			return new Date(dt).toLocaleString("id-ID", {
				dateStyle: "medium",
				timeStyle: "short",
			});
		},
	},
};
</script>
