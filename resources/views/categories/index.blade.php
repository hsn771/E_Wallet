@extends('layouts.app')

@section('title', 'Categories')
@section('header', 'Categories Management')

@section('header_actions')
    <button onclick="document.getElementById('addCategoryModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg">
        <span>➕</span> New Category
    </button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($categories as $category)
        <div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl flex items-center gap-4 group hover:border-indigo-500/50 transition">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-inner border border-slate-600/50" style="background-color: {{ $category->color }}20; color: {{ $category->color }};">
                {{ $category->icon }}
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-white">{{ $category->name }}</h3>
                <p class="text-xs text-slate-400 capitalize">{{ $category->type }}</p>
                @if($category->user_id === null)
                    <p class="text-[10px] text-indigo-400 mt-1 uppercase tracking-wide">System Default</p>
                @endif
            </div>
            @if($category->user_id !== null)
            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="opacity-0 group-hover:opacity-100 p-2 text-slate-500 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
            @endif
        </div>
    @endforeach
</div>

<!-- Modal -->
<div id="addCategoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Create Category</h3>
            <button onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Icon (Emoji)</label>
                    <input type="text" name="icon" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                    <select name="type" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addCategoryModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium">Create</button>
            </div>
        </form>
    </div>
</div>
@endsection
