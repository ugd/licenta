<template>
  <q-layout view="lHh Lpr lFf">
    <q-page-container>
      <q-page class="flex flex-center bg-grey-2">
        <q-card class="q-pa-md shadow-2 my-card" bordered>
          <form
            @submit.prevent.stop="login"
            class="q-gutter-md"
            autocomplete="on"
          >
            <q-card-section class="text-center">
              <div class="text-grey-9 text-h5 text-weight-bold">Sign in</div>
              <div class="text-grey-8">Complete your login process below</div>
            </q-card-section>
            <q-card-section>
              <q-input
                dense
                outlined
                v-model="email"
                type="email"
                autocomplete="on"
                label="Email Address"
                :rules="[(val) => !!val || 'Email is required']"
              />
              <q-input
                dense
                outlined
                class="q-mt-md"
                v-model="password"
                type="password"
                autocomplete="current-password"
                label="Password"
                :rules="[(val) => !!val || 'Password is required']"
              />
            </q-card-section>
            <q-card-section>
              <q-btn
                style="border-radius: 8px"
                color="dark"
                rounded
                size="md"
                label="Sign in"
                no-caps
                type="submit"
                class="full-width"
              />
            </q-card-section>
          </form>
        </q-card>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script lang="ts">
import { useAuthStore } from 'src/stores/auth-store';
import { ref, defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import { useQuasar } from 'quasar';

export default defineComponent({
  name: 'LoginView',
  setup() {
    const $q = useQuasar();
    const router = useRouter();
    const authStore = useAuthStore();
    const email = ref('');
    const password = ref('');

    const login = async () => {
      await authStore.getSanctumCookie();
      const res = await authStore.login(email.value, password.value);

      if (res.error) {
        $q.notify({
          color: 'negative',
          message: res?.message,
          timeout: 2000,
          position: 'top',
          icon: 'report_problem',
        });
      } else {
        $q.notify({
          color: 'positive',
          message: 'Login successful!',
          timeout: 2000,
          position: 'top-right',
          icon: 'check',
        });
        router.push({ name: 'dashboard' });
      }
    };

    return { email, password, login };
  },
});
</script>

<style lang="scss" scoped>
.my-card {
  width: 25rem;
  border-radius: 8px;
  box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1),
    0 8px 10px -6px rgb(0 0 0 / 0.1);
}
</style>
