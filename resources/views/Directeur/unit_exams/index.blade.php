@extends('layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <div class="text-xl font-bold text-[#1a3a5c] tracking-tight">Examens d'unités</div>
        <div class="text-sm text-[#5a8aaa] mt-0.5">Paramétrage des poids par unité</div>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white border border-[#cde4f0] rounded-xl shadow-sm overflow-hidden">
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Unité</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Filière / Niveau</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Type</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Poids</th>
                <th class="px-4 py-2.5 text-left text-[11px] font-bold text-[#5a8aaa] uppercase tracking-wider bg-[#f0f7fc] border-b border-[#cde4f0]">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($unitExams as $exam)
                <tr class="hover:bg-[#f0f7fc] border-b border-slate-100 last:border-b-0 transition-colors duration-100">
                    <td class="px-4 py-3 text-[13.5px] text-slate-800 font-medium">{{ $exam->unite->nom ?? '-' }}</td>
                    <td class="px-4 py-3 text-[13px] text-[#5a8aaa]">
                        {{ $exam->unite->niveau->filiere->nom ?? '-' }}
                        <span class="text-slate-400">/</span>
                        {{ $exam->unite->niveau->nom ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-[13px] text-slate-700">{{ ucfirst($exam->type) }}</td>
                    <td class="px-4 py-3 text-[13px] text-slate-700">{{ $exam->poids }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('directeur.unit_exams.edit', $exam) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold hover:bg-slate-200 transition-all no-underline">
                            Modifier
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-12 px-4 text-slate-400">Aucun examen d'unité enregistré</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $unitExams->links() }}
</div>
@endsection
