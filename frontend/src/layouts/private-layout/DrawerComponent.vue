<template>
  <q-drawer
    v-model="state.drawer"
    class="left-navigation bg-primary text-white drawer"
    show-if-above
    elevated
    bordered
    side="left"
  >
    <div class="full-height" :class="$q.dark.isActive ? 'drawer_dark' : 'drawer_normal'">
      <div style="height: calc(100% - 117px); padding: 10px">
        <q-toolbar>
          <q-avatar>
            <img src="../../assets/announcement.jpeg" />
          </q-avatar>

          <q-toolbar-title>Announcement Hub</q-toolbar-title>
        </q-toolbar>
        <hr />
        <q-scroll-area style="height: 100%">
          <q-list padding>
            <template v-for="(menu, index) in menus" :key="index">
              <q-expansion-item
                v-if="menu.submenus.length > 0"
                :icon="menu.icon"
                :label="menu.label"
                active-class="tab-active"
                class="q-ma-sm expansion-item"
              >
                <q-list class="">
                  <template v-for="(submenu, submenu_index) in menu.submenus" :key="submenu_index">
                    <q-separator :key="'sep-submenu' + index" v-if="submenu.separator" />
                    <q-item
                      active-class="q-item-no-link-highlighting tab-active "
                      exact
                      clickable
                      :to="submenu.path"
                    >
                      <q-item-section avatar class="q-pl-lg">
                        <q-icon class="submenu-icons" name="radio_button_checked" />
                      </q-item-section>
                      <q-item-section class="q-pl-md">
                        <q-item-label>{{ submenu.label }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </template>
                </q-list>
              </q-expansion-item>

              <q-item
                v-else
                :to="menu.path"
                exact
                clickable
                class="q-ma-sm navigation-item"
                active-class="tab-active"
              >
                <q-item-section avatar>
                  <q-icon :name="menu.icon" />
                </q-item-section>
                <q-item-section>
                  {{ menu.label }}
                </q-item-section>
              </q-item>
              <q-separator :key="'sep' + index" v-if="menu.separator" />
            </template>
            <q-item
              exact
              clickable
              class="q-ma-sm navigation-item"
              active-class="tab-active"
              @click="clickLogout()"
            >
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section> Logout </q-item-section>
            </q-item>
            <q-separator key="seperator_logout" />
          </q-list>
        </q-scroll-area>
      </div>
    </div>
  </q-drawer>
</template>

<script setup>
import { reactive, watch } from "vue";
import { useRouter } from "vue-router";
import { LogoutMessage } from "@utils/constants/AuthConstant.js";

import $store from "@store/Store.js";
import $alert from "@utils/helpers/AlertHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";
import $notify from "@utils/helpers/NotifyHelper.js";

const $router = useRouter();

const menus = [
  {
    icon: "campaign",
    label: "Announcements",
    path: "/manage/announcements",
    separator: true,
    submenus: [],
  },
];
const emit = defineEmits();
const props = defineProps({
  drawer: false,
});

const state = reactive({
  drawer: props.drawer,
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
