import axios from "axios";
import { to } from "await-to-js";
import { serialize } from "@utils/helpers/ResponseHelper.js";
import { api } from "@utils/services/Axios.js";

export default {
  namespaced: true,
  actions: {
    async token(context, payload = {}) {},

    async login(context, payload = {}) {

      const [error, data] = await to(api.post(`/api/v1/auth/login`, payload));

      return serialize(error ? error.response : data);
    },

    async logout({ commit, rootState }, payload = {}) {
      const [error, data] = await to(api.get(`/api/v1/auth/logout`));

      return serialize(error ? error.response : data);
    },
  },
};
