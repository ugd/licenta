<template>
  <div class="space-datetime-picker">
    <div class="custom-display" ref="customDisplay">
      <slot></slot>
    </div>
    <div class="default-input">
      <input type="text"
        v-if="!hasDefaultSlot"
        v-model="label"
        :placeholder="placeholder"
        @click="openPopover"
        @blur="onBlur"
        :disabled="disabled"
        ref="spaceDatetimePickerInput"
        class="sapce-datetime-picker-input space-input" />
      <span class="clear-btn" v-if="showClearBtn && !disabled" @click="onClearBtnClick">
        &times;
      </span>
    </div>

    <div class="space-datetime-popover"
      ref="spaceDatetimePopover"
      :style="popoverStyle"
      v-show="displayPopover">
      <SpaceDatePicker v-if="mode === modeEnum.DayPick"
        v-model="datetime"
        :showTime="showTime"
        :showHome="showHome"
        :weekday="weekday"
        :locale="locale"
        :disabled="disabled"
        :startingDay="startingDay"
        @modeChange="onModeChange"
        @select="onSelect"
        @change="onChange"></SpaceDatePicker>
      <SpaceMonthPicker
        v-else-if="mode === modeEnum.MonthPick"
        v-model="datetime"
        :locale="locale"
        :disabled="disabled"
        :month="month"
        @change="(datetime) => onChange(datetime, modeEnum.DayPick)">
      </SpaceMonthPicker>
      <SpaceYearPicker v-else-if="mode === modeEnum.YearPick"
        :disabled="disabled"
        @change="(datetime) => onChange(datetime, modeEnum.DayPick)"
        v-model="datetime"></SpaceYearPicker>
    </div>
  </div>
</template>
<script>

import ModeEnum from './ModeEnum';

import {
  reverseFuncs,
  parseFuncs,
  isValidDate,
} from './helper';

import SpaceDatePicker from './SpaceDatePicker.vue';
import SpaceMonthPicker from './SpaceMonthPicker.vue';
import SpaceYearPicker from './SpaceYearPicker.vue';

