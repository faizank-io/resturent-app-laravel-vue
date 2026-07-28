<script setup>
import { onMounted, ref } from 'vue';
import { useStaffStore } from '../stores/useStaffStore';
import { storeToRefs } from 'pinia';
import { 
  Users, 
  Plus, 
  Search, 
  Mail, 
  Phone, 
  Clock, 
  ShieldCheck, 
  Trash2, 
  UserCheck, 
  UserX,
  Briefcase,
  DollarSign
} from 'lucide-vue-next';

const staffStore = useStaffStore();
const { staffs, loading, searchQuery, selectedRole, filteredStaffs, onShiftStaff, activeStaff, offDutyStaff } = storeToRefs(staffStore);

const roles = ['All', 'Manager', 'Head Chef', 'Chef', 'Waiter', 'Cashier', 'Bartender'];
const shifts = ['Morning', 'Evening', 'Night', 'Full-Day'];

const isNewStaffModalOpen = ref(false);
const submitting = ref(false);
const errorMessage = ref('');

const newStaff = ref({
  name: '',
  email: '',
  phone: '',
  role: 'Waiter',
  shift: 'Morning',
  status: 'on_shift',
  hourly_rate: 16.50,
  avatar: ''
});

onMounted(() => {
  staffStore.fetchStaffs();
});

const handleStatusToggle = async (staffId, newStatus) => {
  try {
    await staffStore.updateStaffStatus(staffId, newStatus);
  } catch (err) {
    console.error('Failed to change status:', err);
  }
};

const handleDeleteStaff = async (staffId, name) => {
  if (confirm(`Are you sure you want to remove ${name} from staff list?`)) {
    try {
      await staffStore.deleteStaff(staffId);
    } catch (err) {
      alert('Could not remove staff member');
    }
  }
};

const handleCreateStaff = async () => {
  if (!newStaff.value.name || !newStaff.value.email) return;
  submitting.value = true;
  errorMessage.value = '';
  try {
    await staffStore.createStaff(newStaff.value);
    newStaff.value = {
      name: '',
      email: '',
      phone: '',
      role: 'Waiter',
      shift: 'Morning',
      status: 'on_shift',
      hourly_rate: 16.50,
      avatar: ''
    };
    isNewStaffModalOpen.value = false;
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error adding staff member. Please check email uniqueness.';
  } finally {
    submitting.value = false;
  }
};

const getRoleBadgeColor = (role) => {
  switch (role) {
    case 'Manager': return 'bg-purple-500/10 text-purple-400 border-purple-500/30';
    case 'Head Chef': return 'bg-amber-500/10 text-amber-400 border-amber-500/30';
    case 'Chef': return 'bg-orange-500/10 text-orange-400 border-orange-500/30';
    case 'Waiter': return 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30';
    case 'Cashier': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
    case 'Bartender': return 'bg-pink-500/10 text-pink-400 border-pink-500/30';
    default: return 'bg-slate-800 text-slate-400 border-slate-700';
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'on_shift': return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30';
    case 'active': return 'bg-sky-500/10 text-sky-400 border-sky-500/30';
    case 'off_duty': return 'bg-slate-800 text-slate-400 border-slate-700';
    default: return 'bg-slate-800 text-slate-400 border-slate-700';
  }
};

const formatStatusText = (status) => {
  switch (status) {
    case 'on_shift': return 'On Duty / Shift';
    case 'active': return 'Active';
    case 'off_duty': return 'Off Duty';
    default: return status;
  }
};
</script>

