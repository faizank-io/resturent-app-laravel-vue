<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { PlusCircle, Clock } from 'lucide-vue-next';

const emit = defineEmits(['open-new-order']);
const route = useRoute();

const currentTime = ref('');
let timer = null;

const updateClock = () => {
  const now = new Date();
  currentTime.value = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
};

onMounted(() => {
  updateClock();
  timer = setInterval(updateClock, 1000);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});

const pageTitle = computed(() => {
  return route.meta?.title || 'Dashboard Overview';
});
</script>

<template>
  <header class="h-20 border-b border-slate-800/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 glass-nav z-20 transition-colors duration-300">
    <div>
      <h2 class="text-xl font-black font-display text-white tracking-tight flex items-center gap-2">
        <span>{{ pageTitle }}</span>
      </h2>
      <p class="text-xs text-slate-400 font-medium">Real-time Restaurant Operations & POS Management</p>
    </div>

    <div class="flex items-center gap-3 sm:gap-4">
      
      <!-- Live Digital Clock -->
      <div class="hidden lg:flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900/90 border border-slate-800 text-xs font-mono text-amber-400 shadow-inner">
        <Clock class="w-3.5 h-3.5 text-amber-400 animate-pulse" />
        <span class="font-bold tracking-wider">{{ currentTime }}</span>
      </div>

      <!-- Create Order Button -->
      <button 
        @click="emit('open-new-order')"
        class="btn-glow-amber flex items-center gap-2 px-4 py-2.5 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg active:scale-95 transition-all cursor-pointer uppercase tracking-wider"
      >
        <PlusCircle class="w-4 h-4 stroke-[3]" />
        <span class="hidden sm:inline">Create Order</span>
      </button>

      <!-- Admin User Badge -->
      <div class="flex items-center gap-2.5 pl-2 border-l border-slate-800">
        <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 font-black text-xs shadow-md">
          RM
        </div>
      </div>

    </div>
  </header>
</template>
