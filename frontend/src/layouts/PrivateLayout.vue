<template>
  <q-layout view="lHh LpR fFf" class="bg-image">
    <HeaderComponent v-model:drawer="state.drawer" v-model:search="state.search" />
    <DrawerComponent v-model:drawer="state.drawer" />
    <q-page-container>
      <router-view :drawer="state.drawer" />
    </q-page-container>
  </q-layout>
</template>

<script setup>
import HeaderComponent from "@layouts/private-layout/HeaderComponent.vue";
import DrawerComponent from "@layouts/private-layout/DrawerComponent.vue";
import { reactive, computed, watch, ref, onMounted, inject, nextTick } from "vue";
import { useRoute } from "vue-router";
import { default as $store } from "@store/Store.js";
import $notify from "@utils/helpers/NotifyHelper.js";

const route = useRoute();
const currentRoute = ref(route.path);

let state = reactive({
  drawer: false,
  search: "",
  entities: {
    loading: false,
    selected: "",
    options: [],
  },
});

watch(
  () => route.path,
  (newValue) => {
    currentRoute.value = newValue;
  }
);
</script>
