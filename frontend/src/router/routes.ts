import { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    redirect: '/dashboard',
    children: [
      {
        path: '/login',
        name: 'login',
        component: () => import('pages/Auth/LoginView.vue'),
      },
      {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('src/pages/Admin/DashboardView.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: '/logout',
        name: 'logout',
        component: () => import('pages/Auth/LogoutView.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: '/statistics',
        name: 'statistics',
        component: () => import('pages/Admin/StatisticsView.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: '/users',
        name: 'users',
        component: () => import('pages/Admin/Users/UsersView.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: '/permissions',
        name: 'permissions',
        component: () => import('pages/Admin/Events/EventPermissionsView.vue'),
        meta: {
          requiresAuth: true,
        },
      },
      {
        path: '/events',
        name: 'events',
        redirect: '/events/dashboard',
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: '/events/dashboard',
            component: () =>
              import('pages/Admin/Events/EventsDashboardView.vue'),
            name: 'events-dashboard',
            meta: {
              requiresAuth: true,
            },
          },
          {
            path: '/events/permissions',
            component: () =>
              import('pages/Admin/Events/EventPermissionsView.vue'),
            name: 'events-permissions',
            meta: {
              requiresAuth: true,
            },
          },
          {
            path: '/events/tickets',
            component: () =>
              import('pages/Admin/Events/Tickets/TicketsDashboardView.vue'),
            name: 'events-tickets',
            meta: {
              requiresAuth: true,
            },
          },
          {
            path: '/events/invitations',
            component: () =>
              import('pages/Admin/Events/Tickets/InvitationsDashboardView.vue'),
            name: 'events-invitations',
            meta: {
              requiresAuth: true,
            },
          },
          {
            path: '/events/add-event/',
            component: () => import('pages/Admin/Events/EventEditView.vue'),
            name: 'add-event',
            meta: {
              requiresAuth: true,
            },
          },
          {
            path: '/events/edit-event/:id',
            component: () => import('pages/Admin/Events/EventEditView.vue'),
            name: 'edit-event',
            meta: {
              requiresAuth: true,
            },
          },
        ],
      },
    ],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
