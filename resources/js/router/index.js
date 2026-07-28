import { createRouter, createWebHistory } from "vue-router";
import DashboardView from "../views/DashboardView.vue";
import MenuView from "../views/MenuView.vue";
import OrdersView from "../views/OrdersView.vue";
import TablesView from "../views/TablesView.vue";
import StaffsView from "../views/StaffsView.vue";

/**
 * Vue Router configuration for SPA Client Navigation
 */
const routes = [
    {
        path: "/",
        name: "dashboard",
        component: DashboardView,
        meta: { title: "Dashboard Overview" },
    },
    {
        path: "/menu",
        name: "menu",
        component: MenuView,
        meta: { title: "Menu Catalog" },
    },
    {
        path: "/orders",
        name: "orders",
        component: OrdersView,
        meta: { title: "Kitchen & Delivery Orders" },
    },
    {
        path: "/tables",
        name: "tables",
        component: TablesView,
        meta: { title: "Table Occupancy" },
    },
    {
        path: "/staffs",
        name: "staffs",
        component: StaffsView,
        meta: { title: "Staff Management" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    linkActiveClass:
        "bg-amber-500/10 text-amber-400 border-l-2 border-amber-500 font-semibold",
});

export default router;
