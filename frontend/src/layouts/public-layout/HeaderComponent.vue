<template>
  <q-header
    elevated
    class="bg-white "
    :style="$q.screen.gt.xs ? { height: '75px' } : { height: '60px' }"
  >
    <q-toolbar :class="$q.screen.gt.xs ? 'q-ml-xl': ''" :style="$q.screen.gt.xs ? { height: '75px' } : { height: '60px' }">
      <div v-if="$q.screen.gt.xs">
        <img src="../../assets/OutWorx.jpg" style="height: 60px; width: auto" />
      </div>
      <div v-else>
        <img src="../../assets/OutWorx.jpg" style="height: 50px; width: auto" />
      </div>
    </q-toolbar>
  </q-header>
</template>

<script setup></script>
