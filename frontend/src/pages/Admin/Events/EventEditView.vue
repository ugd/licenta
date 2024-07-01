<template>
  <div class="q-pa-md">
    <div class="text-h3">
      {{ isAddMode ? 'Add Event' : 'Edit Event' }}
    </div>
    <q-card>
      <q-card-section>
        <q-form @submit.prevent="saveEvent" class="flex justify-center">
          <q-input
            v-model="event.nume_eveniment"
            label="Event Name"
            outlined
            style="width: 100%"
            class="q-ma-md"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            dense
          ></q-input>
          <q-input
            v-model="event.main_act"
            class="q-ma-md"
            label="Main Act"
            style="width: 45%"
            outlined
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            dense
          ></q-input>
          <q-input
            v-model="event.event_edition"
            class="q-ma-md"
            label="Event Edition"
            style="width: 45%"
            outlined
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            dense
          ></q-input>
          <q-select
            label="Performers"
            v-model="event.performers"
            use-input
            class="q-ma-md"
            outlined
            style="width: 100%"
            :rules="[(val) => !!val.length || 'Campul este obligatoriu']"
            dense
            use-chips
            multiple
            hide-dropdown-icon
            input-debounce="0"
            new-value-mode="add-unique"
          />
          <q-input
            v-model="event.locatie"
            label="Venue Name"
            style="width: 25%"
            class="q-ma-md"
            outlined
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            dense
          ></q-input>
          <q-input
            v-model="event.venue_entrance"
            style="width: 25%"
            class="q-ma-md"
            label="Venue Entrance"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            outlined
            dense
          ></q-input>
          <q-input
            v-model="event.venue_room"
            class="q-ma-md"
            style="width: 25%"
            label="Venue Room"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            outlined
            dense
          ></q-input>
          <q-input
            v-model="event.event_location"
            label="Venue Address"
            outlined
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            dense
            class="q-ma-md"
            style="width: 25%"
          ></q-input>
          <q-input
            v-model="event.location_lat"
            style="width: 25%"
            class="q-ma-md"
            label="Venue Loc. Latitude"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            outlined
            dense
          ></q-input>
          <q-input
            v-model="event.location_long"
            style="width: 25%"
            class="q-ma-md"
            label="Venue Loc. Longitude"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            outlined
            dense
          ></q-input>
          <q-input
            outlined
            dense
            style="width: 45%"
            class="q-ma-md"
            v-model="event.data_inceput"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            label="Start Date"
          >
            <template v-slot:prepend>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="event.data_inceput" mask="YYYY-MM-DD HH:mm">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>

            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-time
                    v-model="event.data_inceput"
                    mask="YYYY-MM-DD HH:mm"
                    format24h
                  >
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-time>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <q-input
            outlined
            dense
            class="q-ma-md"
            style="width: 45%"
            v-model="event.data_sfarsit"
            :rules="[(val) => !!val || 'Campul este obligatoriu']"
            label="End Date"
          >
            <template v-slot:prepend>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-date v-model="event.data_sfarsit" mask="YYYY-MM-DD HH:mm">
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-date>
                </q-popup-proxy>
              </q-icon>
            </template>

            <template v-slot:append>
              <q-icon name="access_time" class="cursor-pointer">
                <q-popup-proxy
                  cover
                  transition-show="scale"
                  transition-hide="scale"
                >
                  <q-time
                    v-model="event.data_sfarsit"
                    mask="YYYY-MM-DD HH:mm"
                    format24h
                  >
                    <div class="row items-center justify-end">
                      <q-btn v-close-popup label="Close" color="primary" flat />
                    </div>
                  </q-time>
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
          <div class="row q-mt-md">
            <div class="col-12 q-pa-md">
              <div class="row items-center q-pa-md">
                <div class="col-md-4 col-sm-12 q-pa-md">
                  <q-img
                    :src="event.invitation_background"
                    alt="Event Background"
                    width="100%"
                    height="auto"
                  />
                </div>
                <div class="col-md-8 col-sm-12 q-pa-md">
                  <div>
                    <a
                      :href="`${env.VITE_API}/assets/template_invitatie_background.psd`"
                      target="_blank"
                      >Descarca template-ul .psd pentru background invite</a
                    >
                  </div>
                  <q-uploader
                    label="Invitation Background PDF"
                    outlined
                    flat
                    dense
                    max-files="1"
                    accept="application/pdf"
                    v-model="event.invitation_background"
                    @added="
                      (file) => uploadFile(file[0], 'invitation_background')
                    "
                  ></q-uploader>
                </div>
              </div>
            </div>

            <div class="col-12 q-pa-md">
              <div class="row items-center q-pa-md">
                <div class="col-md-4 col-sm-12 q-pa-md">
                  <q-img
                    :src="event.appleWalletBackground"
                    alt="Wallet Background 224px x 283px"
                    width="100%"
                    height="auto"
                  />
                </div>
                <div class="col-md-8 col-sm-12 q-pa-md">
                  <div>
                    <a
                      :href="`${env.VITE_API}/assets/background@2x.psd`"
                      target="_blank"
                      >Descarca template-ul .psd pentru imagine</a
                    >
                  </div>
                  <q-uploader
                    label="Apple Wallet Pass Background 224px × 283px"
                    class="q-ma-md"
                    outlined
                    flat
                    max-files="1"
                    dense
                    accept="image/png"
                    v-model="event.appleWalletBackground"
                    @added="
                      (file) => uploadFile(file[0], 'appleWalletBackground')
                    "
                  ></q-uploader>
                </div>
              </div>
            </div>

            <div class="col-12 q-pa-md">
              <div class="row items-center q-pa-md">
                <div class="col-md-4 col-sm-12 q-pa-md">
                  <q-img
                    :src="event.appleWalletThumbnail"
                    alt="Wallet Thumbnail 224px × 283px"
                    width="100%"
                    height="auto"
                  />
                </div>
                <div class="col-md-8 col-sm-12 q-pa-md">
                  <div>
                    <a
                      :href="`${env.VITE_API}/assets/thumbnail@2x.psd`"
                      target="_blank"
                      >Descarca template-ul .psd pentru imagine</a
                    >
                  </div>
                  <q-uploader
                    label="Apple Wallet Pass Thumbnail 224px × 283px"
                    class="q-ma-md"
                    outlined
                    flat
                    dense
                    accept="image/png"
                    v-model="event.appleWalletThumbnail"
                    @added="
                      (file) => uploadFile(file[0], 'appleWalletThumbnail')
                    "
                  ></q-uploader>
                </div>
              </div>
            </div>

            <div class="col-12 q-pa-md">
              <div class="row items-center q-pa-md">
                <div class="col-md-4 col-sm-12 q-pa-md">
                  <q-img
                    :src="event.appleWalletIcon"
                    alt="Wallet Icon 87px × 87px"
                    width="100%"
                    height="auto"
                  />
                </div>
                <div class="col-md-8 col-sm-12 q-pa-md">
                  <div>
                    <a
                      :href="`${env.VITE_API}/assets/icon@3x.psd`"
                      target="_blank"
                      >Descarca template-ul .psd pentru imagine</a
                    >
                  </div>
                  <q-uploader
                    label="Apple Wallet Pass Icon 87px × 87px"
                    class="q-ma-md"
                    outlined
                    flat
                    dense
                    max-files="1"
                    accept="image/png"
                    v-model="event.appleWalletIcon"
                    @added="(file) => uploadFile(file[0], 'appleWalletIcon')"
                  ></q-uploader>
                </div>
              </div>
            </div>

            <div class="col-12 q-pa-md">
              <div class="row items-center q-pa-md">
                <div class="col-md-4 col-sm-12 q-pa-md">
                  <q-img
                    :src="event.appleWalletLogo"
                    alt="Wallet Logo 370px × 156px"
                    width="100%"
                    height="auto"
                  />
                </div>
                <div class="col-md-8 col-sm-12 q-pa-md">
                  <div>
                    <a
                      :href="`${env.VITE_API}/assets/logo@2x.psd`"
                      target="_blank"
                      >Descarca template-ul .psd pentru imagine</a
                    >
                  </div>
                  <q-uploader
                    label="Apple Wallet Pass Logo 370px × 156px"
                    class="q-ma-md"
                    outlined
                    flat
                    dense
                    max-files="1"
                    accept="image/png"
                    v-model="event.appleWalletLogo"
                    @added="(file) => uploadFile(file[0], 'appleWalletLogo')"
                  ></q-uploader>
                </div>
              </div>
            </div>
          </div>
          <q-btn
            type="submit"
            color="primary"
            label="Save"
            class="full-width"
          ></q-btn>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onBeforeMount, onMounted } from 'vue';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth-store';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';

