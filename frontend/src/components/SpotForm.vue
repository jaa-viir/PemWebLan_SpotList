<template>
	<div class="spot-form-wrapper">
		<div class="form-card">
			<div class="form-header">
				<h2>{{ isEdit ? "Edit Spot" : "Create New Spot" }}</h2>
				<p class="subtitle">{{ isEdit ? "Update the details for this curated event." : "Fill in the details to publish a new curated event." }}</p>
			</div>

			<div class="form-body">
				<div class="form-section">
					<div class="form-group">
						<label><i class="fa-solid fa-heading"></i> Event Title</label>
						<input v-model="form.title" placeholder="e.g., Surabaya Night Market" />
					</div>

					<div class="form-group">
						<label><i class="fa-solid fa-align-left"></i> Description</label>
						<textarea v-model="form.description" placeholder="What is this event about?" rows="4" />
					</div>

					<div class="form-row">
						<div class="form-group">
							<label><i class="fa-solid fa-shapes"></i> Category</label>
							<select v-model="form.category">
								<option value="" disabled>-- Select Category --</option>
								<option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
							</select>
						</div>

						<div class="form-group">
							<label><i class="fa-solid fa-user-tie"></i> Organizer</label>
							<input v-model="form.organizer" placeholder="e.g., Creative Hub SBY" />
						</div>
					</div>
				</div>

				<hr class="section-divider" />

				<div class="form-section">
					<div class="form-row">
						<div class="form-group">
							<label><i class="fa-solid fa-location-dot"></i> Location</label>
							<input v-model="form.location" placeholder="e.g. Taman Bungkul" />
						</div>

						<div class="form-group">
							<label><i class="fa-solid fa-calendar-days"></i> Date & Time</label>
							<input v-model="form.event_date" type="datetime-local" />
						</div>
					</div>
				</div>

				<hr class="section-divider" />

				<div class="form-section">
					<div class="form-row">
						<div class="form-group">
							<label><i class="fa-solid fa-tag"></i> Price (IDR)</label>
							<input v-model="form.price" type="number" min="0" placeholder="0 (Free)" />
						</div>

						<div class="form-group">
							<label><i class="fa-solid fa-users"></i> Capacity Limit</label>
							<input v-model="form.participant_limit" type="number" min="1" placeholder="Leave empty for unlimited" />
						</div>
					</div>

					<div class="form-group">
						<label><i class="fa-solid fa-link"></i> External Registration Link (Optional)</label>
						<input v-model="form.registration_link" placeholder="https://..." />
					</div>
				</div>

				<hr class="section-divider" />

				<div class="form-section">
					<div class="form-row">
						<div class="form-group">
							<label><i class="fa-solid fa-toggle-on"></i> Status</label>
							<select v-model="form.status">
								<option value="draft">Draft (Hidden)</option>
								<option value="open">Open for Registration</option>
								<option value="closed">Closed / Finished</option>
							</select>
						</div>

						<div class="form-group">
							<label><i class="fa-solid fa-image"></i> Cover Thumbnail</label>
							<input type="file" accept="image/*" @change="handleFile" class="file-input" />
							<p v-if="isEdit" class="input-hint">Leave empty to keep the current image.</p>
						</div>
					</div>
				</div>
			</div>

			<div v-if="error" class="error-box"><i class="fa-solid fa-circle-exclamation"></i> {{ error }}</div>

			<div class="form-actions">
				<button class="btn-secondary" @click="$router.back()">Cancel</button>
				<button class="btn-primary" @click="submit" :disabled="loading">
					<i v-if="loading" class="fa-solid fa-spinner fa-spin"></i>
					{{ loading ? "Saving..." : isEdit ? "Save Changes" : "Publish Spot" }}
				</button>
			</div>
		</div>
	</div>
</template>

<script>
const CATEGORIES = ["Competition", "Convention", "Concert", "Workshop", "Exhibition", "Festival", "Community Meetup"];

export default {
	name: "SpotForm",
	props: {
		spot: { type: Object, default: null }, // null = create mode
		loading: { type: Boolean, default: false },
		error: { type: String, default: null },
	},
	computed: {
		isEdit() {
			return !!this.spot;
		},
		categories() {
			return CATEGORIES;
		},
	},
	data() {
		return {
			form: {
				title: "",
				description: "",
				category: "",
				location: "",
				event_date: "",
				organizer: "",
				price: 0,
				participant_limit: "",
				registration_link: "",
				status: "draft",
			},
			imageFile: null,
		};
	},
	watch: {
		spot: {
			immediate: true,
			handler(val) {
				if (!val) return;
				this.form = {
					title: val.title || "",
					description: val.description || "",
					category: val.category || "",
					location: val.location || "",
					event_date: val.event_date ? val.event_date.slice(0, 16) : "",
					organizer: val.organizer || "",
					price: val.price ?? 0,
					participant_limit: val.participant_limit || "",
					registration_link: val.registration_link || "",
					status: val.status || "draft",
				};
			},
		},
	},
	methods: {
		handleFile(e) {
			this.imageFile = e.target.files[0] || null;
		},
		submit() {
			const fd = new FormData();
			Object.entries(this.form).forEach(([key, val]) => {
				if (val !== "" && val !== null && val !== undefined) {
					fd.append(key, val);
				}
			});
			if (this.imageFile) fd.append("thumbnail", this.imageFile);
			this.$emit("submit", fd);
		},
	},
};
</script>

<style scoped>
.spot-form-wrapper {
	max-width: 800px;
	margin: 0 auto;
	animation: fadeIn 0.3s ease-out;
}

.form-card {
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: var(--radius);
	padding: 32px;
	box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
}

.form-header {
	margin-bottom: 24px;
}
.form-header h2 {
	font-size: 1.75rem;
	font-weight: 800;
	color: var(--text-main);
	margin-bottom: 4px;
}
.subtitle {
	color: var(--text-muted);
	font-size: 0.95rem;
}

.section-divider {
	border: 0;
	height: 1px;
	background: var(--border);
	margin: 24px 0;
}

.form-group {
	margin-bottom: 16px;
}

.form-row {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 16px;
}

label {
	display: flex;
	align-items: center;
	gap: 8px;
	font-size: 0.85rem;
	font-weight: 600;
	color: var(--text-main);
	margin-bottom: 8px;
}

label i {
	color: #94a3b8;
}

.file-input {
	padding: 0.5rem;
	background: #f8fafc;
	border: 1px dashed #cbd5e1;
	cursor: pointer;
}

.input-hint {
	font-size: 0.8rem;
	color: var(--text-muted);
	margin-top: 6px;
}

.error-box {
	background: #fee2e2;
	color: #991b1b;
	padding: 12px 16px;
	border-radius: var(--radius);
	font-size: 0.9rem;
	display: flex;
	align-items: center;
	gap: 8px;
	margin-bottom: 24px;
}

.form-actions {
	display: flex;
	justify-content: flex-end;
	gap: 12px;
	margin-top: 32px;
	padding-top: 24px;
	border-top: 1px solid var(--border);
}

.btn-primary {
	background: var(--primary);
	color: #fff;
}
.btn-primary:hover {
	background: var(--primary-hover);
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

@media (max-width: 640px) {
	.form-row {
		grid-template-columns: 1fr;
		gap: 0;
	}
}
</style>
