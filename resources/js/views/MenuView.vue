<script setup>
import { onMounted, ref } from 'vue';
import { useMenuStore } from '../stores/useMenuStore';
import { storeToRefs } from 'pinia';
import DishModal from '../components/DishModal.vue';
import { Plus, Search, Clock, Edit2, Trash2, CheckCircle2, XCircle } from 'lucide-vue-next';

const menuStore = useMenuStore();

const { categories, selectedCategoryId, searchQuery, filteredMenuItems, loading } = storeToRefs(menuStore);

const isModalOpen = ref(false);
const editingDish = ref(null);

onMounted(() => {
  menuStore.fetchCategories();
  menuStore.fetchMenuItems();
});

const openAddModal = () => {
  editingDish.value = null;
  isModalOpen.value = true;
};

const openEditModal = (dish) => {
  editingDish.value = dish;
  isModalOpen.value = true;
};

const handleSaveDish = async (dishData) => {
  if (dishData.id) {
    await menuStore.updateMenuItem(dishData.id, dishData);
  } else {
    await menuStore.addMenuItem(dishData);
  }
};

const handleDeleteDish = async (id) => {
  if (confirm('Are you sure you want to delete this dish from the menu?')) {
    await menuStore.deleteMenuItem(id);
  }
};
</script>

<template>
  <div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-6 rounded-2xl glass-panel">
      <div class="relative w-full sm:w-80">
        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Search food, beverages, desserts..."
          class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500 transition-colors"
        />
      </div>

      <button 
        @click="openAddModal"
        class="w-full sm:w-auto px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm rounded-xl flex items-center justify-center gap-2 shadow-lg shadow-amber-500/20 active:scale-95 transition-all cursor-pointer"
      >
        <Plus class="w-4 h-4 stroke-[3]" />
        <span>Add New Dish</span>
      </button>
    </div>

    <div class="flex items-center gap-2 overflow-x-auto pb-2">
      <button 
        @click="selectedCategoryId = null"
        :class="[
          'px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border',
          selectedCategoryId === null 
            ? 'bg-amber-500 text-slate-950 border-amber-500 font-bold shadow-md shadow-amber-500/10' 
            : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white'
        ]"
      >
        All Categories
      </button>

      <button 
        v-for="cat in categories" 
        :key="cat.id"
        @click="selectedCategoryId = cat.id"
        :class="[
          'px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all border flex items-center gap-2',
          selectedCategoryId === cat.id 
            ? 'bg-amber-500 text-slate-950 border-amber-500 font-bold shadow-md shadow-amber-500/10' 
            : 'bg-slate-900/80 text-slate-400 border-slate-800 hover:text-white'
        ]"
      >
        <span>{{ cat.name }}</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-slate-950/40">{{ cat.menu_items_count || 0 }}</span>
      </button>
    </div>

    <div v-if="loading" class="text-center py-12 text-slate-400 text-sm">
      Loading menu catalog...
    </div>

    <div v-else-if="filteredMenuItems.length === 0" class="p-12 text-center glass-panel rounded-2xl space-y-3">
      <p class="text-slate-400 font-medium text-sm">No dishes found matching your criteria</p>
      <button @click="openAddModal" class="px-4 py-2 bg-amber-500/10 text-amber-400 font-semibold text-xs rounded-xl border border-amber-500/20">
        Create a new dish
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="dish in filteredMenuItems" 
        :key="dish.id"
        class="rounded-2xl glass-panel overflow-hidden flex flex-col justify-between group hover:border-slate-700 transition-all"
      >
        <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
          <img 
            v-if="dish.image" 
            :src="dish.image" 
            :alt="dish.name"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
          />
          <div v-else class="w-full h-full flex items-center justify-center text-slate-600">
            No Image
          </div>

          <div class="absolute top-3 right-3">
            <span 
              :class="[
                'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-md',
                dish.is_available ? 'bg-emerald-500/80 text-white border-emerald-400' : 'bg-rose-500/80 text-white border-rose-400'
              ]"
            >
              {{ dish.is_available ? 'In Stock' : 'Out of Stock' }}
            </span>
          </div>

          <div v-if="dish.category" class="absolute bottom-3 left-3">
            <span class="px-2.5 py-1 rounded-lg bg-slate-950/80 backdrop-blur-md text-amber-400 text-xs font-semibold border border-slate-800">
              {{ dish.category.name }}
            </span>
          </div>
        </div>

        <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
          <div>
            <div class="flex items-start justify-between gap-2">
              <h3 class="font-bold font-display text-base text-white group-hover:text-amber-400 transition-colors">
                {{ dish.name }}
              </h3>
              <span class="text-base font-extrabold text-amber-400 font-display">${{ Number(dish.price || 0).toFixed(2) }}</span>
            </div>
            
            <p class="text-xs text-slate-400 line-clamp-2 mt-1.5 leading-relaxed">{{ dish.description }}</p>
          </div>

          <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-1.5 text-xs text-slate-400">
              <Clock class="w-3.5 h-3.5" />
              <span>{{ dish.prep_time_minutes }} mins prep</span>
            </div>

            <div class="flex items-center gap-2">
              <button 
                @click="menuStore.toggleStock(dish.id)"
                :title="dish.is_available ? 'Mark Out of Stock' : 'Mark In Stock'"
                class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800 transition-colors"
              >
                <CheckCircle2 v-if="dish.is_available" class="w-4 h-4 text-emerald-400" />
                <XCircle v-else class="w-4 h-4 text-rose-400" />
              </button>

              <button 
                @click="openEditModal(dish)"
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
              >
                <Edit2 class="w-4 h-4" />
              </button>

              <button 
                @click="handleDeleteDish(dish.id)"
                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <DishModal 
      :isOpen="isModalOpen" 
      :dish="editingDish" 
      :categories="categories"
      @close="isModalOpen = false"
      @save="handleSaveDish"
    />

  </div>
</template>
