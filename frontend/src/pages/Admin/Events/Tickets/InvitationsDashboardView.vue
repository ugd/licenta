<template>
  <div class="q-pa-md">
    <div class="q-py-lg flex justify-between">
      <div class="div">
        <q-btn
          color="primary q-mr-md"
          label="Add Invitation"
          :loading="loadingButtons"
          @click="addInvitation"
        ></q-btn>
        <q-btn
          color="primary"
          label="Upload Batch Invitations"
          :loading="loadingButtons"
          @click="uploadBatch = true"
        ></q-btn>
      </div>
      <div class="flex-grow"></div>
      <q-btn
        color="green"
        label="Send Batch Invitation"
        :loading="loadingBatchMailButton"
        @click="sendBatchMail"
        v-if="selectedIds.length > 0"
      ></q-btn>
    </div>
    <q-table
      :filter="filter"
      title="Invitations"
      :rows="rows"
      :columns="columns"
      :loading="loadingTable"
      :grid="$q.screen.lt.lg"
      row-key="id"
      :pagination="tablePagination"
      rows-per-page-label="Elemente pe pagina: "
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
            <q-card-section>
              <q-checkbox
                v-if="props.row.stare_id <= 1"
                v-model="selectedIds"
                :val="props.row.id"
                label="Selecteaza"
              />
              <q-checkbox
                v-else
                v-model="selectedIds"
                disabled
                label="Nu se mai pot efectua actiuni"
              />
            </q-card-section>
            <q-separator />
            <q-list dense>
              <q-item
                v-for="col in props.cols"
                :key="col.name"
                class="q-pt-xs q-pb-xs"
              >
                <q-item-section>
                  <q-item-label>{{ col.label }}</q-item-label>
                  <q-item-label
                    caption
                    v-if="col.name !== 'cod_invitatie' && col.name !== 'stare'"
                    >{{ props.row[col.name] }}</q-item-label
                  >
                  <q-item-label
                    caption
                    v-else-if="col.name === 'cod_invitatie'"
                  >
                    <q-chip
                      :color="computedStatusChipColor(props.row.stare_id)"
                      :text-color="props.row.stare_id > 1 ? 'white' : 'black'"
                      :label="props.row.cod_invitatie"
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
              <div v-if="props.row.stare_id === 1" class="q-pa-xs">
                <q-btn
                  v-if="props.row.stare_id === 1"
                  dense
                  color="green"
                  icon="mail"
                  label="Trimite email"
                  class="full-width"
                  :loading="loadingSendMailButtons[props.row.id]"
                  @click="sendMail(props.row)"
                ></q-btn>
              </div>
              <div v-if="props.row.stare_id === 1" class="q-pa-xs">
                <q-btn
                  dense
                  color="primary"
                  icon="edit"
                  label="Editeaza invitati"
                  class="full-width"
                  :loading="loadingEditButtons[props.row.id]"
                  @click="editInvitation(props.row)"
                ></q-btn>
              </div>
              <div v-if="props.row.stare_id <= 2" class="q-pa-xs">
                <q-btn
                  dense
                  color="negative"
                  icon="delete"
                  label="Anuleaza invitatia"
                  class="full-width"
                  :loading="loadingCancelButtons[props.row.id]"
                  @click="cancelInvite(props.row.id)"
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
      <template v-slot:body-cell-cod_invitatie="props">
        <q-td :props="props">
          <q-chip
            :color="computedStatusChipColor(props.row.stare_id)"
            :text-color="props.row.stare_id > 1 ? 'white' : 'black'"
            :label="props.row.cod_invitatie"
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
      <template v-slot:body-cell-checkbox="props">
        <q-td :props="props">
          <q-checkbox
            v-if="props.row.stare_id <= 1"
            v-model="selectedIds"
            :val="props.row.id"
          />
        </q-td>
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
      <template v-slot:header-cell-stare=""> </template>
      <template v-slot:body-cell-stare=""></template>
      <template v-slot:body-cell-actions="props">
        <q-td auto-width>
          <div class="q-pa-sm" v-if="props.row.stare_id === 1">
            <q-btn
              color="green"
              icon="mail"
              :loading="loadingSendMailButtons[props.row.id]"
              @click="sendMail(props.row)"
            >
              <q-tooltip
                anchor="center start"
                self="center end"
                :offset="[10, 10]"
              >
                <div class="q-pa-xs">
                  <div><strong>Trimite email</strong></div>
                </div>
              </q-tooltip>
            </q-btn>
          </div>
          <div class="q-pa-sm" v-if="props.row.stare_id === 1">
            <q-btn
              color="primary"
              icon="edit"
              :loading="loadingEditButtons[props.row.id]"
              @click="editInvitation(props.row)"
            >
              <q-tooltip
                anchor="center start"
                self="center end"
                :offset="[10, 10]"
              >
                <div class="q-pa-xs">
                  <div><strong>Editeaza invitati</strong></div>
                </div>
              </q-tooltip>
            </q-btn>
          </div>
          <div class="q-pa-sm" v-if="props.row.stare_id <= 2">
            <q-btn
              color="negative"
              icon="delete"
              :loading="loadingCancelButtons[props.row.id]"
              @click="cancelInvite(props.row.id)"
            >
              <q-tooltip
                anchor="center start"
                self="center end"
                :offset="[10, 10]"
              >
                <div class="q-pa-xs">
                  <div><strong>Anuleaza invitatia</strong></div>
                </div>
              </q-tooltip>
            </q-btn>
          </div>
        </q-td>
      </template>
    </q-table>
  </div>
  <q-dialog persistent v-model="invitationDialog">
    <q-card>
      <q-card-section>
        <div class="text-h6">
          {{ addInvitationMode ? 'Adauga invitatie' : 'Editeaza invitatia' }}
        </div>
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="submitInvitation">
          <q-input
            v-model="invitation.nume"
            label="Nume"
            :rules="[(val) => !!val || 'Numele este obligatoriu']"
            outlined
          />
          <q-input
            v-model="invitation.prenume"
            label="Prenume"
            :rules="[(val) => !!val || 'Prenumele este obligatoriu']"
            outlined
          />
          <q-input
            v-model="invitation.adresa_email"
            label="Email"
            type="email"
            :rules="[
              (val) => !!val || 'Emailul este obligatoriu',
              (val) => /.+@.+\..+/.test(val) || 'Emailul este invalid',
            ]"
            outlined
          />
          <q-input
            v-model="invitation.telefon"
            class="q-mb-lg"
            label="Telefon"
            mask="phone"
            tel
            unmasked-value
            outlined
          />
          <q-select
            v-model="invitation.eveniment_id"
            :options="eventsOptions"
            emit-value
            map-options
            :rules="[(val) => !!val || 'Evenimentul este obligatoriu']"
            label="Eveniment"
            outlined
          />
          <q-select
            v-model="invitation.invite_type_id"
            label="Tip invitatie"
            :options="invite_type_idsOptions"
            :rules="[(val) => !!val || 'Tipul invitatiei este obligatoriu']"
            emit-value
            map-options
            outlined
          />
          <q-card-actions align="right">
            <q-btn
              flat
              label="Renunta"
              color="negative"
              :loading="loadingAdd_EditButton"
              v-close-popup
            />
            <q-btn
              flat
              :loading="loadingAdd_EditButton"
              :label="addInvitationMode ? 'Adauga' : 'Salveaza'"
              color="primary"
              type="submit"
            />
          </q-card-actions>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>

  <q-dialog v-model="uploadBatch">
    <q-card>
      <q-card-section>
        <div class="text-h6">Upload Batch Invitations</div>
      </q-card-section>
      <q-card-section class="q-pa-sm">
        <a
          :href="`${env.VITE_API}/assets/template_invitatii.xlsx`"
          target="_blank"
          >Download Excel Template</a
        >
      </q-card-section>
      <q-card-section class="q-pt-none">
        <q-form @submit="invitationBatchUpload">
          <q-file
            v-model="invitationBatch.file"
            label="File"
            outlined
            max-files="1"
            :rules="[(val) => !!val || 'Fisierul este obligatoriu']"
            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xls, .xlsx"
          />
          <q-select
            v-model="invitationBatch.eveniment_id"
            :options="eventsOptions"
            emit-value
            map-options
            :rules="[(val) => !!val || 'Evenimentul este obligatoriu']"
            label="Eveniment"
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