interface Event {
  id: number;
  nume_eveniment: string;
  locatie: string;
  data_inceput: string;
  data_sfarsit: string;
  event_location: string;
  location_lat: string;
  location_long: string;
  invitation_background: string;
  appleWalletBackground: string;
  appleWalletThumbnail: string;
  appleWalletIcon: string;
  appleWalletLogo: string;
  main_act: string;
  performers: string[];
  event_edition: string;
  venue_entrance: string;
  venue_room: string;
  uuid: string | null;
}

export default defineComponent({
  name: 'EventEditView',
  props: {},
  setup() {
    const { getToken } = useAuthStore();
    const router = useRouter();
    const event = ref({} as Event);
    const isAddMode = ref(false);
    const uuid = ref('');
    const $q = useQuasar();

    event.value = {
      id: 0,
      nume_eveniment: '',
      locatie: '',
      data_inceput: '',
      data_sfarsit: '',
      event_location: '',
      location_lat: '',
      location_long: '',
      invitation_background: 'https://via.placeholder.com/710x200',
      appleWalletBackground: 'https://via.placeholder.com/224x283',
      appleWalletThumbnail: 'https://via.placeholder.com/224x283',
      appleWalletIcon: 'https://via.placeholder.com/87x87',
      appleWalletLogo: 'https://via.placeholder.com/370x156',
      main_act: '',
      performers: [],
      event_edition: '',
      venue_entrance: 'Main Entrance',
      venue_room: 'Main Room',
      uuid: null,
    };

    const imagesInternal = ref<{ [key: string]: string }>({
      invitation_background: '',
      appleWalletBackground: '',
      appleWalletThumbnail: '',
      appleWalletIcon: '',
      appleWalletLogo: '',
    });

    const uploadFile = async (file: File, type: string) => {
      const formData = new FormData();

      formData.append('file', file);
      formData.append('container', type);
      formData.append('uuid', uuid.value);
      const response = await api.post('/admin/events/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: `Bearer ${getToken}`,
        },
      });
      if (response.status === 200) {
        $q.notify({
          message: 'Fisier incarcat cu succes!',
          icon: 'done',
          timeout: 2000,
          color: 'positive',
          position: 'top-right',
        });
        event.value[type] = response.data.url;
        imagesInternal.value[type] = response.data.internalPath;
      } else {
        $q.notify({
          message: 'Fisierul nu a putut fi incarcat!',
          color: 'negative',
          icon: 'report_problem',
          timeout: 2000,
          position: 'top-right',
        });
      }
    };

    const saveEvent = async () => {
      if (isAddMode.value) {
        event.value.invitation_background =
          imagesInternal.value.invitation_background;
        event.value.appleWalletBackground =
          imagesInternal.value.appleWalletBackground;
        event.value.appleWalletThumbnail =
          imagesInternal.value.appleWalletThumbnail;
        event.value.appleWalletIcon = imagesInternal.value.appleWalletIcon;
        event.value.appleWalletLogo = imagesInternal.value.appleWalletLogo;
        event.value.uuid = uuid.value;
        await api
          .post('/admin/events/create', event.value, {
            headers: { Authorization: `Bearer ${getToken}` },
          })
          .then((response) => {
            if (response.status === 200) {
              router.push('/events/dashboard');
              $q.notify({
                message: 'Evenimentul a fost adaugat cu succes!',
                color: 'positive',
                icon: 'done',
                timeout: 2000,
                position: 'top-right',
              });
            }
          })
          .catch((error) => {
            $q.notify({
              message: 'Evenimentul nu a putut fi adaugat!',
              color: 'negative',
              icon: 'report_problem',
              timeout: 2000,
              position: 'top-right',
            });
          });
      } else {
        event.value.invitation_background =
          imagesInternal.value.invitation_background;
        event.value.appleWalletBackground =
          imagesInternal.value.appleWalletBackground;
        event.value.appleWalletThumbnail =
          imagesInternal.value.appleWalletThumbnail;
        event.value.appleWalletIcon = imagesInternal.value.appleWalletIcon;
        event.value.appleWalletLogo = imagesInternal.value.appleWalletLogo;
        event.value.uuid = uuid.value;
        await api
          .put(`/admin/events/${event.value.id}/update`, event.value, {
            headers: { Authorization: `Bearer ${getToken}` },
          })
          .then((response) => {
            if (response.status === 200) {
              router.push('/events/dashboard');
              $q.notify({
                message: 'Evenimentul a fost actualizat cu succes!',
                color: 'positive',
                icon: 'done',
                timeout: 2000,
                position: 'top-right',
              });
            }
          })
          .catch((error) => {
            $q.notify({
              message: 'Evenimentul nu a putut fi actualizat!',
              color: 'negative',
              icon: 'report_problem',
              timeout: 2000,
              position: 'top-right',
            });
          });
      }
    };
    onBeforeMount(() => {
      isAddMode.value = router.currentRoute.value.name === 'add-event';
    });

    onMounted(async () => {
      if (isAddMode.value) {
        const response = await api.get('/admin/events/create/generateUUID', {
          headers: { Authorization: `Bearer ${getToken}` },
        });
        uuid.value = response.data.uuid;
      }
      if (!isAddMode.value) {
        const id = router.currentRoute.value.params.id;
        const response = await api.get(`/admin/events/show/${id}`, {
          headers: { Authorization: `Bearer ${getToken}` },
        });
        if (response.status === 200) {
          event.value = response.data;
          imagesInternal.value.appleWalletBackground =
            response.data.apple_wallet_background;
          imagesInternal.value.appleWalletThumbnail =
            response.data.apple_wallet_thumbnail;
          imagesInternal.value.appleWalletIcon =
            response.data.apple_wallet_icon;
          imagesInternal.value.appleWalletLogo =
            response.data.apple_wallet_logo;
          imagesInternal.value.invitation_background =
            response.data.invitation_background;
          if (response.data.uuid) {
            uuid.value = response.data.uuid;
          } else {
            const response = await api.get(
              '/admin/events/create/generateUUID',
              {
                headers: { Authorization: `Bearer ${getToken}` },
              }
            );
            uuid.value = response.data.uuid;
          }
          event.value.invitation_background = `${
            import.meta.env.VITE_API_SERVER
          }/storage${response.data.invitation_background?.replace(
            '.pdf',
            '.png'
          )}`;
          event.value.appleWalletBackground = `${
            import.meta.env.VITE_API_SERVER
          }/storage${response.data.apple_wallet_background}/background@2x.png`;
          event.value.appleWalletThumbnail = `${
            import.meta.env.VITE_API_SERVER
          }/storage${response.data.apple_wallet_thumbnail}/thumbnail@2x.png`;
          event.value.appleWalletIcon = `${
            import.meta.env.VITE_API_SERVER
          }/storage${response.data.apple_wallet_icon}/icon@3x.png`;
          event.value.appleWalletLogo = `${
            import.meta.env.VITE_API_SERVER
          }/storage${response.data.apple_wallet_logo}/logo@2x.png`;
        }
      }
    });

    return {
      imagesInternal,
      uploadFile,
      saveEvent,
      isAddMode,
      env: import.meta.env,
      event,
    };
  },
});
</script>
