<template>
	<div class="admin-spots-page">
		<header class="page-header">
			<div class="header-text">
				<h1>Manage Spots</h1>
				<p class="subtitle">Create, edit, and oversee all curated events.</p>
			</div>

			<router-link to="/admin/spots/create">
				<button class="btn-primary"><i class="fa-solid fa-plus"></i> Create New Spot</button>
			</router-link>
		</header>

		<div v-if="loading" class="state-container">
			<i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
			<p>Loading spots dashboard...</p>
		</div>

		<p v-else-if="error" class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

		<div v-else-if="spots.length === 0" class="state-container state-empty">
			<i class="fa-solid fa-folder-open state-icon"></i>
			<p>No spots have been created yet.</p>
			<router-link to="/admin/spots/create" style="margin-top: 16px; display: inline-block">
				<button class="btn-primary">Create Your First Spot</button>
			</router-link>
		</div>

		<div v-else class="spot-grid">
			<SpotCard v-for="spot in spots" :key="spot.id" :spot="spot" :isAdmin="true" @delete="deleteSpot" />
		</div>
	</div>
</template>

<script>
import SpotCard from "../../components/SpotCard.vue";
import { useFlashStore } from "../../stores/flash.js";
import { apiAdminGetSpots, apiDeleteSpot } from "../../services/api.js";

export default {
	name: "AdminSpotsPage",
	components: { SpotCard },
	setup() {
		return { flash: useFlashStore() };
	},
	data() {
		return { spots: [], loading: false, error: null };
	},
	async mounted() {
		this.loading = true;
		const res = await apiAdminGetSpots().catch(() => null);
		this.loading = false;
		if (!res?.success) {
			this.error = "Failed to load spots.";
			return;
		}
		this.spots = res.data?.data ?? res.data ?? [];
	},
	methods: {
		async deleteSpot(id) {
			if (!confirm("Are you sure you want to delete this spot? This cannot be undone.")) return;
			const res = await apiDeleteSpot(id);
			if (!res.success) return this.flash.error(res.message || "Delete failed.");
			this.flash.success("Spot successfully deleted.");

			// Remove from UI seamlessly without reloading
			this.spots = this.spots.filter((s) => s.id !== id);
		},
	},
};
</script>

<style scoped>
.admin-spots-page {
	animation: fadeIn 0.3s ease-out;
}

/* ── Header ── */
.page-header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	margin-bottom: 32px;
	border-bottom: 1px solid var(--border);
	padding-bottom: 24px;
	flex-wrap: wrap;
	gap: 16px;
}

.header-text h1 {
	font-size: 2rem;
	font-weight: 800;
	color: var(--text-main);
	margin-bottom: 4px;
}

.subtitle {
	color: var(--text-muted);
	font-size: 0.95rem;
}

/* ── Buttons ── */
.btn-primary {
	background: var(--primary);
	color: #fff;
	display: flex;
	align-items: center;
	gap: 8px;
}

.btn-primary:hover {
	background: var(--primary-hover);
}

/* ── Grid ── */
.spot-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
	gap: 24px;
}

/* ── States (Empty, Loading, Error) ── */
.state-container {
	text-align: center;
	padding: 80px 24px;
	background: var(--surface);
	border: 1px dashed var(--border);
	border-radius: var(--radius);
	color: var(--text-muted);
}

.state-icon {
	font-size: 2.5rem;
	margin-bottom: 16px;
	color: #cbd5e1;
}

.loading-icon {
	color: var(--primary);
}

.error-text {
	color: #991b1b;
	background: #fee2e2;
	padding: 16px;
	border-radius: var(--radius);
	display: flex;
	align-items: center;
	gap: 8px;
}

@keyframes fadeIn {
	from {
		opacity: 0;
		transform: translateY(5px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@media (max-width: 600px) {
	.page-header {
		flex-direction: column;
		align-items: flex-start;
	}
}
</style>
