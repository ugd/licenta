<template>
  <div class="q-pa-sm">
    <div class="q-pa-sm">
      <q-btn label="Adauga utilizator" color="green" @click="addUser"></q-btn>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="q-pa-md flex flex-center">
          <q-table
            bordered
            dense
            title="Users"
            wrap-cells
            :rows="rows"
            :columns="columns"
            :grid="$q.screen.lt.lg"
            row-key="id"
            style="width: 100%"
            selection="multiple"
          >
            <template v-slot:header-selection=""> Actions </template>

            <template v-slot:body-selection="scope">
              <div style="display: flex !important">
                <q-btn
                  flat
                  icon="tune"
                  @click="editUser(scope.row)"
                  color="primary"
                />
                <q-btn
                  flat
                  icon="logout"
                  @click="logoutUser(scope.row)"
                  color="negative"
                />
              </div>
            </template>
          </q-table>
        </div>
      </div>
      <div class="col-sm-12 col-xs-12 col-md-6 col-6">
        <div class="q-pa-md flex flex-center">
          <q-card v-if="editUserPopup" style="width: 100%">
            <q-card-section>
              <div class="text-h6">Editare user {{ selectedUser.name }}</div>
            </q-card-section>
            <q-card-section class="q-pt-none">
              <q-form @submit="updateUser(EditType.EDIT)">
                <q-input
                  outlined
                  v-model="selectedUser.name"
                  label="Nume"
                  required
                  :rules="[(val) => !!val || 'Numele este obligatoriu']"
                />
                <q-input
                  outlined
                  v-model="selectedUser.email"
                  label="Email"
                  disable
                  required
                  :rules="[(val) => !!val || 'Emailul este obligatoriu']"
                />
                <q-select
                  v-if="selectedUser.id !== authStore.getId"
                  outlined
                  v-model="selectedUser.permisiune_id"
                  label="Nivel"
                  emit-value
                  map-options
                  :options="permissionOptions"
                  required
                  :rules="[(val) => !!val || 'Nivelul este obligatoriu']"
                />
                <q-input
                  outlined
                  v-model="selectedUser.password"
                  label="Parola"
                />
                <q-card-actions align="right">
                  <q-btn
                    flat
                    label="Renunta"
                    color="negative"
                    @click="editUserPopup = false"
                  />
                  <q-btn
                    flat
                    label="Salveaza si ascunde utilizator"
                    color="primary"
                    type="submit"
                  />
                </q-card-actions>
              </q-form>
            </q-card-section>
          </q-card>
        </div>
      </div>
      <div class="col-sm-12 col-xs-12 col-md-6 col-6">
        <div class="q-pa-md flex flex-center">
          <q-card v-if="permissionsLoaded" style="width: 100%">
            <q-card-section>
              <div class="text-h6">Selecteaza permisiuni</div>
            </q-card-section>
            <q-card-section class="q-pt-none">
              <q-form @submit="updateUser(EditType.PERMISSIONS)">
                <q-card-section>
                  <q-option-group
                    v-model="selectedEvents"
                    :options="eventsListOptions"
                    color="green"
                    type="checkbox"
                  />
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn
                    flat
                    label="Renunta"
                    color="negative"
                    @click="permissionsLoaded = false"
                  />
                  <q-btn flat label="Salveaza" color="primary" type="submit" />
                </q-card-actions>
              </q-form>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>
  </div>

  <q-dialog v-model="addUserPopup" persistent>
    <q-card style="width: 400px">
      <q-card-section>
        <div class="text-h6">Adauga user</div>
      </q-card-section>

      <q-card-section class="q-pt-none">
        <q-form @submit="updateUser(EditType.ADD)">
          <q-input
            outlined
            v-model="newUser.name"
            label="Nume"
            required
            :rules="[(val) => !!val || 'Numele este obligatoriu']"
          />
          <q-input
            outlined
            v-model="newUser.email"
            label="Email"
            required
            :rules="[(val) => !!val || 'Emailul este obligatoriu']"
          />
          <q-select
            outlined
            v-model="newUser.permisiune_id"
            label="Nivel"
            :options="permissionOptions"
            required
            :rules="[(val) => !!val || 'Nivelul este obligatoriu']"
          />
          <q-input
            outlined
            v-model="newUser.password"
            label="Parola"
            required
            :rules="[(val) => !!val || 'Parola este obligatorie']"
          />
          <q-card-actions align="right">
            <q-btn
              flat
              label="Renunta"
              color="negative"
              @click="addUserPopup = false"
            />
            <q-btn flat label="Salveaza" color="primary" type="submit" />
          </q-card-actions>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="logoutUserPopup" persistent>
    <q-card>
      <q-form @submit="updateUser(EditType.LOGOUT)">
        <q-card-section>
          <div class="text-h6">
            Doriti sa delogati user-ul {{ selectedUser.name }} ?
          </div>
        </q-card-section>
        <q-card-actions>
          <q-btn
            flat
            label="Renunta"
            color="negative"
            @click="logoutUserPopup = false"
          />
          <q-btn flat label="Da" color="primary" type="submit" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script lang="ts">
