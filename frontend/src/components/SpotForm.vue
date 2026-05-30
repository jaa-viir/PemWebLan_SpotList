<template>
	<div class="spot-form">
		<h2>{{ isEdit ? "Edit Spot" : "Create Spot" }}</h2>

		<input v-model="form.title" placeholder="Title" />
		<textarea v-model="form.description" placeholder="Description" rows="3" />

		<select v-model="form.category">
			<option value="">-- Select Category --</option>
			<option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
		</select>

		<input v-model="form.location" placeholder="Location" />
		<input v-model="form.event_date" type="datetime-local" />
		<input v-model="form.organizer" placeholder="Organizer" />
		<input v-model="form.price" type="number" placeholder="Price (0 = free)" />
		<input v-model="form.participant_limit" type="number" placeholder="Participant Limit (optional)" />
		<input v-model="form.registration_link" placeholder="Registration Link (optional)" />

		<select v-model="form.status">
			<option value="open">Open</option>
			<option value="closed">Closed</option>
			<option value="draft">Draft</option>
		</select>

		<div>
			<label>Thumbnail</label>
			<input type="file" accept="image/*" @change="handleFile" />
			<p v-if="isEdit" class="hint">Leave empty to keep current thumbnail.</p>
		</div>

		<div class="form-actions">
			<button @click="submit">{{ isEdit ? "Update" : "Create" }}</button>
			<button v-if="isEdit" @click="$emit('cancel')">Cancel</button>
		</div>
	</div>
</template>

<script>
const CATEGORIES = ["Competition", "Convention", "Concert", "Workshop", "Exhibition", "Festival", "Community Meetup"];

export default {
	name: "SpotForm",
	props: {
		spot: { type: Object, default: null },
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
				if (val) {
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
					this.imageFile = null;
				}
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
			this.$emit("submit", fd, this.spot?.id || null);
		},
	},
};
</script>
