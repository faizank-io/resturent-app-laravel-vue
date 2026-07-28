import { defineStore } from 'pinia';
import axios from 'axios';

/**
 * Menu Store (Pinia)
 * Food Dishes, Categories, CRUD operations aur stock availability toggles.
 */
export const useMenuStore = defineStore('menu', {
  state: () => ({
    categories: [],
    menuItems: [],
    selectedCategoryId: null,
    searchQuery: '',
    loading: false,
    error: null
  }),

  getters: {
    filteredMenuItems(state) {
      return state.menuItems.filter(item => {
        const matchesCategory = !state.selectedCategoryId || item.category_id === state.selectedCategoryId;
        const matchesSearch = !state.searchQuery || item.name.toLowerCase().includes(state.searchQuery.toLowerCase()) ||
          (item.description && item.description.toLowerCase().includes(state.searchQuery.toLowerCase()));
        return matchesCategory && matchesSearch;
      });
    }
  },

  actions: {
    async fetchCategories() {
      try {
        const res = await axios.get('/api/categories');
        if (res.data.success) {
          this.categories = res.data.data;
        }
      } catch (err) {
        console.error('Fetch categories error:', err);
      }
    },

    async fetchMenuItems() {
      this.loading = true;
      try {
        const res = await axios.get('/api/menu-items');
        if (res.data.success) {
          this.menuItems = res.data.data;
        }
      } catch (err) {
        this.error = 'Menu items load nahi ho sakay';
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    async addMenuItem(dishData) {
      try {
        const res = await axios.post('/api/menu-items', dishData);
        if (res.data.success) {
          this.menuItems.unshift(res.data.data);
          await this.fetchCategories();
          return res.data;
        }
      } catch (err) {
        throw err;
      }
    },

    async updateMenuItem(id, dishData) {
      try {
        const res = await axios.put(`/api/menu-items/${id}`, dishData);
        if (res.data.success) {
          const index = this.menuItems.findIndex(i => i.id === id);
          if (index !== -1) {
            this.menuItems[index] = res.data.data;
          }
          return res.data;
        }
      } catch (err) {
        throw err;
      }
    },

    async toggleStock(id) {
      try {
        const res = await axios.patch(`/api/menu-items/${id}/toggle-availability`);
        if (res.data.success) {
          const item = this.menuItems.find(i => i.id === id);
          if (item) {
            item.is_available = !item.is_available;
          }
        }
      } catch (err) {
        console.error('Toggle stock error:', err);
      }
    },

    async deleteMenuItem(id) {
      try {
        const res = await axios.delete(`/api/menu-items/${id}`);
        if (res.data.success) {
          this.menuItems = this.menuItems.filter(i => i.id !== id);
          await this.fetchCategories();
        }
      } catch (err) {
        console.error('Delete menu item error:', err);
      }
    }
  }
});