export default {
  components: {
    SpaceDatePicker,
    SpaceMonthPicker,
    SpaceYearPicker,
  },

  data() {
    return {
      datetime: null,
      mode: ModeEnum.DayPick,
      modeEnum: ModeEnum,
      label: null,
      displayPopover: false,
      popoverStyle: null,
      showClearBtn: false,
    };
  },

  props: {
    value: {
      type: Date,
      required: false,
      default: null,
    },

    placeholder: {
      type: String,
      required: false,
      default: null,
    },

    format: {
      type: String,
      required: false,
      default: 'YYYY-MM-DD',
    },

    locale: {
      type: String,
      required: false,
      default: undefined,
    },

    showTime: {
      type: Boolean,
      required: false,
      default: false,
    },

    hour12: {
      type: Boolean,
      required: false,
      default: false,
    },

    weekday: {
      type: String,
      required: false,
      default: 'narrow', // long | short | narrow
    },

    year: {
      type: String,
      required: false,
      default: 'numeric', // numeric (ex: 2012) | 2-digit (ex: 12)
    },

    month: {
      type: String,
      required: false,
      default: 'short', // numeric | 2-digit | long | short | narrow
    },

    day: {
      type: String,
      required: false,
      default: '2-digit', // numeric (ex: 1) | 2-digit (ex: 01)
    },

    hour: {
      type: String,
      required: false,
      default: '2-digit', // numeric | 2-digit
    },

    minute: {
      type: String,
      required: false,
      default: '2-digit', // numeric | 2-digit
    },

    second: {
      type: String,
      required: false,
      default: '2-digit', // numeric | 2-digit
    },

    showHome: {
      type: Boolean,
      required: false,
      default: true,
    },

    disabled: {
      type: Boolean,
      required: false,
      default: false,
    },

    // First date in week days, for exemple Monday is the default first day of week,
    // but can be Sunday for some countries
    startingDay: {
      type: Number,
      required: false,
      default: 1,
      validator: (value) => value >= 0 && value < 7,
    },
  },

  beforeMount() {
    if (this.value && isValidDate(this.value)) {
      this.datetime = this.value;
    }
  },

  watch: {
    datetime(value) {
      if (value) {
        let tmp = this.format;
        for (let i = 0; i < parseFuncs.length; i += 1) {
          const parseFunc = parseFuncs[i];
          if (this.format.includes(parseFunc.key)) {
            tmp = parseFunc.handler(this.datetime, tmp);
          }
        }
        this.label = tmp;
        if (this.label) {
          this.showClearBtn = true;
        } else {
          this.showClearBtn = false;
        }
      } else {
        this.showClearBtn = false;
        this.label = null;
      }
    },
  },

  computed: {
    hasDefaultSlot() {
      return this.$slots.default;
    },
  },

  methods: {
    onChange(datetime, mode = this.mode) {
      this.datetime = datetime;
      this.mode = mode;
    },

    onSelect(datetime) {
      this.datetime = datetime;
      this.displayPopover = false;
      if (!this.disabled) {
        this.$emit('input', this.datetime);
      }
    },

    onModeChange(mode) {
      this.mode = mode;
    },

    onClearBtnClick() {
      this.displayPopover = false;
      this.label = null;
      this.datetime = null;
      this.$emit('input', null);
    },

    onBlur() {
      let datetime = new Date();
      if (!this.label) {
        this.$emit('input', null);
        return;
      }
      for (let i = 0; i < reverseFuncs.length; i += 1) {
        const parseFunc = reverseFuncs[i];
        if (this.format.includes(parseFunc.key)) {
          const tmp = parseFunc.handler(this.label, this.format, datetime);
          datetime = new Date(tmp);
        }
      }
      if (isValidDate(datetime) && !this.disabled) {
        this.datetime = datetime;
        this.$emit('input', this.datetime);
      }
    },

    onClickHandler(e) {
      if (!this.$refs.spaceDatetimePopover) {
        return;
      }
      const viewportOffset = this.$refs.spaceDatetimePopover.getBoundingClientRect();
      const zoneX = viewportOffset.x + viewportOffset.width;
      const zoneY = viewportOffset.y + viewportOffset.height;
      if (e.clientX < viewportOffset.x
          || e.clientX > zoneX
          || e.clientY < viewportOffset.y
          || e.clientY > zoneY) {
        this.displayPopover = false;
        document.removeEventListener('click', this.onClickHandler);
        if (isValidDate(this.datetime) && !this.disabled) {
          this.$emit('input', this.datetime);
        }
        this.mode = ModeEnum.DayPick;
      }
    },

    openPopover(e) {
      this.displayPopover = true;
      const el = this.$slots.default
        ? this.$refs.customDisplay : this.$refs.spaceDatetimePickerInput;
      if (!el) {
        return;
      }
      const viewportOffset = el.getBoundingClientRect();
      const popoverPositionLeft = viewportOffset.left;
      const popoverPositionTop = viewportOffset.top + viewportOffset.height;
      this.popoverStyle = {
        position: 'absolute',
        left: `${popoverPositionLeft}px`,
        top: `${popoverPositionTop}px`,
        'z-index': 100,
      };
      e.stopPropagation();
      e.preventDefault();
      document.addEventListener('click', this.onClickHandler);
    },
  },
};
</script>
<style lang="scss" scoped>
  .default-input {
    max-width: 300px;
    display: flex;
    position: relative;
    outline: 0;

    .clear-btn {
      position: absolute;
      cursor: pointer;
      padding: 3px;
      background: #ececec;
      border-radius: 25px;
      width: 16px;
      height: 16px;
      line-height: 16px;
      top: 5px;
      right: 5px;
    }
  }
  input.space-input {
    outline: 0;
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    list-style: none;
    position: relative;
    display: inline-block;
    width: 100%;
    max-width: 300px;
    height: 32px;
    padding: 4px 11px;
    color: rgba(0, 0, 0, 0.65);
    font-size: 14px;
    line-height: 1.5;
    background-color: #fff;
    border: 1px solid #d9d9d9;
    border-radius: 4px;
  }
</style>
