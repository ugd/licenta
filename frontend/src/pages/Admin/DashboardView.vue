<template>
  <div class="container q-pa-md">
    <div class="scan-button">
      <q-btn
        v-if="isEventSelected"
        rounded
        color="negative"
        size="xl"
        :loading="isAddTicketLoading"
        style="margin-bottom: 50px"
        label="Adauga un bilet fizic"
        @click="openAddTicketPopup"
      ></q-btn>
      <q-btn
        rounded
        outline
        color="blue"
        size="xl"
        label="Scaneaza"
        @click="openScanDialog"
      ></q-btn>
      <q-form @submit="getResponse">
        <div class="flex inline-flex q-pa-sm">
          <q-input v-model="codeForScan" label="Cauta cod" />
          <q-btn label="Cauta" type="submit" />
        </div>
      </q-form>
    </div>
  </div>
  <div v-if="responseType" :class="['response-card', setCardClass]">
    <span class="response-message"
      ><b>{{ message }}</b></span
    >
    <h5 v-if="code">Cod Bilet/Invitatie: {{ code }}</h5>
    <p v-if="name" class="response-name">
      <b>{{ name }}</b>
    </p>
    <p v-if="email">Adresa e-mail: {{ email }}</p>
    <p v-if="channel">
      <u>Canal Bilet: {{ channel }}</u>
    </p>
    <p v-if="lastUpdate">Ultima modificare: {{ lastUpdate }}</p>
    <p v-if="lastStatus">Ultimul status: {{ lastStatus }}</p>
  </div>
  <q-dialog v-model="dialog">
    <q-card>
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Scan</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section>
        <StreamBarcodeReader @decode="onDecode" />
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="addTicketPopup" persistent>
    <q-card>
      <q-form @submit="addTicket">
        <q-card-section>
          <div class="text-h6">Vrei sa adaugi un bilet fizic?</div>
        </q-card-section>
        <q-card-actions>
          <q-space />
          <q-btn
            label="Anuleaza"
            color="negative"
            flat
            dense
            @click="closeAddTicketPopup"
            v-close-popup
          />
          <q-space />
          <q-btn
            label="Adauga"
            color="positive"
            flat
            dense
            type="submit"
            :loading="isAddTicketLoading"
            @click="buttonAddPressed"
            v-close-popup
          />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script lang="ts">
import { ref, defineComponent, computed } from 'vue';
import { useAuthStore } from 'src/stores/auth-store';
import { api } from 'src/boot/axios';
import { format } from 'date-fns';
import { StreamBarcodeReader } from 'vue-barcode-reader';
import { useEventsStore } from 'src/stores/event-store';
import { useQuasar } from 'quasar';
// import { usePlausible } from 'v-plausible/vue';

