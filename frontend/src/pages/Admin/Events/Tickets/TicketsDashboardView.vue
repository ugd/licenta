<template>
  <div class="q-pa-md">
    <div class="q-py-lg">
      <div class="div">
        <q-btn
          color="primary"
          label="Upload Batch Tickets"
          :loading="loadingNeedsUpload"
          @click="uploadBatch = true"
        ></q-btn>
      </div>
    </div>
    <q-table
      title="Tickets"
      :rows="rows"
      :filter="filter"
      :columns="columns"
      :grid="$q.screen.lt.lg"
      :hide-header="$q.screen.lt.lg"
      :pagination="tablePagination"
      :loading="loadingTable"
      row-key="id"
    >
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
            <q-list>
              <q-item
                v-for="col in props.cols"
                :key="col.name"
                class="q-pt-xs q-pb-xs"
              >
                <q-item-section>
                  <q-item-label>{{ col.label }}</q-item-label>
                  <q-item-label
                    caption
                    v-if="col.name !== 'cod_bilet' && col.name !== 'stare'"
                    >{{ props.row[col.name] }}</q-item-label
                  >
                  <q-item-label caption v-else-if="col.name === 'cod_bilet'">
                    <q-chip
                      :color="computedStatusChipColor(props.row.stare_id)"
                      :text-color="props.row.stare_id > 1 ? 'white' : 'black'"
                      :label="props.row.cod_bilet"
                    >
                      <q-tooltip
                        anchor="top middle"
                        self="bottom middle"
                        :offset="[10, 10]"
                      >
                        <div class="q-pa-xs">
                          <div>
                            <strong>Stare:</strong> {{ props.row.stare }}
                          </div>
                        </div>
                      </q-tooltip>
                    </q-chip>
                  </q-item-label>
                  <q-item-label caption v-else-if="col.name === 'stare'">
                    <q-chip
                      :color="computedStatusChipColor(props.row.stare_id)"
                      :text-color="props.row.stare_id > 1 ? 'white' : 'black'"
                      :label="props.row.stare"
                    />
                  </q-item-label>
                </q-item-section>
              </q-item>
              <div v-if="props.row.stare_id <= 2" class="q-pa-xs">
                <q-btn
                  dense
                  color="negative"
                  icon="delete"
                  label="Anuleaza invitatia"
                  class="full-width"
                  :loading="loadingCancelButtons[props.row.id]"
                  @click="cancelTicket(props.row.id)"
                ></q-btn>
              </div>
              <div class="q-pl-md" v-if="props.row.stare_id > 2">
                <q-item-label caption style="text-decoration: underline">
                  <span class="text-h6"> Nu se mai pot efectua actiuni </span>
                </q-item-label>
              </div>
            </q-list>
          </q-card>
        </div>
      </template>
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
      <template v-slot:body-cell-cod_bilet="props">
        <q-td :props="props">
          <q-chip
            :color="computedStatusChipColor(props.row.stare_id)"
            :text-color="props.row.stare_id > 1 ? 'white' : 'black'"
            :label="props.row.cod_bilet"
          >
            <q-tooltip
              anchor="top middle"
              self="bottom middle"
              :offset="[10, 10]"
            >
              <div class="q-pa-xs">
                <div><strong>Stare:</strong> {{ props.row.stare }}</div>
              </div>
            </q-tooltip>
          </q-chip>
        </q-td>
      </template>
      <template v-slot:header-cell-stare=""> </template>
      <template v-slot:body-cell-stare=""></template>
      <template v-slot:body-cell-actions="props">
        <q-td auto-width>
          <div class="q-pa-sm" v-if="props.row.stare_id <= 2">
            <q-btn
              color="negative"
              icon="delete"
              :loading="loadingCancelButtons[props.row.id]"
              @click="cancelTicket(props.row.id)"
            >
              <q-tooltip
                anchor="center start"
                self="center end"
                :offset="[10, 10]"
              >
                <div class="q-pa-xs">
                  <div><strong>Anuleaza biletul</strong></div>
                </div>
              </q-tooltip>
            </q-btn>
          </div>
          <span class="text-caption" v-else> Fara actiuni </span>
        </q-td>
      </template>
    </q-table>
  </div>
  <q-dialog v-model="uploadBatch">
    <q-card>
      <q-card-section>
        <div class="text-h6">Upload Tickets Batch</div>
      </q-card-section>
      <q-card-section class="q-pa-sm">
        <a
          :href="`${env.VITE_API}/assets/template_iabilet.xlsx`"
          target="_blank"
          >Download Excel Template</a
        >
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="ticketBatchUpload">
          <q-file
            v-model="ticketBatch.file"
            label="File"
            outlined
            max-files="1"
            :rules="[(val) => !!val || 'Fisierul este obligatoriu!']"
            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx"
          />
          <q-select
            v-model="ticketBatch.eveniment_id"
            :options="eventsOptions"
            emit-value
            map-options
            label="Eveniment"
            :rules="[(val) => !!val || 'Evenimentul este obligatoriu!']"
            outlined
          />
          <q-select
            v-model="ticketBatch.vendor_id"
            :options="bilete_vendorsOptions"
            emit-value
            map-options
            label="Ticket Vendor"
            :rules="[(val) => !!val || 'Vendorul este obligatoriu!']"
            outlined
          />
          <q-card-actions align="right">
            <q-btn
              flat
              label="Renunta"
              color="negative"
              :loading="loadingBatchButton"
              v-close-popup
            />
            <q-btn
              flat
              label="Adauga"
              color="primary"
              :loading="loadingBatchButton"
              type="submit"
            />
          </q-card-actions>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth-store';
