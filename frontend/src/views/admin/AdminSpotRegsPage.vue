<template>
	<div class="regs-page">
		<header class="page-header">
			<button class="btn-secondary btn-back" @click="$router.back()"><i class="fa-solid fa-arrow-left"></i> Back</button>
			<div class="header-text">
				<h1>Spot Registrations</h1>
				<p class="subtitle">Managing attendees for Spot #{{ $route.params.id }}</p>
			</div>
		</header>

		<div v-if="loading" class="state-container">
			<i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
			<p>Loading attendee list...</p>
		</div>

		<p v-else-if="error" class="error-text"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</p>

		<div v-else-if="registrations.length === 0" class="state-container state-empty">
			<i class="fa-solid fa-users-slash state-icon"></i>
			<p>No registrations yet for this event.</p>
		</div>

		<div v-else class="table-container">
			<table class="reg-table">
				<thead>
					<tr>
						<th>#</th>
						<th>Attendee</th>
						<th>Email</th>
						<th>Status</th>
						<th>Registered At</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(reg, i) in registrations" :key="reg.id">
						<td class="text-muted">{{ i + 1 }}</td>
						<td class="font-medium">
							<i class="fa-solid fa-user user-icon"></i>
							{{ reg.user?.name ?? "Unknown" }}
						</td>
						<td class="text-muted">{{ reg.user?.email ?? "-" }}</td>
						<td>
							<span class="badge" :class="reg.status">{{ reg.status }}</span>
						</td>
						<td class="text-muted">{{ formatDate(reg.created_at) }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import { apiSpotRegistrations } from "../../services/api.js";

export default {
	name: "AdminSpotRegsPage",
	data() {
		return { registrations: [], loading: false, error: null };
	},
	async mounted() {
		this.loading = true;
		const res = await apiSpotRegistrations(this.$route.params.id).catch(() => null);
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
	},
};
</script>

<style scoped>
.regs-page {
	animation: fadeIn 0.3s ease-out;
	max-width: 1000px;
	margin: 0 auto;
}

/* ── Header ── */
.page-header {
	display: flex;
	align-items: center;
	gap: 24px;
	margin-bottom: 32px;
	border-bottom: 1px solid var(--border);
	padding-bottom: 24px;
}

.btn-back {
	padding: 0.6rem 1rem;
	display: flex;
	align-items: center;
	gap: 8px;
}

.header-text h1 {
	font-size: 1.75rem;
	font-weight: 800;
	color: var(--text-main);
	margin-bottom: 4px;
}

.subtitle {
	color: var(--text-muted);
	font-size: 0.95rem;
}

/* ── Table Container ── */
.table-container {
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: var(--radius);
	box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
	overflow-x: auto; /* Enables horizontal scroll on mobile */
}

.reg-table {
	width: 100%;
	border-collapse: collapse;
	text-align: left;
}

/* ── Table Headers ── */
.reg-table th {
	background: #f8fafc;
	color: var(--text-muted);
	font-weight: 700;
	font-size: 0.75rem;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	padding: 16px;
	border-bottom: 1px solid var(--border);
}

/* ── Table Rows ── */
.reg-table td {
	padding: 16px;
	border-bottom: 1px solid var(--border);
	font-size: 0.95rem;
	color: var(--text-main);
	transition: background 0.2s;
}

.reg-table tbody tr:last-child td {
	border-bottom: none;
}

.reg-table tbody tr:hover td {
	background: #f8fafc;
}

/* ── Typography & Icons ── */
.font-medium {
	font-weight: 500;
}
.text-muted {
	color: var(--text-muted);
	font-size: 0.9rem !important;
}
.user-icon {
	color: #cbd5e1;
	margin-right: 8px;
	font-size: 0.85rem;
}

/* ── States ── */
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

/* Responsive constraints */
@media (max-width: 640px) {
	.page-header {
		flex-direction: column;
		align-items: flex-start;
		gap: 16px;
	}
}
</style>
