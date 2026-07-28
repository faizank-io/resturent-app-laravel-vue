<script setup>
import { ref, watch } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
  isOpen: { type: Boolean, required: true },
  dish: { type: Object, default: null },
  categories: { type: Array, required: true }
});

const emit = defineEmits(['close', 'save']);

const form = ref({
  id: null,
  name: '',
  category_id: '',
  price: '',
  description: '',
  image: '',
  prep_time_minutes: 15,
  is_available: true
});

const isSubmitting = ref(false);

watch(() => props.dish, (newDish) => {
  if (newDish) {
    form.value = { ...newDish };
  } else {
    form.value = {
      id: null,
      name: '',
      category_id: props.categories[0]?.id || '',
      price: '',
      description: '',
      image: '',
      prep_time_minutes: 15,
      is_available: true
    };
  }
}, { immediate: true });

const handleSubmit = async () => {
  if (!form.value.name || !form.value.category_id || !form.value.price) return;
  
  isSubmitting.value = true;
  try {
    emit('save', { ...form.value });
    emit('close');
  } catch (err) {
    console.error(err);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <Teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div @click="emit('close')" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

      <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl z-10 text-white">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
          <h3 class="text-lg font-bold font-display text-white">
            {{ form.id ? 'Edit Dish Item' : 'Add New Dish to Menu' }}
          </h3>
          <button @click="emit('close')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Dish Name *</label>
            <input 
              v-model="form.name"
              type="text" 
              required
              placeholder="e.g. Double Cheeseburger" 
              class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Category *</label>
              <select 
                v-model="form.category_id"
                required
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
              >
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Price ($) *</label>
              <input 
                v-model="form.price"
                type="number" 
                step="0.01"
                required
                placeholder="14.99" 
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Image URL</label>
            <input 
              v-model="form.image"
              type="url" 
              placeholder="https://images.unsplash.com/..." 
              class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Prep Time (mins)</label>
              <input 
                v-model="form.prep_time_minutes"
                type="number" 
                min="1"
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
              />
            </div>

            <div class="flex items-center gap-3 pt-6">
              <input 
                v-model="form.is_available"
                type="checkbox" 
                id="is_available"
                class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-amber-500 focus:ring-amber-500"
              />
              <label for="is_available" class="text-sm text-slate-300 font-medium cursor-pointer">Available in Stock</label>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
            <textarea 
              v-model="form.description"
              rows="3"
              placeholder="List ingredients or details..."
              class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:border-amber-500 focus:outline-none"
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
            <button 
              type="button" 
              @click="emit('close')"
              class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-sm font-medium hover:bg-slate-700"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="isSubmitting"
              class="px-5 py-2 rounded-xl bg-amber-500 text-slate-950 font-bold text-sm hover:bg-amber-400 disabled:opacity-50"
            >
              {{ isSubmitting ? 'Saving...' : (form.id ? 'Update Dish' : 'Create Dish') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