import { useQuasar } from 'quasar';

interface Ticket {
  id: number;
  nume_prenume: string;
  eveniment_id: number | null;
  adresa_email: string;
  phone: string | null;
  invite_type_id: number | null;
}

interface TicketBatch {
  file: File | Blob | null;
  eveniment_id: number;
  vendor_id: number;
}

interface Event {
  id: number;
  nume_eveniment: string;
}

interface invite_type_id {
  id: number;
  invite_type: string;
}

export default defineComponent({
  name: 'TicketsDashboardView',
  setup() {
    const { getToken } = useAuthStore();
    const $q = useQuasar();
    const addEventPopup = ref(false);
    const ticketDialog = ref(false);
    const addTicketMode = ref(false);
    const ticket = ref({} as Ticket);
    const ticketBatch = ref({} as TicketBatch);
    const eventsOptions = ref([] as { label: string; value: number }[]);
    const invite_type_idsOptions = ref(
      [] as { label: string; value: number }[]
    );
    const bilete_vendorsOptions = ref([] as { label: string; value: number }[]);
    const uploadBatch = ref(false);
    const selectedIds = ref([] as number[]);
    const rows = ref([] as Ticket[]);
    const filter = ref('');
    const loadingTable = ref(true);
    const loadingNeedsUpload = ref(true);
    const loadingBatchButton = ref(false);
    const loadingCancelButtons = ref({} as { [key: number]: boolean });

    ticket.value = {
      id: 1,
      nume_prenume: '',
      eveniment_id: null,
      adresa_email: '',
      phone: '',
      invite_type_id: null,
    };

    const columns = [
      {
        name: 'id',
        label: 'ID',
        align: 'center',
        field: 'id',
        sortable: true,
      },
      {
        name: 'cod_bilet',
        label: 'Cod Bilet',
        align: 'center',
        field: 'cod_bilet',
      },
      {
        name: 'nume_prenume',
        label: 'Nume Prenume',
        align: 'center',
        sortable: true,
        field: 'nume_prenume',
      },
      {
        name: 'email',
        label: 'Email',
        sortable: true,
        align: 'center',
        field: 'email',
        format: (val: string) => {
          return val ? val : 'N/A';
        },
      },
      {
        name: 'telefon',
        label: 'Telefon',
        align: 'center',
        field: 'telefon',
        format: (val: string) => {
          return val ? val : 'N/A';
        },
      },
      {
        name: 'eveniment',
        sortable: true,
        label: 'Eveniment',
        align: 'center',
        field: 'eveniment',
      },
      {
        name: 'vendor',
        sortable: true,
        label: 'Vendor',
        align: 'center',
        field: 'vendor',
      },
      {
        name: 'stare',
        label: 'Stare',
        align: 'center',
        field: 'stare',
      },
      { name: 'actions', label: 'Actions', align: 'center', field: 'actions' },
    ];

    onMounted(() => {
      api
        .get('/admin/tickets', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          rows.value = response.data;
          loadingTable.value = false;
        })
        .catch((error) => {
          console.log(error);
          loadingTable.value = false;
        });

      api
        .get('admin/invitations/getEvents', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          eventsOptions.value = response.data.map((event: Event) => {
            loadingNeedsUpload.value = false;
            return { label: event.nume_eveniment, value: event.id };
          });
        })
        .catch((error) => {
          console.log(error);
          loadingNeedsUpload.value = false;
        });

      api
        .get('admin/invitations/getInviteTypes', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          invite_type_idsOptions.value = response.data.map(
            (invite_type_id: invite_type_id) => {
              return {
                label: invite_type_id.invite_type,
                value: invite_type_id.id,
              };
            }
          );
        })
        .catch((error) => {
          console.log(error);
        });

      api
        .get('admin/vendors', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          bilete_vendorsOptions.value = response.data.map((vendor: any) => {
            return {
              label: vendor.nume_vendor,
              value: vendor.id,
            };
          });
        })
        .catch((error) => {
          console.log(error);
        });
    });

    const reloadTable = () => {
      loadingTable.value = true;
      api
        .get('/admin/tickets', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          rows.value = response.data;
          loadingTable.value = false;
          $q.notify({
            message: 'Lista actualizata cu success',
            color: 'green',
            icon: 'check',
            position: 'top-right',
            timeout: 2000,
          });
        })
        .catch((error) => {
          console.log(error);
          loadingTable.value = false;
          $q.notify({
            message: 'Lista nu a fost actualizata',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        });
    };

    const ticketBatchUpload = () => {
      const formData = new FormData();
      formData.append('file', ticketBatch.value.file as Blob);
      formData.append('eveniment_id', ticketBatch.value.eveniment_id as number);
      formData.append('vendor_id', ticketBatch.value.vendor_id as number);
      loadingBatchButton.value = true;
      api
        .post('admin/tickets/storeBatch', formData, {
          headers: {
            Authorization: `Bearer ${getToken}`,
            'Content-Type': 'multipart/form-data',
          },
        })
        .then((response) => {
          if (response.status === 200) {
            uploadBatch.value = false;
            loadingBatchButton.value = false;
            $q.notify({
              message: 'Biletele au fost adaugate cu succes',
              color: 'green',
              icon: 'check',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
          }
        })
        .catch((error) => {
          console.log(error);
          loadingBatchButton.value = false;
          $q.notify({
            message: 'Biletele nu au fost adaugate',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        });
    };

    const cancelTicket = (id: number) => {
      loadingCancelButtons.value[id] = true;
      api
        .delete(`admin/tickets/${id}`, {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          if (response.status === 200) {
            loadingCancelButtons.value[id] = false;
            $q.notify({
              message: 'Biletul a fost anulat cu succes',
              icon: 'check',
              color: 'green',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
          }
        })
        .catch((error) => {
          loadingCancelButtons.value[id] = false;
          $q.notify({
            message: 'Biletul nu a fost anulat',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        });
    };

    const computedStatusChipColor = (props: any) => {
      if (props === 1) {
        return 'yellow';
      } else if (props === 2) {
        return 'green';
      } else if (props === 3) {
        return 'blue';
      } else if (props === 4) {
        return 'red';
      }
    };

    const tablePagination = {
      rowsPerPage: 10,
    };

    return {
      addEventPopup,
      rows,
      env: import.meta.env,
      columns,
      ticket,
      selectedIds,
      filter,
      ticketDialog,
      uploadBatch,
      eventsOptions,
      addTicketMode,
      invite_type_idsOptions,
      ticketBatch,
      tablePagination,
      loadingTable,
      loadingNeedsUpload,
      loadingBatchButton,
      loadingCancelButtons,
      bilete_vendorsOptions,
      cancelTicket,
      ticketBatchUpload,
      computedStatusChipColor,
    };
  },
});
</script>

<style lang="scss" scoped>
.full-width {
  width: 100%;
}
.full-height {
  height: 100%;
}
</style>
