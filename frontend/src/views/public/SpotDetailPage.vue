<template>
	<div class="spot-detail-page">
		<div class="page-header">
			<button class="btn-secondary btn-back" @click="$router.back()"><i class="fa-solid fa-arrow-left"></i> Back to List</button>
		</div>

		<div v-if="loading" class="state-container">
			<i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
			<p>Loading spot details...</p>
		</div>
		<p v-else-if="error" class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

		<div v-else-if="spot" class="detail-container">
			<div class="detail-main">
				<div class="image-wrapper" v-if="spot.thumbnail">
					<img :src="spot.thumbnail" :alt="spot.title" class="detail-image" />
				</div>

				<div class="content-card">
					<div class="detail-badges">
						<span class="badge" :class="spot.status">{{ spot.status }}</span>
						<span class="category-tag">{{ spot.category }}</span>
					</div>

					<h1 class="spot-title">{{ spot.title }}</h1>
					<p class="spot-description">{{ spot.description }}</p>
				</div>
			</div>

			<div class="detail-sidebar">
				<div class="info-card">
					<table class="info-table">
						<tr>
							<td><i class="fa-solid fa-location-dot"></i> Location</td>
							<td class="font-medium">{{ spot.location }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-calendar-days"></i> Date</td>
							<td class="font-medium">{{ formatDate(spot.event_date) }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-user-tie"></i> Organizer</td>
							<td class="font-medium">{{ spot.organizer }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-tag"></i> Price</td>
							<td class="font-medium">{{ spot.price === 0 ? "Free" : "Rp" + Number(spot.price).toLocaleString() }}</td>
						</tr>
						<tr>
							<td><i class="fa-solid fa-users"></i> Capacity</td>
							<td class="font-medium">{{ spot.participant_limit ?? "Unlimited" }}</td>
						</tr>
					</table>

					<hr class="divider" />

					<div v-if="auth.isLoggedIn && !auth.isAdmin" class="action-box">
						<div v-if="spot.status === 'open'">
							<div v-if="!myReg">
								<button class="btn-primary btn-full" @click="registerForSpot" :disabled="regLoading">
									<i v-if="regLoading" class="fa-solid fa-spinner fa-spin"></i>
									{{ regLoading ? "Registering..." : "Register for this Event" }}
								</button>
							</div>
							<div v-else class="success-nudge">
								<p><i class="fa-solid fa-circle-check"></i> Registered ({{ myReg.status }})</p>
								<button v-if="myReg.status !== 'cancelled'" class="btn-danger btn-full" @click="cancelReg" :disabled="regLoading">Cancel Registration</button>
							</div>
						</div>
						<div v-else class="status-alert"><i class="fa-solid fa-lock"></i> Registrations are currently {{ spot.status }}.</div>
					</div>

					<div v-if="!auth.isLoggedIn" class="login-nudge">
						<i class="fa-solid fa-circle-user icon-large"></i>
						<p><router-link to="/login">Login</router-link> to register for this event.</p>
					</div>
				</div>

				<div v-if="auth.isAdmin" class="admin-box">
					<router-link :to="`/admin/spots/${spot.id}/edit`" class="flex-1">
						<button class="btn-full"><i class="fa-solid fa-pen"></i> Edit Spot</button>
					</router-link>
					<router-link :to="`/admin/spots/${spot.id}/registrations`" class="flex-1">
						<button class="btn-secondary btn-full"><i class="fa-solid fa-users-gear"></i> Registrations</button>
					</router-link>
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
.spot-detail-page {
	max-width: 1100px;
	margin: 0 auto;
	animation: fadeIn 0.4s ease-out;
}

.page-header {
	margin-bottom: 24px;
}
.btn-back {
	display: inline-flex;
	align-items: center;
	gap: 8px;
	padding: 0.6rem 1rem;
}

/* ── Grid Layout ── */
.detail-container {
	display: grid;
	grid-template-columns: 1fr 380px;
	gap: 32px;
}

.detail-main {
	display: flex;
	flex-direction: column;
	gap: 24px;
}

/* Centers the sidebar vertically relative to the left column */
.detail-sidebar {
	display: flex;
	flex-direction: column;
	gap: 16px;
	align-self: center;
}

/* ── Left Side Cards ── */
.image-wrapper {
	border-radius: var(--radius);
	overflow: hidden;
	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
	border: 1px solid var(--border);
	background: var(--surface);
}

.detail-image {
	width: 100%;
	height: 420px;
	object-fit: cover;
	display: block;
}

.content-card {
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: var(--radius);
	padding: 32px;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
}

.detail-badges {
	display: flex;
	gap: 8px;
	margin-bottom: 16px;
}

.category-tag {
	background: #e0e7ff;
	color: var(--primary);
	padding: 6px 14px;
	border-radius: 999px;
	font-size: 0.75rem;
	font-weight: 800;
	text-transform: uppercase;
	letter-spacing: 0.05em;
}

.spot-title {
	font-size: 2rem;
	font-weight: 800;
	color: var(--text-main);
	margin-bottom: 16px;
	line-height: 1.2;
}

.spot-description {
	font-size: 1.05rem;
	color: var(--text-muted);
	line-height: 1.8;
}

/* ── Right Side Cards ── */
.info-card {
	background: var(--surface);
	border: 1px solid var(--border);
	padding: 24px;
	border-radius: var(--radius);
	box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
}

.info-table {
	width: 100%;
	border-collapse: separate;
	border-spacing: 0 16px;
}

.info-table td {
	font-size: 0.95rem;
}
.font-medium {
	font-weight: 500;
	color: var(--text-main);
}

.info-table td:first-child {
	color: #64748b;
	display: flex;
	align-items: center;
	gap: 10px;
	width: 130px;
}

.divider {
	border: 0;
	height: 1px;
	background: var(--border);
	margin: 8px 0 24px 0;
}

/* ── Action & Admin Boxes ── */
.btn-full {
	width: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	gap: 8px;
}
.btn-primary {
	background: var(--primary);
	color: white;
}
.btn-primary:hover {
	background: var(--primary-hover);
}

.admin-box {
	display: flex;
	gap: 12px; /* Adds the requested spacing between Edit and Reg buttons */
}
.flex-1 {
	flex: 1;
}

.success-nudge {
	background: #f0fdf4;
	border: 1px solid #bbf7d0;
	padding: 16px;
	border-radius: var(--radius);
	text-align: center;
	color: #166534;
}
.success-nudge p {
	margin-bottom: 12px;
	font-weight: 600;
}

.status-alert {
	background: #f1f5f9;
	color: var(--text-muted);
	padding: 16px;
	border-radius: var(--radius);
	text-align: center;
	font-weight: 500;
}

.login-nudge {
	text-align: center;
	padding: 24px 16px;
	background: #f8fafc;
	border: 1px dashed #cbd5e1;
	border-radius: var(--radius);
}
.icon-large {
	font-size: 2rem;
	color: #94a3b8;
	margin-bottom: 12px;
}
.login-nudge a {
	color: var(--primary);
	font-weight: 700;
}

/* ── States ── */
.state-container {
	text-align: center;
	padding: 80px 24px;
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

@media (max-width: 900px) {
	.detail-container {
		grid-template-columns: 1fr;
	}
	.detail-sidebar {
		align-self: stretch;
	}
	.detail-image {
		height: 300px;
	}
}
</style>
