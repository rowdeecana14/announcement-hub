<template>
  <template>
    <q-dialog persistent ref="dialogRef"  v-model="states.modal">
      <q-card class="column" style="width: 700px; max-width: 80vw">
        <q-card-section>
          <q-toolbar class="row justify-between">
            <q-toolbar-title shrink class="text-center text-weight-bold text-dark q-pt-sm">
              <q-icon name="edit_square" /> Update Announcement
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
            id="update_announcement_form"
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
            form="update_announcement_form"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </template>
</template>

<script setup>
import { reactive, nextTick } from "vue";
import { Field, Form } from "vee-validate";
import { schema } from "@validations/announcement/UpdateAnnouncementValidation.js";
import { EditMessage, GetMessage } from "@utils/constants/AnnouncementConstant.js";

import $store from "@store/Store.js";
import $alert from "@utils/helpers/AlertHelper.js";
import $notify from "@utils/helpers/NotifyHelper.js";
import $validate from "@utils/helpers/ValidationHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";

const states = reactive({
  modal: false,
  loading: false,
  announcement: null,
  payload: {
    title: "",
    content: ""
  }
});
const props = defineProps({
  refreshAnnouncements: {
    type: Function,
  },
});

async function getAnnouncement(id) {
  try {
    $loading.show();
    states.loading = true;

    const response = await $store.dispatch("Announcement/show", { id })

    if (![200, 201].includes(response.status)) {
      $notify.error(response?.data?.message ?? GetMessage.ERROR);
    }

    states.announcement = response?.data?.data ?? [];

    $loading.hide();
    await nextTick();
  } catch (error) {
    console.log(error);
    $loading.hide();
    $notify.error(GetMessage.ERROR);
  }
}

async function showUpdateAnnouncementModal({ id }) {
 await getAnnouncement(id);
  states.payload.title = states.announcement.title;
  states.payload.content = states.announcement.content;
  states.modal = true;
}

async function onSubmit(payload) {
  try {
    const alert = await $alert.confirm("Confirm Message", EditMessage.CONFIRM);

    if (!alert.isConfirmed) {
      return;
    }
    $loading.show();

    const response = await $store.dispatch("Announcement/update", {
      id: states.announcement.id,
      announcement: payload
    });

    if (![200, 201].includes(response.status)) {
      $loading.hide();
      return $notify.error(response.data.message || EditMessage.ERROR);
    }

    $loading.hide();
    states.modal = false;
    $notify.info(response.data.message || EditMessage.SUCCESS);
    props.refreshAnnouncements();
  } catch (error) {
    console.log(error);
    $loading.hide();
    $notify.error(EditMessage.ERROR);
  }
}

async function onInvalidSubmit({ values, errors, results }) {
  $notify.error(EditMessage.ERROR);
  $validate.focus(errors);
}

defineExpose({ showUpdateAnnouncementModal });
</script>
