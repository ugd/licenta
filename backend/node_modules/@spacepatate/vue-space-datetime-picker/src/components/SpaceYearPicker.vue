<template>
  <div class="space-year-picker">
    <div class="header">
      <a class="prev-10year-btn" @click="selectPrev10Year"></a>
      <div class="current-10years-label">
        {{ formatedCurrent10YearsLabel() }}
      </div>
      <a class="next-10year-btn" @click="selectNext10Year"></a>
    </div>
    <div class="years-grid">
      <div class="years-list" v-for="(yearsList, index) of years" :key="`year-list-${index}`">
        <div class="year"
          @click="selectYear(year)"
          v-for="(year) of yearsList"
          :key="`year-${year}`"
          :class="{
            'current-year': year.getFullYear() === datetime.getFullYear(),
          }">
          <span>
            {{ formatedYearLabel(year) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
const years = [
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

    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  data() {
    return {
      years: [],
      startingYear: null,
      datetime: null,
    };
  },

  computed: {
    currentYear() {
      return this.datetime.getFullYear();
    },
  },

  beforeMount() {
    if (this.value) {
      this.datetime = this.value;
    } else {
      this.datetime = new Date();
    }
    this.init();
  },

  watch: {
    value() {
      this.datetime = this.value;
      if (this.value) {
        this.datetime = this.value;
      }
      this.init();
    },
  },

  methods: {
    init() {
      const yearStr = this.datetime.getFullYear().toString();
      const lastDigit = yearStr.substr(3, 1);
      const diff = Number.parseInt(lastDigit, 10) - (-1);
      this.startingYear = new Date(this.datetime.getFullYear() - diff,
        this.datetime.getMonth(),
        this.datetime.getDate(),
        this.datetime.getHours(),
        this.datetime.getMinutes(),
        this.datetime.getSeconds());
      this.initYears();
    },

    initYears() {
      this.years = [];
      for (let i = 0; i < years.length; i += 1) {
        const tempYears = [];
        const currentYears = years[i];
        for (let j = 0; j < currentYears.length; j += 1) {
          const tmpYear = new Date(
            this.startingYear.getFullYear() + currentYears[j],
            this.datetime.getMonth(),
            this.datetime.getDate(),
            this.datetime.getHours(),
            this.datetime.getMinutes(),
            this.datetime.getSeconds(),
          );
          tempYears.push(tmpYear);
        }
        this.years.push(tempYears);
      }
    },

    formatedYearLabel(year) {
      return year.getFullYear();
    },

    formatedCurrent10YearsLabel() {
      const start = this.years[0][1];
      const end = this.years[3][1];
      return `${start.getFullYear()} - ${end.getFullYear()}`;
    },

    selectPrev10Year() {
      this.startingYear = new Date(
        this.startingYear.getFullYear() - 10,
        this.datetime.getMonth(),
        this.datetime.getDate(),
        this.datetime.getHours(),
        this.datetime.getMinutes(),
        this.datetime.getSeconds(),
      );
      this.initYears();
    },

    selectNext10Year() {
      this.startingYear = new Date(
        this.startingYear.getFullYear() + 10,
        this.datetime.getMonth(),
        this.datetime.getDate(),
        this.datetime.getHours(),
        this.datetime.getMinutes(),
        this.datetime.getSeconds(),
      );
      this.initYears();
    },

    selectYear(year) {
      if (this.disabled) {
        return;
      }
      this.$emit('change', year);
    },
  },
};
</script>
<style lang="scss" scoped>
  .space-year-picker {
    display: flex;
    flex-direction: column;
    width: 300px;
    box-shadow: 1px 2px 6px 0px #bfbfbf;

    .header {
      display: inline-flex;
      justify-content: center;
      border-bottom: 1px solid #ececec;
      .current-10years-label {
        display: inline-flex;
        line-height: 33px;
      }

      .prev-10year-btn,
      .next-10year-btn {
        padding: 0 10px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 20px;
        cursor: pointer;
        &:hover {
          color: #a4d0ff;
        }
      }
      .prev-10year-btn::after {
        content: '\00AB';
      }
      .next-10year-btn::after {
        content: '\00BB';
      }
    }

    .years-grid {
      height: 200px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .years-list {
      display: flex;
      justify-content: space-around;

      .year {
        span {
          padding: 3px 5px;
          border: 1px solid transparent;
        }
        padding: 5px;
        cursor: pointer;
        &:hover {
          color: #61a9f5;
        }

        &.current-year {
          span {
            color: #61a9f5;
          }
        }
      }
    }
  }
</style>
