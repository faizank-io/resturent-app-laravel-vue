<script setup>
defineProps({
  title: { type: String, required: true },
  value: { type: [String, Number], required: true },
  subtitle: { type: String, default: '' },
  trend: { type: String, default: '' },
  isPositive: { type: Boolean, default: true },
  iconBg: { type: String, default: 'bg-amber-500/10 text-amber-400 border-amber-500/30' }
});
</script>

<template>
  <div class="p-6 rounded-2xl glass-card relative overflow-hidden transition-all duration-300 group cursor-default">
    <!-- Ambient Blur Background -->
    <div class="absolute -right-8 -bottom-8 w-28 h-28 rounded-full bg-amber-500/10 blur-2xl group-hover:bg-amber-500/20 transition-all duration-500"></div>

    <div class="flex items-start justify-between relative z-10">
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">{{ title }}</p>
        <h3 class="text-3xl font-black font-display text-white tracking-tight group-hover:text-amber-400 transition-colors">
          {{ value }}
        </h3>
        <p v-if="subtitle" class="text-xs text-slate-400 mt-1 font-medium">{{ subtitle }}</p>
      </div>

      <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center border shadow-lg group-hover:scale-110 transition-all duration-300', iconBg]">
        <slot name="icon"></slot>
      </div>
    </div>

    <div v-if="trend" class="mt-4 pt-3 border-t border-slate-800/80 flex items-center gap-2 text-xs font-bold relative z-10">
      <span :class="['px-2 py-0.5 rounded-md font-mono text-[11px]', isPositive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20']">
        {{ isPositive ? '↑' : '↓' }} {{ trend }}
      </span>
      <span class="text-slate-400 font-medium text-[11px]">vs last period</span>
    </div>
  </div>
</template>
