<script setup>
import { onMounted, ref } from 'vue';
import { useTableStore } from '../stores/useTableStore';
import { storeToRefs } from 'pinia';
import { Users, MapPin, Plus, Sparkles, CheckCircle2, AlertCircle, BookmarkCheck } from 'lucide-vue-next';

const tableStore = useTableStore();
const { tables, loading } = storeToRefs(tableStore);

const isNewTableModalOpen = ref(false);
const newTable = ref({
  table_number: '',
  capacity: 4,
  location: 'Main Dining'
});

onMounted(() => {
  tableStore.fetchTables();
});

const handleStatusToggle = async (tableId, newStatus) => {
  await tableStore.updateTableStatus(tableId, newStatus);
};

const handleCreateTable = async () => {
  if (!newTable.value.table_number) return;
  try {
    await tableStore.createTable(newTable.value);
    newTable.value = { table_number: '', capacity: 4, location: 'Main Dining' };
    isNewTableModalOpen.value = false;
  } catch (err) {
    console.error(err);
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'available': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
    case 'occupied': return 'bg-rose-500/10 text-rose-400 border-rose-500/30';
    case 'reserved': return 'bg-amber-500/10 text-amber-400 border-amber-500/30';
    default: return 'bg-slate-800 text-slate-400 border-slate-700';
  }
};
</script>

<template>
  <div class="space-y-6">
    
    <!-- Top Header Banner -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 rounded-2xl glass-panel">
      <div>
        <h3 class="text-lg font-black font-display text-white tracking-tight flex items-center gap-2">
          <Sparkles class="w-5 h-5 text-amber-500" />
          <span>Dining Floor & Table Seating Plan</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1">Real-time table occupancy, customer reservations & seating availability</p>
      </div>

      <button 
        @click="isNewTableModalOpen = true"
        class="btn-glow-amber px-4.5 py-2.5 text-slate-950 font-extrabold text-xs rounded-xl flex items-center gap-2 cursor-pointer uppercase tracking-wider"
      >
        <Plus class="w-4 h-4 stroke-[3]" />
        <span>Add Dining Table</span>
      </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="p-5 rounded-2xl glass-panel flex items-center justify-between border-l-4 border-l-emerald-500 glow-emerald">
        <div>
          <p class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider">Available Tables</p>
          <p class="text-3xl font-black font-display text-emerald-400 mt-1">{{ tableStore.availableTables.length }}</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-500/20">
          <CheckCircle2 class="w-6 h-6" />
        </div>
      </div>

      <div class="p-5 rounded-2xl glass-panel flex items-center justify-between border-l-4 border-l-rose-500">
        <div>
          <p class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider">Occupied Tables</p>
          <p class="text-3xl font-black font-display text-rose-400 mt-1">{{ tableStore.occupiedTables.length }}</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center font-bold text-sm border border-rose-500/20">
          <AlertCircle class="w-6 h-6" />
        </div>
      </div>

      <div class="p-5 rounded-2xl glass-panel flex items-center justify-between border-l-4 border-l-amber-500 glow-amber">
        <div>
          <p class="text-[11px] text-slate-400 font-extrabold uppercase tracking-wider">Reserved Tables</p>
          <p class="text-3xl font-black font-display text-amber-400 mt-1">{{ tableStore.reservedTables.length }}</p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm border border-amber-500/20">
          <BookmarkCheck class="w-6 h-6" />
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16 text-slate-400 text-sm glass-panel rounded-2xl">
      <div class="inline-block animate-spin w-6 h-6 border-2 border-amber-500 border-t-transparent rounded-full mb-2"></div>
      <p>Loading floor layout & seating status...</p>
    </div>

    <!-- Tables Layout Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div 
        v-for="tbl in tables" 
        :key="tbl.id"
        class="p-5 rounded-2xl glass-card flex flex-col justify-between space-y-5 relative overflow-hidden group border border-slate-800/80 hover:border-slate-700"
      >
        <!-- Top Status Indicator Glow -->
        <div 
          :class="[
            'absolute top-0 left-0 right-0 h-1.5',
            tbl.status === 'available' ? 'bg-emerald-500' : (tbl.status === 'occupied' ? 'bg-rose-500' : 'bg-amber-500')
          ]"
        ></div>

        <div class="space-y-3 pt-1">
          <div class="flex items-center justify-between">
            <h4 class="text-xl font-black font-display text-white group-hover:text-amber-400 transition-colors">{{ tbl.table_number }}</h4>
            <span :class="['px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider border', getStatusColor(tbl.status)]">
              {{ tbl.status }}
            </span>
          </div>

          <div class="space-y-1.5 text-xs text-slate-300 p-3 rounded-xl bg-slate-950/50 border border-slate-800/60 font-medium">
            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <Users class="w-3.5 h-3.5 text-slate-400" />
                Capacity
              </span>
              <span class="font-bold text-white">{{ tbl.capacity }} Seats</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <MapPin class="w-3.5 h-3.5 text-slate-400" />
                Location
              </span>
              <span class="font-bold text-amber-400">{{ tbl.location }}</span>
            </div>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-800/80 grid grid-cols-3 gap-1.5">
          <button 
            @click="handleStatusToggle(tbl.id, 'available')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-extrabold uppercase tracking-wider border transition-all cursor-pointer',
              tbl.status === 'available' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/50 shadow-sm' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-emerald-300'
            ]"
          >
            Free
          </button>

          <button 
            @click="handleStatusToggle(tbl.id, 'occupied')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-extrabold uppercase tracking-wider border transition-all cursor-pointer',
              tbl.status === 'occupied' ? 'bg-rose-500/20 text-rose-400 border-rose-500/50 shadow-sm' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-rose-300'
            ]"
          >
            Occupy
          </button>

          <button 
            @click="handleStatusToggle(tbl.id, 'reserved')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-extrabold uppercase tracking-wider border transition-all cursor-pointer',
              tbl.status === 'reserved' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50 shadow-sm' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-amber-300'
            ]"
          >
            Reserve
          </button>
        </div>
      </div>
    </div>

    <!-- Add Table Modal -->
    <Teleport to="body">
      <div v-if="isNewTableModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="isNewTableModalOpen = false" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        
        <div class="relative w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 z-10 text-white space-y-4 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-extrabold font-display">Add Dining Table</h3>
            <button @click="isNewTableModalOpen = false" class="text-slate-400 hover:text-white font-bold">✕</button>
          </div>
          
          <form @submit.prevent="handleCreateTable" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Table Name/Number *</label>
              <input v-model="newTable.table_number" type="text" required placeholder="e.g. Table 09" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Capacity (Seats)</label>
                <input v-model="newTable.capacity" type="number" min="1" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500" />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Location Zone</label>
                <select v-model="newTable.location" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500">
                  <option value="Main Dining">Main Dining</option>
                  <option value="Window Side">Window Side</option>
                  <option value="Patio">Patio</option>
                  <option value="Rooftop">Rooftop</option>
                  <option value="VIP Lounge">VIP Lounge</option>
                </select>
              </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
              <button type="button" @click="isNewTableModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold cursor-pointer">Cancel</button>
              <button type="submit" class="btn-glow-amber px-5 py-2 rounded-xl text-slate-950 font-extrabold text-xs cursor-pointer">Create Table</button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

  </div>
</template>
