<template>
  <q-layout view="lHh LpR lfr" class="bg-grey-4">
    <HeaderComponent />
    <q-page-container>
      <router-view />
    </q-page-container>
    <FooterComponent />
  </q-layout>
</template>

<script setup>
import HeaderComponent from "@layouts/public-layout/HeaderComponent.vue";
import FooterComponent from "@layouts/public-layout/FooterComponent";

const menus = [
  {
    icon: "home",
    label: "HOME",
    route: "public.home",
  },
  {
    icon: "login",
    label: "SIGN IN",
    route: "public.login",
  },
  {
    icon: "question_mark",
    label: "ABOUT",
    route: "public.about",
  },
  {
    icon: "phone",
    label: "CONTACT US",
    route: "public.contact",
  },
  {
    icon: "image",
    label: "GALLERY",
    route: "public.gallery",
  },
];
</script>
