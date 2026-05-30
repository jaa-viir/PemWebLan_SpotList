<template>
	<div class="spot-list">
		<p v-if="loading">Loading spots...</p>
		<p v-else-if="error">{{ error }}</p>
		<p v-else-if="spots.length === 0">No spots found.</p>

		<div v-else class="spot-grid">
			<SpotCard
				v-for="spot in spots"
				:key="spot.id"
				:spot="spot"
				:isAdmin="isAdmin"
				@view="$emit('view', $event)"
				@edit="$emit('edit', $event)"
				@delete="$emit('delete', $event)"
				@view-registrations="$emit('view-registrations', $event)"
			/>
		</div>
	</div>
</template>

<script>
import SpotCard from "./SpotCard.vue";

export default {
	name: "SpotList",
	components: { SpotCard },
	props: {
		spots: { type: Array, required: true },
		loading: { type: Boolean, default: false },
		error: { type: String, default: null },
		isAdmin: { type: Boolean, default: false },
	},
};
</script>
