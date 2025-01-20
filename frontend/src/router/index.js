import { createRouter, createWebHistory } from "vue-router";
import routes from "@app/src/router/routes";
import $loader from "@utils/helpers/PageLoaderHelper";
import $store from "@store/Store.js";
import $alert from "@utils/helpers/AlertHelper.js";

const router = createRouter({
  base: import.meta.env.VITE_BASE_URL,
  history: createWebHistory(),
  routes: routes,
});

router.beforeEach(async (to, from, next) => {
  $loader.show();

  if ("middleware" in to.meta) {
    if (to.meta.middleware === "auth") {
      let hasToken = $store.getters.hasToken;

      if (!hasToken) {
        $loader.hide();
        await $alert.expired();
        return (window.location.href = "/");
      }
    }
  }

  return next();
});

router.afterEach(() => {
  $loader.hide();
});

export default router;
