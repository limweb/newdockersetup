<script setup lang="ts">
import { ref, computed } from "vue";

const apiResponse = ref<any>(null);
const isLoading = ref(false);
const error = ref<string | null>(null);

// Mock data สำหรับทดสอบ
const mockTestData = {
  name: "John Doe",
  email: "john@example.com",
  age: 30,
  items: ["item1", "item2", "item3"],
  metadata: {
    source: "web1",
    version: "1.0.0",
  },
};

// เลือก HTTP method
const selectedMethod = ref("GET");
const methods = ["GET", "POST", "PUT", "PATCH", "DELETE"];

// Query params
const queryParams = ref([
  { key: "page", value: "1" },
  { key: "limit", value: "10" },
  { key: "search", value: "test" },
]);

// Custom headers
const customHeaders = ref([
  { key: "X-Custom-Header", value: "custom-value" },
  { key: "X-Request-ID", value: "req-12345" },
]);

// Body data
const bodyData = ref(JSON.stringify(mockTestData, null, 2));

const apiBaseUrl = computed(() => import.meta.env.VITE_API_BASE_URL);

function addQueryParam() {
  queryParams.value.push({ key: "", value: "" });
}

function removeQueryParam(index: number) {
  queryParams.value.splice(index, 1);
}

function addHeader() {
  customHeaders.value.push({ key: "", value: "" });
}

function removeHeader(index: number) {
  customHeaders.value.splice(index, 1);
}

async function callTestApi() {
  isLoading.value = true;
  error.value = null;
  apiResponse.value = null;

  try {
    // สร้าง query string
    const params = new URLSearchParams();
    queryParams.value.forEach((p) => {
      if (p.key) params.append(p.key, p.value);
    });
    const queryString = params.toString() ? `?${params.toString()}` : "";

    // สร้าง headers
    const headers: Record<string, string> = {
      "Content-Type": "application/json",
    };
    customHeaders.value.forEach((h) => {
      if (h.key) headers[h.key] = h.value;
    });

    // สร้าง request options
    const options: RequestInit = {
      method: selectedMethod.value,
      headers,
      credentials: "include",
    };

    // เพิ่ม body สำหรับ POST, PUT, PATCH
    if (["POST", "PUT", "PATCH"].includes(selectedMethod.value)) {
      options.body = bodyData.value;
    }

    const response = await fetch(
      `${apiBaseUrl.value}/testapi${queryString}`,
      options,
    );

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
            <router-link
              to="/test-api"
              class="text-blue-600 hover:text-blue-800 px-3 py-2 rounded-md text-sm font-medium"
            >
              Test API
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div class="bg-white rounded-lg shadow p-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">
            Test /testapi Endpoint
          </h2>

          <!-- Method Selection -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >HTTP Method</label
            >
            <div class="flex space-x-2">
              <button
                v-for="method in methods"
                :key="method"
                @click="selectedMethod = method"
                :class="[
                  'px-4 py-2 rounded-md text-sm font-medium',
                  selectedMethod === method
                    ? 'bg-blue-500 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300',
                ]"
              >
                {{ method }}
              </button>
            </div>
          </div>

          <!-- Endpoint -->
          <div class="mb-6">
            <p class="text-sm text-gray-500 mb-2">Endpoint:</p>
            <code class="bg-gray-100 px-3 py-2 rounded text-sm block">
              {{ selectedMethod }}
              {{ apiBaseUrl }}/testapi
            </code>
          </div>

          <!-- Query Parameters -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-medium text-gray-700"
                >Query Parameters</label
              >
              <button
                @click="addQueryParam"
                class="text-blue-500 hover:text-blue-700 text-sm"
              >
                + Add Param
              </button>
            </div>
            <div
              v-for="(param, index) in queryParams"
              :key="index"
              class="flex space-x-2 mb-2"
            >
              <input
                v-model="param.key"
                placeholder="Key"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
              <input
                v-model="param.value"
                placeholder="Value"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
              <button
                @click="removeQueryParam(index)"
                class="text-red-500 hover:text-red-700 px-2"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- Custom Headers -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
              <label class="block text-sm font-medium text-gray-700"
                >Custom Headers</label
              >
              <button
                @click="addHeader"
                class="text-blue-500 hover:text-blue-700 text-sm"
              >
                + Add Header
              </button>
            </div>
            <div
              v-for="(header, index) in customHeaders"
              :key="index"
              class="flex space-x-2 mb-2"
            >
              <input
                v-model="header.key"
                placeholder="Header Name"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
              <input
                v-model="header.value"
                placeholder="Header Value"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm"
              />
              <button
                @click="removeHeader(index)"
                class="text-red-500 hover:text-red-700 px-2"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- Body (for POST, PUT, PATCH) -->
          <div
            v-if="['POST', 'PUT', 'PATCH'].includes(selectedMethod)"
            class="mb-6"
          >
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Request Body (JSON)</label
            >
            <textarea
              v-model="bodyData"
              rows="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono"
            ></textarea>
          </div>

          <!-- Call Button -->
          <button
            @click="callTestApi"
            :disabled="isLoading"
            class="bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white px-6 py-2 rounded-md font-medium"
          >
            <span v-if="isLoading">Loading...</span>
            <span v-else>Send Request</span>
          </button>

          <!-- Error -->
          <div
            v-if="error"
            class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg"
          >
            <p class="text-red-700 font-medium">Error:</p>
            <p class="text-red-600">{{ error }}</p>
          </div>

          <!-- Response -->
          <div v-if="apiResponse" class="mt-6">
            <p class="text-sm font-medium text-gray-700 mb-2">Response:</p>
            <pre
              class="bg-gray-900 text-green-400 p-4 rounded-lg text-sm overflow-auto max-h-96"
              >{{ JSON.stringify(apiResponse, null, 2) }}</pre
            >
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
