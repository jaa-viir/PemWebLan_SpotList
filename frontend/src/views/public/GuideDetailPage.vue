<template>
  <div class="guide-detail-page">
    <div class="page-header">
      <button class="btn-back" @click="$router.back()">
        <i class="fa-solid fa-arrow-left"></i> Back to Guides Directory
      </button>
    </div>

    <div v-if="loading" class="state-container">
      <i class="fa-solid fa-circle-notch fa-spin state-icon loading-icon"></i>
      <p>Opening document view...</p>
    </div>

    <p v-else-if="error" class="error-text">
      <i class="fa-solid fa-circle-exclamation"></i> {{ error }}
    </p>

    <article v-else-if="guide" class="article-layout">
      <div class="banner-wrapper">
        <img
          :src="guide.banner_image_url || guide.banner_image"
          :alt="guide.title"
          class="banner-image"
        />
      </div>

      <div class="article-content">
        <div class="article-meta">
          <span class="meta-item author">
            <i class="fa-solid fa-user-shield"></i> Terverifikasi oleh
            {{ guide.user?.name || "Admin" }}
          </span>
          <span class="meta-item date">
            <i class="fa-solid fa-calendar-day"></i>
            {{ formatDate(guide.created_at) }}
          </span>
        </div>

        <h1 class="article-title">{{ guide.title }}</h1>

        <div class="article-body">
          <p class="content-text">{{ guide.content }}</p>
        </div>
      </div>
    </article>
  </div>
</template>

<script>
import { apiGetGuideDetail } from "../../services/api.js";

export default {
  name: "GuideDetailPage",
  data() {
    return {
      guide: null,
      loading: false,
      error: null,
    };
  },
  async mounted() {
    this.loading = true;
    const id = this.$route.params.id;
    const res = await apiGetGuideDetail(id).catch(() => null);
    this.loading = false;

    if (!res || !res.success) {
      this.error =
        "Panduan terkurasi tidak ditemukan atau telah dihapus oleh tim moderator.";
      return;
    }
    this.guide = res.data;
  },
  methods: {
    formatDate(dt) {
      if (!dt) return "-";
      return new Date(dt).toLocaleDateString("id-ID", { dateStyle: "long" });
    },
  },
};
</script>

<style scoped>
.guide-detail-page {
  max-width: 850px;
  margin: 0 auto;
  animation: fadeIn 0.4s ease-out;
}
.page-header {
  margin-bottom: 24px;
}
.btn-back {
  background: var(--surface);
  border: 1px solid var(--border);
  color: var(--text-main);
  padding: 10px 20px;
  border-radius: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}
.article-layout {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
}
.banner-wrapper {
  width: 100%;
  height: 380px;
  background: #f1f5f9;
}
.banner-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.article-content {
  padding: 40px;
}
.article-meta {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  font-size: 0.88rem;
  color: var(--text-muted);
}
.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
}
.meta-item.author {
  color: var(--primary);
  font-weight: 600;
}
.article-title {
  font-size: 2.25rem;
  font-weight: 800;
  color: var(--text-main);
  line-height: 1.2;
  margin-bottom: 24px;
}
.article-body {
  border-top: 1px solid var(--border);
  padding-top: 24px;
}
.content-text {
  font-size: 1.08rem;
  color: var(--text-main);
  line-height: 1.9;
  white-space: pre-line;
}
.state-container {
  text-align: center;
  padding: 80px;
  color: var(--text-muted);
}
.state-icon {
  font-size: 2.5rem;
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
@media (max-width: 640px) {
  .banner-wrapper {
    height: 220px;
  }
  .article-content {
    padding: 24px;
  }
  .article-title {
    font-size: 1.75rem;
  }
}
</style>
