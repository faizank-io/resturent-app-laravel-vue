<script setup>
import { ref } from 'vue';
import { RouterView } from 'vue-router';
import Sidebar from './components/Sidebar.vue';
import Navbar from './components/Navbar.vue';
import NewOrderModal from './components/NewOrderModal.vue';

const isNewOrderModalOpen = ref(false);
</script>

<template>
  <div class="flex min-h-screen bg-[#0b0f19] text-slate-100 font-sans antialiased">
    <Sidebar />

    <div class="flex-1 flex flex-col min-w-0">
      <Navbar @open-new-order="isNewOrderModalOpen = true" />

      <main class="flex-1 p-8 overflow-y-auto">
        <RouterView v-slot="{ Component }">
          <Transition name="fade" mode="out-in">
            <component :is="Component" />
          </Transition>
        </RouterView>
      </main>
    </div>

    <NewOrderModal 
      :isOpen="isNewOrderModalOpen" 
      @close="isNewOrderModalOpen = false" 
    />
  </div>
</template>
