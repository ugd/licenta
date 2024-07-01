<template>
  <div class="q-pa-md event-chart">
    <div class="totals">
      <div>Total Alte Canale: {{ totals.total_bilete_alte_canale }}</div>
      <div>Total Invitații: {{ totals.total_invitatii }}</div>
      <div>Total Bilete Fizice: {{ totals.total_intrari }}</div>
      <b>Total Intrari: {{ totals.total_general }}</b>
    </div>
    <apexchart
      type="area"
      height="300"
      style="max-width: 800px; max-height: 300px"
      :options="chartOptions"
      :series="chartSeries"
    ></apexchart>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'EventChart',
  props: {
    eventTitle: String,
    statistics: Object,
    totals: Object,
  },
  setup(props) {
    const chartOptions = ref({
      chart: {
        id: 'area-chart-' + props.eventTitle,
      },
      xaxis: {
        categories: Object.keys(props.statistics),
      },
      title: {
        text: props.eventTitle,
        align: 'left',
      },
    });

    const chartSeries = ref([
      {
        name: 'Alte Canale',
        data: Object.values(props.statistics).map(
          (interval) => interval.bilete
        ),
      },
      {
        name: 'Invitații',
        data: Object.values(props.statistics).map(
          (interval) => interval.invitatii
        ),
      },
      {
        name: 'Bilete Fizice',
        data: Object.values(props.statistics).map(
          (interval) => interval.intrari
        ),
      },
    ]);

    return {
      chartOptions,
      chartSeries,
    };
  },
});
</script>

<style lang="scss" scoped>
.totals {
  margin-bottom: 10px;
}
</style>
