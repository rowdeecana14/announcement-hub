import Swal from "sweetalert2";

export default class AlertHelper {
  static info(text, confirmButtonText = "Close") {
    return Swal.fire({
      icon: "success",
      title: "Success!",
      text,
      confirmButtonText,
      allowOutsideClick: false,
      allowEscapeKey: false,
    });
  }

  static confirm(title = "", message = "", confirm = "YES", cancel = "NO") {
    return Swal.fire({
      icon: "question",
      title: title,
      text: message,
      confirmButtonText: confirm,
      cancelButtonText: cancel,
      showCancelButton: true,
      reverseButtons: true,
      allowOutsideClick: false,
      allowEscapeKey: false,
      confirmButtonColor: "#2196f3",
      cancelButtonColor: "#d33",
      allowOutsideClick: false,
      allowEscapeKey: false,
    });
  }

  static expired() {
    return Swal.fire({
      icon: 'warning',
      title: "Unauthorized",
      text: `Session expired.`,
      confirmButtonText: "Okay",
      allowOutsideClick:false
    });
  }
}
