# Vue 3 & Laravel Fullstack Architecture — Learning Guide (Roman Urdu)

> **Laravel Senior Backend Developers Ke Liye Vue 3 (Composition API) Seekhne Ki Comprehensive Guide**

---

## 📌 1. Blade vs Vue 3 SPA Me Farq

| Feature | Laravel Blade (Server-Side) | Vue 3 SPA (Client-Side) |
| :--- | :--- | :--- |
| **Page Load** | Browser har click par poora page reload karta hai. | Sirf ek baar `welcome.blade.php` load hota hai. Baqi UI Javascript se bina refresh ke update hoti hai. |
| **Data Fetching** | Controller View ko directly PHP variables pass karta hai (`return view('dashboard', compact('data'))`). | Controller JSON return karta hai (`return response()->json(...)`). Vue 3 Axios se API call karta hai. |
| **State Management** | PHP Sessions / Database. | Pinia Stores (`useMenuStore`, `useOrderStore`). |
| **Routing** | Laravel `routes/web.php`. | Vue Router (`resources/js/router/index.js`) for UI, Laravel `routes/api.php` for Data. |

---

## 🏗️ 2. Data Flow (Aap Ke System Ka Architecture)

1. **User Action**: User screen par click karta hai (e.g. "Add to Cart" ya "Change Status").
2. **Pinia Store Action**: Vue component Pinia Store ke function ko call karta hai.
3. **Axios API Call**: Pinia Store Axios se Laravel API ko hit karta hai (`axios.post('/api/orders')`).
4. **Laravel Controller**: Laravel DB query chalata hai aur JSON response deta hai.
5. **Auto UI Update**: Pinia state update hoti hai aur Vue 3 screen ko bina refresh kiye foran update kar deta hai.

---

## 📁 3. Project Directory Structure (Humne Jo Files Banayi Hain)

```
d:/www/fullstack/
├── bootstrap/app.php              <-- Yahan 'routes/api.php' register kiya gaya hai (/api prefix ke sath)
├── routes/
│   ├── api.php                    <-- Endpoints (Categories, MenuItems, Orders, Tables)
│   └── web.php                    <-- Catch-all route jo 'welcome.blade.php' ko serve karta hai
├── app/
│   ├── Models/                    <-- Eloquent Models (Category, MenuItem, Order, Table)
│   └── Http/Controllers/Api/      <-- JSON API Controllers (DashboardController, etc.)
└── resources/
    ├── views/welcome.blade.php    <-- Mount point container <div id="app"></div>
    ├── css/app.css                <-- Tailwind CSS & Custom Glassmorphism styles
    └── js/
        ├── app.js                 <-- Vue 3 main entry (Pinia aur Vue Router ko mount karta hai)
        ├── router/index.js        <-- Vue Router client-side paths (/menu, /orders, /tables)
        ├── stores/                <-- Pinia Stores (Global State)
        │   ├── useDashboardStore.js
        │   ├── useMenuStore.js
        │   ├── useOrderStore.js
        │   └── useTableStore.js
        ├── components/            <-- Reusable UI Widgets (Sidebar, Navbar, StatCard, Modals)
        └── views/                 <-- Main Page Views (DashboardView, MenuView, OrdersView, TablesView)
```

---

## ⚡ 4. Vue 3 Ke 5 Golden Rules (`<script setup>`)

Har `.vue` file ke 2 main hissey hote hain: `<script setup>` (Logic) aur `<template>` (UI Layout).

### Rule 1: Reactivity (`ref`)
Normal variable change karne se UI update nahi hoti. `ref()` use karne se jab variable ki value change hoti hai, Vue UI ko automatically update kar deta hai.

```html
<script setup>
import { ref } from 'vue';

// Reactive variable declaration
const count = ref(0);

// Javascript me value access ya change karne ke liye .value use karein
const increment = () => {
  count.value++;
};
</script>

<template>
  <!-- Template me direct variable name use karein (without .value) -->
  <button @click="increment">Count is: {{ count }}</button>
</template>
```

### Rule 2: Template Bindings (`{{ }}`, `:`, `@`)
- **Text Display**: `{{ dish.name }}`
- **Attribute Binding (`:`)**: HTML attribute ke aage colon `:` lagayein dynamic value pass karne ke liye (e.g. `:src="dish.image"`, `:class="dish.is_active ? 'text-green-400' : 'text-red-400'"`).
- **Event Listener (`@`)**: Click ya submit handle karne ke liye `@` use karein (e.g. `@click="save()"`, `@submit.prevent="handleSubmit()"`).

