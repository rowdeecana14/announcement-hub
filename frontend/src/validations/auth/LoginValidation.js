import * as yup from "yup";

export const data = {
  email: "",
  password: "",
};

export const schema = yup.object({
  email: yup.string().required().email().label("Email"),
  password: yup.string().required().label("Password"),
});
