import { boot } from 'quasar/wrappers';
import Vue3NativeNotification from 'vue3-native-notification';

// "async" is optional;
// more info on params: https://v2.quasar.dev/quasar-cli/boot-files
export default boot(async ({ app }) => {
  // something to do
  app.use(Vue3NativeNotification, {
    // Automatic permission request before
    // showing notification (default: true)
    requestOnNotify: true
  });
});
