<template>
	<div class="spots-page">
		<div class="page-header">
			<div class="title-area">
				<h1>Upcoming Events</h1>
				<p class="subtitle">Curated gatherings, workshops, and scenes handpicked for you.</p>
			</div>

			<div class="search-wrapper">
				<i class="fa-solid fa-magnifying-glass search-icon"></i>
				<input v-model="search" placeholder="Search title, category, location..." class="search-input" />
			</div>
		</div>

		<div v-if="loading" class="state-container">
			<i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
			<p>Curating the best local spots for you...</p>
		</div>

		<div v-else-if="error" class="state-container state-error">
			<i class="fa-solid fa-triangle-exclamation state-icon"></i>
			<p>{{ error }}</p>
		</div>

		<div v-else-if="filtered.length === 0" class="state-container state-empty">
			<i class="fa-solid fa-calendar-xmark state-icon"></i>
			<p>No spots match your search. Try looking for something else!</p>
		</div>

		<div v-else class="spot-grid">
			<SpotCard v-for="spot in filtered" :key="spot.id" :spot="spot" :isAdmin="false" />
		</div>
	</div>
</template>

<script>
import SpotCard from "../../components/SpotCard.vue";
import { apiGetSpots } from "../../services/api.js";

export default {
	name: "SpotListPage",
	components: { SpotCard },
	data() {
		return {
			spots: [],
			search: "",
			loading: false,
			error: null,
		};
	},
	computed: {
		filtered() {
			if (!this.search) return this.spots;
			const q = this.search.toLowerCase();
			return this.spots.filter((s) => s.title?.toLowerCase().includes(q) || s.category?.toLowerCase().includes(q) || s.location?.toLowerCase().includes(q));
		},
	},
	async mounted() {
		this.loading = true;
		const res = await apiGetSpots().catch(() => null);
		this.loading = false;
		if (!res?.success) {
			this.error = "Failed to load spots right now. Please try again.";
			return;
		}
		this.spots = res.data?.data ?? res.data ?? [];
	},
};
</script>

<style scoped>
/* ── Layout Structures ── */
.spots-page {
	animation: fadeIn 0.4s ease-out;
}

.page-header {
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
	gap: 24px;
	margin-bottom: 40px;
	flex-wrap: wrap;
	border-bottom: 1px solid #f0f0f0;
	padding-bottom: 24px;
}

.title-area h1 {
	font-size: 2.25rem;
	font-weight: 800;
	letter-spacing: -0.03em;
	color: #1f2937;
	margin-bottom: 6px;
}

.title-area .subtitle {
	color: #6b7280;
	font-size: 1rem;
}

/* ── Modern Font Awesome Search Bar ── */
.search-wrapper {
	position: relative;
	width: 100%;
	max-width: 380px;
}

.search-icon {
	position: absolute;
	left: 14px;
	top: 50%;
	transform: translateY(-50%);
	color: #9ca3af;
	font-size: 0.95rem;
	pointer-events: none;
	transition: color 0.2s;
}

.search-input {
	width: 100%;
	padding: 0.75rem 1rem 0.75rem 2.5rem; /* Extends left padding to make room for icon */
	margin-bottom: 0;
	background: #ffffff;
	border: 1px solid #e5e7eb;
	border-radius: 10px;
	font-size: 0.95rem;
	color: #1f2937;
	box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
	transition: all 0.2s ease;
}

.search-wrapper:focus-within .search-icon {
	color: #4f46e5; /* Icon colors up when active */
}

/* ── Enhanced Grid ── */
.spot-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
	gap: 24px;
}

/* ── UI Feedback States (Loading, Empty, Errors) ── */
.state-container {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	text-align: center;
	padding: 80px 24px;
	background: #ffffff;
	border: 1px dashed #e5e7eb;
	border-radius: 16px;
	color: #6b7280;
}

.state-icon {
	font-size: 2.5rem;
	margin-bottom: 16px;
	color: #9ca3af;
}

.loading-icon {
	color: #4f46e5;
}

.state-error {
	background: #fef2f2;
	border-color: #fca5a5;
	color: #991b1b;
}
.state-error .state-icon {
	color: #ef4444;
}

/* ── Micro-interactions ── */
@keyframes fadeIn {
	from {
		opacity: 0;
		transform: translateY(8px);
	}
	to {
		opacity: 1;
		transform: translateY(0);
	}
}

@media (max-width: 640px) {
	.page-header {
		flex-direction: column;
		align-items: flex-start;
	}
	.search-wrapper {
		max-width: 100%;
	}
}
</style>
