<template>
  <div class="q-pa-md">
    <div class="q-py-lg">
      <q-btn color="primary" label="Add Event" @click="addEvent"></q-btn>
    </div>
    <q-table
      title="Events"
      :rows="rows"
      :columns="columns"
      :grid="$q.screen.lt.lg"
      :hide-header="$q.screen.lt.lg"
      :filter="filter"
      :loading="loadingData"
      :pagination="paginationTable"
      row-key="id"
    >
      <template v-slot:top-right>
        <q-input
          borderless
          dense
          debounce="300"
          v-model="filter"
          placeholder="Search"
        >
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
      <template v-slot:body-cell-image="props">
        <q-td :props="props">
          <q-img
            :src="computedImage(props.row)"
            :error-src="`https://picsum.photos/405/153?random=${props.row.id}`"
            style="height: auto; width: 145px"
          >
            <template v-slot:error>
              <div class="absolute-full flex flex-center">
                Imagine indisponibila
              </div>
            </template>
          </q-img>
        </q-td>
      </template>
      <template v-slot:body-cell-actions="props">
        <q-td auto-width>
          <div class="row q-pa-sm">
            <q-btn
              color="primary"
              label="Edit"
              @click="editEvent(props.row)"
            ></q-btn>
          </div>
          <div class="row q-pa-sm">
            <q-btn
              color="negative"
              label="Delete"
              @click="deleteEvent(props.row)"
            ></q-btn>
          </div>
        </q-td>
      </template>
      <template v-slot:item="props">
        <div
          class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
          :style="props.selected ? 'transform: scale(0.95);' : ''"
        >
          <q-card
            bordered
            flat
            class="full-height"
            :class="props.selected ? 'bg-grey-2' : ''"
          >
            <q-card-section>
              <q-img
                :src="computedImage(props.row)"
                :error-src="`https://picsum.photos/405/153?random=${props.row.id}`"
                style="width: 100%; height: auto"
              >
                <template v-slot:error>
                  <div class="absolute-full flex flex-center">
                    Imaginea nu este disponibila
                  </div>
                </template>
              </q-img>
            </q-card-section>
            <q-list dense>
              <q-item
                v-for="col in props.cols"
                :key="col.name"
                class="q-pt-xs q-pb-xs"
              >
                <q-item-section class="full-height">
                  <q-item-label
                    v-if="col.name !== 'image' && col.name !== 'actions'"
                  >
                    {{ col.label }}
                  </q-item-label>
                  <q-item-label
                    v-if="col.name !== 'image' && col.name !== 'actions'"
                    :style="
                      col.name === 'nume_eveniment' ? 'height: 2.4em' : ''
                    "
                    caption
                  >
                    {{ props.row[col.name] }}
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
            <q-space />
            <q-separator />
            <q-card-actions>
              <q-btn
                color="primary"
                label="Edit"
                @click="editEvent(props.row)"
              ></q-btn>
              <q-btn
                color="negative"
                label="Delete"
                @click="deleteEvent(props.row)"
              ></q-btn>
            </q-card-actions>
          </q-card>
        </div>
      </template>
    </q-table>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth-store';
import { format } from 'date-fns';
import { useRouter } from 'vue-router';

interface Event {
  id: number;
  nume_eveniment: string;
  locatie: string;
  data_inceput: string;
  data_sfarsit: string;
  invitation_background: string;
}

export default defineComponent({
  name: 'EventDashboardView',
  setup() {
    const { getToken } = useAuthStore();
    const addEventPopup = ref(false);
    const router = useRouter();
    const filter = ref('');
    const loadingData = ref(true);

    const rows = ref([] as Event[]);
    const columns = [
      {
        name: 'image',
        label: 'Image',
        align: 'center',
        field: 'image',
        slot: 'image',
      },
      {
        name: 'id',
        label: 'ID',
        align: 'center',
        field: 'id',
        sortable: true,
      },
      {
        name: 'nume_eveniment',
        label: 'Nume',
        align: 'center',
        sortable: true,
        field: 'nume_eveniment',
      },
      { name: 'locatie', label: 'Locatie', align: 'left', field: 'locatie' },
      {
        name: 'data_inceput',
        label: 'Data inceput',
        align: 'center',
        field: 'data_inceput',
        format: (val: string | number | Date) =>
          format(new Date(val), 'HH:mm dd.MMM.yyyy'),
      },
      {
        name: 'data_sfarsit',
        label: 'Data final',
        align: 'center',
        field: 'data_sfarsit',
        format: (val: string | number | Date) =>
          format(new Date(val), 'HH:mm dd.MMM.yyyy'),
      },
      { name: 'actions', label: 'Actions', align: 'center', field: 'actions' },
    ];
    onMounted(() => {
      api
        .get('/admin/events', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          rows.value = response.data;
          loadingData.value = false;
        })
        .catch((error) => {
          console.log(error);
          loadingData.value = false;
        });
    });

    const computedImage = (row: Event) => {
      if (row.invitation_background) {
        return (
          import.meta.env.VITE_API_SERVER +
          '/storage' +
          row.invitation_background.replace('.pdf', '.png')
        );
      } else {
        return (
          import.meta.env.VITE_API_SERVER +
          '/storage/images/non-existing-path.png'
        );
      }
    };

    const addEvent = () => {
      router.push({ name: 'add-event' });
    };

    const editEvent = (event: Event) => {
      router.push({ name: 'edit-event', params: { id: event.id } });
    };

    const deleteEvent = (event: Event) => {
      console.log('delete', event);
      //TODO: delete Event
    };

    const formatDate = (date: string) => {
      return format(new Date(date), 'HH:mm dd.MMM.yyyy');
    };

    const paginationTable = {
      rowsPerPage: 10,
    };

    return {
      filter,
      addEventPopup,
      rows,
      columns,
      loadingData,
      paginationTable,
      computedImage,
      addEvent,
      editEvent,
      deleteEvent,
      formatDate,
    };
  },
});
</script>

<style lang="scss" scoped>
.full-width-btn {
  width: 100%;
}
.full-height {
  height: 100%;
}
.grid-style-transition {
  transition: transform 0.3s ease;
}
.bg-grey-2 {
  background-color: #f5f5f5;
}
</style>
