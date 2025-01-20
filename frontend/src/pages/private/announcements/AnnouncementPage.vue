<template>
  <q-page class="q-py-md q-px-sm">
    <q-card class="no-shadow" bordered>
      <q-card-section class="q-py-sm">
        <div class="text-h6"><q-icon name="campaign" size="md" /> Manage Announcement</div>
      </q-card-section>
      <q-separator></q-separator>
      <q-card-section class="q-pa-none q-mb-md">
        <div class="q-px-lg q-py-md">
          <q-table
            class="no-shadow q-mx-sm"
            :rows="states.announcements.rows"
            :columns="states.announcements.columns"
            :pagination="states.announcements.pagination"
            :rows-per-page-options="states.announcements.page_options"
            :row-key="'id'"
            @request="onRequest"
            :filter="states.search"
          >
            <template v-slot:top-left="props">
              <div class="row">
                <q-btn
                  color="primary"
                  rounded
                  icon="add_circle"
                  no-caps
                  label="Create"
                  @click="create_announcement_modal.showCreateAnnouncementModal()"
                >
                </q-btn>
              </div>
            </template>
            <template v-slot:top-right="props">
              <div class="row">
                <q-input
                  bg-color="grey-12"
                  outlined
                  borderless
                  dense
                  debounce="300"
                  placeholder="Search"
                  class="q-mr-sm"
                  v-model="states.search"
                  @input="onSearch"
                  
                >
                  <template v-slot:append>
                    <q-btn class="q-ml-sm" icon="search" flat round color="primary" />
                    <q-tooltip>Click to search Vendor</q-tooltip>
                  </template>
                </q-input>
                <div class="table-buttons">
                  <q-btn color="primary" class="q-mr-xs" icon="refresh" @click="clickRefresh()">
                    <q-tooltip>Refresh</q-tooltip>
                  </q-btn>

                  <q-btn
                    color="primary"
                    :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                    @click="props.toggleFullscreen"
                  >
                    <q-tooltip
                      >{{ props.inFullscreen ? "Exit Fullscreen" : "Toggle Fullscreen" }}
                    </q-tooltip>
                  </q-btn>
                </div>
              </div>
            </template>
            <template v-slot:header="props">
              <q-tr :props="props">
                <q-th v-for="col in props.cols" :key="col.name" :props="props">
                  {{ col.label }}
                </q-th>
                <q-th class="text-left"> Action </q-th>
              </q-tr>
            </template>

            <template v-slot:body="props">
              <q-tr :props="props" class="hoverable">
                <q-td key="id" :props="props">
                  {{ props.row.id }}
                </q-td>
                <q-td key="title" :props="props">
                  {{ props.row.title }}
                </q-td>
                <q-td key="content" :props="props">
                  {{ props.row.content }}
                </q-td>
                <q-td key="start_date" :props="props">
                  {{ props.row.start_date }}
                </q-td>
                <q-td key="end_date" :props="props">
                  {{ props.row.end_date }}
                </q-td>
                <q-td key="active" :props="props">
                  <q-badge outline :color="props.row.active ? 'info' : 'red'">
                    {{ props.row.active ? "YES" : "NO" }}
                  </q-badge>
                </q-td>
                <q-td>
                  <q-btn-group rounded>
                    <q-btn-dropdown
                      auto-close
                      outline
                      rounded
                      color="primary"
                      icon="settings"
                      split
                      size="sm"
                    >
                      <q-list padding style="width: 160px">
                        <q-item
                          clickable
                          @click="update_announcement_modal.showUpdateAnnouncementModal(props.row)"
                        >
                          <q-item-section avatar>
                            <q-avatar icon="edit_square" color="orange" text-color="white" />
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>Edit</q-item-label>
                          </q-item-section>
                        </q-item>

                        <q-item
                          clickable
                          @click="
                            update_dates_announcement_modal.showUpdateDatesAnnouncementModal(
                              props.row
                            )
                          "
                        >
                          <q-item-section avatar>
                            <q-avatar icon="edit_calendar" color="primary" text-color="white" />
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>Reschedule</q-item-label>
                          </q-item-section>
                        </q-item>

                        <q-item clickable @click="showDeleteConfirmation(props.row)">
                          <q-item-section avatar>
                            <q-avatar icon="delete" color="red" text-color="white" />
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>Delete</q-item-label>
                          </q-item-section>
                        </q-item>
                      </q-list>
                    </q-btn-dropdown>
                  </q-btn-group>
                </q-td>
              </q-tr>
            </template>
          </q-table>
        </div>
      </q-card-section>
    </q-card>

    <CreateAnnouncementModal
      ref="create_announcement_modal"
      :refreshAnnouncements="refreshAnnouncements"
    />

    <UpdateAnnouncementModal
      ref="update_announcement_modal"
      :refreshAnnouncements="refreshAnnouncements"
    />

    <UpdateDatesAnnouncementModal
      ref="update_dates_announcement_modal"
      :refreshAnnouncements="refreshAnnouncements"
    />
  </q-page>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick } from "vue";