export default defineComponent({
  name: 'DashboardView',
  components: {
    StreamBarcodeReader,
  },
  setup() {
    const { getToken } = useAuthStore();
    const dialog = ref(false);
    const codeForScan = ref('');
    const message = ref('');
    const code = ref('');
    const name = ref('');
    const email = ref('');
    const channel = ref('');
    const lastUpdate = ref('');
    const lastStatus = ref('');
    const responseType = ref<number | null>(null);
    const eventStore = useEventsStore();
    const isAddTicketLoading = ref(false);
    const addTicketPopup = ref(false);
    const $q = useQuasar();
    // const { trackEvent } = usePlausible();

    const isEventSelected = computed(() => {
      const eventId = eventStore.getEventSelectedId;
      return eventId !== null && eventId !== undefined && eventId !== 0;
    });

    const openScanDialog = () => {
      dialog.value = true;
    };

    const openAddTicketPopup = () => {
      addTicketPopup.value = true;
      // trackEvent('Buton adauga apasat', {
      //   localTime: new Date().toLocaleString(),
      // });
    };

    const closeAddTicketPopup = () => {
      addTicketPopup.value = false;
      // trackEvent('Buton renunta apasat', {
      //   localTime: new Date().toLocaleString(),
      // });
    };

    const onDecode = (value: string) => {
      codeForScan.value = value;
      dialog.value = false;
      getResponse();
    };

    const getStatusName = (id: number) => {
      const statusNames = {
        1: 'Inregistrat',
        2: 'Email Trimis',
        3: 'Validat',
        4: 'Anulat',
      };
      return statusNames[id] || 'In asteptare';
    };

    const getResponse = async () => {
      resetResponseData();

      const responseRaw = await api.post(
        '/entrance/scan',
        { code: codeForScan.value },
        {
          headers: { Authorization: `Bearer ${getToken}` },
        }
      );

      if (responseRaw.status === 200) {
        $q.notify({
          type: 'positive',
          message: `Interogare efectuata cu success, Ora: ${new Date().toLocaleTimeString()}`,
          position: 'top-right',
          icon: 'check_circle',
          timeout: 5000,
        });
      }

      processResponse(responseRaw.data);
    };

    const processResponse = (response: any) => {
      responseType.value = response.type;

      if (response.type !== 4) {
        channel.value = response.data.vendor;
        lastUpdate.value = format(
          new Date(response.data.updated_at),
          'HH:mm:ss dd.MM.yyyy'
        );
        lastStatus.value = getStatusName(response.data.lastState);
        name.value = response.data.stdName;
        email.value = response.data.stdEmail;
        code.value = response.data.stdCode;

        if (response.type === 2) {
          message.value = response.data.vendor;
          responseType.value = 2;
        } else if (response.type === 1) {
          message.value =
            response.data.invite_type_id === 1 ? 'General Access' : 'VIP';
          responseType.value = response.data.invite_type_id === 1 ? 1 : 4;
        } else if (response.data.stare_id === 4) {
          message.value = 'Biletul anulat';
          responseType.value = 3;
        } else {
          message.value = 'Biletul utilizat';
          responseType.value = 3;
        }
      } else {
        message.value = `Cod invalid: ${codeForScan.value}`;
        responseType.value = 5;
      }
    };

    const resetResponseData = () => {
      message.value = '';
      code.value = '';
      name.value = '';
      email.value = '';
      channel.value = '';
      lastUpdate.value = '';
      lastStatus.value = '';
      responseType.value = null;
    };

    const addTicket = async () => {
      isAddTicketLoading.value = true;

      const response = await api.post(
        `/entrance/event/addTicket/${eventStore.getEventSelectedId}`,
        {},
        {
          headers: { Authorization: `Bearer ${getToken}` },
        }
      );

      // trackEvent('Bilet', { localTime: new Date().toLocaleString() });

      isAddTicketLoading.value = false;

      if (response.status === 201) {
        // trackEvent('Bilet adaugat', {
        //   localTime: new Date().toLocaleString(),
        // });
        $q.notify({
          type: 'positive',
          icon: 'check_circle',
          message: `Bilet adaugat cu success, Ora: ${new Date().toLocaleTimeString()}`,
          position: 'top-right',
          timeout: 5000,
        });
      } else {
        // trackEvent('Eroare Bilet', {
        //   localTime: new Date().toLocaleString(),
        // });
        $q.notify({
          type: 'negative',
          message: `Biletul nu a putut fi adaugat, Ora: ${new Date().toLocaleTimeString()}`,
          icon: 'report_problem',
          position: 'center',
          timeout: 10000,
        });
      }
    };

    const buttonAddPressed = () => {
      // trackEvent('Buton adauga apasat', {
      //   localTime: new Date().toLocaleString(),
      // });
    };

    const setCardClass = computed(() => {
      const cardClasses = {
        1: 'response-valid',
        2: 'response-alt-channel',
        3: 'response-validated',
        4: 'response-gold',
        5: 'response-invalid',
      };
      return cardClasses[responseType.value] || 'response-invalid';
    });

    return {
      getResponse,
      onDecode,
      addTicket,
      openAddTicketPopup,
      closeAddTicketPopup,
      openScanDialog,
      addTicketPopup,
      isAddTicketLoading,
      eventStore,
      setCardClass,
      responseType,
      dialog,
      code,
      codeForScan,
      message,
      lastStatus,
      lastUpdate,
      name,
      email,
      channel,
      isEventSelected,
      buttonAddPressed,
    };
  },
});
</script>

<style lang="scss" scoped>
.scan-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-card {
  border-radius: 20px;
  margin: 0 16px;
  padding: 16px 16px;
}

.response-message {
  font-size: xx-large;
}

.response-name {
  font-size: xx-large;
}

.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-valid {
  background-color: green;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-validated {
  background-color: red;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-invalid {
  background-color: black;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-gold {
  background-color: gold;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.response-alt-channel {
  background-color: blue;
  color: white;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
}
</style>
