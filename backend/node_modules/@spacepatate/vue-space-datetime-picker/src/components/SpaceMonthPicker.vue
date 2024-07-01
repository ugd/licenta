<template>
  <div class="space-date-month-picker">
    <div class="header">
      <a class="prev-year-btn" @click="selectPrevYear"></a>
      <div class="current-year-label">
        {{ datetime.getFullYear() }}
      </div>
      <a class="next-year-btn" @click="selectNextYear"></a>
    </div>
    <div class="months-grid">
      <div class="months-list" v-for="(monthsList, index) of months" :key="`month-list-${index}`">
        <div class="month"
          v-for="(month, index) of monthsList"
          @click="selectMonth(month)"
          :key="`month-${index}`"
          :class="{
            'current-month': month === datetime.getMonth(),
          }">
          <span>
            {{  formatedMonthLabel(month) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const months = [
  [0, 1, 2],
  [3, 4, 5],
  [6, 7, 8],
  [9, 10, 11],
];

export default {
  props: {
    value: {
      type: Date,
      required: false,
      default: null,
    },

    locale: {
      type: String,
      required: false,
      default: undefined,
    },

    month: {
      type: String,
      required: false,
      default: 'short', // numeric | 2-digit | long | short | narrow
    },

    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  data() {
    return {
      months,
      datetime: null,
    };
  },

  beforeMount() {
    if (this.value) {
      this.datetime = this.value;
    } else {
      this.datetime = new Date();
    }
  },

  watch: {
    value() {
      if (this.value) {
        this.datetime = this.value;
      }
    },
  },

  methods: {
    formatedMonthLabel(month) {
      const datetime = new Date(this.datetime.getFullYear(), month, 1);
      return datetime.toLocaleString(this.locale, { month: this.month });
    },

    emitSelectedMonth(datetime) {
      this.$emit('change', datetime);
    },

    selectMonth(selectedMonth) {
      if (this.disabled) {
        return;
      }
      const tmp = this.datetime.setMonth(selectedMonth);
      this.datetime = new Date(tmp);
      this.emitSelectedMonth(this.datetime);
    },

    selectPrevYear() {
      const year = this.datetime.getFullYear() - 1;
      const tmp = this.datetime.setYear(year);
      this.datetime = new Date(tmp);
    },

    selectNextYear() {
      const year = this.datetime.getFullYear() + 1;
      const tmp = this.datetime.setYear(year);
      this.datetime = new Date(tmp);
    },
  },
};
</script>
<style lang="scss" scoped>
  .space-date-month-picker {
    display: flex;
    flex-direction: column;
    width: 300px;
    box-shadow: 1px 2px 6px 0px #bfbfbf;

    .header {
      display: inline-flex;
      justify-content: center;
      border-bottom: 1px solid #ececec;
      .current-year-label {
        display: inline-flex;
        line-height: 33px;
      }

      .prev-year-btn,
      .next-year-btn {
        padding: 0 10px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 20px;
        cursor: pointer;
        &:hover {
          color: #a4d0ff;
        }
      }
      .prev-year-btn::after {
        content: '\00AB';
      }
      .next-year-btn::after {
        content: '\00BB';
      }
    }

    .months-grid {
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .months-list {
      display: flex;
      justify-content: space-around;

      .month {
        flex: 1;
        span {
          padding: 3px 5px;
          border: 1px solid transparent;
        }
        padding: 5px;
        cursor: pointer;
        &:hover {
          color: #61a9f5;
        }

        &.current-month {
          span {
            color: #61a9f5;
          }
        }
      }
    }
  }
</style>
