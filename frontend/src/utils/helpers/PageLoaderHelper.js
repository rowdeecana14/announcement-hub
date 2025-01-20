import { Loading, QSpinnerHourglass } from "quasar";

export default class PageLoaderHelper {
  static show(message = "Loading. Please wait...") {
    Loading.show({
      spinner: QSpinnerHourglass,
      spinnerColor: "primary",
      spinnerSize: 100,
      message: message,
      messageColor: "dark",
    });
  }

  static hide() {
    Loading.hide();
  }
}
