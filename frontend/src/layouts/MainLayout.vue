<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title> {{ env.VITE_APP_NAME }} </q-toolbar-title>

        <q-space />

        <q-btn
          v-if="isAuthenticated && !$q.screen.lt.sm"
          class="q-mx-md"
          color="warning"
          @click="callAnAdminPopup = true"
          >Cheama un Supervizor</q-btn
        >
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered>
      <q-list>
        <q-item-label header> Menu </q-item-label>
        <q-item-section class="q-pa-md">
          <q-select
            v-if="isAuthenticated"
            v-model="eventSelected"
            :options="eventsListOptions"
            label="!!!Selecteaza evenimentul!!!"
          />
        </q-item-section>
        <q-item-section
          ><q-btn
            v-if="isAuthenticated && $q.screen.lt.sm"
            class="q-mx-sm"
            color="warning"
            @click="callAnAdminPopup = true"
            >Cheama un Supervizor</q-btn
          ></q-item-section
        >
        <EssentialLink
          v-for="link in filteredLinks"
          :key="link.title"
          v-bind="link"
        />

        <div class="q-pa-md">
          UI {{ env.VITE_APP_UI_VERSION }} - {{ env.VITE_ENV_MODE }}
        </div>
      </q-list>
    </q-drawer>
    <q-dialog v-model="callAnAdminPopup">
      <q-card>
        <q-card-section>
          <div class="text-h6">Trimite mesaj catre admin</div>
        </q-card-section>

        <q-card-section class="q-pt-none">
          Lasa un mesaj pentru admin
        </q-card-section>
        <q-card-section class="q-pa-sm">
          <q-input outlined v-model="mesaj" label="Mesaj optional" />
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat label="Renunta" color="negative" v-close-popup />
          <q-btn
            flat
            label="Trimite"
            color="primary"
            @click="sendNotification"
            v-close-popup
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { defineComponent, ref, computed, watch, onMounted } from 'vue';
import EssentialLink from 'components/EssentialLink.vue';
import { useAuthStore } from 'stores/auth-store';
import { api } from 'src/boot/axios';
import { useEventsStore } from 'stores/event-store';
import { useQuasar } from 'quasar';

const linksList = [
  {
    title: 'Login',
    caption: 'Login',
    icon: 'login',
    link: '/login',
  },
  {
    title: 'Dashboard',
    caption: 'Dashboard',
    icon: 'dashboard',
    link: '/dashboard',
    authRequired: true,
  },
  {
    title: 'Statistics',
    caption: 'Statistics',
    icon: 'bar_chart',
    link: '/statistics',
    authRequired: true,
    isAdminRequired: true,
  },
  {
    title: 'Users',
    caption: 'Users',
    icon: 'people',
    link: '/users',
    authRequired: true,
    isAdminRequired: true,
  },
  {
    title: 'Events',
    caption: 'Events',
    icon: 'event',
    link: '/events/dashboard',
    authRequired: true,
    isAdminRequired: true,
  },
  {
    title: 'Tickets',
    caption: 'Tickets',
    icon: 'local_activity',
    link: '/events/tickets',
    authRequired: true,
    isAdminRequired: true,
  },
  {
    title: 'Invitations',
    caption: 'Invitations',
    icon: 'mail',
    link: '/events/invitations',
    authRequired: true,
    isAdminRequired: true,
  },
  {
    title: 'Logout',
    caption: 'Logout',
    icon: 'logout',
    link: '/logout',
    authRequired: true,
  },
];

export default defineComponent({
  name: 'MainLayout',

  components: {
    EssentialLink,
  },

  setup() {
    const authStore = useAuthStore();
    const leftDrawerOpen = ref(false);
    const isAuthenticated = ref(false);
    const isAdmin = ref(false);
    const callAnAdminPopup = ref(false);
    const mesaj = ref('');
    const eventsStore = useEventsStore();
    const eventsListOptions = ref([]);
    const eventSelected = ref([]);
    const $q = useQuasar();

    watch(
      () => authStore.isAuthenticated(),
      async (newVal, oldVal) => {
        isAuthenticated.value = await newVal;
      },
      { immediate: true }
    );

    watch(
      () => authStore.isAdmin(),
      async (newVal, oldVal) => {
        isAdmin.value = await newVal;
      },
      { immediate: true }
    );

    const filteredLinks = computed(() => {
      return linksList.filter((link) => {
        if (!isAuthenticated.value) {
          return !link.authRequired && !link.isAdminRequired;
        }
        if (isAuthenticated.value && !isAdmin.value) {
          return link.authRequired && !link.isAdminRequired;
        }
        if (isAuthenticated.value && isAdmin.value) {
          return link.authRequired || link.isAdminRequired;
        }
        return false;
      });
    });
    const computedCustomMessage = computed(() => {
      if (mesaj.value.length > 0) {
        return 'Mesaj: ' + mesaj.value;
      } else {
        return 'Trimis fara mesaj';
      }
    });

    const sendNotification = async () => {
      await fetch(
        import.meta.env.VITE_ENV_NOTIFICATION_SERVER +
          import.meta.env.VITE_ENV_NOTIFICATION_TOPIC,
        {
          method: 'POST',
          headers: {
            Title: 'Un Admin este solicitat la intrare',
            Priority: '5',
            Tags: 'warning',
            Actions: 'view, Deschide Whatsapp, whatsapp://',
          },
          body: computedCustomMessage.value,
        }
      );
      mesaj.value = '';
    };

    const getEventsForCurentUser = async () => {
      const response = await api.get('/entrance/eventsAccess', {
        headers: { Authorization: `Bearer ${authStore.getToken}` },
      });
      if (response.status === 200) {
        eventsListOptions.value = response.data.events.map((event: any) => {
          return {
            label: event.nume_eveniment,
            value: event.id,
          };
        });
      } else {
        console.error('Get users failed:', response.status, response.data);
        $q.notify({
          type: 'negative',
          message: 'Get events failed',
          icon: 'report_problem',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };

    watch(
      () => eventsListOptions.value,
      async (newVal, oldVal) => {
        if (newVal.length !== 0) {
          const selectedOption = newVal.find(
            (option) => option.value === eventsStore.selectedEventId
          );
          if (selectedOption) {
            eventSelected.value = selectedOption;
          }
        }
      },
      { immediate: true }
    );

    watch(
      () => eventSelected.value,
      async (newVal, oldVal) => {
        if (newVal && newVal.value) {
          eventsStore.selectEventID(newVal.value);
          eventsStore.selectEventName(newVal.label);
        }
      },
      { immediate: true }
    );

    onMounted(async () => {
      await getEventsForCurentUser();
    });

    return {
      mesaj,
      essentialLinks: linksList,
      leftDrawerOpen,
      eventSelected,
      env: import.meta.env,
      eventsListOptions,
      isAuthenticated,
      filteredLinks,
      callAnAdminPopup,
      sendNotification,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value;
      },
    };
  },
});
</script>
