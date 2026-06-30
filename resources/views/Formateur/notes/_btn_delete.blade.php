@php
    $rowId = $rowId ?? 'row';
    $ccs   = collect($ccs ?? []);
@endphp

{{-- Hidden DELETE form (outside any parent form — safe here since we're in a table) --}}
<form
    id="del-confirm-form-{{ $rowId }}"
    method="POST"
    action=""
    class="hidden">
    @csrf
    @method('DELETE')
</form>

<div class="relative inline-block" id="del-{{ $rowId }}">

    {{-- Toggle button --}}
    <button
        type="button"
        onclick="toggleDelDD('{{ $rowId }}')"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 border border-red-200
               text-red-600 text-xs font-semibold hover:bg-red-100 hover:border-red-300
               transition-all shadow-sm cursor-pointer whitespace-nowrap">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="3 6 5 6 21 6"/>
            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            <path d="M10 11v6M14 11v6"/>
            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
        </svg>
        Supprimer
        <svg id="del-chev-{{ $rowId }}" class="transition-transform duration-200"
             width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </button>

    {{-- Dropdown list of CCs --}}
    <div
        id="del-menu-{{ $rowId }}"
        class="hidden absolute left-0 top-full mt-1 z-50 min-w-[190px] bg-white border
               border-slate-200 rounded-xl shadow-xl py-1.5">

        <span class="block px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider
                     text-slate-400 border-b border-slate-100 mb-1">
            Supprimer la note
        </span>

        @forelse($ccs as $cc)
            <div
                onclick="showDelConfirm('{{ $rowId }}', '{{ addslashes($cc->evaluation->nom ?? 'CC'.($loop->index + 1)) }}', {{ $cc->id }})"
                class="flex items-center gap-2 px-3 py-2 text-xs text-red-600
                       hover:bg-red-50 transition-colors cursor-pointer">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-md
                             bg-red-50 text-red-500 font-bold text-[10px] flex-shrink-0">
                    {{ $loop->iteration }}
                </span>
                <span class="flex-1">{{ $cc->evaluation->nom ?? 'CC'.($loop->index + 1) }}</span>
                @if($cc->note !== null)
                    <span class="text-[11px] font-mono font-semibold text-slate-400
                                 bg-slate-100 px-1.5 py-0.5 rounded">
                        {{ number_format($cc->note, 2) }}
                    </span>
                @endif
            </div>
        @empty
            <div class="px-3 py-3 text-xs text-slate-400 text-center">Aucune note disponible</div>
        @endforelse
    </div>

    {{-- Confirmation box --}}
    <div
        id="del-confirm-{{ $rowId }}"
        class="hidden absolute left-0 top-full mt-1 z-50 w-60 bg-white
               border border-red-200 rounded-xl shadow-xl p-4">

        <div class="flex items-start gap-2 mb-3">
            <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-700 mb-0.5">Supprimer cette note ?</p>
                <span id="del-confirm-lbl-{{ $rowId }}" class="text-[11px] text-slate-400"></span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            {{-- Annuler --}}
            <button
                type="button"
                onclick="closeAllDD()"
                class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 bg-white
                       text-slate-600 text-xs font-semibold hover:bg-slate-50 transition-all cursor-pointer">
                Annuler
            </button>

            {{-- Confirmer — triggers the hidden form via JS, NO nested form --}}
            <button
                type="button"
                onclick="submitDelForm('{{ $rowId }}')"
                class="flex-1 px-3 py-1.5 rounded-lg bg-red-600 text-white
                       text-xs font-semibold hover:bg-red-700 transition-all cursor-pointer">
                Confirmer
            </button>
        </div>
    </div>

</div>