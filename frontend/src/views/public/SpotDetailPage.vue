<template>
	<div class="spot-detail-page">
		<button class="btn-secondary" @click="$router.back()"><i class="fa-solid fa-arrow-left"></i> Back to List</button>

		<p v-if="loading" class="state-text"><i class="fa-solid fa-spinner fa-spin"></i> Loading details...</p>
		<p v-else-if="error" class="error-text">{{ error }}</p>

		<div v-else-if="spot" class="detail-container">
			<div class="detail-main">
				<img v-if="spot.thumbnail" :src="spot.thumbnail" :alt="spot.title" class="detail-image" />

				<div class="detail-badges">
					<span class="badge" :class="spot.status">{{ spot.status }}</span>
					<span class="category-tag">{{ spot.category }}</span>
				</div>

				<h1>{{ spot.title }}</h1>
				<p class="spot-description">{{ spot.description }}</p>
			</div>

			<div class="detail-sidebar">
				<div class="info-card">
					<table class="info-table">
						<tr>
							<td><i class="fa-solid fa-location-dot"></i> Location</td>
							<td>{{ spot.location }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-calendar-days"></i> Date</td>
							<td>{{ formatDate(spot.event_date) }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-user-tie"></i> Organizer</td>
							<td>{{ spot.organizer }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-tag"></i> Price</td>
							<td>{{ spot.price === 0 ? "Free" : "Rp" + Number(spot.price).toLocaleString() }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-users"></i> Capacity</td>
							<td>{{ spot.participant_limit ?? "Unlimited" }}</td>
						</tr>
					</table>

					<div v-if="auth.isLoggedIn && !auth.isAdmin" class="action-box">
						<div v-if="spot.status === 'open'">
							<div v-if="!myReg">
								<button class="btn-full" @click="registerForSpot" :disabled="regLoading">
									{{ regLoading ? "Registering..." : "Register for this Event" }}
								</button>
							</div>
							<div v-else class="success-nudge">
								<p><i class="fa-solid fa-circle-check"></i> You are registered ({{ myReg.status }})</p>
								<button v-if="myReg.status !== 'cancelled'" class="btn-danger" @click="cancelReg">Cancel</button>
							</div>
						</div>
						<p v-else class="status-alert">Registrations are currently {{ spot.status }}.</p>
					</div>

					<div v-if="!auth.isLoggedIn" class="login-nudge"><router-link to="/login">Login</router-link> to register for this event.</div>
				</div>

				<div v-if="auth.isAdmin" class="admin-box">
					<router-link :to="`/admin/spots/${spot.id}/edit`"><button>Edit Spot</button></router-link>
					<router-link :to="`/admin/spots/${spot.id}/registrations`"><button class="btn-secondary">Registrations</button></router-link>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { useAuthStore } from "../../stores/auth.js";
import { useFlashStore } from "../../stores/flash.js";
import { apiGetSpot, apiMyRegistrations, apiRegisterForSpot, apiCancelRegistration } from "../../services/api.js";

export default {
	name: "SpotDetailPage",
	setup() {
		return { auth: useAuthStore(), flash: useFlashStore() };
	},
	data() {
		return {
			spot: null,
			myRegistrations: [],
			loading: false,
			error: null,
			regLoading: false,
		};
	},
	computed: {
		myReg() {
			return this.myRegistrations.find((r) => r.spot_id === this.spot?.id) || null;
		},
	},
	async mounted() {
		this.loading = true;
		const id = this.$route.params.id;
		const res = await apiGetSpot(id).catch(() => null);
		this.loading = false;
		if (!res?.success) {
			this.error = "Spot not found.";
			return;
		}
		this.spot = res.data;

		if (this.auth.isLoggedIn) {
			const regRes = await apiMyRegistrations().catch(() => null);
			this.myRegistrations = regRes?.data ?? [];
		}
	},
	methods: {
		formatDate(dt) {
			if (!dt) return "-";
			return new Date(dt).toLocaleString("id-ID", { dateStyle: "long", timeStyle: "short" });
		},
		async registerForSpot() {
			this.regLoading = true;
			const res = await apiRegisterForSpot(this.spot.id);
			this.regLoading = false;
			if (!res.success) return this.flash.error(res.message);
			this.flash.success("Berhasil mendaftar event!");
			const regRes = await apiMyRegistrations().catch(() => null);
			this.myRegistrations = regRes?.data ?? [];
		},
		async cancelReg() {
			if (!confirm("Cancel your registration?")) return;
			this.regLoading = true;
			const res = await apiCancelRegistration(this.spot.id);
			this.regLoading = false;
			if (!res.success) return this.flash.error(res.message);
			this.flash.success("Registrasi dibatalkan.");
			const regRes = await apiMyRegistrations().catch(() => null);
			this.myRegistrations = regRes?.data ?? [];
		},
	},
};
</script>

<style scoped>
.detail-container {
	display: grid;
	grid-template-columns: 1fr 380px;
	gap: 40px;
	margin-top: 24px;
}

.detail-image {
	width: 100%;
	height: 400px;
	object-fit: cover;
	border-radius: var(--radius);
	margin-bottom: 24px;
}

.detail-badges {
	display: flex;
	gap: 8px;
	margin-bottom: 12px;
}

.category-tag {
	background: #e0e7ff;
	color: var(--primary);
	padding: 4px 12px;
	border-radius: var(--radius);
	font-size: 0.8rem;
	font-weight: 700;
}

.spot-description {
	font-size: 1.1rem;
	color: var(--text-muted);
	line-height: 1.8;
}

/* Info Sidebar Card */
.info-card {
	background: var(--surface);
	border: 1px solid var(--border);
	padding: 24px;
	border-radius: var(--radius);
}

.info-table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0 12px;
}
.info-table td {
	font-size: 0.9rem;
}
.info-table td:first-child {
	color: var(--text-muted);
	font-weight: 500;
	display: flex;
	align-items: center;
	gap: 8px;
	width: 140px;
}

.btn-full {
	width: 100%;
}

.login-nudge {
	margin-top: 20px;
	padding: 16px;
	background: #f1f5f9;
	border-radius: var(--radius);
	text-align: center;
}

@media (max-width: 900px) {
	.detail-container {
		grid-template-columns: 1fr;
	}
}
</style>
