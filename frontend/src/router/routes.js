const routes = [
  {
    path: "/",
    name: "public.home",
    component: () => import("@pages/public/AnnouncementPage.vue"),
  },
  {
    path: "/login",
    name: "public.login",
    component: () => import("@pages/public/LoginPage.vue"),
  },
  {
    path: "/manage",
    component: () => import("@layouts/PrivateLayout.vue"),
    children: [
      {
        path: "/manage/announcements",
        name: "manage.announcements",
        component: () => import("@pages/private/announcements/AnnouncementPage.vue"),
        meta: { middleware: "auth" },
      },
      {
        path: "/manage/users",
        name: "manage.users",
        component: () => import("@pages/private/users/UsersPage.vue"),
        meta: { middleware: "auth" },
      },
    ]
  },
  {
    path: "/unauthorized",
    name: "401",
    component: () => import("@pages/Unauthorized.vue"),
  },
  {
    path: "/:catchAll(.*)*",
    name: "404",
    component: () => import("@pages/NotFound.vue"),
  },
];

export default routes;
