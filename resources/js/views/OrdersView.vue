<script setup>
import { onMounted } from 'vue';
import { useOrderStore } from '../stores/useOrderStore';
import { storeToRefs } from 'pinia';
import { ShoppingBag, CheckCircle, ChefHat } from 'lucide-vue-next';

const orderStore = useOrderStore();
const { filterStatus, loading } = storeToRefs(orderStore);

onMounted(() => {
  orderStore.fetchOrders();
});

const handleStatusChange = async (orderId, newStatus) => {
  await orderStore.updateOrderStatus(orderId, newStatus);
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'pending': return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
    case 'preparing': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
    case 'ready': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
    case 'completed': return 'bg-slate-800 text-slate-400 border-slate-700';
    case 'cancelled': return 'bg-rose-500/10 text-rose-400 border-rose-500/20';
    default: return 'bg-slate-800 text-slate-400';
  }
};
</script>

<template>
  <div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 rounded-2xl glass-panel">
      <div>
        <h3 class="text-base font-bold font-display text-white">Kitchen & Delivery Orders</h3>
        <p class="text-xs text-slate-400">Track and update active kitchen tickets</p>
      </div>

      <div class="flex items-center gap-1 bg-slate-900 p-1 rounded-xl border border-slate-800 overflow-x-auto">
        <button 
          v-for="status in ['all', 'pending', 'preparing', 'ready', 'completed', 'cancelled']"
          :key="status"
          @click="filterStatus = status"
          :class="[
            'px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all',
            filterStatus === status 
              ? 'bg-amber-500 text-slate-950 font-bold' 
              : 'text-slate-400 hover:text-white'
          ]"
        >
          {{ status }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400 text-sm">
      Loading order board...
    </div>

    <div v-else-if="orderStore.filteredOrders.length === 0" class="p-12 text-center glass-panel rounded-2xl">
      <p class="text-slate-400 text-sm font-medium">No orders found in status '{{ filterStatus }}'</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="order in orderStore.filteredOrders" 
        :key="order.id"
        class="rounded-2xl glass-panel p-5 flex flex-col justify-between space-y-4 hover:border-slate-700 transition-all"
      >
        <div class="flex items-start justify-between pb-3 border-b border-slate-800">
          <div>
            <div class="flex items-center gap-2">
              <span class="font-mono font-bold text-sm text-white">{{ order.order_number }}</span>
              <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border', getStatusBadgeClass(order.status)]">
                {{ order.status }}
              </span>
            </div>
            <p class="text-xs text-slate-400 mt-1 font-semibold">
              {{ order.customer_name }}
              <span v-if="order.table" class="text-amber-400"> ({{ order.table.table_number }})</span>
            </p>
          </div>

          <span class="px-2 py-1 rounded bg-slate-900 border border-slate-800 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
            {{ order.order_type.replace('_', ' ') }}
          </span>
        </div>

        <div class="space-y-2 py-1 flex-1">
          <div 
            v-for="item in order.items" 
            :key="item.id"
            class="flex items-center justify-between text-xs text-slate-300"
          >
            <div class="flex items-center gap-2">
              <span class="w-5 h-5 rounded bg-slate-800 font-bold text-amber-400 text-[11px] flex items-center justify-center">
                {{ item.quantity }}x
              </span>
              <span class="font-medium text-white">{{ item.menu_item ? item.menu_item.name : 'Dish Item' }}</span>
            </div>
            <span class="font-mono text-slate-400">${{ Number(item.subtotal || 0).toFixed(2) }}</span>
          </div>

          <p v-if="order.notes" class="text-[11px] text-amber-400/80 italic pt-2 border-t border-slate-800/40">
            Note: {{ order.notes }}
          </p>
        </div>

        <div class="pt-3 border-t border-slate-800 space-y-3">
          <div class="flex items-center justify-between text-xs font-bold">
            <span class="text-slate-400">Total Price:</span>
            <span class="text-base text-amber-400 font-display">${{ Number(order.total_amount || 0).toFixed(2) }}</span>
          </div>

          <div class="flex items-center gap-2">
            <button 
              v-if="order.status === 'pending'"
              @click="handleStatusChange(order.id, 'preparing')"
              class="w-full py-2 rounded-xl bg-blue-500/20 text-blue-400 hover:bg-blue-500 hover:text-slate-950 font-bold text-xs border border-blue-500/30 transition-all flex items-center justify-center gap-1.5"
            >
              <ChefHat class="w-3.5 h-3.5" />
              <span>Start Preparing</span>
            </button>

            <button 
              v-if="order.status === 'preparing'"
              @click="handleStatusChange(order.id, 'ready')"
              class="w-full py-2 rounded-xl bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-slate-950 font-bold text-xs border border-emerald-500/30 transition-all flex items-center justify-center gap-1.5"
            >
              <CheckCircle class="w-3.5 h-3.5" />
              <span>Mark Ready</span>
            </button>

            <button 
              v-if="order.status === 'ready'"
              @click="handleStatusChange(order.id, 'completed')"
              class="w-full py-2 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs hover:bg-amber-400 transition-all flex items-center justify-center gap-1.5"
            >
              <ShoppingBag class="w-3.5 h-3.5" />
              <span>Complete & Serve</span>
            </button>

            <button 
              v-if="['pending', 'preparing'].includes(order.status)"
              @click="handleStatusChange(order.id, 'cancelled')"
              class="py-2 px-3 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white font-semibold text-xs border border-rose-500/20 transition-all"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
