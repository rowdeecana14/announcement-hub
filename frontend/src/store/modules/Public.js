import { to } from "await-to-js";
import { serialize } from "@utils/helpers/ResponseHelper.js";
import { api } from "@utils/services/Axios.js";

export default {
  namespaced: true,
  actions: {
    async announcement(context, payload = {}) {
      const [error, data] = await to(
        api.get(`/api/v1/public/announcements`, {
          params: payload,
        })
      );

      return serialize(error ? error.response : data);
    },

  },
};
