<template>
	<div class="spot-card">
		<div class="thumbnail-wrapper">
			<img :src="spot.thumbnail" :alt="spot.title" class="spot-thumbnail" />
		</div>

		<div class="spot-body">
			<div class="meta-header">
				<span class="category-tag">{{ spot.category }}</span>
				<span class="status-indicator" :class="spot.status"> <span class="status-dot"></span> {{ spot.status }} </span>
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
					<i class="fa-solid fa-ticket"></i>
					<span>{{ spot.price === 0 ? "Free" : "Rp " + Number(spot.price).toLocaleString() }}</span>
				</div>
			</div>
		</div>

		<div class="spot-footer">
			<router-link :to="`/spots/${spot.id}`" class="btn-view"> View Details </router-link>

			<button v-if="isAdmin" class="btn-delete" @click="$emit('delete', spot.id)" title="Delete Spot">
				<i class="fa-solid fa-trash"></i>
			</button>
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
	border-radius: 20px; /* Softer, larger radius */
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition:
		transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
		box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.spot-card:hover {
	transform: translateY(-4px);
	box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08);
	border-color: #cbd5e1; /* Subtle border change instead of heavy primary color */
}

/* ── Image ── */
.thumbnail-wrapper {
	height: 220px; /* Slightly taller for better aspect ratio */
	background: #f1f5f9;
}

.spot-thumbnail {
	width: 100%;
	height: 100%;
	object-fit: cover;
}

/* ── Body ── */
.spot-body {
	padding: 24px 24px 16px 24px;
	flex: 1;
}

.meta-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 12px;
}

.category-tag {
	color: var(--text-muted);
	font-weight: 700;
	font-size: 0.75rem;
	text-transform: uppercase;
	letter-spacing: 0.05em;
}

/* Sleek Status Pill */
.status-indicator {
	display: flex;
	align-items: center;
	gap: 6px;
	font-size: 0.7rem;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	padding: 4px 10px;
	border-radius: 999px;
	background: #f8fafc;
	color: var(--text-muted);
}

.status-dot {
	width: 6px;
	height: 6px;
	border-radius: 50%;
	background: currentColor;
}

.status-indicator.open {
	background: #ecfdf5;
	color: #059669;
}
.status-indicator.closed {
	background: #fef2f2;
	color: #dc2626;
}
.status-indicator.draft {
	background: #fffbeb;
	color: #d97706;
}

.spot-title {
	margin: 0 0 16px 0;
	font-size: 1.35rem;
	font-weight: 800;
	color: var(--text-main);
	line-height: 1.3;
}

/* ── Info List ── */
.spot-info {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.info-item {
	display: flex;
	align-items: center;
	gap: 12px;
	font-size: 0.9rem;
	color: var(--text-muted);
}

.info-item i {
	width: 16px;
	text-align: center;
	color: #cbd5e1;
}

.info-item.price {
	font-weight: 600;
	color: var(--text-main);
}

.info-item.price i {
	color: var(--text-muted);
}

/* ── Footer ── */
.spot-footer {
	padding: 0 24px 24px 24px;
	display: flex;
	gap: 12px;
}

.btn-view {
	flex: 1;
	background: #f8fafc;
	color: var(--text-main);
	text-align: center;
	padding: 12px;
	border-radius: 12px;
	font-weight: 600;
	font-size: 0.9rem;
	transition: all 0.2s;
}

.btn-view:hover {
	background: var(--primary);
	color: white;
}

.btn-delete {
	background: #fef2f2;
	color: #ef4444;
	border: none;
	border-radius: 12px;
	width: 44px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: background 0.2s;
}

.btn-delete:hover {
	background: #fee2e2;
}
</style>
