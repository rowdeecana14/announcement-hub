<template>
  <q-layout view="lHh Lpr lFf">
    <HeaderComponent />
    <q-page-container>
      <q-page>
        <q-card>
          <q-card-section>
            <div class="q-px-md q-py-md">
              <div class="q-mx-auto q-bg-white q-pa-md" style="max-width: 800px">
                <q-timeline>
                  <q-timeline-entry heading>
                    <q-avatar size="70px">
                      <img src="../../assets/announcement.jpeg" />
                    </q-avatar>
                    Latest announcements
                  </q-timeline-entry>
                  <template
                    v-for="announcement in states.announcements.rows"
                    :key="announcement.id"
                  >
                    <q-timeline-entry
                      :title="announcement.title"
                      :subtitle="moment(announcement.start_date).format('MMMM Do YYYY')"
                    >
                      <div>
                        {{ announcement.content }}
                      </div>
                    </q-timeline-entry>
                  </template>
                </q-timeline>
                <div class="q-pa-lg flex flex-center">
                  <q-pagination
                    v-model="states.announcements.pagination.currentPage"
                    :max="states.announcements.pagination.totalPages"
                    :rows-per-page="states.announcements.pagination.perPage"
                    @update:model-value="onPageChange"
                    direction-links
                    :max-pages="15"
                    boundary-numbers
                  />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
        <ScrollToTop />
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick } from "vue";
import HeaderComponent from "@layouts/public-layout/HeaderComponent.vue";
import ScrollToTop from "@components/ScrollToTop.vue";
import moment from "moment";

import $notify from "@utils/helpers/NotifyHelper.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";
import $store from "@store/Store.js";

const states = reactive({
  search: "",
  filters: {},
  announcements: {
    loading: true,
    pagination: {
      currentPage: 1,
      perPage: 20,
      totalPages: 0,
    },
    rows: [],
  },
});

onMounted(async () => {
  await getAnnouncements(states.announcements.pagination.currentPage);
});

async function getAnnouncements(page = 1) {
  try {
    $loading.show();
    states.announcements.loading = true;

    const response = await $store.dispatch("Public/announcement", {
      page,
      per_page: states.announcements.pagination.perPage,
    });

    if (![200, 201].includes(response.status)) {
      $notify.error(response?.data?.message ?? "Error fetching announcements");
      return;
    }

    states.announcements.rows = response.data.data;
    states.announcements.pagination.totalPages = Math.ceil(
      response.data.pagination.total / states.announcements.pagination.perPage
    );
    states.announcements.pagination.currentPage = response.data.pagination.current_page;
    states.announcements.loading = false;

    $loading.hide();
    await nextTick();
  } catch (error) {
    console.log(error);
    $loading.hide();
    $notify.error("Error fetching announcements");
  }
}

function onPageChange(page) {
  states.announcements.pagination.currentPage = page;
  getAnnouncements(page);
}
</script>