### Rule 3: Loops & Conditionals (`v-for`, `v-if`)
```html
<template>
  <!-- Condition check (Jaise PHP me @if) -->
  <span v-if="dish.is_available" class="text-green-400">In Stock</span>
  <span v-else class="text-red-400">Out of Stock</span>

  <!-- Loop chalana (Jaise PHP me @foreach) -->
  <div v-for="dish in menuItems" :key="dish.id">
    <h4>{{ dish.name }}</h4>
    <p>${{ Number(dish.price).toFixed(2) }}</p>
  </div>
</template>
```

### Rule 4: Lifecycle Hooks (`onMounted`)
Pehli baar jab page/component screen par mount hota hai, yeh automatically chalta hai (Initial API calls ke liye best hai).

```html
<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  console.log('Page load ho gaya! Data fetch kar rahe hain...');
  menuStore.fetchMenuItems();
});
</script>
```

### Rule 5: Pinia Stores & Axios Integration
State management stores me app ka global data aur API calls hoti hain.

```javascript
// resources/js/stores/useMenuStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useMenuStore = defineStore('menu', {
  state: () => ({
    menuItems: [],
    loading: false
  }),
  actions: {
    async fetchMenuItems() {
      this.loading = true;
      try {
        const res = await axios.get('/api/menu-items');
        this.menuItems = res.data.data; // State update hui!
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    }
  }
});
```

---

## 🛠️ 5. Step-by-Step Guide: Kal Naya Feature Kaise Banayein?

Maan lein kal aap ko **"Customers Management"** ka naya feature banana hai:

### Step 1: Laravel Backend Setup
1. Model & Migration: `php artisan make:model Customer -m`
2. API Controller: `php artisan make:controller Api/CustomerController --api`
3. API Route in `routes/api.php`:
   ```php
   Route::apiResource('customers', CustomerController::class);
   ```

### Step 2: Pinia Store Setup
File: `resources/js/stores/useCustomerStore.js`
```javascript
import { defineStore } from 'pinia';
import axios from 'axios';

export const useCustomerStore = defineStore('customer', {
  state: () => ({
    customers: [],
    loading: false
  }),
  actions: {
    async fetchCustomers() {
      const res = await axios.get('/api/customers');
      this.customers = res.data.data;
    }
  }
});
```

### Step 3: Vue Page View Setup
File: `resources/js/views/CustomersView.vue`
```html
<script setup>
import { onMounted } from 'vue';
import { useCustomerStore } from '../stores/useCustomerStore';

const customerStore = useCustomerStore();

onMounted(() => {
  customerStore.fetchCustomers();
});
</script>

<template>
  <div class="space-y-4">
    <h2 class="text-xl font-bold text-white">Customer List</h2>
    <div v-for="cust in customerStore.customers" :key="cust.id" class="p-4 bg-slate-900 rounded-xl text-white">
      <p class="font-semibold">{{ cust.name }}</p>
      <p class="text-xs text-slate-400">{{ cust.email }}</p>
    </div>
  </div>
</template>
```

### Step 4: Vue Router Route Add Karein
1. `resources/js/router/index.js` me add karein:
   ```javascript
   {
     path: '/customers',
     name: 'customers',
     component: () => import('../views/CustomersView.vue')
   }
   ```
2. `resources/js/components/Sidebar.vue` me link add karein:
   ```html
   <RouterLink to="/customers" class="...">
     Customers
   </RouterLink>
   ```

---

## 🚨 6. Important Tips & Errors Avoid Karna

1. **`.toFixed()` TypeError Fix**:
   - Aggregate DB queries se number aksar String me aata hai. Hamesha `Number(val || 0).toFixed(2)` format use karein.
2. **Reactivity `.value` Usage**:
   - `<script setup>` ke andar `ref` variables ke sath `.value` lagayein (`count.value = 5`). Template `<template>` ke andar `.value` nahi lagana.
3. **Asset Compilation**:
   - Code change karte waqt terminal me `npm run dev` chalayein taake auto-reload hota rahe.
