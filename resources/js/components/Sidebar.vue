<script setup>
import { computed } from "vue";
import { RouterLink } from "vue-router";
import {
    LayoutDashboard,
    UtensilsCrossed,
    ShoppingBag,
    Grid3X3,
    ChefHat,
    Sparkles,
    Users,
    Activity,
    Flame,
    ChevronLeft,
    ChevronRight
} from "lucide-vue-next";
import { useOrderStore } from "../stores/useOrderStore";
import { useThemeStore } from "../stores/useThemeStore";
import { storeToRefs } from "pinia";

const orderStore = useOrderStore();
const themeStore = useThemeStore();

const { orders } = storeToRefs(orderStore);
const { isSidebarCollapsed } = storeToRefs(themeStore);

const activeCount = computed(() => {
    return orders.value.filter((o) =>
        ["pending", "preparing", "ready"].includes(o.status),
    ).length;
});
</script>

<template>
    <aside
        :class="[
            'bg-slate-950/90 border-r border-slate-800/80 flex flex-col justify-between shrink-0 h-screen sticky top-0 backdrop-blur-2xl z-30 shadow-2xl transition-all duration-300',
            isSidebarCollapsed ? 'w-20' : 'w-64'
        ]"
    >
        <div>
            <!-- Brand Logo Header & Toggle Button -->
            <div
                class="h-20 flex items-center justify-between px-4 border-b border-slate-800/80"
            >
                <div class="flex items-center gap-3 overflow-hidden">
                    <div
                        class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 via-amber-500 to-orange-600 flex items-center justify-center shrink-0 shadow-lg shadow-amber-500/25 text-slate-950 glow-amber"
                    >
                        <ChefHat class="w-6 h-6 stroke-[2.5]" />
                    </div>
                    
                    <div v-if="!isSidebarCollapsed" class="transition-all duration-300">
                        <h1
                            class="font-display font-black text-lg tracking-tight text-white flex items-center gap-1.5 whitespace-nowrap"
                        >
                            GourmetOS
                            <Sparkles
                                class="w-3.5 h-3.5 text-amber-400 fill-amber-400 animate-pulse"
                            />
                        </h1>
                        <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase whitespace-nowrap">
                            Restaurant Suite
                        </p>
                    </div>
                </div>

                <!-- Collapse Toggle Button -->
                <button 
                    @click="themeStore.toggleSidebar"
                    :title="isSidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                    class="p-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 transition-all cursor-pointer"
                >
                    <ChevronLeft v-if="!isSidebarCollapsed" class="w-4 h-4" />
                    <ChevronRight v-else class="w-4 h-4" />
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1.5">
                <p
                    v-if="!isSidebarCollapsed"
                    class="px-3 text-[10px] font-extrabold tracking-widest text-slate-500 uppercase mb-2.5"
                >
                    Main Operations
                </p>

                <RouterLink
                    to="/"
                    :title="isSidebarCollapsed ? 'Dashboard Overview' : ''"
                    :class="[
                        'flex items-center gap-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all duration-200 group relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3.5'
                    ]"
                >
                    <LayoutDashboard
                        class="w-5 h-5 text-slate-400 group-hover:text-amber-400 transition-colors shrink-0"
                    />
                    <span v-if="!isSidebarCollapsed" class="text-xs font-bold tracking-wide truncate">Dashboard Overview</span>
                </RouterLink>

                <RouterLink
                    to="/menu"
                    :title="isSidebarCollapsed ? 'Menu & Dishes' : ''"
                    :class="[
                        'flex items-center gap-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all duration-200 group relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3.5'
                    ]"
                >
                    <UtensilsCrossed
                        class="w-5 h-5 text-slate-400 group-hover:text-amber-400 transition-colors shrink-0"
                    />
                    <span v-if="!isSidebarCollapsed" class="text-xs font-bold tracking-wide truncate">Menu & Dishes</span>
                </RouterLink>

                <RouterLink
                    to="/orders"
                    :title="isSidebarCollapsed ? `Live Orders (${activeCount})` : ''"
                    :class="[
                        'flex items-center gap-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all duration-200 group relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3.5 justify-between'
                    ]"
                >
                    <div class="flex items-center gap-3">
                        <ShoppingBag
                            class="w-5 h-5 text-slate-400 group-hover:text-amber-400 transition-colors shrink-0"
                        />
                        <span v-if="!isSidebarCollapsed" class="text-xs font-bold tracking-wide truncate">Live Orders</span>
                    </div>
                    <span
                        v-if="activeCount > 0"
                        :class="[
                            'px-2 py-0.5 text-[10px] font-black bg-amber-500/20 text-amber-400 rounded-full border border-amber-500/40 flex items-center gap-1 shadow-sm shrink-0',
                            isSidebarCollapsed ? 'absolute top-1 right-1 px-1 py-0 text-[9px]' : ''
                        ]"
                    >
                        <Flame v-if="!isSidebarCollapsed" class="w-3 h-3 text-amber-400 fill-amber-400 animate-bounce" />
                        {{ activeCount }}
                    </span>
                </RouterLink>

                <RouterLink
                    to="/tables"
                    :title="isSidebarCollapsed ? 'Dining Tables' : ''"
                    :class="[
                        'flex items-center gap-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all duration-200 group relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3.5'
                    ]"
                >
                    <Grid3X3
                        class="w-5 h-5 text-slate-400 group-hover:text-amber-400 transition-colors shrink-0"
                    />
                    <span v-if="!isSidebarCollapsed" class="text-xs font-bold tracking-wide truncate">Dining Tables</span>
                </RouterLink>

                <RouterLink
                    to="/staffs"
                    :title="isSidebarCollapsed ? 'Staff & Team' : ''"
                    :class="[
                        'flex items-center gap-3 py-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800/50 transition-all duration-200 group relative overflow-hidden',
                        isSidebarCollapsed ? 'justify-center px-0' : 'px-3.5'
                    ]"
                >
                    <Users
                        class="w-5 h-5 text-slate-400 group-hover:text-amber-400 transition-colors shrink-0"
                    />
                    <span v-if="!isSidebarCollapsed" class="text-xs font-bold tracking-wide truncate"
                        >Staff & Team</span
                    >
                </RouterLink>
            </nav>
        </div>

        <!-- Footer Quick Status Card -->
        <div class="p-3 border-t border-slate-800/80">
            <div
                :class="[
                    'p-3 rounded-2xl bg-slate-900/80 border border-slate-800/90 flex items-center gap-3 shadow-inner',
                    isSidebarCollapsed ? 'justify-center p-2' : ''
                ]"
            >
                <div class="relative flex items-center justify-center shrink-0">
                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                    <div class="absolute w-3 h-3 rounded-full bg-emerald-400 animate-ping opacity-75"></div>
                </div>
                <div v-if="!isSidebarCollapsed" class="overflow-hidden">
                    <p class="text-xs font-bold text-white flex items-center gap-1 truncate">
                        <span>API Online</span>
                        <Activity class="w-3 h-3 text-emerald-400 shrink-0" />
                    </p>
                    <p class="text-[10px] text-slate-400 font-mono truncate">
                        Laravel v11
                    </p>
                </div>
            </div>
        </div>
    </aside>
</template>
