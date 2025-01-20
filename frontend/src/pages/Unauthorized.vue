<template>
  <div class="unauthorized fullscreen bg-image text-primary text-center q-pa-md flex flex-center">
    <h1>Unauthorized Access</h1>
    <p>You are not authorized to access this page.</p>

    <div class="text-h6 text-center text-white q-mt-lg q-gutter-md">
      <q-btn
        color="primary"
        text-color="primary"
        outline
        label="Back"
        no-caps
        icon="arrow_back"
        @click="goBack()"
      />
      <!-- <q-btn
        color="primary"
        text-color="primary"
        outline
        label="Login"
        no-caps
        icon="lock"
        to="/"
      /> -->
    </div>
  </div>
</template>

<script setup>
async function goBack() {
  window.history.back();
}
</script>

<style scoped>
.unauthorized {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  text-align: center;
}
</style>
