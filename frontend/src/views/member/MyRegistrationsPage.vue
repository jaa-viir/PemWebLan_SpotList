<template>
	<div class="reg-page">
		<header class="page-header">
			<h1>My Registrations</h1>
			<p class="subtitle">Manage your upcoming events and status.</p>
		</header>

		<div v-if="loading" class="state-container">
			<i class="fa-solid fa-spinner fa-spin state-icon"></i>
			<p>Loading your registrations...</p>
		</div>

		<p v-else-if="error" class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

		<div v-else-if="registrations.length === 0" class="state-container state-empty">
			<i class="fa-solid fa-ticket state-icon"></i>
			<p>No registrations yet. Ready to find something fun?</p>
			<router-link to="/" style="margin-top: 16px; display: inline-block">
				<button>Browse Events</button>
			</router-link>
		</div>

		<div v-else class="reg-list">
			<div v-for="reg in registrations" :key="reg.id" class="reg-card">
				<div class="reg-thumb">
					<img v-if="reg.spot?.thumbnail" :src="reg.spot.thumbnail" alt="" class="thumb-img" />
					<div v-else class="no-img"><i class="fa-solid fa-image"></i></div>
				</div>

				<div class="reg-card-body">
					<div class="reg-header">
						<h3>{{ reg.spot?.title }}</h3>
						<span class="badge" :class="reg.status">{{ reg.status }}</span>
					</div>

					<div class="reg-meta">
						<span><i class="fa-solid fa-location-dot"></i> {{ reg.spot?.location }}</span>
						<span><i class="fa-solid fa-calendar-days"></i> {{ formatDate(reg.spot?.event_date) }}</span>
					</div>
				</div>

				<div class="reg-actions">
					<router-link :to="`/spots/${reg.spot_id}`">
						<button class="btn-secondary"><i class="fa-solid fa-eye"></i> View</button>
					</router-link>

					<button v-if="reg.status !== 'cancelled'" class="btn-danger btn-icon-only" @click="cancel(reg.spot_id)" :disabled="cancellingId === reg.spot_id" title="Cancel Registration">
						<i v-if="cancellingId === reg.spot_id" class="fa-solid fa-spinner fa-spin"></i>
						<i v-else class="fa-solid fa-trash"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { useFlashStore } from "../../stores/flash.js";
import { apiMyRegistrations, apiCancelRegistration } from "../../services/api.js";

export default {
	name: "MyRegistrationsPage",
	setup() {
		return { flash: useFlashStore() };
	},
	data() {
		return { registrations: [], loading: false, error: null, cancellingId: null };
	},
	async mounted() {
		this.loading = true;
		const res = await apiMyRegistrations().catch(() => null);
		this.loading = false;
		if (!res?.success) {
			this.error = "Failed to load registrations.";
			return;
		}
		this.registrations = res.data ?? [];
	},
	methods: {
		formatDate(dt) {
			if (!dt) return "-";
			return new Date(dt).toLocaleString("id-ID", { dateStyle: "medium", timeStyle: "short" });
		},
		async cancel(spotId) {
			if (!confirm("Are you sure you want to cancel your registration?")) return;
			this.cancellingId = spotId;
			const res = await apiCancelRegistration(spotId);
			this.cancellingId = null;
			if (!res.success) return this.flash.error(res.message);
			this.flash.success("Registration cancelled successfully.");

			// Refresh list seamlessly
			const updated = await apiMyRegistrations().catch(() => null);
			this.registrations = updated?.data ?? [];
		},
	},
};
</script>

<style scoped>
.reg-page {
	max-width: 900px; /* Slightly wider to let the card breathe */
	margin: 0 auto;
	animation: fadeIn 0.3s ease-out;
}

.page-header {
	margin-bottom: 32px;
	border-bottom: 1px solid var(--border);
	padding-bottom: 16px;
}

.page-header h1 {
	font-size: 2rem;
	font-weight: 800;
	color: var(--text-main);
	margin-bottom: 4px;
}

.subtitle {
	color: var(--text-muted);
}

.reg-list {
	display: flex;
	flex-direction: column;
	gap: 16px;
}

/* ── The Card ── */
.reg-card {
	display: flex;
	align-items: center;
	gap: 24px;
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: var(--radius);
	padding: 20px;
	box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
	transition:
		box-shadow 0.2s,
		border-color 0.2s;
}

.reg-card:hover {
	border-color: #cbd5e1;
	box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

/* ── Thumbnail Fixes ── */
.reg-thumb {
	width: 80px;
	height: 80px;
	border-radius: 8px;
	overflow: hidden;
	flex-shrink: 0;
	background: #f1f5f9;
	border: 1px solid var(--border);
}

.thumb-img {
	width: 100%;
	height: 100%;
	object-fit: cover;
	/* Hides broken image icons/text gracefully */
	color: transparent;
}

.no-img {
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	color: #cbd5e1;
	font-size: 1.5rem;
}

/* ── Content Body ── */
.reg-card-body {
	flex: 1; /* Pushes the actions strictly to the right */
}

.reg-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 8px;
}

.reg-header h3 {
	margin: 0;
	font-size: 1.15rem;
	font-weight: 700;
	color: var(--text-main);
}

.reg-meta {
	display: flex;
	flex-wrap: wrap;
	gap: 16px;
	font-size: 0.85rem;
	color: var(--text-muted);
}

.reg-meta i {
	color: #94a3b8;
	margin-right: 4px;
}

/* ── Actions ── */
.reg-actions {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-left: auto;
}

.btn-icon-only {
	padding: 0.6rem 0.8rem;
}

/* ── States ── */
.state-container {
	text-align: center;
	padding: 80px 20px;
	background: var(--surface);
	border: 1px dashed var(--border);
	border-radius: var(--radius);
	color: var(--text-muted);
}

.state-icon {
	font-size: 2.5rem;
	margin-bottom: 1rem;
	color: #cbd5e1;
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

/* Responsive constraints */
@media (max-width: 640px) {
	.reg-card {
		flex-direction: column;
		align-items: flex-start;
		gap: 16px;
	}
	.reg-actions {
		width: 100%;
		justify-content: space-between;
		margin-left: 0;
	}
}
</style>