import _ from "lodash";

import $store from "@store/Store.js";
import $loading from "@utils/helpers/ActionLoaderHelper.js";
import $notify from "@utils/helpers/NotifyHelper.js";
import $alert from "@utils/helpers/AlertHelper.js";

import { GetAllMessage, DeleteMessage } from "@utils/constants/AnnouncementConstant.js";
import CreateAnnouncementModal from "./CreateAnnouncementModal.vue";
import UpdateAnnouncementModal from "./UpdateAnnouncementModal.vue";
import UpdateDatesAnnouncementModal from "./UpdateDatesAnnouncementModal.vue";

const create_announcement_modal = ref(null);
const update_announcement_modal = ref(null);
const update_dates_announcement_modal = ref(null);

const states = reactive({
  search: "",
  announcements: {
    loading: true,
    rows: [],
    columns: [
      { name: "id", label: "ID", field: "id", sortable: true },
      { name: "title", label: "Title", field: "title", sortable: true },
      { name: "content", label: "Content", field: "content", sortable: true },
      { name: "start_date", label: "Start Date", field: "start_date", sortable: true },
      { name: "end_date", label: "End Date", field: "end_date", sortable: true },
      { name: "active", label: "Active", field: "active", sortable: true },
    ],
    pagination: {
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0,
      sortBy: "title",
      descending: false,
    },
    page_options: [10, 20, 50, 100],
  },
});

onMounted(async () => {
  await getAnnouncements(states.announcements.pagination);
});

async function getAnnouncements(pagination) {
  try {
    states.announcements.loading = true;
    const { page, rowsPerPage, sortBy, descending } = pagination;

    const response = await $store.dispatch("Announcement/all", {
      page,
      per_page: rowsPerPage,
      sort_by: sortBy,
      sort_direction: descending ? "desc" : "asc",
      search: states.search,
    });

    if (![200, 201].includes(response.status)) {
      $notify.error(response.data.message);
      return;
    }

    states.announcements.rows = response.data.data;
    states.announcements.pagination.rowsNumber = response.data.pagination.total;
    states.announcements.loading = false;
  } catch (error) {
    console.error(error);
    $notify.error("Failed to fetch announcements.");
  }
}

async function refreshAnnouncements() {
  await getAnnouncements(states.announcements.pagination);
}

async function clickRefresh() {
  $loading.show();
  states.search = "";
  await refreshAnnouncements();
  $loading.hide();
}

async function showDeleteConfirmation(announcement) {
  try {
    const alert = await $alert.confirm("Confirm Message", DeleteMessage.CONFIRM);

    if (!alert.isConfirmed) {
      return;
    }
    $loading.show();

    const response = await $store.dispatch("Announcement/delete", announcement);

    if (![200, 201].includes(response.status)) {
      $loading.hide();
      return $notify.error(response.data.message || DeleteMessage.ERROR);
    }

    $loading.hide();
    states.modal = false;
    $notify.info(response.data.message || DeleteMessage.SUCCESS);

    await refreshAnnouncements();
  } catch (error) {
    console.log(error);
    $loading.hide();
    $notify.error(DeleteMessage.ERROR);
  }
}

const onSearch = _.debounce(async () => {
  await getAnnouncements(states.announcements.pagination); 
}, 500); 

function onRequest(params) {
  const { pagination } = params;
  states.announcements.pagination = {
    ...states.announcements.pagination,
    page: pagination.page,
    rowsPerPage: pagination.rowsPerPage,
    sortBy: pagination.sortBy,
    descending: pagination.descending,
  };
  getAnnouncements(states.announcements.pagination);
}
</script>
