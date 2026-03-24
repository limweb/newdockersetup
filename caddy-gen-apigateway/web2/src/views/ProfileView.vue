<script setup lang="ts">
import { useAuthStore } from "@/stores";

const authStore = useAuthStore();
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
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Profile</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Subject ID</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{ authStore.user?.sub || "-" }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Email Verified</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{ authStore.user?.email_verified ? "Yes" : "No" }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Client ID</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{ authStore.user?.azp || "-" }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Authentication Time</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{
                  authStore.user?.auth_time
                    ? new Date(authStore.user.auth_time * 1000).toLocaleString()
                    : "-"
                }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Session State</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{ authStore.user?.session_state || "-" }}
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-500"
                >Token Expires</label
              >
              <p class="mt-1 text-lg text-gray-900">
                {{
                  authStore.user?.exp
                    ? new Date(authStore.user.exp * 1000).toLocaleString()
                    : "-"
                }}
              </p>
            </div>
          </div>

          <!-- Debug: แสดงข้อมูล user ทั้งหมด -->
          <details class="mt-6">
            <summary class="text-sm text-gray-500 cursor-pointer">
              Debug: Raw User Data
            </summary>
            <pre class="mt-2 p-4 bg-gray-100 rounded text-xs overflow-auto">{{
              JSON.stringify(authStore.user, null, 2)
            }}</pre>
          </details>

          <!-- Token -->
          <details class="mt-4">
            <summary class="text-sm text-gray-500 cursor-pointer">
              Access Token
            </summary>
            <pre
              class="mt-2 p-4 bg-gray-100 rounded text-xs overflow-auto break-all whitespace-pre-wrap"
              >{{ authStore.token }}</pre
            >
          </details>
        </div>
      </div>
    </main>
  </div>
</template>
