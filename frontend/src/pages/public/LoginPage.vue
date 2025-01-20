<template>
  <q-layout>
    <HeaderComponent />
    <q-page-container>
      <q-page class="flex bg-image flex-center">
        <q-card class="login-container">
          <q-card-section>
            <div class="text-center q-pt-lg">
              <q-toolbar>
                <q-toolbar-title class="text-center" ellipsis>
                  <q-avatar size="70px">
                    <img src="../../assets/ah.png" />
                  </q-avatar>
                  Announcement Hub
                </q-toolbar-title>
              </q-toolbar>
            </div>
          </q-card-section>
          <q-card-section class="q-mx-md">
            <Form
              @submit="onSubmit"
              @invalid-submit="onInvalidSubmit"
              :initial-values="data"
              :validation-schema="schema"
              id="login_form"
              v-slot="{ errors, meta }"
              class="q-gutter-md"
            >
              <Field id="email" name="email" v-slot="{ field, errorMessage, meta }">
                <q-input
                  label="Email"
                  outlined
                  standout
                  type="email"
                  v-bind="field"
                  :model-value="field.value"
                  :error-message="errorMessage"
                  :error="meta.touched && !meta.valid"
                >
                  <template v-slot:append>
                    <q-icon name="mail" class="cursor-pointer" />
                  </template>
                </q-input>
              </Field>

              <Field id="password" name="password" v-slot="{ field, errorMessage, meta }">
                <q-input
                  type="password"
                  label="Password"
                  outlined
                  standout
                  v-bind="field"
                  :model-value="field.value"
                  :error-message="errorMessage"
                  :error="meta.touched && !meta.valid"
                >
                  <template v-slot:append>
                    <q-icon name="lock" class="cursor-pointer" />
                  </template>
                </q-input>
              </Field>

              <div class="q-my-md">
                <q-btn
                  class="full-width q-mb-sm btn-login"
                  label="Log in"
                  size="lg"
                  type="submit"
                  icon-right="login"
                  no-caps
                  ellipsis
                />
              </div>
            </Form>
          </q-card-section>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { Field, Form } from "vee-validate";
import { useRouter } from "vue-router";

import $store from "@store/Store.js";
import $notify from "@utils/helpers/NotifyHelper.js";
import $validate from "@utils/helpers/ValidationHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";

import { schema, data } from "@validations/auth/LoginValidation.js";
import { LoginMessage } from "@utils/constants/AuthConstant.js";
import HeaderComponent from "@layouts/public-layout/HeaderComponent.vue";

const $router = useRouter();

async function onSubmit(payload) {
  try {
    $loading.show(LoginMessage.LOADING);

    const response = await $store.dispatch("Auth/login", payload);

    if (![200, 201].includes(response.status)) {
      $loading.hide();
      return $notify.error(response?.data?.message ?? LoginMessage.ERROR);
    }

    $store.commit("setLogin", { token: response.data.token, data: response.data.data });
    $notify.success(response?.data?.message ?? LoginMessage.SUCCESS);
    $loading.hide();

    setTimeout(() => {
      $router.replace({ name: "manage.announcements" });
    }, 500);
  } catch (error) {
    console.error(error);
    $loading.hide();
  }
}

async function onInvalidSubmit({ values, errors, results }) {
  $notify.error(LoginMessage.VALIDATION);
  $validate.focus(errors);
}
</script>
