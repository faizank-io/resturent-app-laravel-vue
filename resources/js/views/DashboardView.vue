<script setup>
import { onMounted, computed } from 'vue';
import { useDashboardStore } from '../stores/useDashboardStore';
import { storeToRefs } from 'pinia';
import StatCard from '../components/StatCard.vue';
import { 
  DollarSign, 
  ShoppingBag, 
  Grid3X3, 
  Flame, 
  TrendingUp, 
  ArrowRight,
  Sparkles,
  Utensils,
  Clock
} from 'lucide-vue-next';

const dashboardStore = useDashboardStore();
const { kpis, salesChart, topDishes, recentOrders } = storeToRefs(dashboardStore);

onMounted(() => {
  dashboardStore.fetchDashboardStats();
});

// Calculate maximum revenue dynamically to keep chart bars scaled perfectly
const maxChartRevenue = computed(() => {
  if (!salesChart.value || salesChart.value.length === 0) return 500;
  const max = Math.max(...salesChart.value.map(p => Number(p.revenue) || 0));
  return max > 0 ? Math.ceil(max * 1.1) : 500;
});

// Calculate total 7-day revenue sum
const totalWeeklyRevenue = computed(() => {
  if (!salesChart.value) return 0;
  return salesChart.value.reduce((acc, curr) => acc + (Number(curr.revenue) || 0), 0);
});

// Identify peak sales day
const peakSalesDay = computed(() => {
  if (!salesChart.value || salesChart.value.length === 0) return null;
  return [...salesChart.value].sort((a, b) => (Number(b.revenue) || 0) - (Number(a.revenue) || 0))[0];
});

// Maximum dish sold for progress bar calculation
const maxDishSold = computed(() => {
  if (!topDishes.value || topDishes.value.length === 0) return 1;
  return Math.max(...topDishes.value.map(d => d.total_sold || 1));
});

const getStatusBadge = (status) => {
  switch (status) {
    case 'pending': return 'bg-amber-500/10 text-amber-400 border-amber-500/30';
    case 'preparing': return 'bg-sky-500/10 text-sky-400 border-sky-500/30';
    case 'ready': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
    case 'completed': return 'bg-slate-800 text-slate-400 border-slate-700';
    case 'cancelled': return 'bg-rose-500/10 text-rose-400 border-rose-500/30';
    default: return 'bg-slate-800 text-slate-400';
  }
};

const formatPrice = (val) => Number(val || 0).toFixed(2);
</script>

