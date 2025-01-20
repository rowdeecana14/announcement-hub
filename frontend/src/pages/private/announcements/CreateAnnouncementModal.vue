<template>
  <template>
    <q-dialog persistent ref="dialogRef" full-height v-model="states.modal">
      <q-card class="column full-height" style="width: 700px; max-width: 80vw">
        <q-card-section>
          <q-toolbar class="row justify-between">
            <q-toolbar-title shrink class="text-center text-weight-bold text-dark q-pt-sm">
              <q-icon name="edit_square" /> Create Announcement
            </q-toolbar-title>
            <q-btn
              class="q-ml-sm"
              round
              outline
              size="sm"
              color="accent"
              icon="close"
              v-close-popup
            >
              <q-tooltip>Click to Close</q-tooltip>
            </q-btn>
          </q-toolbar>
        </q-card-section>

        <q-separator />

        <q-card-section class="col q-pt-5">
          <Form
            @submit="onSubmit"
            @invalid-submit="onInvalidSubmit"
            :initial-values="states.payload"
            :validation-schema="schema"
            id="create_announcement_form"
            v-slot="{ errors, meta }"
            class="q-gutter-md"
          >
            <Field id="title" name="title" v-slot="{ field, errorMessage, meta }">
              <q-input
                label="Title"
                outlined
                standout
                type="text"
                borderless
                v-bind="field"
                bg-color="grey-12"
                :model-value="field.value"
                :error-message="errorMessage"
                :error="meta.touched && !meta.valid"
              >
              </q-input>
            </Field>

            <Field id="content" name="content" v-slot="{ field, errorMessage, meta }">
              <q-input
                label="Content"
                bottom-slots
                bg-color="grey-12"
                outlined
                standout
                borderless
                type="textarea"
                rows="12"
                v-bind="field"
                :model-value="field.value"
                :error-message="errorMessage"
                :error="meta.touched && !meta.valid"
              />
            </Field>

            <Field id="start_date" name="start_date" v-slot="{ field, errorMessage, meta }">
              <q-input
                v-bind="field"
                :model-value="field.value"
                :error-message="errorMessage"
                :error="meta.touched && !meta.valid"
                label="Start date"
                bg-color="grey-12"
                outlined
                borderless
                dense
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-date v-bind="field" :model-value="field.value">
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="OKAY" color="primary" outline />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </Field>

            <Field id="end_date" name="end_date" v-slot="{ field, errorMessage, meta }">
              <q-input
                v-bind="field"
                :model-value="field.value"
                :error-message="errorMessage"
                :error="meta.touched && !meta.valid"
                label="End date"
                bg-color="grey-12"
                outlined
                borderless
                dense
              >
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-date v-bind="field" :model-value="field.value">
                        <div class="row items-center justify-end">
                          <q-btn v-close-popup label="OKAY" color="primary" outline />
                        </div>
                      </q-date>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </Field>
          </Form>
        </q-card-section>

        <q-separator />

        <q-card-actions align="right" class="modal-footer-buttons q-my-sm">
          <q-btn outline icon="cancel" color="accent" label="Close" v-close-popup />
          <q-btn
            class="q-mr-sm"
            outline
            icon="check_circle"
            color="info"
            label="Submit"
            type="submit"
            form="create_announcement_form"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </template>
</template>

<script setup>
import { reactive, inject, watch } from "vue";
import { Field, Form } from "vee-validate";
import { schema, data } from "@validations/announcement/CreateAnnouncementValidation.js";
import { AddMessage } from "@utils/constants/AnnouncementConstant.js";

import $store from "@store/Store.js";
import $alert from "@utils/helpers/AlertHelper.js";
import $notify from "@utils/helpers/NotifyHelper.js";
import $validate from "@utils/helpers/ValidationHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";

const states = reactive({
  modal: false,
  payload: {
    title: "",
    content: "",
    start_date: "",
    end_date: "",
  },
});
const props = defineProps({
  refreshAnnouncements: {
    type: Function,
  },
});

async function showCreateAnnouncementModal() {
  states.modal = true;
}

async function onSubmit(payload) {
  try {
    const alert = await $alert.confirm("Confirm Message", AddMessage.CONFIRM);

    if (!alert.isConfirmed) {
      return;
    }
    $loading.show();

    const response = await $store.dispatch("Announcement/create", payload);

    if (![200, 201].includes(response.status)) {
      $loading.hide();
      return $notify.error(response.data.message || AddMessage.ERROR);
    }

    $loading.hide();
    states.modal = false;
    $notify.info(response.data.message || AddMessage.SUCCESS);
    props.refreshAnnouncements();
  } catch (error) {
    console.log(error);
    $loading.hide();
    $notify.error(AddMessage.ERROR);
  }
}

async function onInvalidSubmit({ values, errors, results }) {
  $notify.error(AddMessage.ERROR);
  $validate.focus(errors);
}

defineExpose({ showCreateAnnouncementModal });
</script>
