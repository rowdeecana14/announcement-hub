import { Loading } from "quasar";

export default class ActionLoaderHelper {
  static show(message = "Loading. Please wait...") {
    Loading.show({
      message: message,
      boxClass: "bg-white",
      spinnerColor: "primary",
      messageColor: "dark",
    });
  }

  static hide() {
    Loading.hide();
  }
}
