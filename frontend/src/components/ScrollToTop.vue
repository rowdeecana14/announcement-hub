<template>
  <q-page-sticky position="bottom-right" :offset="[18, 18]">
    <q-btn v-if="showScrollBtn" icon="arrow_upward" color="primary" round @click="scrollToTop" />
  </q-page-sticky>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";

const showScrollBtn = ref(false);

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const onScroll = () => {
  showScrollBtn.value = window.scrollY > 200;
};

onMounted(() => {
  window.addEventListener("scroll", onScroll);
});

onBeforeUnmount(() => {
  window.removeEventListener("scroll", onScroll);
});
</script>
