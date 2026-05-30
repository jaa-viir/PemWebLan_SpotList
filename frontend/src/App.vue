<template>
	<div id="app">
		<!-- ── NAVBAR ────────────────────────────────────────────────── -->
		<nav class="navbar">
			<strong>SpotList</strong>
			<div class="nav-links">
				<button @click="setView('spots')">Spots</button>
				<button v-if="isLoggedIn" @click="setView('my-registrations')">My Registrations</button>
				<button v-if="isAdmin" @click="setView('admin-spots')">Admin: All Spots</button>
				<button v-if="isAdmin" @click="setView('create-spot')">+ Create Spot</button>
			</div>
			<div class="nav-user">
				<span v-if="isLoggedIn">{{ user.name }} ({{ user.role }})</span>
				<button v-if="isLoggedIn" @click="logout">Logout</button>
				<button v-else @click="setView('auth')">Login / Register</button>
			</div>
		</nav>

		<!-- ── FLASH MESSAGE ─────────────────────────────────────────── -->
		<div v-if="flash.message" :class="['flash', flash.type]">
			{{ flash.message }}
		</div>

		<!-- ── VIEWS ─────────────────────────────────────────────────── -->

		<!-- Auth -->
		<AuthForm v-if="view === 'auth'" @login="login" @register="register" />

		<!-- Public spot list -->
		<div v-else-if="view === 'spots'">
			<h1>Upcoming Events</h1>
			<input v-model="searchQuery" placeholder="Search spots..." class="search-input" />
			<SpotList :spots="filteredSpots" :loading="spotsLoading" :error="spotsError" :isAdmin="false" @view="viewSpot" />
		</div>

		<!-- Admin: all spots -->
		<div v-else-if="view === 'admin-spots'">
			<h1>All Spots (Admin)</h1>
			<SpotList
				:spots="adminSpots"
				:loading="adminSpotsLoading"
				:error="adminSpotsError"
				:isAdmin="true"
				@view="viewSpot"
				@edit="startEdit"
				@delete="deleteSpot"
				@view-registrations="viewRegistrations"
			/>
		</div>

		<!-- Spot detail -->
		<SpotDetail
			v-else-if="view === 'spot-detail'"
			:spot="currentSpot"
			:loading="currentSpotLoading"
			:error="currentSpotError"
			:isLoggedIn="isLoggedIn"
			:isAdmin="isAdmin"
			:alreadyRegistered="alreadyRegistered"
			:registrationStatus="myRegistrationStatus"
			@back="setView(isAdmin ? 'admin-spots' : 'spots')"
			@register="registerForSpot"
			@cancel="cancelRegistration"
			@view-registrations="viewRegistrations"
		/>

		<!-- Create spot -->
		<div v-else-if="view === 'create-spot'">
			<SpotForm @submit="createSpot" @cancel="setView('admin-spots')" />
		</div>

		<!-- Edit spot -->
		<div v-else-if="view === 'edit-spot'">
			<SpotForm :spot="editingSpot" @submit="updateSpot" @cancel="setView('admin-spots')" />
		</div>

		<!-- My registrations (member) -->
		<MyRegistrations v-else-if="view === 'my-registrations'" :registrations="myRegistrations" :loading="myRegLoading" :error="myRegError" @cancel="cancelRegistration" />

		<!-- Spot registrations (admin) -->
		<SpotRegistrations
			v-else-if="view === 'spot-registrations'"
			:spotId="registrationsSpotId"
			:registrations="spotRegistrations"
			:loading="spotRegLoading"
			:error="spotRegError"
			@back="setView('admin-spots')"
		/>
	</div>
</template>

<script>
import AuthForm from "./components/AuthForm.vue";
import SpotList from "./components/SpotList.vue";
import SpotCard from "./components/SpotCard.vue";
import SpotForm from "./components/SpotForm.vue";
import SpotDetail from "./components/SpotDetail.vue";
import MyRegistrations from "./components/MyRegistrations.vue";
import SpotRegistrations from "./components/SpotRegistrations.vue";

import {
	apiLogin,
	apiRegister,
	apiLogout,
	apiMe,
	apiGetSpots,
	apiGetSpot,
	apiAdminGetSpots,
	apiCreateSpot,
	apiUpdateSpot,
	apiDeleteSpot,
	apiRegisterForSpot,
	apiMyRegistrations,
	apiCancelRegistration,
	apiSpotRegistrations,
} from "./services/api.js";

