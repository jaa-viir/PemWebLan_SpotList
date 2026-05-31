<template>
  <div class="guides-page">
    <div class="page-header">
      <div class="title-area">
        <h1>Curated Guides</h1>
        <p class="subtitle">
          Structured insights and preparation checklists written by our team.
        </p>
      </div>

      <div class="search-wrapper">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input
          v-model="search"
          placeholder="Search guides by title or content..."
          class="search-input"
        />
      </div>
    </div>

    <div v-if="loading" class="state-container">
      <i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
      <p>Fetching curated guidelines for you...</p>
    </div>

    <div v-else-if="error" class="state-container state-error">
      <i class="fa-solid fa-triangle-exclamation state-icon"></i>
      <p>{{ error }}</p>
    </div>

    <div
      v-else-if="filteredGuides.length === 0"
      class="state-container state-empty"
    >
      <i class="fa-solid fa-book-open-reader state-icon"></i>
      <p>No guides found matching your query.</p>
    </div>

    <div v-else class="guide-grid">
      <GuideCard
        v-for="guide in filteredGuides"
        :key="guide.id"
        :guide="guide"
        :isAdmin="false"
      />
    </div>
  </div>
</template>

<script>
import GuideCard from "../../components/GuideCard.vue";
import { apiGetGuides } from "../../services/api.js";

export default {
  name: "GuidesPage",
  components: { GuideCard },
  data() {
    return {
      guides: [],
      search: "",
      loading: false,
      error: null,
    };
  },
  computed: {
    filteredGuides() {
      if (!this.search) return this.guides;
      const q = this.search.toLowerCase();
      return this.guides.filter(
        (g) =>
          g.title?.toLowerCase().includes(q) ||
          g.content?.toLowerCase().includes(q),
      );
    },
  },
  async mounted() {
    this.loading = true;
    const res = await apiGetGuides().catch(() => null);
    this.loading = false;
    if (!res?.success) {
      this.error = "Failed to load guides dashboard. Please try again later.";
      return;
    }
    this.guides = res.data?.data || res.data || [];
  },
};
</script>

<style scoped>
.guides-page {
  animation: fadeIn 0.4s ease-out;
  max-width: 1100px;
  margin: 0 auto;
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
  color: #1f2937;
  margin-bottom: 6px;
}

.title-area .subtitle {
  color: #6b7280;
  font-size: 1rem;
}

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
}

.search-input {
  width: 100%;
  padding: 0.75rem 1rem 0.75rem 2.5rem;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  font-size: 0.95rem;
}

.guide-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
  gap: 24px;
}

.state-container {
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
  color: var(--primary);
}

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
</style>
