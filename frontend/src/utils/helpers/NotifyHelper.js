import { Notify } from "quasar";
export default class NotifyHelper {
  static info(message = "Test alert", position = "top-right") {
    Notify.create({
      color: "info",
      icon: "info",
      position: position,
      message: message,
      progress: true,
    });
  }

  static success(message = "Test alert", position = "top-right") {
    Notify.create({
      color: "positive",
      icon: "check",
      position: position,
      message: message,
      progress: true,
    });
  }

  static warning(message = "Test alert", position = "top-right") {
    Notify.create({
      type: "warning",
      icon: "warning",
      position: position,
      message: message,
      progress: true,
    });
  }

  static error(message = "Test alert", position = "top-right") {
    Notify.create({
      type: "negative",
      position: position,
      message: message,
      progress: true,
    });
  }
}
