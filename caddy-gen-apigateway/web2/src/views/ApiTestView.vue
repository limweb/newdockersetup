<script setup lang="ts">
import { ref } from "vue";
import { useAuthStore } from "@/stores";

const authStore = useAuthStore();
const apiResponse = ref<any>(null);
const isLoading = ref(false);
const error = ref<string | null>(null);

async function callApi() {
  isLoading.value = true;
  error.value = null;
  apiResponse.value = null;

  try {
    // Refresh token ก่อนเรียก API
    await authStore.refreshToken();

    const response = await fetch("https://api.shopsthai.com/me", {
      method: "GET",
      headers: {
        Authorization: `Bearer ${authStore.token}`,
        "Content-Type": "application/json",
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    apiResponse.value = await response.json();
  } catch (err: any) {
    error.value = err.message || "Unknown error";
  } finally {
    isLoading.value = false;
  }
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold text-gray-900">
              Vue Keycloak App
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link
              to="/"
              class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              Home
            </router-link>
            <router-link
              to="/profile"
              class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              Profile
            </router-link>
            <router-link
              to="/api-test"
              class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
            >
              API Test
            </router-link>
            <button
              @click="authStore.logout()"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">API Test</h2>

          <div class="mb-6">
            <p class="text-sm text-gray-500 mb-2">Endpoint:</p>
            <code class="bg-gray-100 px-3 py-2 rounded text-sm block"
              >POST https://api.shopsthai.com/me</code
            >
          </div>

          <button
            @click="callApi"
            :disabled="isLoading"
            class="bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white px-6 py-2 rounded-md font-medium"
          >
            <span v-if="isLoading">Loading...</span>
            <span v-else>Call API</span>
          </button>

          <div
            v-if="error"
            class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg"
          >
            <p class="text-red-700 font-medium">Error:</p>
            <p class="text-red-600">{{ error }}</p>
          </div>

          <div v-if="apiResponse" class="mt-6">
            <p class="text-sm font-medium text-gray-700 mb-2">Response:</p>
            <pre class="bg-gray-100 p-4 rounded-lg text-sm overflow-auto">{{
              JSON.stringify(apiResponse, null, 2)
            }}</pre>
          </div>

          <details class="mt-6">
            <summary class="text-sm text-gray-500 cursor-pointer">
              Current Token
            </summary>
            <div class="mt-2 p-4 bg-gray-100 rounded">
              <p class="text-xs mb-2">
                Token Length: {{ authStore.token?.length || 0 }}
              </p>
              <p class="text-xs mb-2">
                Token Preview: {{ authStore.token?.substring(0, 50) }}...
              </p>
              <pre
                class="text-xs overflow-auto break-all whitespace-pre-wrap"
                >{{ authStore.token }}</pre
              >
            </div>
          </details>
        </div>
      </div>
    </main>
  </div>
</template>
