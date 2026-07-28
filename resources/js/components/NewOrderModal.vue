<script setup>
import { onMounted, ref } from 'vue';
import { useOrderStore } from '../stores/useOrderStore';
import { useMenuStore } from '../stores/useMenuStore';
import { useTableStore } from '../stores/useTableStore';
import { storeToRefs } from 'pinia';
import { X, Plus, Minus, ShoppingCart, Check } from 'lucide-vue-next';

defineProps({
  isOpen: { type: Boolean, required: true }
});

const emit = defineEmits(['close']);

const orderStore = useOrderStore();
const menuStore = useMenuStore();
const tableStore = useTableStore();

const { cart, cartTotal, deliveryFee, taxAmount, grandTotal, cartCount, customerName, orderType, selectedTableId, orderNotes } = storeToRefs(orderStore);
const { menuItems } = storeToRefs(menuStore);
const { availableTables } = storeToRefs(tableStore);

const isSubmitting = ref(false);
const successMsg = ref('');

onMounted(() => {
  if (menuItems.value.length === 0) menuStore.fetchMenuItems();
  if (tableStore.tables.length === 0) tableStore.fetchTables();
});

const handleOrderSubmit = async () => {
  if (cart.value.length === 0) return;
  
  isSubmitting.value = true;
  try {
    await orderStore.submitOrder();
    successMsg.value = 'Order created successfully!';
    setTimeout(() => {
      successMsg.value = '';
      emit('close');
    }, 1500);
  } catch (err) {
    console.error('Order submit error:', err);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex justify-end">
      <div @click="emit('close')" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

      <div class="relative w-full max-w-2xl bg-slate-900 border-l border-slate-800 h-full flex flex-col z-10 text-white shadow-2xl">
        <div class="h-20 px-6 border-b border-slate-800 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center justify-center">
              <ShoppingCart class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-lg font-bold font-display">Create New Order</h3>
              <p class="text-xs text-slate-400">Select dishes & table to send to kitchen</p>
            </div>
          </div>
          <button @click="emit('close')" class="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800">
            <X class="w-5 h-5" />
          </button>
        </div>

        <div v-if="successMsg" class="p-3 bg-emerald-500/20 border-b border-emerald-500/30 text-emerald-400 text-sm font-semibold flex items-center gap-2 px-6">
          <Check class="w-4 h-4" />
          <span>{{ successMsg }}</span>
        </div>

        <div class="flex-1 overflow-hidden grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-800">
          <div class="p-4 overflow-y-auto space-y-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Select Dishes</p>
            
            <div 
              v-for="dish in menuItems" 
              :key="dish.id"
              @click="orderStore.addToCart(dish)"
              class="p-3 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-500/50 cursor-pointer flex items-center justify-between gap-3 group transition-all"
            >
              <div class="flex items-center gap-3">
                <img 
                  v-if="dish.image" 
                  :src="dish.image" 
                  class="w-12 h-12 rounded-lg object-cover" 
                />
                <div>
                  <h4 class="text-sm font-semibold text-white group-hover:text-amber-400 transition-colors">{{ dish.name }}</h4>
                  <p class="text-xs text-amber-400 font-bold">${{ Number(dish.price || 0).toFixed(2) }}</p>
                </div>
              </div>
              <button class="w-8 h-8 rounded-lg bg-slate-800 group-hover:bg-amber-500 group-hover:text-slate-950 flex items-center justify-center text-slate-300 font-bold">
                <Plus class="w-4 h-4" />
              </button>
            </div>
          </div>

          <div class="p-4 overflow-y-auto flex flex-col justify-between space-y-4">
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-300">Customer Name</label>
                <input 
                  v-model="customerName"
                  type="text" 
                  placeholder="e.g. John Doe"
                  class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500"
                />
              </div>

              <div class="grid grid-cols-3 gap-2">
                <button 
                  v-for="type in ['dine_in', 'takeaway', 'delivery']" 
                  :key="type"
                  @click="orderType = type"
                  :class="[
                    'py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider border transition-all',
                    orderType === type 
                      ? 'bg-amber-500 text-slate-950 border-amber-500' 
                      : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-white'
                  ]"
                >
                  {{ type.replace('_', ' ') }}
                </button>
              </div>

              <div v-if="orderType === 'dine_in'" class="space-y-1">
                <label class="block text-xs font-semibold text-slate-300">Select Table</label>
                <select 
                  v-model="selectedTableId"
                  class="w-full px-3 py-1.5 rounded-lg bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500"
                >
                  <option :value="null">No table assigned</option>
                  <option v-for="tbl in availableTables" :key="tbl.id" :value="tbl.id">
                    {{ tbl.table_number }} ({{ tbl.location }} - {{ tbl.capacity }} seats)
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-semibold text-slate-400">
                  <span>Selected Items ({{ cartCount }})</span>
                  <button v-if="cart.length > 0" @click="orderStore.clearCart()" class="text-rose-400 hover:underline">Clear</button>
                </div>

                <div v-if="cart.length === 0" class="p-6 text-center text-xs text-slate-500 border border-dashed border-slate-800 rounded-xl">
                  Click dishes on the left to add to order
                </div>

                <div v-else class="space-y-2 max-h-48 overflow-y-auto">
                  <div 
                    v-for="item in cart" 
                    :key="item.menuItem.id"
                    class="p-2.5 rounded-lg bg-slate-950 border border-slate-800 flex items-center justify-between text-xs"
                  >
                    <div>
                      <p class="font-semibold text-white">{{ item.menuItem.name }}</p>
                      <p class="text-amber-400 font-medium">${{ Number(item.menuItem.price * item.quantity).toFixed(2) }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                      <button 
                        @click="orderStore.updateCartQuantity(item.menuItem.id, item.quantity - 1)"
                        class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center font-bold"
                      >
                        <Minus class="w-3 h-3" />
                      </button>
                      <span class="font-bold text-white w-4 text-center">{{ item.quantity }}</span>
                      <button 
                        @click="orderStore.updateCartQuantity(item.menuItem.id, item.quantity + 1)"
                        class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:bg-slate-700 flex items-center justify-center font-bold"
                      >
                        <Plus class="w-3 h-3" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="pt-3 border-t border-slate-800/80 space-y-2.5">
              <div class="space-y-1.5 text-xs text-slate-400 font-medium">
                <div class="flex items-center justify-between">
                  <span>Subtotal</span>
                  <span class="font-mono text-slate-200">${{ Number(cartTotal || 0).toFixed(2) }}</span>
                </div>
                <div v-if="orderType === 'delivery'" class="flex items-center justify-between">
                  <span>Delivery Fee</span>
                  <span class="font-mono text-amber-400">+${{ Number(deliveryFee || 0).toFixed(2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Sales Tax (5%)</span>
                  <span class="font-mono text-slate-300">+${{ Number(taxAmount || 0).toFixed(2) }}</span>
                </div>
              </div>

              <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-sm font-bold">
                <span class="text-white">Grand Total:</span>
                <span class="text-xl font-display font-black text-amber-400">${{ Number(grandTotal || 0).toFixed(2) }}</span>
              </div>

              <button 
                @click="handleOrderSubmit"
                :disabled="cart.length === 0 || isSubmitting"
                class="w-full py-3 rounded-xl bg-amber-500 text-slate-950 font-bold text-sm hover:bg-amber-400 disabled:opacity-40 transition-all shadow-lg shadow-amber-500/20 cursor-pointer uppercase tracking-wider"
              >
                {{ isSubmitting ? 'Submitting Order...' : 'Send Order to Kitchen' }}
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