<template>
  <div class="space-y-6">
    
    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 p-6 rounded-2xl glass-panel">
      <div>
        <h3 class="text-lg font-extrabold font-display text-white tracking-tight flex items-center gap-2">
          <Users class="w-5 h-5 text-amber-500" />
          <span>Staff & Team Management</span>
        </h3>
        <p class="text-xs text-slate-400 mt-1">Manage restaurant personnel, role assignments, shift schedules & active duty statuses</p>
      </div>

      <button 
        @click="isNewStaffModalOpen = true"
        class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs rounded-xl flex items-center gap-2 shadow-lg shadow-amber-500/20 transition-all cursor-pointer"
      >
        <Plus class="w-4 h-4 stroke-[3]" />
        <span>Add Staff Member</span>
      </button>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
      <div class="p-4 rounded-xl glass-panel flex items-center justify-between border-l-4 border-l-amber-500">
        <div>
          <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Total Team</p>
          <p class="text-2xl font-black font-display text-white mt-1">{{ staffs.length }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm">
          <Users class="w-5 h-5" />
        </div>
      </div>

      <div class="p-4 rounded-xl glass-panel flex items-center justify-between border-l-4 border-l-emerald-500">
        <div>
          <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Currently On Shift</p>
          <p class="text-2xl font-black font-display text-emerald-400 mt-1">{{ onShiftStaff.length }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-sm">
          <UserCheck class="w-5 h-5" />
        </div>
      </div>

      <div class="p-4 rounded-xl glass-panel flex items-center justify-between border-l-4 border-l-sky-500">
        <div>
          <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Active Staff</p>
          <p class="text-2xl font-black font-display text-sky-400 mt-1">{{ activeStaff.length }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-sm">
          <ShieldCheck class="w-5 h-5" />
        </div>
      </div>

      <div class="p-4 rounded-xl glass-panel flex items-center justify-between border-l-4 border-l-slate-600">
        <div>
          <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Off Duty</p>
          <p class="text-2xl font-black font-display text-slate-300 mt-1">{{ offDutyStaff.length }}</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-sm">
          <UserX class="w-5 h-5" />
        </div>
      </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="p-4 rounded-2xl glass-panel flex flex-col md:flex-row items-center justify-between gap-4">
      <!-- Search Box -->
      <div class="relative w-full md:w-80">
        <Search class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" />
        <input 
          v-model="searchQuery"
          type="text" 
          placeholder="Search staff by name, email, role..." 
          class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-950/70 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 transition-all"
        />
      </div>

      <!-- Role Filter Pills -->
      <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-1 md:pb-0 scrollbar-none">
        <button 
          v-for="role in roles" 
          :key="role"
          @click="selectedRole = role"
          :class="[
            'px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer whitespace-nowrap',
            selectedRole === role 
              ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' 
              : 'bg-slate-950/40 text-slate-400 border border-slate-800 hover:text-white hover:bg-slate-800/50'
          ]"
        >
          {{ role }}
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16 text-slate-400 text-sm glass-panel rounded-2xl">
      <div class="inline-block animate-spin w-6 h-6 border-2 border-amber-500 border-t-transparent rounded-full mb-2"></div>
      <p>Loading staff registry...</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredStaffs.length === 0" class="text-center py-16 text-slate-400 text-sm glass-panel rounded-2xl space-y-2">
      <Users class="w-10 h-10 text-slate-600 mx-auto" />
      <p class="font-semibold text-white">No team members found</p>
      <p class="text-xs text-slate-500">Try adjusting your search filter or add a new staff member.</p>
    </div>

    <!-- Staff Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="stf in filteredStaffs" 
        :key="stf.id"
        class="p-5 rounded-2xl glass-panel flex flex-col justify-between space-y-5 border border-slate-800/80 hover:border-slate-700 transition-all relative overflow-hidden group"
      >
        <!-- Top Status Indicator Bar -->
        <div 
          :class="[
            'absolute top-0 left-0 right-0 h-1',
            stf.status === 'on_shift' ? 'bg-emerald-500' : (stf.status === 'active' ? 'bg-sky-500' : 'bg-slate-700')
          ]"
        ></div>

        <!-- Staff Member Info -->
        <div class="space-y-4 pt-1">
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
              <img 
                :src="stf.avatar || 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=300&q=80'" 
                :alt="stf.name" 
                class="w-12 h-12 rounded-xl object-cover border border-slate-700 shadow-md"
              />
              <div>
                <h4 class="text-sm font-bold font-display text-white group-hover:text-amber-400 transition-colors">{{ stf.name }}</h4>
                <div class="flex items-center gap-1.5 mt-1">
                  <span :class="['px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase border', getRoleBadgeColor(stf.role)]">
                    {{ stf.role }}
                  </span>
                  <span :class="['px-2 py-0.5 rounded-md text-[10px] font-bold border', getStatusColor(stf.status)]">
                    {{ formatStatusText(stf.status) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Delete Button -->
            <button 
              @click="handleDeleteStaff(stf.id, stf.name)"
              title="Remove staff member"
              class="p-1.5 rounded-lg text-slate-500 hover:text-rose-400 hover:bg-rose-500/10 transition-all opacity-0 group-hover:opacity-100 cursor-pointer"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>

          <!-- Attributes Grid -->
          <div class="p-3 rounded-xl bg-slate-950/50 border border-slate-800/60 space-y-2 text-xs text-slate-300">
            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <Mail class="w-3.5 h-3.5 text-slate-400" />
                Email
              </span>
              <span class="font-medium text-slate-200 truncate max-w-[180px]">{{ stf.email }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <Phone class="w-3.5 h-3.5 text-slate-400" />
                Phone
              </span>
              <span class="font-medium text-slate-200">{{ stf.phone || 'N/A' }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <Clock class="w-3.5 h-3.5 text-slate-400" />
                Shift
              </span>
              <span class="font-bold text-amber-400/90">{{ stf.shift }}</span>
            </div>

            <div class="flex items-center justify-between">
              <span class="text-slate-500 flex items-center gap-1.5">
                <DollarSign class="w-3.5 h-3.5 text-slate-400" />
                Pay Rate
              </span>
              <span class="font-bold text-emerald-400">${{ Number(stf.hourly_rate).toFixed(2) }}/hr</span>
            </div>
          </div>
        </div>

        <!-- Shift & Status Action Buttons -->
        <div class="pt-3 border-t border-slate-800/80 grid grid-cols-3 gap-1.5">
          <button 
            @click="handleStatusToggle(stf.id, 'on_shift')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider border transition-all cursor-pointer',
              stf.status === 'on_shift' 
                ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/50 shadow-sm shadow-emerald-500/20' 
                : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-emerald-300 hover:border-slate-700'
            ]"
          >
            On Shift
          </button>

          <button 
            @click="handleStatusToggle(stf.id, 'active')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider border transition-all cursor-pointer',
              stf.status === 'active' 
                ? 'bg-sky-500/20 text-sky-400 border-sky-500/50 shadow-sm shadow-sky-500/20' 
                : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-sky-300 hover:border-slate-700'
            ]"
          >
            Active
          </button>

          <button 
            @click="handleStatusToggle(stf.id, 'off_duty')"
            :class="[
              'py-1.5 rounded-xl text-[10px] font-bold uppercase tracking-wider border transition-all cursor-pointer',
              stf.status === 'off_duty' 
                ? 'bg-slate-800 text-slate-300 border-slate-600' 
                : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:text-slate-200 hover:border-slate-700'
            ]"
          >
            Off Duty
          </button>
        </div>
      </div>
    </div>

    <!-- Add New Staff Member Modal -->
    <Teleport to="body">
      <div v-if="isNewStaffModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="isNewStaffModalOpen = false" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        
        <div class="relative w-full max-w-lg rounded-2xl bg-slate-900 border border-slate-800 p-6 z-10 text-white space-y-5 shadow-2xl">
          <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-extrabold font-display flex items-center gap-2">
              <Users class="w-5 h-5 text-amber-500" />
              <span>Add New Staff Member</span>
            </h3>
            <button @click="isNewStaffModalOpen = false" class="text-slate-400 hover:text-white text-lg font-bold">✕</button>
          </div>

          <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-medium">
            {{ errorMessage }}
          </div>
          
          <form @submit.prevent="handleCreateStaff" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
              <input 
                v-model="newStaff.name" 
                type="text" 
                required 
                placeholder="e.g. Sarah Connor" 
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500" 
              />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                <input 
                  v-model="newStaff.email" 
                  type="email" 
                  required 
                  placeholder="sarah@restaurant.com" 
                  class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500" 
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                <input 
                  v-model="newStaff.phone" 
                  type="text" 
                  placeholder="+1 (555) 000-0000" 
                  class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-none focus:border-amber-500" 
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Role *</label>
                <select v-model="newStaff.role" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500">
                  <option v-for="r in roles.filter(x => x !== 'All')" :key="r" :value="r">{{ r }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Shift</label>
                <select v-model="newStaff.shift" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500">
                  <option v-for="s in shifts" :key="s" :value="s">{{ s }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Hourly Rate ($)</label>
                <input 
                  v-model="newStaff.hourly_rate" 
                  type="number" 
                  step="0.50" 
                  min="0" 
                  class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500" 
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-300 mb-1">Avatar Image URL (Optional)</label>
              <input 
                v-model="newStaff.avatar" 
                type="url" 
                placeholder="https://images.unsplash.com/..." 
                class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-none focus:border-amber-500" 
              />
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
              <button type="button" @click="isNewStaffModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold cursor-pointer">Cancel</button>
              <button 
                type="submit" 
                :disabled="submitting" 
                class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs cursor-pointer flex items-center gap-2"
              >
                <span v-if="submitting">Adding...</span>
                <span v-else>Save Staff Member</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

  </div>
</template>
