import { defineStore } from 'pinia';
import axios from 'axios';

/**
 * Dashboard Store (Pinia)
 * Is store me hum Restaurant ke overview metrics, sales chart data,
 * popular dishes aur recent orders handle karte hain.
 */
export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    kpis: {
      today_revenue: 0,
      today_orders: 0,
      active_orders: 0,
      table_occupancy: {
        total: 0,
        occupied: 0,
        rate_percentage: 0
      }
    },
    salesChart: [],
    topDishes: [],
    recentOrders: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchDashboardStats() {
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/dashboard/stats');
        if (response.data.success) {
          const { kpis, sales_chart, top_dishes, recent_orders } = response.data.data;
          this.kpis = kpis;
          this.salesChart = sales_chart;
          this.topDishes = top_dishes;
          this.recentOrders = recent_orders;
        }
      } catch (err) {
        this.error = err.response?.data?.message || 'Dashboard stats fetch karne me error aaya';
        console.error('Fetch dashboard stats error:', err);
      } finally {
        this.loading = false;
      }
    }
  }
});
