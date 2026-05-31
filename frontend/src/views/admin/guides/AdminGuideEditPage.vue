<template>
  <div class="admin-edit-container">
    <div class="navigation-bar">
      <button class="btn-back" @click="$router.back()">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
      </button>
    </div>

    <div v-if="fetching" class="loading-state">
      <i class="fa-solid fa-circle-notch fa-spin spinner-icon"></i>
      <p>Retrieving guide records...</p>
    </div>

    <p v-else-if="notFoundError" class="error-text-display">
      <i class="fa-solid fa-circle-exclamation"></i> {{ notFoundError }}
    </p>

    <GuideForm
      v-else
      :guide="guide"
      :loading="loading"
      :error="error"
      @submit="updateGuide"
    />
  </div>
</template>

<script>
import GuideForm from "../../../components/GuideForm.vue";
import { useFlashStore } from "../../../stores/flash.js";
import { apiGetGuideDetail, apiUpdateGuide } from "../../../services/api.js";

export default {
  name: "AdminGuideEditPage",
  components: { GuideForm },
  setup() {
    return { flash: useFlashStore() };
  },
  data() {
    return {
      guide: null,
      fetching: false,
      loading: false,
      error: null,
      notFoundError: null,
    };
  },
  async mounted() {
    this.fetching = true;
    const id = this.$route.params.id;
    const res = await apiGetGuideDetail(id).catch(() => null);
    this.fetching = false;

    if (!res || !res.success) {
      this.notFoundError = "Curated guide not found or database mismatch.";
      return;
    }
    this.guide = res.data;
  },
  methods: {
    async updateGuide(formData) {
      this.loading = true;
      this.error = null;
      const id = this.$route.params.id;

      const res = await apiUpdateGuide(id, formData).catch(() => null);
      this.loading = false;

      if (!res || !res.success) {
        this.error = res?.message || "Failed to save modifications.";
        return;
      }

      this.flash.success("Artikel panduan berhasil diperbarui!");
      this.$router.push("/admin/guides");
    },
  },
};
</script>

<style scoped>
.admin-edit-container {
  padding: 20px 0;
  animation: fadeIn 0.3s ease-out;
}
.navigation-bar {
  max-width: 800px;
  margin: 0 auto 20px auto;
}
.btn-back {
  background: transparent;
  border: 1px solid var(--border);
  color: var(--text-muted);
  padding: 10px 16px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}
.loading-state {
  text-align: center;
  padding: 60px;
  color: var(--text-muted);
}
.spinner-icon {
  font-size: 2rem;
  color: var(--primary);
  margin-bottom: 12px;
}
.error-text-display {
  color: #991b1b;
  background: #fee2e2;
  padding: 16px;
  border-radius: 12px;
  max-width: 800px;
  margin: 0 auto;
}
</style>
