<template>
  <div class="space-time-picker">
    <div class="hours">
      <input class="space-input"
        v-model="hours"
        :disabled="disabled"
        @blur="checkHourValue"
        @keypress="sanitizeInput"
        maxlength="2"
        type="text" />
      <div class="arrows">
        <a class="arrow up" @click="increaseHours(1)"></a>
        <a class="arrow down" @click="increaseHours(-1)"></a>
      </div>
    </div>
    <span class="separator">:</span>
    <div class="minutes">
      <input @keypress="sanitizeInput"
        :disabled="disabled"
        @blur="checkMinuteValue"
        class="space-input"
        v-model="minutes"
        maxlength="2"
        type="text" />
      <div class="arrows">
        <a class="arrow up" @click="increaseMinutes(1)"></a>
        <a class="arrow down" @click="increaseMinutes(-1)"></a>
      </div>
    </div>
    <span class="separator">:</span>
    <div class="seconds">
      <input @keypress="sanitizeInput"
        :disabled="disabled"
        @blur="checkSecondValue"
        class="space-input"
        v-model="seconds"
        maxlength="2"
        type="text" />
      <div class="arrows">
        <a class="arrow up" @click="increaseSeconds(1)"></a>
        <a class="arrow down" @click="increaseSeconds(-1)"></a>
      </div>
    </div>
    <div class="hour12" v-if="hour12">
      <a class="am-btn"
        @click="!isAM ? increaseHours(12) : null"
        :class="{ 'selected': isAM }">AM</a>
      <a class="pm-btn"
        @click="isAM ? increaseHours(-12) : null"
        :class="{ 'selected': !isAM }">PM</a>
    </div>
    <div class="confirm" @click="onOkClick" v-if="!disabled">
      <a class="ok-btn">OK</a>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    value: {
      type: Date,
      required: true,
    },
    hour12: {
      type: Boolean,
      required: false,
      default: false,
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },
  },

  data() {
    return {
      datetime: null,
      hours: 0,
      minutes: 0,
      seconds: 0,
    };
  },

  beforeMount() {
    this.datetime = this.value;
  },

  watch: {
    value() {
      this.datetime = this.value;
    },

    datetime() {
      this.init();
    },
  },

  computed: {
    isAM() {
      const hours = this.datetime.getHours();
      return hours <= 12;
    },
  },

  methods: {
    init() {
      const hours = this.datetime.getHours();
      const minutes = this.datetime.getMinutes();
      const seconds = this.datetime.getSeconds();
      this.hours = hours < 10 ? `0${hours}` : hours;
      if (this.hour12) {
        if (hours > 12) {
          const tmp = hours - 12;
          this.hours = tmp < 10 ? `0${tmp}` : tmp;
        } else if (hours === 0) {
          this.hours = 12;
        }
      }
      this.minutes = minutes < 10 ? `0${minutes}` : minutes;
      this.seconds = seconds < 10 ? `0${seconds}` : seconds;
    },

    increaseHours(value) {
      if (this.disabled) {
        return;
      }
      const datetime = new Date(this.datetime);
      const hours = datetime.getHours() + value;

      datetime.setHours(hours);
      this.datetime = datetime;
    },

    increaseMinutes(value) {
      if (this.disabled) {
        return;
      }
      const datetime = new Date(this.datetime);
      const minutes = datetime.getMinutes() + value;

      datetime.setMinutes(minutes);
      this.datetime = datetime;
    },

    increaseSeconds(value) {
      if (this.disabled) {
        return;
      }
      const datetime = new Date(this.datetime);
      const seconds = datetime.getSeconds() + value;

      datetime.setSeconds(seconds);
      this.datetime = datetime;
    },

    sanitizeInput(e) {
      const charCode = (e.which) ? e.which : e.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        e.preventDefault();
        e.stopPropagation();
      }
    },

    checkHourValue() {
      let { hours } = this;
      if (hours > 24) {
        hours = 0;
      }
      const tmp = new Date(this.datetime);
      this.datetime = new Date(tmp.setHours(hours));
    },

    checkMinuteValue() {
      let { minutes } = this;
      if (minutes > 60) {
        minutes = 0;
      }
      const tmp = new Date(this.datetime);
      this.datetime = new Date(tmp.setMinutes(minutes));
    },

    checkSecondValue() {
      let { seconds } = this;
      if (seconds > 60) {
        seconds = 0;
      }
      const tmp = new Date(this.datetime);
      this.datetime = new Date(tmp.setSeconds(seconds));
    },

    onOkClick() {
      if (this.disabled) {
        return;
      }
      this.$emit('ok', this.datetime);
    },
  },
};
</script>
<style lang="scss" scoped>
  .space-time-picker {
    display: inline-flex;
    border-top: 1px solid #ececec;
    width: 100%;
    padding: 5px;
    width: calc(100% - 10px);
    position: relative;
    .hours,
    .minutes,
    .seconds {
      width: 50px;
      display: inline-flex;

      input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
        padding: 5px;
        height: 24px;
      }
    }

    .separator {
      line-height: 24px;
      padding: 0px 5px;
    }

    .hour12 {
      line-height: 24px;
      font-size: small;
      cursor: pointer;
      .am-btn,
      .pm-btn {
        padding: 5px;
        &.selected {
          color: #a4d0ff;
        }
      }
    }

    .confirm {
      border: 1px solid #ececec;
      border-radius: 4px;
      height: 24px;
      width: 30px;
      font-size: xx-small;
      line-height: 24px;
      padding: 0px 4px;
      position: absolute;
      right: 5px;
      cursor: pointer;
      &:hover {
        background: #a4d0ff;
      }
    }

    .arrows {
      width: 15px;
      line-height: 8px;
      padding: 0px 5px;
      background: #ececec;
      height: 24px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
      .arrow {
        color: rgba(0, 0, 0, 0.45);
        font-size: 20px;
        cursor: pointer;
        &:hover {
          color: #a4d0ff;
        }
        &.up::after {
          content: '\2039';
          transform: rotate(90deg);
          display: inline-block;
        }
        &.down::after {
          content: '\203A';
          transform: rotate(90deg);
          display: inline-block;
        }
      }
    }
  }
</style>
