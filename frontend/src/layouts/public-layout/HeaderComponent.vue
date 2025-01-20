<template>
  <q-header
    elevated
    class="bg-white "
    :style="$q.screen.gt.xs ? { height: '75px' } : { height: '60px' }"
  >
    <q-toolbar :class="$q.screen.gt.xs ? 'q-ml-xl': ''" :style="$q.screen.gt.xs ? { height: '75px' } : { height: '60px' }">
      <div v-if="$q.screen.gt.xs">
        <img src="../../assets/ah.png" style="height: 60px; width: auto" />
      </div>
      <div v-else>
        <img src="../../assets/ah.png" style="height: 50px; width: auto" />
      </div>
      <div class="row">
        <div class="col-md-12 q-mt-none q-pt-none">
          <q-toolbar-title shrink class="text-weight-bold text-dark" style="padding-left: 1rem">
            <div
              class="text-bold"
              :style="
                $q.screen.gt.sm ? { 'font-size': '12pt' } : { 'font-size': '11pt', width: '80vw' }
              "
            >
             ANNOUNCEMENT HUB
            </div>
          </q-toolbar-title>
        </div>
      </div>
    </q-toolbar>
  </q-header>
</template>

<script setup></script>