<template>
  <div class="space-y-8">
    
    <!-- Top Stats Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard 
        title="Today's Revenue" 
        :value="'$' + formatPrice(kpis.today_revenue)"
        subtitle="Gross sales today"
        trend="+14.2%"
        :isPositive="true"
      >
        <template #icon>
          <DollarSign class="w-6 h-6" />
        </template>
      </StatCard>

      <StatCard 
        title="Today's Orders" 
        :value="kpis.today_orders || 0"
        subtitle="Total orders processed"
        trend="+8.5%"
        :isPositive="true"
        iconBg="bg-blue-500/10 text-blue-400 border-blue-500/20"
      >
        <template #icon>
          <ShoppingBag class="w-6 h-6" />
        </template>
      </StatCard>

      <StatCard 
        title="Active Kitchen Orders" 
        :value="kpis.active_orders || 0"
        subtitle="Pending & Preparing"
        iconBg="bg-orange-500/10 text-orange-400 border-orange-500/20"
      >
        <template #icon>
          <Flame class="w-6 h-6" />
        </template>
      </StatCard>

      <StatCard 
        title="Table Occupancy" 
        :value="(kpis.table_occupancy?.rate_percentage || 0) + '%'"
        :subtitle="`${kpis.table_occupancy?.occupied || 0} of ${kpis.table_occupancy?.total || 0} tables occupied`"
        iconBg="bg-emerald-500/10 text-emerald-400 border-emerald-500/20"
      >
        <template #icon>
          <Grid3X3 class="w-6 h-6" />
        </template>
      </StatCard>
    </div>

    <!-- Main Section: Weekly Chart & Popular Dishes -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Weekly Revenue Chart Card (FIXED OVERFLOW & CONTAINED UI) -->
      <div class="lg:col-span-2 p-6 rounded-2xl glass-panel space-y-6 flex flex-col justify-between overflow-hidden">
        
        <!-- Header & Quick Badges -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-extrabold font-display text-white flex items-center gap-2">
              <TrendingUp class="w-4 h-4 text-amber-400" />
              <span>Weekly Sales Performance</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Revenue trend & daily order totals across past 7 days</p>
          </div>

          <div class="flex items-center gap-2">
            <span v-if="peakSalesDay" class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 text-xs font-semibold border border-amber-500/20 flex items-center gap-1">
              <Sparkles class="w-3 h-3" />
              Peak: {{ peakSalesDay.day }} (${{ formatPrice(peakSalesDay.revenue) }})
            </span>
            <span class="px-2.5 py-1 rounded-lg bg-slate-950/80 text-xs text-slate-300 font-bold border border-slate-800">
              Total: ${{ formatPrice(totalWeeklyRevenue) }}
            </span>
          </div>
        </div>

        <!-- Chart Container with Explicit Box & Clean Layout -->
        <div class="bg-slate-950/40 p-4 rounded-xl border border-slate-800/80 space-y-3">
          
          <!-- Bar Graph Area (Fixed Height 180px with Grid Lines) -->
          <div class="relative h-48 w-full flex items-end justify-between gap-2 pt-6">
            
            <!-- Horizontal Grid Lines -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-15">
              <div class="border-b border-slate-600 w-full flex justify-end">
                <span class="text-[9px] font-mono text-slate-300 pr-1 -mt-2">${{ formatPrice(maxChartRevenue) }}</span>
              </div>
              <div class="border-b border-slate-600 w-full flex justify-end">
                <span class="text-[9px] font-mono text-slate-300 pr-1 -mt-2">${{ formatPrice(maxChartRevenue / 2) }}</span>
              </div>
              <div class="border-b border-slate-600 w-full"></div>
            </div>

            <!-- Bar Columns -->
            <div 
              v-for="(point, idx) in salesChart" 
              :key="idx" 
              class="flex-1 flex flex-col items-center h-full justify-end group relative z-10"
            >
              <!-- Tooltip on Hover -->
              <div class="absolute -top-7 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-150 bg-slate-900 border border-amber-500/40 px-2 py-0.5 rounded text-[10px] text-amber-400 font-mono font-bold whitespace-nowrap shadow-xl z-20 pointer-events-none">
                ${{ formatPrice(point.revenue) }} ({{ point.orders }} orders)
              </div>

              <!-- Bar Fill (Clamped 0% - 100%) -->
              <div 
                class="w-full max-w-[32px] rounded-t-lg bg-gradient-to-t from-amber-600 via-amber-500 to-amber-400 group-hover:from-amber-500 group-hover:to-orange-400 transition-all duration-300 shadow-md shadow-amber-500/10"
                :style="{ height: Math.min(100, Math.max(6, ((Number(point.revenue) || 0) / maxChartRevenue) * 100)) + '%' }"
              ></div>
            </div>

          </div>

          <!-- X-Axis Day Labels Row -->
          <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-800/80">
            <div 
              v-for="(point, idx) in salesChart" 
              :key="idx" 
              class="flex-1 text-center"
            >
              <span class="text-[11px] font-bold text-slate-400 group-hover:text-amber-400 uppercase tracking-wider">
                {{ point.day }}
              </span>
            </div>
          </div>

        </div>

      </div>

      <!-- Popular Dishes Card -->
      <div class="p-6 rounded-2xl glass-panel space-y-6 flex flex-col justify-between">
        <div>
          <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
            <Utensils class="w-4 h-4 text-amber-400" />
            <span>Popular Dishes</span>
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Top performing items by order quantity</p>
        </div>

        <div class="space-y-3.5">
          <div 
            v-for="(dish, index) in topDishes" 
            :key="dish.id"
            class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 hover:border-slate-700 transition-all space-y-2 group"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <span :class="[
                  'w-5 h-5 rounded-md text-[10px] font-black flex items-center justify-center',
                  index === 0 ? 'bg-amber-500 text-slate-950' : (index === 1 ? 'bg-slate-300 text-slate-950' : 'bg-amber-900/50 text-amber-400')
                ]">
                  #{{ index + 1 }}
                </span>
                <div>
                  <h4 class="text-xs font-bold text-white group-hover:text-amber-400 transition-colors line-clamp-1">{{ dish.name }}</h4>
                  <p class="text-[11px] text-slate-400 font-mono">${{ formatPrice(dish.price) }}</p>
                </div>
              </div>

              <span class="px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-400 text-xs font-bold border border-amber-500/20">
                {{ dish.total_sold }} sold
              </span>
            </div>

            <!-- Relative Sales Progress Bar -->
            <div class="w-full bg-slate-900 rounded-full h-1.5 overflow-hidden">
              <div 
                class="bg-gradient-to-r from-amber-500 to-orange-400 h-1.5 rounded-full transition-all duration-500"
                :style="{ width: Math.min(100, Math.max(10, (dish.total_sold / maxDishSold) * 100)) + '%' }"
              ></div>
            </div>
          </div>
        </div>

      </div>

    </div>

    <!-- Recent Kitchen Orders Table Card -->
    <div class="p-6 rounded-2xl glass-panel space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-base font-bold font-display text-white flex items-center gap-2">
            <Clock class="w-4 h-4 text-amber-400" />
            <span>Recent Kitchen Orders</span>
          </h3>
          <p class="text-xs text-slate-400 mt-0.5">Real-time incoming orders and preparation statuses</p>
        </div>

        <RouterLink 
          to="/orders" 
          class="px-3.5 py-1.5 rounded-xl bg-slate-950/60 hover:bg-slate-800 text-xs font-bold text-amber-400 hover:text-amber-300 border border-slate-800 transition-all flex items-center gap-1.5"
        >
          <span>View All Orders</span>
          <ArrowRight class="w-3.5 h-3.5" />
        </RouterLink>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead>
            <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px] font-bold">
              <th class="pb-3 px-3">Order ID</th>
              <th class="pb-3 px-3">Customer / Table</th>
              <th class="pb-3 px-3">Service Type</th>
              <th class="pb-3 px-3">Total Amount</th>
              <th class="pb-3 px-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/60">
            <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-slate-800/30 transition-colors group">
              <td class="py-3.5 px-3 font-mono font-bold text-white group-hover:text-amber-400 transition-colors">
                {{ order.order_number }}
              </td>

              <td class="py-3.5 px-3 text-slate-300">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded-full bg-slate-800 text-amber-400 font-bold text-[10px] flex items-center justify-center">
                    {{ (order.customer_name || 'C')[0].toUpperCase() }}
                  </div>
                  <div>
                    <span class="font-semibold text-white">{{ order.customer_name || 'Guest' }}</span>
                    <span v-if="order.table" class="text-slate-400 text-[11px] ml-1.5">({{ order.table.table_number }})</span>
                  </div>
                </div>
              </td>

              <td class="py-3.5 px-3">
                <span class="px-2 py-0.5 rounded-md bg-slate-900 text-slate-400 uppercase font-bold text-[9px] tracking-wider border border-slate-800">
                  {{ (order.order_type || 'dine_in').replace('_', ' ') }}
                </span>
              </td>

              <td class="py-3.5 px-3 font-mono font-bold text-amber-400">
                ${{ formatPrice(order.total_amount) }}
              </td>

              <td class="py-3.5 px-3">
                <span :class="['px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border tracking-wider flex items-center gap-1.5 w-max', getStatusBadge(order.status)]">
                  <span v-if="['pending', 'preparing'].includes(order.status)" class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                  <span>{{ order.status }}</span>
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>
