import axios from "axios";
import { Cookies as $cookies } from "quasar";
import { default as $alert } from "@utils/helpers/AlertHelper.js";
import { default as $loading } from "@utils/helpers/ActionLoaderHelper.js";

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
});

export function runInterceptors() {
  api.interceptors.request.use(async (config) => {
    if ($cookies.has("token")) {
      config.headers.authorization = "Bearer " + $cookies.get("token");
    }
    return config;
  });

  api.interceptors.response.use(
    (response) => {
      return Promise.resolve(response);
    },
    async (error) => {
      $loading.hide();
      let status = error?.response?.status || null;

      if (status === 401) {
        await $alert.expired();
        return window.location.href = "/";
      }

      return Promise.resolve(error);
    }
  );
}