import { useAuthStore } from 'src/stores/auth-store';
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'src/boot/axios';
import { useQuasar } from 'quasar';

interface UserDto {
  id?: number;
  name?: string;
  email?: string;
  permisiune_id?: number;
  password?: string;
}

export enum EditType {
  ADD,
  EDIT,
  LOGOUT,
  PERMISSIONS,
}

export default defineComponent({
  name: 'UsersView',
  setup() {
    const authStore = useAuthStore();
    const rows = ref([] as UserDto[]);
    const selectedUser = ref({} as UserDto);
    const newUser = ref({} as UserDto);
    const editUserPopup = ref(false);
    const logoutUserPopup = ref(false);
    const permissionsLoaded = ref(false);
    const addUserPopup = ref(false);
    const eventsList = ref([]);
    const eventsListOptions = ref([]);
    const selectedEvents = ref([]);
    const $q = useQuasar();
    const columns = [
      {
        name: 'name',
        required: true,
        label: 'Nume',
        align: 'left',
        field: (row: UserDto) => row.name,
        format: (val: UserDto) => `${val}`,
        sortable: true,
      },
      {
        name: 'email',
        align: 'left',
        label: 'Email',
        field: 'email',
        sortable: true,
      },
      {
        name: 'permisiune_id',
        label: 'Nivel',
        field: 'permisiune_id',
        sortable: true,
        format: (val: number) => {
          if (val === 1) {
            return 'Admin';
          } else if (val === 2) {
            return 'Gate Check';
          }
        },
      },
    ];
    const permissionOptions = [
      { label: 'Admin', value: 1 },
      { label: 'Gate Check', value: 2 },
    ];

    const getUsersFromAPI = async () => {
      const response = await api.get('/admin/users', {
        headers: { Authorization: `Bearer ${authStore.getToken}` },
      });

      if (response.status === 200) {
        rows.value = response.data;
      } else {
        console.error('Get users failed:', response.status, response.data);
      }
    };
    const editUser = (userRow: UserDto) => {
      selectedUser.value = userRow;
      editUserPopup.value = true;
      getUserCurrentEvents();
    };
    const logoutUser = (userRow: UserDto) => {
      selectedUser.value = userRow;
      logoutUserPopup.value = true;
    };
    const addUser = () => {
      addUserPopup.value = true;
      newUser.value = {} as UserDto;
    };
    const updateUserInBackend = async () => {
      const response = await api.put(
        `/admin/user/${selectedUser.value.id}`,
        {
          id: selectedUser.value.id,
          name: selectedUser.value.name,
          email: selectedUser.value.email,
          permisiune_id: selectedUser.value.permisiune_id,
          password: selectedUser.value.password,
        },
        {
          headers: { Authorization: `Bearer ${authStore.getToken}` },
        }
      );
      if (response.status === 200) {
        getUsersFromAPI();
        editUserPopup.value = false;
        permissionsLoaded.value = false;
        $q.notify({
          message: 'Utilizatorul a fost editat cu succes',
          color: 'positive',
          icon: 'done',
          position: 'top-right',
          timeout: 2000,
        });
      } else {
        console.error('Update user failed:', response.status, response.data);
        $q.notify({
          message: 'Editarea utilizatorului a esuat',
          color: 'negative',
          icon: 'report_problem',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };
    const logoutUserInBackend = async () => {
      const response = await api.post(
        `/admin/user/${selectedUser.value.id}/logout`,
        {
          id: selectedUser.value.id,
          name: selectedUser.value.name,
          email: selectedUser.value.email,
          permisiune_id: selectedUser.value.permisiune_id,
          password: selectedUser.value.password,
        },
        {
          headers: { Authorization: `Bearer ${authStore.getToken}` },
        }
      );
      if (response.status === 200) {
        getUsersFromAPI();
        logoutUserPopup.value = false;
        $q.notify({
          message: 'Utilizatorul a fost delogat cu succes',
          color: 'positive',
          icon: 'done',
          position: 'top-right',
          timeout: 2000,
        });
      } else {
        console.error('Logout user failed:', response.status, response.data);
        $q.notify({
          message: 'Delogarea utilizatorului a esuat',
          color: 'negative',
          icon: 'report_problem',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };
    const addUserInBackend = async () => {
      const response = await api.post(
        '/admin/user',
        {
          name: newUser.value.name,
          email: newUser.value.email,
          permisiune_id: newUser.value.permisiune_id,
          password: newUser.value.password,
        },
        {
          headers: { Authorization: `Bearer ${authStore.getToken}` },
        }
      );
      if (response.status === 201) {
        getUsersFromAPI();
        addUserPopup.value = false;
        $q.notify({
          message: 'Utilizatorul a fost adaugat cu succes',
          color: 'positive',
          icon: 'done',
          position: 'top-right',
          timeout: 2000,
        });
      } else {
        console.error('Add user failed:', response.status, response.data);
        $q.notify({
          message: 'Adaugarea utilizatorului a esuat',
          color: 'negative',
          icon: 'report_problem',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };
    const getEvents = async () => {
      const response = await api.get('/admin/events', {
        headers: { Authorization: `Bearer ${authStore.getToken}` },
      });
      if (response.status === 200) {
        eventsList.value = response.data;
        eventsListOptions.value = response.data.map((event: any) => {
          return {
            label: event.nume_eveniment,
            value: event.id,
          };
        });
      } else {
        console.error('Get events failed:', response.status, response.data);
      }
    };

    onMounted(async () => {
      getUsersFromAPI();
      getEvents();
    });
    const getUserCurrentEvents = async () => {
      const response = await api.get(
        `/admin/events/user/${selectedUser.value.id}`,
        {
          headers: { Authorization: `Bearer ${authStore.getToken}` },
        }
      );
      if (response.status === 200) {
        selectedEvents.value = response.data.events.map(
          (event: any) => event.id
        );
        permissionsLoaded.value = true;
      } else {
        console.error(
          'Get user events failed:',
          response.status,
          response.data
        );
      }
    };
    const updateUser = (type: EditType) => {
      switch (type) {
        case EditType.ADD:
          addUserInBackend();
          break;
        case EditType.EDIT:
          updateUserInBackend();
          break;
        case EditType.LOGOUT:
          logoutUserInBackend();
          break;
        case EditType.PERMISSIONS:
          updateUserEventPermissionInBackend();
          break;
      }
    };
    const updateUserEventPermissionInBackend = async () => {
      const response = await api.post(
        `/admin/events/user/${selectedUser.value.id}/permissions`,
        {
          event_ids: selectedEvents.value,
        },
        {
          headers: { Authorization: `Bearer ${authStore.getToken}` },
        }
      );
      if (response.status === 200) {
        getUsersFromAPI();
        $q.notify({
          message: 'Permisiunile utilizatorului au fost actualizate cu succes',
          color: 'positive',
          icon: 'done',
          position: 'top-right',
          timeout: 2000,
        });
      } else {
        console.error(
          'Update user events failed:',
          response.status,
          response.data
        );
        $q.notify({
          message: 'Actualizarea permisiunilor utilizatorului a esuat',
          color: 'negative',
          icon: 'report_problem',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };

    return {
      editUser,
      logoutUser,
      updateUser,
      addUser,
      updateUserEventPermissionInBackend,
      permissionsLoaded,
      selectedEvents,
      eventsListOptions,
      eventsList,
      newUser,
      EditType,
      addUserPopup,
      selectedUser,
      logoutUserPopup,
      editUserPopup,
      columns,
      rows,
      permissionOptions,
      authStore,
    };
  },
});
</script>

<style lang="scss" scoped></style>