export default {
	name: "App",
	components: {
		AuthForm,
		SpotList,
		SpotCard,
		SpotForm,
		SpotDetail,
		MyRegistrations,
		SpotRegistrations,
	},

	// ── STATE ──────────────────────────────────────────────────────
	data() {
		return {
			view: "spots",

			// Auth
			token: localStorage.getItem("token") || "",
			user: JSON.parse(localStorage.getItem("user")) || null,

			// Flash message
			flash: { message: "", type: "success" },

			// Public spots
			spots: [],
			spotsLoading: false,
			spotsError: null,
			searchQuery: "",

			// Admin spots
			adminSpots: [],
			adminSpotsLoading: false,
			adminSpotsError: null,

			// Spot detail
			currentSpot: null,
			currentSpotLoading: false,
			currentSpotError: null,

			// Editing
			editingSpot: null,

			// My registrations
			myRegistrations: [],
			myRegLoading: false,
			myRegError: null,

			// Spot registrations (admin)
			spotRegistrations: [],
			spotRegLoading: false,
			spotRegError: null,
			registrationsSpotId: null,
		};
	},

	// ── COMPUTED ───────────────────────────────────────────────────
	computed: {
		isLoggedIn() {
			return !!this.token;
		},
		isAdmin() {
			return this.user?.role === "admin" || this.user?.role === "super_admin";
		},

		filteredSpots() {
			if (!this.searchQuery) return this.spots;
			const q = this.searchQuery.toLowerCase();
			return this.spots.filter((s) => s.title.toLowerCase().includes(q) || s.category.toLowerCase().includes(q) || s.location.toLowerCase().includes(q));
		},

		// Check if current user is already registered for currentSpot
		alreadyRegistered() {
			if (!this.currentSpot) return false;
			return this.myRegistrations.some((r) => r.spot_id === this.currentSpot.id);
		},
		myRegistrationStatus() {
			if (!this.currentSpot) return null;
			return this.myRegistrations.find((r) => r.spot_id === this.currentSpot.id)?.status || null;
		},
	},

	// ── LIFECYCLE ──────────────────────────────────────────────────
	mounted() {
		this.fetchSpots();
		if (this.isLoggedIn) {
			this.fetchMyRegistrations();
			if (this.isAdmin) this.fetchAdminSpots();
		}
	},

	// ── METHODS ────────────────────────────────────────────────────
	methods: {
		// ── Helpers ────────────────────────────────────────────────
		setView(v) {
			this.view = v;
		},

		showFlash(message, type = "success") {
			this.flash = { message, type };
			setTimeout(() => {
				this.flash.message = "";
			}, 3000);
		},

		saveAuth(token, user) {
			this.token = token;
			this.user = user;
			localStorage.setItem("token", token);
			localStorage.setItem("user", JSON.stringify(user));
		},

		clearAuth() {
			this.token = "";
			this.user = null;
			localStorage.removeItem("token");
			localStorage.removeItem("user");
		},

		// ── Auth ───────────────────────────────────────────────────
		async login(email, password) {
			const res = await apiLogin(email, password);
			if (!res.success) return this.showFlash(res.message, "error");
			this.saveAuth(res.token, res.user);
			await this.fetchMyRegistrations();
			if (this.isAdmin) await this.fetchAdminSpots();
			this.showFlash("Login berhasil!");
			this.setView("spots");
		},

		async register(name, email, password) {
			const res = await apiRegister(name, email, password);
			if (!res.success) return this.showFlash(res.message, "error");
			this.showFlash("Register berhasil! Silakan login.");
		},

		async logout() {
			await apiLogout();
			this.clearAuth();
			this.myRegistrations = [];
			this.adminSpots = [];
			this.showFlash("Logout berhasil.");
			this.setView("spots");
		},

		// ── Spots ──────────────────────────────────────────────────
		async fetchSpots() {
			this.spotsLoading = true;
			this.spotsError = null;
			const res = await apiGetSpots().catch(() => null);
			this.spotsLoading = false;
			if (!res || !res.success) {
				this.spotsError = "Failed to load spots.";
				return;
			}
			// paginated response: res.data.data
			this.spots = res.data?.data ?? res.data ?? [];
		},

		async fetchAdminSpots() {
			this.adminSpotsLoading = true;
			this.adminSpotsError = null;
			const res = await apiAdminGetSpots().catch(() => null);
			this.adminSpotsLoading = false;
			if (!res || !res.success) {
				this.adminSpotsError = "Failed to load spots.";
				return;
			}
			this.adminSpots = res.data?.data ?? res.data ?? [];
		},

		async viewSpot(id) {
			this.currentSpot = null;
			this.currentSpotLoading = true;
			this.currentSpotError = null;
			this.setView("spot-detail");
			const res = await apiGetSpot(id).catch(() => null);
			this.currentSpotLoading = false;
			if (!res || !res.success) {
				this.currentSpotError = "Failed to load spot.";
				return;
			}
			this.currentSpot = res.data;
		},

		async createSpot(formData) {
			const res = await apiCreateSpot(formData);
			if (!res.success) return this.showFlash(res.message || "Failed to create spot.", "error");
			this.showFlash("Spot berhasil ditambahkan!");
			await this.fetchAdminSpots();
			await this.fetchSpots();
			this.setView("admin-spots");
		},

		startEdit(spot) {
			this.editingSpot = spot;
			this.setView("edit-spot");
		},

		async updateSpot(formData, id) {
			const res = await apiUpdateSpot(id, formData);
			if (!res.success) return this.showFlash(res.message || "Failed to update spot.", "error");
			this.showFlash("Spot berhasil diperbarui!");
			await this.fetchAdminSpots();
			await this.fetchSpots();
			this.setView("admin-spots");
		},

		async deleteSpot(id) {
			if (!confirm("Delete this spot?")) return;
			const res = await apiDeleteSpot(id);
			if (!res.success) return this.showFlash(res.message || "Failed to delete.", "error");
			this.showFlash("Spot berhasil dihapus.");
			await this.fetchAdminSpots();
			await this.fetchSpots();
		},

		// ── Registrations ──────────────────────────────────────────
		async fetchMyRegistrations() {
			this.myRegLoading = true;
			this.myRegError = null;
			const res = await apiMyRegistrations().catch(() => null);
			this.myRegLoading = false;
			if (!res || !res.success) {
				this.myRegError = "Failed to load registrations.";
				return;
			}
			this.myRegistrations = res.data ?? [];
			this.setView("my-registrations");
		},

		async registerForSpot(spotId) {
			const res = await apiRegisterForSpot(spotId);
			if (!res.success) return this.showFlash(res.message, "error");
			this.showFlash("Berhasil mendaftar event!");
			await apiMyRegistrations().then((r) => {
				this.myRegistrations = r.data ?? [];
			});
		},

		async cancelRegistration(spotId) {
			if (!confirm("Cancel your registration?")) return;
			const res = await apiCancelRegistration(spotId);
			if (!res.success) return this.showFlash(res.message, "error");
			this.showFlash("Registrasi dibatalkan.");
			await apiMyRegistrations().then((r) => {
				this.myRegistrations = r.data ?? [];
			});
		},

		async viewRegistrations(spotId) {
			this.registrationsSpotId = spotId;
			this.spotRegistrations = [];
			this.spotRegLoading = true;
			this.spotRegError = null;
			this.setView("spot-registrations");
			const res = await apiSpotRegistrations(spotId).catch(() => null);
			this.spotRegLoading = false;
			if (!res || !res.success) {
				this.spotRegError = "Failed to load registrations.";
				return;
			}
			this.spotRegistrations = res.data ?? [];
		},
	},
};
</script>

