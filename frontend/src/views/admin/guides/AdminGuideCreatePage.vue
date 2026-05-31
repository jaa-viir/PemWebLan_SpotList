<template>
  <div class="admin-create-container">
    <div class="navigation-bar">
      <button class="btn-back" @click="$router.back()">
        <i class="fa-solid fa-arrow-left"></i> Back to Guides
      </button>
    </div>

    <GuideForm :loading="loading" :error="error" @submit="createGuide" />
  </div>
</template>

<script>
import GuideForm from "../../../components/GuideForm.vue";
import { useFlashStore } from "../../../stores/flash.js";
import { apiCreateGuide } from "../../../services/api.js";

export default {
  name: "AdminGuideCreatePage",
  components: { GuideForm },
  setup() {
    return { flash: useFlashStore() };
  },
  data() {
    return {
      loading: false,
      error: null,
    };
  },
  methods: {
    async createGuide(formData) {
      this.loading = true;
      this.error = null;

      const res = await apiCreateGuide(formData).catch(() => null);
      this.loading = false;

      if (!res || !res.success) {
        this.error = res?.message || "Failed to publish new guide.";
        return;
      }

      this.flash.success("Artikel panduan baru berhasil diterbitkan!");
      this.$router.push("/admin/guides");
    },
  },
};
</script>

<style scoped>
.admin-create-container {
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
  transition: all 0.2s;
}
.btn-back:hover {
  background: var(--surface);
  color: var(--text-main);
  border-color: #cbd5e1;
}
</style>
