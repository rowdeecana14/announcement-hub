import * as yup from "yup";

export const data = {
  start_date: "",
  end_date: "",
};

export const schema = yup.object({
  start_date: yup.date()
    .transform((value, originalValue) =>
      originalValue === '' ? null : value
    )
    .required().label("Start Date"),
    end_date: yup.date()
    .transform((value, originalValue) =>
      originalValue === '' ? null : value
    )
    .required().label("End Date"),
});
