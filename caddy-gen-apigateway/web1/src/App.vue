<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useAuthStore } from '@/stores'

const authStore = useAuthStore()
const isLoading = ref(true)

onMounted(async () => {
  await authStore.init()
  isLoading.value = false
})
</script>

<template>
  <div v-if="isLoading" class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
      <p class="mt-4 text-gray-600">Authenticating...</p>
    </div>
  </div>
  <router-view v-else />
</template>
