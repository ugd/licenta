import { boot } from 'quasar/wrappers';
import { createPlausible } from 'v-plausible/vue';

const plausible = createPlausible({
  init: {
    domain: import.meta.env.VITE_PLAUSIBLE_DOMAIN,
    apiHost: import.meta.env.VITE_PLAUSIBLE_API,
    trackLocalhost: true,
  },
  settings: {
    enableAutoOutboundTracking: true,
    enableAutoPageviews: true,
  },
  partytown: false,
});

// more info on params: https://v2.quasar.dev/quasar-cli/boot-files
export default boot(async ({ app }) => {
  // app.config.globalProperties.$analytics = analytics;
  app.use(plausible);
});
