<template>
  <div style="display: flex; justify-content: center; align-items: center">
    Loging out... <q-spinner color="primary" size="3em" :thickness="10" />
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted } from 'vue';
import { api } from 'src/boot/axios';
import { useAuthStore } from 'src/stores/auth-store';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';
export default defineComponent({
  name: 'LogoutView',
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    const $q = useQuasar();

    const logoutAndRedirect = async () => {
      try {
        const response = await api.post(
          '/auth/logout-me',
          {},
          {
            headers: { Authorization: `Bearer ${authStore.getToken}` },
          }
        );
        if (response.status === 200) {
          authStore.clearState();
          $q.notify({
            type: 'positive',
            message: 'Logout successful',
            icon: 'done',
            position: 'top-right',
            timeout: 2000,
          });
          router.push('/');
        } else {
          console.error('Logout failed:', response.status, response.data);
          $q.notify({
            type: 'negative',
            message: 'Logout failed',
            icon: 'report_problem',
            position: 'top-right',
            timeout: 2000,
          });
        }
      } catch (error) {
        console.error('Logout error:', error);
        $q.notify({
          type: 'negative',
          icon: 'report_problem',
          message: 'Logout error',
          position: 'top-right',
          timeout: 2000,
        });
      }
    };

    onMounted(() => {
      logoutAndRedirect();
    });

    return {};
  },
});
</script>

<style lang="scss" scoped></style>
