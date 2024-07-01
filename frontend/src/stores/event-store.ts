import { defineStore } from 'pinia';

export const useEventsStore = defineStore({
  id: 'events',
  state: () => ({
    selectedEventId: 0 as number,
    selectedEventName: '' as string,
  }),
  actions: {
    selectEventID(eventId: number) {
      this.selectedEventId = eventId;
    },
    selectEventName(eventName: string) {
      this.selectedEventName = eventName;
    },
  },
  getters: {
    getEventSelectedId: (state) => state.selectedEventId,
  },
  persist: true,
});
