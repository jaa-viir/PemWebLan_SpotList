<template>
  <div class="guide-form-wrapper">
    <div class="form-card">
      <div class="form-header">
        <h2>{{ isEdit ? "Edit Guide" : "Create New Guide" }}</h2>
        <p class="subtitle">
          {{
            isEdit
              ? "Update the instructions and insights for this activity."
              : "Fill in the structured sections to publish a new activity guide."
          }}
        </p>
      </div>

      <div class="form-body">
        <div class="form-group">
          <label><i class="fa-solid fa-heading"></i> Guide Title</label>
          <input
            v-model="form.title"
            placeholder="e.g., Ultimate Checklist for Surabaya Night Market"
          />
        </div>

        <div class="form-group">
          <label><i class="fa-solid fa-file-lines"></i> Detailed Content</label>
          <textarea
            v-model="form.content"
            placeholder="Write step-by-step preparations, insider tips, and important instructions..."
            rows="12"
          />
        </div>

        <div class="form-group">
          <label><i class="fa-solid fa-image"></i> Banner Cover Image</label>
          <input
            type="file"
            accept="image/*"
            @change="handleFile"
            class="file-input"
          />
          <p v-if="isEdit" class="input-hint">
            Leave empty to retain the current banner image.
          </p>
        </div>
      </div>

      <div v-if="error" class="error-box">
        <i class="fa-solid fa-circle-exclamation"></i> {{ error }}
      </div>

      <div class="form-actions">
        <button class="btn-secondary" @click="$router.back()">Cancel</button>
        <button class="btn-primary" @click="submit" :disabled="loading">
          <i v-if="loading" class="fa-solid fa-spinner fa-spin"></i>
          {{
            loading ? "Saving..." : isEdit ? "Save Changes" : "Publish Guide"
          }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "GuideForm",
  props: {
    guide: { type: Object, default: null },
    loading: { type: Boolean, default: false },
    error: { type: String, default: null },
  },
  computed: {
    isEdit() {
      return !!this.guide;
    },
  },
  data() {
    return {
      form: {
        title: "",
        content: "",
      },
      imageFile: null,
    };
  },
  watch: {
    guide: {
      immediate: true,
      handler(val) {
        if (!val) return;
        this.form = {
          title: val.title || "",
          content: val.content || "",
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
      fd.append("title", this.form.title);
      fd.append("content", this.form.content);

      if (this.imageFile) {
        fd.append("banner_image", this.imageFile);
      }

      this.$emit("submit", fd);
    },
  },
};
</script>

<style scoped>
.guide-form-wrapper {
  max-width: 800px;
  margin: 0 auto;
  animation: fadeIn 0.3s ease-out;
}

.form-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 24px;
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

.form-group {
  margin-bottom: 20px;
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

input,
textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid var(--border);
  border-radius: 12px;
  background: #f8fafc;
  color: var(--text-main);
  font-size: 0.95rem;
  transition: all 0.2s;
}

input:focus,
textarea:focus {
  outline: none;
  border-color: #cbd5e1;
  background: #ffffff;
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
  border-radius: 12px;
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

.btn-secondary {
  background: #f1f5f9;
  color: var(--text-main);
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-primary {
  background: var(--primary);
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
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
</style>
