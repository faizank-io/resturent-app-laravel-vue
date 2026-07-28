import { defineStore } from 'pinia';
import axios from 'axios';

/**
 * Order Store (Pinia)
 * Kitchen Kanban order board, live order status updates,
 * aur naye order ko cart se create karna handle karta hai.
 */
export const useOrderStore = defineStore('order', {
  state: () => ({
    orders: [],
    loading: false,
    filterStatus: 'all',
    cart: [],
    orderNotes: '',
    selectedTableId: null,
    orderType: 'dine_in',
    customerName: ''
  }),

  getters: {
    filteredOrders(state) {
      if (state.filterStatus === 'all') return state.orders;
      return state.orders.filter(o => o.status === state.filterStatus);
    },

    cartTotal(state) {
      return state.cart.reduce((sum, item) => sum + (item.menuItem.price * item.quantity), 0);
    },

    deliveryFee(state) {
      return state.orderType === 'delivery' ? 4.50 : 0.00;
    },

    taxAmount(state) {
      const subtotal = state.cart.reduce((sum, item) => sum + (item.menuItem.price * item.quantity), 0);
      return Math.round(subtotal * 0.05 * 100) / 100;
    },

    grandTotal(state) {
      const subtotal = state.cart.reduce((sum, item) => sum + (item.menuItem.price * item.quantity), 0);
      const delivery = state.orderType === 'delivery' ? 4.50 : 0.00;
      const tax = Math.round(subtotal * 0.05 * 100) / 100;
      return subtotal + delivery + tax;
    },

    cartCount(state) {
      return state.cart.reduce((sum, item) => sum + item.quantity, 0);
    }
  },

  actions: {
    async fetchOrders() {
      this.loading = true;
      try {
        const res = await axios.get('/api/orders');
        if (res.data.success) {
          this.orders = res.data.data;
        }
      } catch (err) {
        console.error('Fetch orders error:', err);
      } finally {
        this.loading = false;
      }
    },

    async updateOrderStatus(orderId, status) {
      try {
        const res = await axios.patch(`/api/orders/${orderId}/status`, { status });
        if (res.data.success) {
          const order = this.orders.find(o => o.id === orderId);
          if (order) {
            order.status = status;
          }
          return res.data;
        }
      } catch (err) {
        console.error('Update status error:', err);
      }
    },

    addToCart(menuItem) {
      const existing = this.cart.find(i => i.menuItem.id === menuItem.id);
      if (existing) {
        existing.quantity += 1;
      } else {
        this.cart.push({ menuItem, quantity: 1 });
      }
    },

    removeFromCart(menuItemId) {
      this.cart = this.cart.filter(i => i.menuItem.id !== menuItemId);
    },

    updateCartQuantity(menuItemId, quantity) {
      if (quantity <= 0) {
        this.removeFromCart(menuItemId);
        return;
      }
      const item = this.cart.find(i => i.menuItem.id === menuItemId);
      if (item) {
        item.quantity = quantity;
      }
    },

    clearCart() {
      this.cart = [];
      this.orderNotes = '';
      this.selectedTableId = null;
      this.customerName = '';
    },

    async submitOrder() {
      if (this.cart.length === 0) return;

      const payload = {
        customer_name: this.customerName || 'Walk-in Customer',
        table_id: this.orderType === 'dine_in' ? this.selectedTableId : null,
        order_type: this.orderType,
        notes: this.orderNotes,
        items: this.cart.map(item => ({
          menu_item_id: item.menuItem.id,
          quantity: item.quantity
        }))
      };

      try {
        const res = await axios.post('/api/orders', payload);
        if (res.data.success) {
          this.orders.unshift(res.data.data);
          this.clearCart();
          return res.data;
        }
      } catch (err) {
        throw err;
      }
    }
  }
});
