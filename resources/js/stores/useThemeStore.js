import { defineStore } from 'pinia';

export const useThemeStore = defineStore('theme', {
  state: () => ({
    isSidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true'
  }),

  actions: {
    toggleSidebar() {
      this.isSidebarCollapsed = !this.isSidebarCollapsed;
      localStorage.setItem('sidebar_collapsed', this.isSidebarCollapsed);
    }
  }
});
