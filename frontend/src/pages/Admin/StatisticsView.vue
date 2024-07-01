<template>
  <div class="q-pa-md">
    <EventChart
      v-for="event in events"
      :key="event.id"
      :eventTitle="event.nume_eveniment"
      :statistics="event.statistici"
      :totals="event.totaluri"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { api } from 'src/boot/axios';
import EventChart from './EventChart.vue';
import { useAuthStore } from 'src/stores/auth-store';
import { useQuasar } from 'quasar';

export default defineComponent({
  name: 'StatisticsView',
  components: {
    EventChart,
  },
  setup() {
    const events = ref([]);
    const { getToken } = useAuthStore();
    const $q = useQuasar();

    onMounted(async () => {
      const response = await api.post(
        '/admin/events/statistics',
        {},
        {
          headers: { Authorization: `Bearer ${getToken}` },
        }
      );
      if (response.status === 200) {
        events.value = response.data.statistics;
        $q.notify({
          type: 'positive',
          icon: 'done',
          message: 'Statistici incarcate cu succes',
          position: 'top-right',
          timeout: 2000,
        });
      } else {
        console.error('Error fetching statistics');
        $q.notify({
          type: 'negative',
          icon: 'report_problem',
          message: 'Eroare la incarcarea statisticilor',
          position: 'top-right',
          timeout: 2000,
        });
      }
    });

    return {
      events,
    };
  },
});
</script>

<style lang="scss" scoped></style>
