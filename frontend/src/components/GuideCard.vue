<template>
  <div class="guide-card">
    <div class="thumbnail-wrapper">
      <img
        :src="guide.banner_image || '/placeholder.jpg'"
        :alt="guide.title"
        class="guide-thumbnail"
      />
    </div>

    <div class="guide-body">
      <div class="meta-header">
        <span class="author-tag">
          <i class="fa-solid fa-user-nib"></i> By
          {{ guide.user?.name || "Admin" }}
        </span>
        <span class="date-tag">
          <i class="fa-solid fa-calendar-day"></i>
          {{ formatDate(guide.created_at) }}
        </span>
      </div>

      <h3 class="guide-title">{{ guide.title }}</h3>

      <p class="guide-excerpt">{{ getExcerpt(guide.content) }}</p>
    </div>

    <div class="guide-footer">
      <router-link :to="`/guides/${guide.id}`" class="btn-view">
        <i class="fa-solid fa-book-open"></i> Read Guide
      </router-link>

      <button
        v-if="isAdmin"
        class="btn-delete"
        @click="$emit('delete', guide.id)"
        title="Delete Guide"
      >
        <i class="fa-solid fa-trash"></i>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "GuideCard",
  props: {
    // Objek data tunggal guide dari looping v-for di halaman dashboard
    guide: {
      type: Object,
      required: true,
    },
    // Status penentu apakah kartu dirender di halaman Admin atau User umum
    isAdmin: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    /**
     * Mengubah format ISO Date dari database menjadi format lokal Indonesia (contoh: 31 Mei 2026)
     */
    formatDate(dt) {
      if (!dt) return "-";
      return new Date(dt).toLocaleDateString("id-ID", {
        dateStyle: "medium",
      });
    },

    /**
     * Memotong isi teks artikel jika terlalu panjang untuk menjaga konsistensi tinggi kartu grid
     */
    getExcerpt(text) {
      if (!text) return "";
      const limit = 110; // Batas maksimal karakter teks di dashboard list
      return text.length > limit ? text.substring(0, limit) + "..." : text;
    },
  },
};
</script>

<style scoped>
/* ── Struktur Kotak Kartu ── */
.guide-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition:
    transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1),
    border-color 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

/* ── Efek Hover Modern ── */
.guide-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 32px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

/* ── Pembungkus & Kontrol Dimensi Gambar ── */
.thumbnail-wrapper {
  height: 190px;
  background: #f1f5f9;
  border-bottom: 1px solid var(--border);
}

.guide-thumbnail {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ── Area Konten Teks ── */
.guide-body {
  padding: 24px 24px 16px 24px;
  flex: 1; /* Mengisi ruang kosong agar posisi tombol aksi di footer selalu presisi sejajar */
  display: flex;
  flex-direction: column;
}

.meta-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.author-tag {
  color: var(--primary);
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: flex;
  align-items: center;
  gap: 6px;
}

.date-tag {
  color: var(--text-muted);
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 4px;
}

.guide-title {
  margin: 0 0 12px 0;
  font-size: 1.25rem;
  font-weight: 800;
  color: var(--text-main);
  line-height: 1.4;
}

.guide-excerpt {
  font-size: 0.9rem;
  color: var(--text-muted);
  line-height: 1.6;
  margin: 0;
}

/* ── Area Tombol Navigasi Bawah ── */
.guide-footer {
  padding: 0 24px 24px 24px;
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn-view {
  flex: 1;
  background: #f8fafc;
  color: var(--text-main);
  text-align: center;
  padding: 12px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all 0.2s ease;
  border: 1px solid var(--border);
}

.btn-view:hover {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
}

.btn-delete {
  background: #fef2f2;
  color: #ef4444;
  border: none;
  border-radius: 12px;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s ease;
}

.btn-delete:hover {
  background: #fee2e2;
}
</style>
