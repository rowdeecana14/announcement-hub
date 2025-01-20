import { nextTick } from "vue";

export default class ValidationHelper {
  static async focus(errors) {
    const first_field = document.querySelector(`form [name="${Object.keys(errors)[0]}"]`);

    if (first_field) {
      await first_field.focus();
      first_field.scrollIntoView({
        behavior: "smooth",
        block: "end",
        inline: "nearest",
      });
      await nextTick();
    }
  }
}
