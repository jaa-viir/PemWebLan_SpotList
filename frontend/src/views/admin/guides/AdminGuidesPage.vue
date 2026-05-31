<template>
  <div class="admin-guides-page">
    <header class="page-header">
      <div class="header-text">
        <h1>Manage Guides</h1>
        <p class="subtitle">
          Create, monitor, and oversee all curated knowledge bases.
        </p>
      </div>

      <router-link to="/admin/guides/create">
        <button class="btn-primary">
          <i class="fa-solid fa-plus"></i> Create New Guide
        </button>
      </router-link>
    </header>

    <div v-if="loading" class="state-container">
      <i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
      <p>Loading master guide dashboard...</p>
    </div>

    <p v-else-if="error" class="error-text">
      <i class="fa-solid fa-circle-exclamation"></i> {{ error }}
    </p>

    <div v-else-if="guides.length === 0" class="state-container state-empty">
      <i class="fa-solid fa-book-bookmark state-icon"></i>
      <p>No guides have been written by the admin unit yet.</p>
      <router-link
        to="/admin/guides/create"
        style="margin-top: 16px; display: inline-block"
      >
        <button class="btn-primary">Write Your First Guide</button>
      </router-link>
    </div>

    <div v-else class="guide-grid">
      <div v-for="guide in guides" :key="guide.id" class="card-wrapper">
        <GuideCard :guide="guide" :isAdmin="true" @delete="deleteGuide" />
        <div class="admin-overlay-actions">
          <router-link
            :to="`/admin/guides/${guide.id}/edit`"
            class="btn-edit-link"
          >
            <i class="fa-solid fa-pen-to-square"></i> Edit Text Content
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import GuideCard from "../../../components/GuideCard.vue";
import { useFlashStore } from "../../../stores/flash.js";
import { apiGetGuides, apiDeleteGuide } from "../../../services/api.js";

export default {
  name: "AdminGuidesPage",
  components: { GuideCard },
  setup() {
    return { flash: useFlashStore() };
  },
  data() {
    return { guides: [], loading: false, error: null };
  },
  async mounted() {
    this.loading = true;
    const res = await apiGetGuides().catch(() => null);
    this.loading = false;
    if (!res?.success) {
      this.error = "Failed to load guides.";
      return;
    }
    this.guides = res.data?.data || res.data || [];
  },
  methods: {
    async deleteGuide(id) {
      if (!confirm("Are you sure you want to delete this guide completely?"))
        return;
      const res = await apiDeleteGuide(id);
      if (!res.success)
        return this.flash.error(res.message || "Deletion sequence failed.");
      this.flash.success("Guide cleared successfully.");
      this.guides = this.guides.filter((g) => g.id !== id);
    },
  },
};
</script>

<style scoped>
.admin-guides-page {
  animation: fadeIn 0.3s ease-out;
  max-width: 1000px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
  border-bottom: 1px solid var(--border);
  padding-bottom: 24px;
  flex-wrap: wrap;
  gap: 16px;
}

.header-text h1 {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-main);
}

.subtitle {
  color: var(--text-muted);
  font-size: 0.95rem;
  margin-top: 4px;
}

.btn-primary {
  background: var(--primary);
  color: #fff;
  border: none;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: var(--primary-hover);
}

.guide-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
  gap: 24px;
}

.card-wrapper {
  position: relative;
  display: flex;
  flex-direction: column;
}

.admin-overlay-actions {
  padding: 0 24px 24px 24px;
  background: var(--surface);
  border: 1px solid var(--border);
  border-top: none;
  margin-top: -24px; /* Menarik panel agar menempel pas di bawah GuideCard */
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
  display: flex;
  flex-direction: column;
}

/* Tombol Edit Text Content */
.btn-edit-link {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  text-align: center;
  background: #ffffff;
  color: var(--text-muted);
  padding: 11px;
  border: 1px solid var(--border);
  border-radius: 12px;
  font-size: 0.85rem;
  font-weight: 600;
  transition: all 0.2s ease;
  margin-top: 12px;
}

.btn-edit-link:hover {
  background: #f1f5f9;
  color: var(--text-main);
  border-color: #cbd5e1;
}

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
  color: #cbd5e1;
  margin-bottom: 16px;
}

.loading-icon {
  color: var(--primary);
}

.error-text {
  color: #991b1b;
  background: #fee2e2;
  padding: 16px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
