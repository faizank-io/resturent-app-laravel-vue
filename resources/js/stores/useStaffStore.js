import { defineStore } from 'pinia';
import axios from 'axios';

/**
 * Staff Store (Pinia)
 * Team management, shifts, roles, and status tracking store.
 */
export const useStaffStore = defineStore('staff', {
  state: () => ({
    staffs: [],
    loading: false,
    error: null,
    searchQuery: '',
    selectedRole: 'All'
  }),

  getters: {
    onShiftStaff(state) {
      return state.staffs.filter(s => s.status === 'on_shift');
    },
    activeStaff(state) {
      return state.staffs.filter(s => s.status === 'active');
    },
    offDutyStaff(state) {
      return state.staffs.filter(s => s.status === 'off_duty');
    },
    filteredStaffs(state) {
      return state.staffs.filter(s => {
        const matchesRole = state.selectedRole === 'All' || s.role === state.selectedRole;
        const matchesSearch = !state.searchQuery || 
          s.name.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
          s.email.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
          s.role.toLowerCase().includes(state.searchQuery.toLowerCase());
        return matchesRole && matchesSearch;
      });
    }
  },

  actions: {
    async fetchStaffs() {
      this.loading = true;
      try {
        const res = await axios.get('/api/staffs');
        if (res.data.success) {
          this.staffs = res.data.data;
        }
      } catch (err) {
        console.error('Fetch staffs error:', err);
        this.error = err.response?.data?.message || 'Failed to load staff list';
      } finally {
        this.loading = false;
      }
    },

    async updateStaffStatus(staffId, status) {
      try {
        const res = await axios.patch(`/api/staffs/${staffId}/status`, { status });
        if (res.data.success) {
          const stf = this.staffs.find(s => s.id === staffId);
          if (stf) {
            stf.status = status;
          }
          return res.data;
        }
      } catch (err) {
        console.error('Update staff status error:', err);
        throw err;
      }
    },

    async createStaff(staffData) {
      try {
        const res = await axios.post('/api/staffs', staffData);
        if (res.data.success) {
          this.staffs.unshift(res.data.data);
          return res.data;
        }
      } catch (err) {
        console.error('Create staff error:', err);
        throw err;
      }
    },

    async deleteStaff(staffId) {
      try {
        const res = await axios.delete(`/api/staffs/${staffId}`);
        if (res.data.success) {
          this.staffs = this.staffs.filter(s => s.id !== staffId);
          return res.data;
        }
      } catch (err) {
        console.error('Delete staff error:', err);
        throw err;
      }
    }
  }
});
