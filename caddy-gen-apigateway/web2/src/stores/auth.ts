import { defineStore } from "pinia";
import { ref, computed } from "vue";
import keycloak from "@/keycloak";

export const useAuthStore = defineStore("auth", () => {
  const isAuthenticated = ref(false);
  const user = ref<any>(null);
  const token = ref<string | null>(null);

  const username = computed(() => user.value?.preferred_username || "");

  async function init() {
    try {
      console.log("Initializing Keycloak...");
      const authenticated = await keycloak.init({
        onLoad: "login-required",
        checkLoginIframe: false,
      });

      console.log("Keycloak authenticated:", authenticated);
      console.log("Keycloak token:", keycloak.token ? "exists" : "null");
      console.log("Keycloak tokenParsed:", keycloak.tokenParsed);

      isAuthenticated.value = authenticated;
      if (authenticated) {
        // ดึงข้อมูลจาก token ก่อน
        user.value = { ...keycloak.tokenParsed };
        token.value = keycloak.token || null;

        console.log("Store token set:", token.value ? "exists" : "null");

        // ดึง user profile เพิ่มเติมจาก Keycloak
        try {
          const profile = await keycloak.loadUserProfile();
          console.log("Keycloak profile loaded:", profile);
          user.value = {
            ...user.value,
            ...profile,
          };
        } catch (profileError) {
          console.warn("Could not load user profile:", profileError);
          console.log("Available token data:", keycloak.tokenParsed);
        }
      }
    } catch (error) {
      console.error("Keycloak init failed:", error);
    }
  }

  function login() {
    console.log("Login called");
    keycloak.login();
  }

  function logout() {
    keycloak.logout();
  }

  function getToken() {
    console.log(
      "getToken called, keycloak.token:",
      keycloak.token ? "exists" : "null",
    );
    return keycloak.token;
  }

  async function refreshToken() {
    try {
      console.log("Refreshing token...");
      const refreshed = await keycloak.updateToken(30);
      console.log("Token refreshed:", refreshed);
      console.log("New token:", keycloak.token ? "exists" : "null");

      if (refreshed) {
        token.value = keycloak.token || null;
        user.value = { ...keycloak.tokenParsed };
        console.log("Store updated with new token");
      }
    } catch (error) {
      console.error("Token refresh failed:", error);
      logout();
    }
  }

  return {
    isAuthenticated,
    user,
    token,
    username,
    init,
    login,
    logout,
    getToken,
    refreshToken,
  };
});
