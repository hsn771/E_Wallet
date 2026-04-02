@extends('layouts.app')

@section('title', 'Assets')
@section('header', 'My Assets')

@section('header_actions')
    <button onclick="document.getElementById('addAssetModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-500/25">
        <span>📈</span> Add Asset
    </button>
@endsection

@section('content')
<div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl">🏦</div>
        <div>
            <p class="text-slate-400 font-medium">Total Assets Value</p>
            <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
                {{ number_format($assets->sum('value'), 0) }} <span class="text-lg text-slate-500">{{ auth()->user()->currency }}</span>
            </h2>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($assets as $asset)
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-lg group hover:border-emerald-500/50 transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $asset->name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-xs text-slate-400 uppercase tracking-wider">{{ str_replace('_', ' ', $asset->type) }}</p>
                        @if($asset->type === 'accounts_receivable' && $asset->source)
                            <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                            <p class="text-xs text-emerald-400 font-medium">via {{ $asset->source->name ?? 'Deleted Source' }}</p>
                        @endif
                        <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                        <p class="text-xs text-slate-500">{{ $asset->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="openEditAssetModal({{ json_encode($asset) }})" class="opacity-0 group-hover:opacity-100 p-1 text-slate-500 hover:text-indigo-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </button>
                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 text-slate-500 hover:text-red-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="mt-4">
                <h2 class="text-2xl font-bold text-emerald-400">
                    {{ number_format($asset->value, 0) }}
                </h2>
            </div>
        </div>
    @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 opacity-50">
            <div class="text-5xl mb-4">🏛️</div>
            <p class="text-slate-400">No assets recorded yet. Start building your portfolio!</p>
        </div>
    @endforelse
</div>

<!-- Add Asset Modal -->
<div id="addAssetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full"></div>
        <div class="flex justify-between items-center mb-6 relative z-10">
            <h3 class="text-xl font-bold text-white">Add Asset</h3>
            <button onclick="document.getElementById('addAssetModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form action="{{ route('assets.store') }}" method="POST" class="relative z-10">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white placeholder-slate-600" placeholder="e.g. City Bank Savings">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                    <select name="type" id="add_asset_type" onchange="toggleSourceFields('add')" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Balance</option>
                        <option value="property">Property</option>
                        <option value="investment">Investment</option>
                        <option value="accounts_receivable">Accounts Receivable</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div id="add_source_selection" style="display: none;" class="grid grid-cols-2 gap-3 p-4 bg-slate-900/50 rounded-2xl border border-slate-700/50">
                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Source Type</label>
                        <select name="source_type" id="add_source_type" onchange="updateSourceOptions('add')" class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-3 py-2 text-sm text-white">
                            <option value="wallet">Wallet</option>
                            <option value="asset">Asset (Bank)</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Target Account</label>
                        <select name="source_id" id="add_source_id" class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-3 py-2 text-sm text-white">
                            <!-- Options populated by JS -->
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Value ({{ auth()->user()->currency ?? 'BDT' }})</label>
                    <input type="number" step="0.01" name="value" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                <button type="button" onclick="document.getElementById('addAssetModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium shadow-lg shadow-emerald-500/25 transition">Save Asset</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Asset Modal -->
<div id="editAssetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-500/10 blur-3xl rounded-full"></div>
        <div class="flex justify-between items-center mb-6 relative z-10">
            <h3 class="text-xl font-bold text-white">Edit Asset</h3>
            <button onclick="document.getElementById('editAssetModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form id="editAssetForm" method="POST" class="relative z-10">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" id="edit_asset_name" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                    <select name="type" id="edit_asset_type" onchange="toggleSourceFields('edit')" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Balance</option>
                        <option value="property">Property</option>
                        <option value="investment">Investment</option>
                        <option value="accounts_receivable">Accounts Receivable</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div id="edit_source_selection" style="display: none;" class="grid grid-cols-2 gap-3 p-4 bg-slate-900/50 rounded-2xl border border-slate-700/50">
                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Source Type</label>
                        <select name="source_type" id="edit_source_type" onchange="updateSourceOptions('edit')" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-3 py-2 text-sm text-white">
                            <option value="wallet">Wallet</option>
                            <option value="asset">Asset (Bank)</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-xs font-medium text-slate-500 mb-1 uppercase tracking-wider">Target Account</label>
                        <select name="source_id" id="edit_source_id" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-3 py-2 text-sm text-white">
                            <!-- Options populated by JS -->
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Value ({{ auth()->user()->currency ?? 'BDT' }})</label>
                    <input type="number" step="0.01" name="value" id="edit_asset_value" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                <button type="button" onclick="document.getElementById('editAssetModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg shadow-indigo-500/25 transition">Update Asset</button>
            </div>
        </form>
    </div>
</div>

<script>
    const wallets = @json($wallets);
    const bankAssets = @json($bankAssets);

    function toggleSourceFields(mode) {
        const typeSelect = document.getElementById(`${mode}_asset_type`);
        const sourceSelection = document.getElementById(`${mode}_source_selection`);
        
        if (typeSelect.value === 'accounts_receivable') {
            sourceSelection.style.display = 'grid';
            updateSourceOptions(mode);
        } else {
            sourceSelection.style.display = 'none';
        }
    }

    function updateSourceOptions(mode, selectedId = null) {
        const sourceType = document.getElementById(`${mode}_source_type`).value;
        const sourceIdSelect = document.getElementById(`${mode}_source_id`);
        sourceIdSelect.innerHTML = '';
        
        let options = sourceType === 'wallet' ? wallets : bankAssets;
        
        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.id;
            option.textContent = opt.name;
            if (selectedId && opt.id == selectedId) {
                option.selected = true;
            }
            sourceIdSelect.appendChild(option);
        });
    }

    function openEditAssetModal(asset) {
        const modal = document.getElementById('editAssetModal');
        const form = document.getElementById('editAssetForm');
        
        // Update form action URL structure: /assets/{id}
        form.action = `/assets/${asset.id}`;
        
        // Fill form fields
        document.getElementById('edit_asset_name').value = asset.name;
        document.getElementById('edit_asset_type').value = asset.type;
        document.getElementById('edit_asset_value').value = asset.value;
        
        if (asset.type === 'accounts_receivable') {
            document.getElementById('edit_source_selection').style.display = 'grid';
            if (asset.source_type) {
                document.getElementById('edit_source_type').value = asset.source_type;
            }
            updateSourceOptions('edit', asset.source_id);
        } else {
            document.getElementById('edit_source_selection').style.display = 'none';
        }
        
        modal.classList.remove('hidden');
    }
</script>
@endsection
