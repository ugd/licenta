import { defineStore } from 'pinia';
import { api, server } from 'src/boot/axios';

export const useAuthStore = defineStore('auth-store', {
  state: () => ({
    id: null,
    name: null,
    email: null,
    permisiune_id: null,
    token: null,
  }),
  getters: {
    getId: (state) => state.id,
    getName: (state) => state.name,
    getPermisiuneId: (state) => state.permisiune_id,
    getEmail: (state) => state.email,
    getToken: (state) => state.token,
  },
  actions: {
    async getSanctumCookie() {
      try {
        await server.get('sanctum/csrf-cookie');
      } catch (e) {}
    },
    async login(email: string, password: string) {
      try {
        const user = await api.post('/auth/login', {
          email: email,
          password: password,
        });
        this.setUser(user.data);
        if (user.status === 200) {
          return { error: false, message: 'Autentificare cu succes!' };
        }
      } catch (error: any) {
        console.error(error);
        if (error.response) {
          const message =
            error.response.data.message || 'Eroare de autentificare';
          return { error: true, message: message };
        } else {
          return {
            error: true,
            message: 'A apărut o eroare. Vă rugăm să încercați din nou.',
          };
        }
      }
    },

    async isAuthenticated() {
      try {
        if (!this.token) {
          return false;
        }
        const response = await api.post('/auth/check-token', null, {
          headers: { Authorization: `Bearer ${this.token}` },
        });

        return response.data.message === 'Token valid';
      } catch (error) {
        console.error(error);
        return false;
      }
    },

    async isAdmin() {
      if (this.permisiune_id === 1) {
        return true;
      } else {
        return false;
      }
    },

    async fetchUser() {
      try {
        return await api.get('/user', {
          headers: { Authorization: `Bearer ${this.token}` },
        });
      } catch (error) {}
    },
    async clearState() {
      this.clearUser();
    },
    setUser(payload: any) {
      if (payload.user.id) this.id = payload.user.id;
      if (payload.user.name) this.name = payload.user.name;
      if (payload.user.permisiune_id)
        this.permisiune_id = payload.user.permisiune_id;
      if (payload.user.email) this.email = payload.user.email;
      if (payload.token) this.token = payload.token;
    },

    clearUser() {
      this.$state.id = null;
      this.$state.name = null;
      this.$state.permisiune_id = null;
      this.$state.email = null;
      this.$state.token = null;
    },
  },
  persist: true,
});
