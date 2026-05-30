<template>
	<div class="spot-card">
		<div class="thumbnail-wrapper">
			<img :src="spot.thumbnail" :alt="spot.title" class="spot-thumbnail" />
			<div class="badge-overlay">
				<span class="badge" :class="spot.status">{{ spot.status }}</span>
			</div>
		</div>

		<div class="spot-body">
			<div class="meta-row">
				<span class="category-tag">{{ spot.category }}</span>
			</div>

			<h3 class="spot-title">{{ spot.title }}</h3>

			<div class="spot-info">
				<div class="info-item">
					<i class="fa-solid fa-location-dot"></i>
					<span>{{ spot.location }}</span>
				</div>
				<div class="info-item">
					<i class="fa-solid fa-calendar-days"></i>
					<span>{{ formatDate(spot.event_date) }}</span>
				</div>
				<div class="info-item price">
					<i class="fa-solid fa-tag"></i>
					<span>{{ spot.price === 0 ? "Free" : "Rp" + Number(spot.price).toLocaleString() }}</span>
				</div>
			</div>
		</div>

		<div class="spot-actions">
			<router-link :to="`/spots/${spot.id}`" class="btn-link"> View Details <i class="fa-solid fa-arrow-right"></i> </router-link>

			<div v-if="isAdmin" class="admin-controls">
				<button class="btn-icon" @click="$emit('delete', spot.id)">
					<i class="fa-solid fa-trash"></i>
				</button>
			</div>
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
			return new Date(dt).toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" });
		},
	},
};
</script>

<style scoped>
.spot-card {
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: 16px; /* Slightly more rounded */
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition: all 0.3s ease;
}

.spot-card:hover {
	border-color: var(--primary);
	box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.thumbnail-wrapper {
	position: relative;
	height: 200px;
	background: #e2e8f0; /* Placeholder color */
}

.spot-thumbnail {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

.badge-overlay {
	position: absolute;
	top: 16px;
	left: 16px;
}

.badge {
	background: rgba(255, 255, 255, 0.9);
	backdrop-filter: blur(4px);
	color: var(--text-main);
	padding: 6px 12px;
	border-radius: 8px;
	font-size: 0.7rem;
	font-weight: 800;
	letter-spacing: 0.05em;
	text-transform: uppercase;
}

.spot-body {
	padding: 1.5rem;
	flex: 1;
}

.category-tag {
	color: var(--primary);
	font-weight: 600;
	font-size: 0.75rem;
	text-transform: uppercase;
}

.spot-title {
	margin: 12px 0;
	font-size: 1.25rem;
	font-weight: 800;
	color: var(--text-main);
	line-height: 1.3;
}

.spot-info {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.info-item {
	display: flex;
	align-items: center;
	gap: 10px;
	font-size: 0.9rem;
	color: var(--text-muted);
}

.info-item i {
	width: 16px;
	text-align: center;
	color: #cbd5e1;
}

.spot-actions {
	padding: 1rem 1.5rem;
	border-top: 1px solid var(--border);
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.btn-link {
	font-weight: 600;
	color: var(--primary);
	font-size: 0.9rem;
	display: flex;
	align-items: center;
	gap: 8px;
}

.btn-icon {
	background: none;
	border: none;
	color: #94a3b8;
	padding: 8px;
	cursor: pointer;
}

.btn-icon:hover {
	color: #ef4444;
}
</style>
