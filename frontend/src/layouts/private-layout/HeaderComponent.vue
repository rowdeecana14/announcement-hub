<template>
  <q-header
    reveal
    style="height: 65px"
    class="bg-white"
    :class="$q.dark.isActive ? 'header_dark' : 'header_normal'"
  >
    <q-toolbar>
      <q-btn @click="toggleLeftDrawer" round dense icon="menu" color="grey-6" />
      <q-toolbar-title shrink class="text-weight-bold text-dark q-pt-sm"> </q-toolbar-title>

      <q-space />

      <q-btn
        round
        flat
        class="q-ml-sm"
        :class="$q.screen.gt.sm ? 'q-mr-lg' : ''"
      >
        <q-avatar size="40px">
          <img src="../../assets/user.png" />
        </q-avatar>
        <q-toolbar-title class="text-primary">
          {{ $store.getters.getUser.user.data.name }}
        </q-toolbar-title>
        <q-menu>
          <q-list class="text-light-8 menu-logout q-my-sm">
            <q-item aria-hidden="true">
              <q-item-section avatar>
                <q-icon name="account_circle" />
              </q-item-section>
              <q-item-section class="q-ml--sm">
                <div class="text-uppercase text-weight-bolder">{{ $store.getters.getUser.user.data.name }}</div>
                <div class="">{{  $store.getters.getUser.user.data.email }}</div>
              </q-item-section>
            </q-item>
            <q-item clickable v-close-popup aria-hidden="true" @click="clickLogout()">
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section class="q-ml--sm">Logout</q-item-section>
            </q-item>
          </q-list>
        </q-menu>
      </q-btn>
    </q-toolbar>
  </q-header>
</template>

<script setup>
import { reactive, ref, watch, inject, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { LogoutMessage } from "@utils/constants/AuthConstant.js";

import $store from "@store/Store.js";
import $alert from "@utils/helpers/AlertHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";
import $notify from "@utils/helpers/NotifyHelper.js";

const $router = useRouter();

const emit = defineEmits();
const props = defineProps({
  drawer: false,
  search: "",
});
const state = reactive({
  drawer: props.drawer,
  search: props.search,
  user: {
    name: '',
    email: ''
  }
});

// STATES UPDATE FOR DRAWER
watch(
  () => state.drawer,
  (newValue) => {
    if (newValue === props.drawer) {
      return;
    }
    emit("update:drawer", newValue);
  },
  { deep: true }
);

watch(
  () => props.drawer,
  (newValue) => {
    if (newValue === state.drawer) {
      return;
    }
    state.drawer = newValue;
  },
  { deep: true }
);

// STATES UPDATE FOR SEARCH
watch(
  () => state.search,
  (newValue) => {
    if (newValue === props.search) {
      return;
    }
    emit("update:search", newValue);
  },
  { deep: true }
);

watch(
  () => props.search,
  (newValue) => {
    if (newValue === state.search) {
      return;
    }
    state.search = newValue;
  },
  { deep: true }
);

function toggleLeftDrawer() {
  state.drawer = !state.drawer;
}

async function clickLogout() {
  try {
    const alert = await $alert.confirm("Logout Account", LogoutMessage.CONFIRM, "Logout", "Cancel");

    if (!alert.isConfirmed) {
      return;
    }

    $loading.show();

    const response = await $store.dispatch("Auth/logout");

    if (![200, 201].includes(response.status)) {
      $loading.hide();
      return $notify.error(response?.data?.message ?? LogoutMessage.ERROR);
    }

    $store.commit("setLogout");
    $notify.success(response?.data?.message ?? LogoutMessage.SUCCESS);

    setTimeout(() => {
      $loading.hide();
      $router.replace({ name: "public.home" });
    }, 500);
  } catch (error) {
    console.error(error);
    $loading.hide();
  }
}
</script>
