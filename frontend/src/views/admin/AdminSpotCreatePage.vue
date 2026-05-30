<template>
  <div>
    <SpotForm
      :loading="loading"
      :error="error"
      @submit="createSpot"
    />
  </div>
</template>

<script>
import SpotForm from '../../components/SpotForm.vue'
import { useFlashStore } from '../../stores/flash.js'
import { apiCreateSpot } from '../../services/api.js'

export default {
  name: 'AdminSpotCreatePage',
  components: { SpotForm },
  setup() { return { flash: useFlashStore() } },
  data() {
    return { loading: false, error: null }
  },
  methods: {
    async createSpot(formData) {
      this.loading = true
      this.error = null
      const res = await apiCreateSpot(formData)
      this.loading = false
      if (!res.success) {
        this.error = res.message || 'Failed to create spot.'
        return
      }
      this.flash.success('Spot berhasil ditambahkan!')
      this.$router.push('/admin/spots')
    },
  },
}
</script>