<style>
* {
	box-sizing: border-box;
	margin: 0;
	padding: 0;
}

body {
	font-family: sans-serif;
	background: #f5f5f5;
	color: #222;
}

#app {
	max-width: 1100px;
	margin: 0 auto;
	padding: 0 16px 40px;
}

/* Navbar */
.navbar {
	display: flex;
	align-items: center;
	gap: 12px;
	padding: 12px 0;
	border-bottom: 2px solid #222;
	margin-bottom: 24px;
	flex-wrap: wrap;
}
.navbar strong {
	font-size: 1.3rem;
	margin-right: auto;
}
.nav-links,
.nav-user {
	display: flex;
	gap: 8px;
	align-items: center;
}

/* Flash */
.flash {
	padding: 10px 16px;
	margin-bottom: 16px;
	border-radius: 4px;
	font-weight: 600;
}
.flash.success {
	background: #d4edda;
	color: #155724;
}
.flash.error {
	background: #f8d7da;
	color: #721c24;
}

/* Forms */
input,
textarea,
select {
	display: block;
	width: 100%;
	padding: 8px 10px;
	margin-bottom: 10px;
	border: 1px solid #ccc;
	border-radius: 4px;
	font-size: 0.95rem;
}

/* Buttons */
button {
	padding: 8px 14px;
	margin-right: 6px;
	margin-bottom: 6px;
	border: 1px solid #222;
	border-radius: 4px;
	background: #222;
	color: #fff;
	cursor: pointer;
	font-size: 0.9rem;
}
button:hover {
	background: #444;
}
.btn-danger {
	background: #c0392b;
	border-color: #c0392b;
}
.btn-danger:hover {
	background: #922b21;
}

