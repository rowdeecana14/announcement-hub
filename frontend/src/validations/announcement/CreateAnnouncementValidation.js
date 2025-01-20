import * as yup from "yup";

export const data = {
  title: "",
  content: "",
  start_date: "2025-01-20",
  end_date: "2025-01-20",
};

export const schema = yup.object({
  title: yup.string().required().label("Title"),
  content: yup.string().required().label("Content"),
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