interface Invitation {
  id: number;
  nume: string;
  prenume: string;
  eveniment_id: number | null;
  adresa_email: string;
  telefon: string | null;
  invite_type_id: number | null;
}

interface InvitationBatch {
  file: File | Blob | null;
  eveniment_id: number;
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
  name: 'InvitationsDashboardView',
  setup() {
    const { getToken } = useAuthStore();
    const $q = useQuasar();
    const addEventPopup = ref(false);
    const invitationDialog = ref(false);
    const addInvitationMode = ref(false);
    const invitation = ref({} as Invitation);
    const invitationBatch = ref({} as InvitationBatch);
    const eventsOptions = ref([] as { label: string; value: number }[]);
    const filter = ref('');
    const invite_type_idsOptions = ref(
      [] as { label: string; value: number }[]
    );
    const uploadBatch = ref(false);
    const selectedIds = ref([] as number[]);
    const rows = ref([] as Invitation[]);
    const loadingTable = ref(true);
    const loadingButtons = ref(true);
    const loadingBatchButton = ref(false);
    const loadingBatchMailButton = ref(false);
    const loadingAdd_EditButton = ref(false);
    const loadingStates = ref({} as { [key: number]: boolean });
    const loadingSendMailButtons = ref({} as { [key: number]: boolean });
    const loadingEditButtons = ref({} as { [key: number]: boolean });
    const loadingCancelButtons = ref({} as { [key: number]: boolean });

    invitation.value = {
      id: 1,
      nume: '',
      prenume: '',
      eveniment_id: null,
      adresa_email: '',
      telefon: '',
      invite_type_id: null,
    };

    const columns = [
      {
        name: 'checkbox',
        align: 'center',
        field: 'checkbox',
      },
      {
        name: 'id',
        label: 'ID',
        align: 'center',
        field: 'id',
        sortable: true,
      },
      {
        name: 'cod_invitatie',
        label: 'Cod Invitatie',
        align: 'center',
        sortable: true,
        field: 'cod_invitatie',
      },
      {
        name: 'nume',
        label: 'Nume',
        align: 'center',
        sortable: true,
        field: 'nume',
      },
      {
        name: 'prenume',
        label: 'Prenume',
        align: 'center',
        sortable: true,
        field: 'prenume',
      },
      {
        name: 'adresa_email',
        label: 'Email',
        align: 'center',
        sortable: true,
        field: 'adresa_email',
      },
      {
        name: 'telefon',
        label: 'Telefon',
        align: 'center',
        sortable: true,
        field: 'telefon',
        format: (val: string) => {
          return val ? val : 'N/A';
        },
      },

      {
        name: 'eveniment',
        label: 'Eveniment',
        sortable: true,
        align: 'center',
        field: 'eveniment',
      },
      {
        name: 'tip_invitatie',
        sortable: true,
        label: 'Tip Invitatie',
        align: 'center',
        field: 'tip_invitatie',
      },
      {
        name: 'stare',
        label: 'Stare',
        align: 'center',
        field: 'stare',
        hide: 'xl',
      },
      { name: 'actions', label: 'Actions', align: 'center', field: 'actions' },
    ];

    onMounted(() => {
      api
        .get('/admin/invitations', {
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
            loadingButtons.value = false;
            return { label: event.nume_eveniment, value: event.id };
          });
        })
        .catch((error) => {
          console.log(error);
          loadingButtons.value = false;
        });
      api
        .get('admin/invitations/getInviteTypes', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          invite_type_idsOptions.value = response.data.map(
            (invite_type_id: invite_type_id) => {
              loadingButtons.value = false;
              return {
                label: invite_type_id.invite_type,
                value: invite_type_id.id,
              };
            }
          );
        })
        .catch((error) => {
          console.log(error);
          loadingButtons.value = false;
        });
    });

    const reloadTable = () => {
      loadingTable.value = true;
      api
        .get('admin/invitations', {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          loadingTable.value = false;
          rows.value = response.data;
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

    const invitationBatchUpload = () => {
      const formData = new FormData();
      formData.append('file', invitationBatch.value.file as Blob);
      formData.append(
        'eveniment_id',
        invitationBatch.value.eveniment_id as number
      );
      loadingBatchButton.value = true;
      api
        .post('admin/invitations/storeBatch', formData, {
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
              message: 'Invitatii adaugate cu succes',
              color: 'green',
              icon: 'check',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
          }
        })
        .catch((error) => {
          loadingBatchButton.value = false;
          console.log(error);
          $q.notify({
            message: 'Invitatiile nu au fost adaugate',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        });
    };

    const submitInvitation = () => {
      loadingAdd_EditButton.value = true;
      if (addInvitationMode.value === true) {
        api
          .post('admin/invitations/create', invitation.value, {
            headers: { Authorization: `Bearer ${getToken}` },
          })
          .then((response) => {
            if (response.status === 200) {
              invitationDialog.value = false;
              loadingAdd_EditButton.value = false;
              $q.notify({
                message: 'Invitatie adaugata cu succes',
                icon: 'check',
                color: 'green',
                position: 'top-right',
                timeout: 2000,
              });
              reloadTable();
            }
          })
          .catch((error) => {
            console.log(error);
            loadingAdd_EditButton.value = false;
            $q.notify({
              message: 'Invitatia nu a fost adaugata',
              icon: 'report_problem',
              color: 'red',
              position: 'top-right',
              timeout: 2000,
            });
          });
      } else {
        api
          .put(
            `admin/invitations/${invitation.value.id}/update`,
            invitation.value,
            {
              headers: { Authorization: `Bearer ${getToken}` },
            }
          )
          .then((response) => {
            if (response.status === 200) {
              invitationDialog.value = false;
              loadingAdd_EditButton.value = false;
              $q.notify({
                message: 'Invitatie editata cu succes',
                icon: 'check',
                color: 'green',
                position: 'top-right',
                timeout: 2000,
              });
              reloadTable();
            }
          })
          .catch((error) => {
            console.log(error);
            loadingAdd_EditButton.value = false;
            $q.notify({
              message: 'Invitatia nu a fost editata',
              icon: 'report_problem',
              color: 'red',
              position: 'top-right',
              timeout: 2000,
            });
          });
      }
    };

    const addInvitation = () => {
      invitationDialog.value = true;
      addInvitationMode.value = true;
      invitation.value = {
        id: 1,
        nume: '',
        prenume: '',
        eveniment_id: null,
        adresa_email: '',
        telefon: '',
        invite_type_id: null,
      };
    };

    const editInvitation = (row: Invitation) => {
      loadingEditButtons.value[row.id] = true;
      invitation.value = row;
      addInvitationMode.value = false;
      invitationDialog.value = true;
      loadingEditButtons.value[row.id] = false;
    };

    const cancelInvite = (id: number) => {
      loadingCancelButtons.value[id] = true;
      api
        .delete(`admin/invitations/${id}`, {
          headers: { Authorization: `Bearer ${getToken}` },
        })
        .then((response) => {
          if (response.status === 200) {
            $q.notify({
              message: 'Invitatie anulata cu succes',
              icon: 'check',
              color: 'green',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
            loadingCancelButtons.value[id] = false;
          }
        })
        .catch((error) => {
          console.log(error);
          $q.notify({
            message: 'Invitatia nu a fost anulata',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
          loadingCancelButtons.value[id] = false;
        });
    };

    const sendMail = (row: Invitation) => {
      loadingSendMailButtons.value[row.id] = true;
      api
        .post(
          'admin/invitations/sendMail',
          { invite_id: row.id },
          {
            headers: { Authorization: `Bearer ${getToken}` },
          }
        )
        .then((response) => {
          loadingSendMailButtons.value[row.id] = false;
          if (response.status === 200) {
            $q.notify({
              message: 'Invitatie trimisa cu succes',
              color: 'green',
              icon: 'check',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
          }
        })
        .catch((error) => {
          loadingSendMailButtons.value[row.id] = false;
          console.log(error);
          $q.notify({
            message: 'Invitatia nu a fost trimisa',
            color: 'red',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        });
    };

    const sendBatchMail = () => {
      loadingBatchMailButton.value = true;
      api
        .post(
          'admin/invitations/sendBatchMail',
          { invite_ids: selectedIds.value },
          {
            headers: { Authorization: `Bearer ${getToken}` },
          }
        )
        .then((response) => {
          loadingBatchMailButton.value = false;
          if (response.status === 200) {
            selectedIds.value = [];
            $q.notify({
              message: 'Invitatii trimise cu succes',
              color: 'green',
              icon: 'check',
              position: 'top-right',
              timeout: 2000,
            });
            reloadTable();
          }
        })
        .catch((error) => {
          loadingBatchMailButton.value = false;
          console.log(error);
          $q.notify({
            message: 'Invitatiile nu au fost trimise',
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
      invitation,
      selectedIds,
      filter,
      invitationDialog,
      uploadBatch,
      eventsOptions,
      addInvitationMode,
      invite_type_idsOptions,
      invitationBatch,
      tablePagination,
      loadingTable,
      loadingButtons,
      loadingBatchButton,
      loadingAdd_EditButton,
      loadingBatchMailButton,
      loadingStates,
      loadingSendMailButtons,
      loadingEditButtons,
      loadingCancelButtons,
      invitationBatchUpload,
      submitInvitation,
      computedStatusChipColor,
      editInvitation,
      addInvitation,
      cancelInvite,
      sendBatchMail,
      sendMail,
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
  padding: 4px;
}
</style>
