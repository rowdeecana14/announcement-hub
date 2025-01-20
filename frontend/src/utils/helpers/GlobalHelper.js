import moment from "moment";

export default class GlobalHelper {
  static statusHtml(status) {
    let statuses = {
      active: '<span class="badge badge-success">Active</span>',
      inactive: '<span class="badge badge-warning">Inactive</span>',
    };

    return statuses[status.toLowerCase()] || "";
  }

  static formatDate(date, format = "MMM. DD, YYYY hh:mm A") {
    return date ? moment(new Date(date)).format(format) : null;
  }

  static formatNumber(number = "") {
    return number.toLocaleString();
  }

  static formatAmount(amount = "") {
    let isNumber = typeof amount === "number";
    return isNumber ? amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,") : 0;
  }
}
