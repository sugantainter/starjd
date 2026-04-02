<template>
  <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none">Bank Accounts</h3>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">For Payouts & Settlements</p>
      </div>
      <button 
        @click="showAdd = true"
        class="h-10 px-5 rounded-xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition active:scale-95"
      >
        Add New Bank
      </button>
    </div>

    <!-- Bank Accounts List -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 gap-4 animate-pulse">
        <div v-for="i in 2" :key="i" class="h-32 bg-slate-50 rounded-2xl border border-slate-100"></div>
    </div>
    <div v-else-if="accounts.length === 0" class="flex flex-col items-center justify-center py-12 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
      <div class="h-16 w-16 bg-white rounded-2xl flex items-center justify-center text-slate-300 shadow-sm mb-4">
        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
      </div>
      <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No Bank Accounts Found</p>
    </div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div 
        v-for="acc in accounts" 
        :key="acc.id" 
        class="relative group bg-white rounded-2xl p-6 border border-slate-200 hover:border-slate-900 transition-all cursor-pointer shadow-sm hover:shadow-md"
        @click="$emit('select', acc)"
      >
        <div class="flex justify-between items-start mb-4">
          <div class="bg-slate-50 h-10 w-10 rounded-xl flex items-center justify-center text-slate-400">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
          </div>
          <div v-if="acc.is_verified" class="h-5 px-2 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-lg flex items-center gap-1">
            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            Verified
          </div>
        </div>
        <p class="text-xs font-black text-slate-900 leading-none truncate mb-1 uppercase tracking-tight">{{ acc.account_holder_name }}</p>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 truncate">{{ acc.bank_name }}</p>
        <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50">
          <p class="font-mono text-sm font-bold text-slate-600 tracking-wider">•••• {{ acc.account_number.slice(-4) }}</p>
          <button @click.stop="deleteAccount(acc.id)" class="text-slate-300 hover:text-red-500 transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Add Bank Modal Overlay -->
    <div v-if="showAdd" class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm p-4">
      <div class="w-full max-w-md bg-white rounded-[40px] p-10 shadow-2xl animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between mb-8">
           <div>
             <h4 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Add Bank</h4>
             <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-1">Enter Verified Banking Details</p>
           </div>
           <button @click="showAdd = false" class="h-10 w-10 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-900 transition-colors hover:bg-slate-100">
             <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
           </button>
        </div>

        <form @submit.prevent="addAccount" class="space-y-5">
           <div>
             <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1.5 ml-1">Account Holder Name</label>
             <input v-model="form.account_holder_name" type="text" required class="w-full h-14 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 px-6 font-bold text-slate-900 placeholder-slate-300 transition-shadow" placeholder="Full Name as per Bank" />
           </div>
           <div>
             <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1.5 ml-1">Bank Name</label>
             <input v-model="form.bank_name" type="text" required class="w-full h-14 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 px-6 font-bold text-slate-900 placeholder-slate-300 transition-shadow" placeholder="e.g. HDFC Bank" />
           </div>
           <div class="grid grid-cols-2 gap-4">
             <div>
               <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1.5 ml-1">Account Number</label>
               <input v-model="form.account_number" type="text" required class="w-full h-14 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 px-6 font-bold text-slate-900 placeholder-slate-300 transition-shadow" placeholder="1234..." />
             </div>
             <div>
               <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1.5 ml-1">IFSC Code</label>
               <input v-model="form.ifsc_code" type="text" required class="w-full h-14 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-slate-900 px-6 font-bold text-slate-900 placeholder-slate-300 transition-shadow" placeholder="HDFC0001..." />
             </div>
           </div>
           <button :disabled="saving" type="submit" class="w-full h-16 bg-slate-900 rounded-2xl text-white font-black uppercase tracking-widest mt-4 shadow-xl shadow-slate-900/20 hover:bg-slate-800 transition-all active:scale-[0.98] disabled:opacity-50">
             {{ saving ? 'Saving Details...' : 'Save & Verify Bank' }}
           </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { notify } from '../../lib/notify.js';

const accounts = ref([]);
const loading = ref(true);
const showAdd = ref(false);
const saving = ref(false);
const form = ref({
  account_holder_name: '',
  account_number: '',
  bank_name: '',
  ifsc_code: ''
});

defineEmits(['select']);

onMounted(fetchAccounts);

async function fetchAccounts() {
  loading.value = true;
  try {
    const res = await axios.get('/api/bank-accounts');
    accounts.value = res.data;
  } catch (e) {
    notify.error('Failed to load bank accounts.');
  } finally {
    loading.value = false;
  }
}

async function addAccount() {
  saving.value = true;
  try {
    const res = await axios.post('/api/bank-accounts', form.value);
    accounts.value.push(res.data);
    showAdd.value = false;
    form.value = { account_holder_name: '', account_number: '', bank_name: '', ifsc_code: '' };
    notify.success('Bank account added and verified successfully.');
  } catch (e) {
    notify.error(e.response?.data?.message || 'Failed to add bank account.');
  } finally {
    saving.value = false;
  }
}

async function deleteAccount(id) {
  if (!confirm('Are you sure you want to remove this bank account?')) return;
  try {
    await axios.delete(`/api/bank-accounts/${id}`);
    accounts.value = accounts.value.filter(a => a.id !== id);
    notify.success('Bank account removed.');
  } catch (e) {
    notify.error('Failed to remove bank account.');
  }
}
</script>
