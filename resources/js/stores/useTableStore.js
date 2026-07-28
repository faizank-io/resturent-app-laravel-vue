import { defineStore } from 'pinia';
import axios from 'axios';

/**
 * Table Store (Pinia)
 * Restaurant floor seating tables and occupancy management store.
 */
export const useTableStore = defineStore('table', {
  state: () => ({
    tables: [],
    loading: false,
    error: null
  }),

  getters: {
    availableTables(state) {
      return state.tables.filter(t => t.status === 'available');
    },
    occupiedTables(state) {
      return state.tables.filter(t => t.status === 'occupied');
    },
    reservedTables(state) {
      return state.tables.filter(t => t.status === 'reserved');
    }
  },

  actions: {
    async fetchTables() {
      this.loading = true;
      try {
        const res = await axios.get('/api/tables');
        if (res.data.success) {
          this.tables = res.data.data;
        }
      } catch (err) {
        console.error('Fetch tables error:', err);
      } finally {
        this.loading = false;
      }
    },

    async updateTableStatus(tableId, status) {
      try {
        const res = await axios.patch(`/api/tables/${tableId}/status`, { status });
        if (res.data.success) {
          const tbl = this.tables.find(t => t.id === tableId);
          if (tbl) {
            tbl.status = status;
          }
          return res.data;
        }
      } catch (err) {
        console.error('Update table status error:', err);
      }
    },

    async createTable(tableData) {
      try {
        const res = await axios.post('/api/tables', tableData);
        if (res.data.success) {
          this.tables.push(res.data.data);
          return res.data;
        }
      } catch (err) {
        throw err;
      }
    }
  }
});
