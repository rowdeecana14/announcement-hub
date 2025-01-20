import { createStore } from "vuex";
import { Cookies as $cookies } from "quasar";
import Auth from "@store/modules/Auth.js";
import Announcement from "@store/modules/Announcement.js";
import Public from "@store/modules/Public.js";
import HashingHelper from "@utils/helpers/HashingHelper.js";


const store = createStore({
  state: {
    BASE_API: import.meta.env.VITE_API_URL,
  },
  mutations: {
    setLogin: (state, user) => {
      const hashed = HashingHelper().encrypt({
        user
      });
      $cookies.set("welcome", true);
      $cookies.set("user", hashed);
      $cookies.set("token", user.token);
    },
    setLogout: (state) => {
      $cookies.remove("welcome");
      $cookies.remove("user");
      $cookies.remove("token");
    },
  },
  getters: {
    getUser: (state) => {
      if ($cookies.has("user")) {
        const user = $cookies.get("user");
        const decrypted = HashingHelper().decrypt(user);

        return decrypted;
      }

      return null;
    },

    hasToken: (state) => {
      return $cookies.has("token");
    },
  },
  modules: {
    Auth,
    Public,
    Announcement
  },
});

export default store;
