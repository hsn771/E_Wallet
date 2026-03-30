@extends('layouts.app')

@section('title', 'Liabilities')
@section('header', 'My Liabilities')

@section('header_actions')
    <button onclick="document.getElementById('addLiabilityModal').classList.remove('hidden')"
        class="bg-rose-600 hover:bg-rose-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg shadow-rose-500/25">
        <span>📉</span> Add Liability
    </button>
@endsection

@section('content')
    <div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-3xl">💳
            </div>
            <div>
                <p class="text-slate-400 font-medium">Total Liabilities</p>
                <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-red-400">
                    -{{ number_format($liabilities->sum('amount'), 0) }} <span
                        class="text-lg text-slate-500">{{ auth()->user()->currency ?? 'BDT' }}</span>
                </h2>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($liabilities as $liability)
            <div onclick="openHistoryModal({{ json_encode($liability) }})"
                class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-lg group hover:border-rose-500/50 transition cursor-pointer relative overflow-hidden">
                <div
                    class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-[0.07] transition pointer-events-none">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3c-.49-4.47-4.07-8.05-8.54-8.54V1h-2v1.46C5.93 2.95 2.35 6.53 1.86 11H1v2h.86c.49 4.47 4.07 8.05 8.54 8.54V23h2v-1.46c4.47-.49 8.05-4.07 8.54-8.54H23v-2h-1.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z">
                        </path>
                    </svg>
                </div>

                <div class="flex justify-between items-start mb-4 relative z-10">
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $liability->name }}</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <p
                                class="text-[10px] text-slate-400 uppercase tracking-widest bg-slate-900/50 px-2 py-0.5 rounded-md border border-slate-700">
                                {{ str_replace('_', ' ', $liability->type) }}</p>
                            <span class="w-1 h-1 rounded-full bg-slate-700"></span>
                            <p class="text-[10px] text-slate-500 whitespace-nowrap">Taken:
                                {{ $liability->created_at->format('M d, Y') }}</p>
                            @if($liability->updated_at > $liability->created_at)
                                <span class="w-1 h-1 rounded-full bg-emerald-500/50"></span>
                                <p class="text-[10px] text-emerald-400 whitespace-nowrap">Paid:
                                    {{ $liability->updated_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1" onclick="event.stopPropagation()">
                        <button onclick="openPayLiabilityModal({{ json_encode($liability) }})"
                            class="p-2 text-slate-500 hover:text-emerald-400 hover:bg-emerald-500/10 rounded-xl transition-all duration-300"
                            title="Repay Debt">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </button>
                        <button onclick="openEditLiabilityModal({{ json_encode($liability) }})"
                            class="p-2 text-slate-500 hover:text-indigo-400 hover:bg-indigo-500/10 rounded-xl transition-all duration-300"
                            title="Edit details">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                        </button>
                        <form action="{{ route('liabilities.destroy', $liability) }}" method="POST"
                            onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 text-slate-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-all duration-300"
                                title="Delete record">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 relative z-10">
                    <p class="text-xs text-slate-500 mb-1">Outstanding Balance</p>
                    <h2
                        class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-pink-500">
                        -{{ number_format($liability->amount, 0) }} <span
                            class="text-sm font-medium opacity-50 text-slate-400">BDT</span>
                    </h2>
                </div>
            </div>
        @empty
            <div
                class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16 opacity-50 border-2 border-dashed border-slate-700/50 rounded-3xl">
                <div class="text-6xl mb-4">🙌</div>
                <p class="text-xl font-bold text-white mb-1">Debt Free!</p>
                <p class="text-slate-400">You don't have any liabilities recorded yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Add Liability Modal -->
    <div id="addLiabilityModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div
            class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-rose-500/10 blur-3xl rounded-full"></div>
            <div class="flex justify-between items-center mb-6 relative z-10">
                <h3 class="text-xl font-bold text-white">Add Liability</h3>
                <button onclick="document.getElementById('addLiabilityModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
            <form action="{{ route('liabilities.store') }}" method="POST" class="relative z-10">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                        <input type="text" name="name" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-rose-500 rounded-xl px-4 py-3 text-white placeholder-slate-600"
                            placeholder="e.g. Current Car Loan">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                        <select name="type" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-rose-500 rounded-xl px-4 py-3 text-white">
                            <option value="loan">Loan</option>
                            <option value="credit_due">Credit Due</option>
                            <option value="borrowed">Borrowed Money</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Amount owed
                            ({{ auth()->user()->currency ?? 'BDT' }})</label>
                        <input type="number" step="0.01" name="amount" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-rose-500 rounded-xl px-4 py-3 text-white">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                    <button type="button" onclick="document.getElementById('addLiabilityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-medium shadow-lg shadow-rose-500/25 transition">Save
                        Liability</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Liability Modal -->
    <div id="editLiabilityModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div
            class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/10 blur-3xl rounded-full"></div>
            <div class="flex justify-between items-center mb-6 relative z-10">
                <h3 class="text-xl font-bold text-white">Edit Liability</h3>
                <button onclick="document.getElementById('editLiabilityModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="editLiabilityForm" method="POST" class="relative z-10">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                        <input type="text" name="name" id="edit_liability_name" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                        <select name="type" id="edit_liability_type" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                            <option value="loan">Loan</option>
                            <option value="credit_due">Credit Due</option>
                            <option value="borrowed">Borrowed Money</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Outstanding Balance
                            ({{ auth()->user()->currency ?? 'BDT' }})</label>
                        <input type="number" step="0.01" name="amount" id="edit_liability_amount" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                    <button type="button" onclick="document.getElementById('editLiabilityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg shadow-indigo-500/25 transition">Update
                        Details</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pay Liability Modal -->
    <div id="payLiabilityModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div
            class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 blur-3xl rounded-full"></div>
            <div class="flex justify-between items-center mb-6 relative z-10">
                <h3 class="text-xl font-bold text-white">Pay Liability</h3>
                <button onclick="document.getElementById('payLiabilityModal').classList.add('hidden')"
                    class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
            <form id="payLiabilityForm" method="POST" class="relative z-10">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Liability</label>
                        <p id="pay_liability_name" class="text-white font-bold text-lg mb-2"></p>
                        <p class="text-xs text-slate-500">Total remaining: <span id="pay_liability_max"
                                class="text-rose-400"></span></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Select Source (Wallet or Asset)</label>
                        <select name="payment_source" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                            <optgroup label="Wallets" class="bg-slate-800 text-slate-400">
                                @foreach($wallets as $wallet)
                                    <option value="wallet_{{ $wallet->id }}" class="text-white">💳 {{ $wallet->name }} (Balance:
                                        {{ number_format($wallet->balance, 0) }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Assets" class="bg-slate-800 text-slate-400">
                                @foreach($assets as $asset)
                                    <option value="asset_{{ $asset->id }}" class="text-white">💎 {{ $asset->name }} (Value:
                                        {{ number_format($asset->value, 0) }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Amount to Pay</label>
                        <input type="number" step="0.01" name="amount" id="pay_liability_amount" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                    <button type="button" onclick="document.getElementById('payLiabilityModal').classList.add('hidden')"
                        class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium shadow-lg shadow-emerald-500/25 transition">Confirm
                        Payment</button>
                </div>
            </form>
        </div>
    </div>

    @push('modals')
        <!-- History Modal -->
        <div id="historyModal" style="background-color: rgba(2, 6, 23, 0.85); z-index: 10000;"
            class="fixed inset-0 hidden flex items-center justify-center p-4 backdrop-blur-sm">
            <div style="background-color: #1e293b; border: 1px solid #334155;"
                class="rounded-[2.5rem] w-full max-w-md shadow-2xl relative overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Modal Header -->
                <div class="p-8 flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚖️</span>
                        <h3 id="history_title" class="text-xl font-bold text-white tracking-tight">Loan Details</h3>
                    </div>
                    <button onclick="document.getElementById('historyModal').classList.add('hidden')"
                        class="text-slate-400 hover:text-white transition-colors text-xl">&times;</button>
                </div>

                <!-- Modal Body -->
                <div class="px-8 pb-4 overflow-y-auto custom-scrollbar space-y-3">
                    <!-- Original Info Row -->
                    <div
                        class="bg-slate-900/40 border border-slate-700/50 rounded-2xl p-4 flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></span>
                            <span class="text-slate-200 font-medium tracking-wide">Original Loan</span>
                        </div>
                        <div class="text-right">
                            <span id="history_taken_date_label" class="text-slate-300 font-bold"></span>
                            <span class="text-slate-500 text-[10px] ml-1 uppercase font-black">DATE</span>
                        </div>
                    </div>

                    <div class="py-2">
                        <h4
                            class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Repayment Log
                        </h4>

                        <div id="payment_list" class="space-y-2">
                            <!-- Payments injected here in Row Style -->
                        </div>
                    </div>
                </div>

                <!-- Modal Footer (Net Balance Style) -->
                <div class="p-6">
                    <div
                        class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-400 font-black text-xs uppercase tracking-widest">= REMAINING</span>
                        </div>
                        <div class="text-right">
                            <span id="history_remaining" class="text-2xl font-black text-emerald-400 tracking-tighter"></span>
                            <span class="text-emerald-500/50 text-[10px] ml-1 font-black uppercase">BDT</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            function openHistoryModal(liability) {
                const modal = document.getElementById('historyModal');
                document.getElementById('history_title').innerText = liability.name;
                document.getElementById('history_taken_date_label').innerText = new Date(liability.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                document.getElementById('history_remaining').innerText = parseFloat(liability.amount).toLocaleString();

                const list = document.getElementById('payment_list');
                list.innerHTML = '';

                if (liability.transactions && liability.transactions.length > 0) {
                    liability.transactions.forEach(t => {
                        const row = document.createElement('div');
                        row.className = "bg-slate-900/40 border border-slate-700/50 rounded-2xl p-4 flex items-center justify-between transition-all hover:bg-slate-800/60";
                        row.innerHTML = `
                                <div class="flex items-center gap-3">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                    <div>
                                        <p class="text-slate-200 font-medium tracking-wide text-sm whitespace-nowrap overflow-hidden text-ellipsis max-w-[150px]">${t.description || 'Repayment'}</p>
                                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">${new Date(t.date).toLocaleDateString()}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-emerald-400 font-bold">-${parseFloat(t.amount).toLocaleString()}</span>
                                    <span class="text-slate-500 text-[10px] ml-1 font-black">BDT</span>
                                </div>
                            `;
                        list.appendChild(row);
                    });
                } else {
                    list.innerHTML = `
                            <div class="bg-slate-900/20 rounded-2xl p-6 text-center border border-dashed border-slate-700/50">
                                <p class="text-slate-500 text-xs italic font-medium">No payment history available.</p>
                            </div>
                        `;
                }

                modal.classList.remove('hidden');
            }

            function openPayLiabilityModal(liability) {
                const modal = document.getElementById('payLiabilityModal');
                const form = document.getElementById('payLiabilityForm');

                form.action = `/liabilities/${liability.id}/pay`;

                document.getElementById('pay_liability_name').innerText = liability.name;
                document.getElementById('pay_liability_max').innerText = liability.amount;
                document.getElementById('pay_liability_amount').value = liability.amount; // Default to full pay
                document.getElementById('pay_liability_amount').setAttribute('max', liability.amount);

                modal.classList.remove('hidden');
            }

            function openEditLiabilityModal(liability) {
                const modal = document.getElementById('editLiabilityModal');
                const form = document.getElementById('editLiabilityForm');

                form.action = `/liabilities/${liability.id}`;

                document.getElementById('edit_liability_name').value = liability.name;
                document.getElementById('edit_liability_type').value = liability.type;
                document.getElementById('edit_liability_amount').value = liability.amount;

                modal.classList.remove('hidden');
            }
        </script>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
        </style>
    @endpush
@endsection