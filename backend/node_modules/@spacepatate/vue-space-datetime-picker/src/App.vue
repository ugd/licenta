<template>
  <div id="app">
    <SpaceDatetimePicker
      v-model="datetime"></SpaceDateTimePicker>
    <SpaceDaterangePicker :placeholder="['start date', 'end date']"
      v-model="rangeDatetimes" :disabled="false"></SpaceDaterangePicker>

  </div>
</template>

<script>
import SpaceDatetimePicker from '@/components/SpaceDatetimePicker.vue';
import SpaceDaterangePicker from '@/components/SpaceDaterangePicker.vue';
// import SpaceMonthPicker from '@/components/SpaceMonthPicker.vue';

export default {
  name: 'App',
  components: {
    SpaceDatetimePicker,
    SpaceDaterangePicker,
    // SpaceMonthPicker,
  },
  data() {
    return {
      datetime: null,
      rangeDatetimes: null,
    };
  },
};
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
