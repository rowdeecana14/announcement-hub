import { to } from "await-to-js";
import { serialize } from "@utils/helpers/ResponseHelper.js";
import { api } from "@utils/services/Axios.js";

export default {
  namespaced: true,
  actions: {
    async all(context, payload = {}) {
      const [error, data] = await to(
        api.get(`/api/v1/announcements`, {
          params: payload,
        })
      );

      return serialize(error ? error.response : data);
    },

    async show(context, payload = {  }) {
      const [error, data] = await to(api.get(`/api/v1/announcements/${payload.id}`));

      return serialize(error ? error.response : data);
    },
    async create(context, payload = {}) {
      const [error, data] = await to(api.post(`/api/v1/announcements`, payload));

      return serialize(error ? error.response : data);
    },

    async update(context, payload = {}) {
      const [error, data] = await to(
        api.put(`/api/v1/announcements/${payload.id}`, payload.announcement)
      );

      return serialize(error ? error.response : data);
    },

    async updateDates(context, payload = {}) {
      const [error, data] = await to(
        api.put(`/api/v1/announcements/dates/${payload.id}`, payload.announcement)
      );

      return serialize(error ? error.response : data);
    },

    async delete(context, payload = {}) {
      const [error, data] = await to(api.delete(`/api/v1/announcements/${payload.id}`));

      return serialize(error ? error.response : data);
    },
  },
};
