import * as yup from "yup";

export const data = {
  title: "",
  content: "",
};

export const schema = yup.object({
  title: yup.string().required().label("Title"),
  content: yup.string().required().label("Content"),
});
