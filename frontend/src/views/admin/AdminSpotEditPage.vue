<template>
  <div>
    <p v-if="fetching">Loading spot...</p>

    <SpotForm
      v-else
      :spot="spot"
      :loading="loading"
      :error="error"
      @submit="updateSpot"
    />
  </div>
</template>

<script>
import SpotForm from '../../components/SpotForm.vue'
import { useFlashStore } from '../../stores/flash.js'
import { apiGetSpot, apiUpdateSpot } from '../../services/api.js'

export default {
  name: 'AdminSpotEditPage',
  components: { SpotForm },
  setup() { return { flash: useFlashStore() } },
  data() {
    return { spot: null, fetching: false, loading: false, error: null }
  },
  async mounted() {
    this.fetching = true
    const res = await apiGetSpot(this.$route.params.id).catch(() => null)
    this.fetching = false
    if (!res?.success) { this.error = 'Spot not found.'; return }
    this.spot = res.data
  },
  methods: {
    async updateSpot(formData) {
      this.loading = true
      this.error = null
      const res = await apiUpdateSpot(this.$route.params.id, formData)
      this.loading = false
      if (!res.success) {
        this.error = res.message || 'Failed to update spot.'
        return
      }
      this.flash.success('Spot berhasil diperbarui!')
      this.$router.push('/admin/spots')
    },
  },
}
</script>