/* Spot grid */
.spot-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
	gap: 16px;
}

/* Spot card */
.spot-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 6px;
	overflow: hidden;
}
.spot-thumbnail {
	width: 100%;
	height: 160px;
	object-fit: cover;
}
.spot-info {
	padding: 12px;
}
.spot-info h3 {
	margin: 6px 0;
}
.spot-info p {
	font-size: 0.85rem;
	color: #555;
	margin: 3px 0;
}
.spot-actions {
	padding: 8px 12px;
	border-top: 1px solid #eee;
	display: flex;
	flex-wrap: wrap;
}

/* Status badges */
.spot-status,
.spot-category {
	display: inline-block;
	padding: 2px 8px;
	border-radius: 20px;
	font-size: 0.75rem;
	font-weight: 600;
	margin-bottom: 4px;
	margin-right: 4px;
}
.spot-category {
	background: #eef;
	color: #336;
	border: 1px solid #ccf;
}
.open {
	background: #d4edda;
	color: #155724;
}
.closed {
	background: #f8d7da;
	color: #721c24;
}
.draft {
	background: #fff3cd;
	color: #856404;
}
.pending {
	background: #fff3cd;
	color: #856404;
}
.confirmed {
	background: #d4edda;
	color: #155724;
}
.cancelled {
	background: #f8d7da;
	color: #721c24;
}

/* Detail */
.detail-thumbnail {
	width: 100%;
	max-height: 300px;
	object-fit: cover;
	border-radius: 6px;
	margin-bottom: 16px;
}
.detail-table {
	width: 100%;
	border-collapse: collapse;
	margin: 16px 0;
}
.detail-table td {
	padding: 8px 12px;
	border-bottom: 1px solid #eee;
	font-size: 0.9rem;
}
.detail-table td:first-child {
	font-weight: 600;
	width: 140px;
	color: #555;
}

/* Auth */
.auth-form {
	max-width: 400px;
	margin: 40px auto;
	background: #fff;
	padding: 24px;
	border-radius: 6px;
	border: 1px solid #ddd;
}
.auth-tabs {
	display: flex;
	gap: 8px;
	margin-bottom: 20px;
}
.auth-tabs button {
	flex: 1;
	background: #eee;
	color: #222;
	border: 1px solid #ccc;
}
.auth-tabs button.active {
	background: #222;
	color: #fff;
}

/* Registrations */
.reg-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 6px;
	padding: 14px;
	margin-bottom: 12px;
}
.reg-table {
	width: 100%;
	border-collapse: collapse;
	margin-top: 16px;
}
.reg-table th,
.reg-table td {
	padding: 10px 12px;
	border: 1px solid #ddd;
	text-align: left;
	font-size: 0.9rem;
}
.reg-table th {
	background: #f0f0f0;
	font-weight: 600;
}

/* Search */
.search-input {
	max-width: 400px;
	margin-bottom: 16px;
}

/* Misc */
h1 {
	margin-bottom: 16px;
}
.hint {
	font-size: 0.8rem;
	color: #888;
}
.member-actions {
	margin-top: 20px;
	padding: 16px;
	background: #f9f9f9;
	border-radius: 6px;
	border: 1px solid #ddd;
}
.form-actions {
	display: flex;
	gap: 8px;
	margin-top: 8px;
}
.spot-form {
	max-width: 600px;
	margin: 0 auto;
	background: #fff;
	padding: 24px;
	border-radius: 6px;
	border: 1px solid #ddd;
}
.spot-form h2 {
	margin-bottom: 16px;
}
</style>
