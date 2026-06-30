<div class="relative inline-block" id="edit-{{ $id }}">

  {{-- الزر --}}
  <button
    onclick="toggleEditDD('{{ $id }}')"
    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm cursor-pointer whitespace-nowrap">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
    </svg>
    Modifier
    <svg id="edit-chev-{{ $id }}" class="transition-transform duration-200" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
      <polyline points="6 9 12 15 18 9"/>
    </svg>
  </button>

  {{-- القائمة --}}
  <div
    id="edit-menu-{{ $id }}"
    class="hidden absolute left-0 top-full mt-1 z-50 min-w-[190px] bg-white border border-slate-200 rounded-xl shadow-xl py-1.5">

    <span class="block px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100 mb-1">
      Choisir la note à modifier
    </span>

    @foreach($ccs as $cc)
    <a href="{{ route('formateur.notes.edit', $cc->id) }}"
       class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 transition-colors no-underline">
      <span class="inline-flex items-center justify-center w-5 h-5 rounded-md bg-blue-50 text-blue-600 font-bold text-[10px] flex-shrink-0">
        {{ $loop->iteration }}
      </span>
      <span class="flex-1">{{ $cc->evaluation->nom ?? 'CC'.$loop->iteration }}</span>
      @if($cc->note !== null)
        <span class="text-[11px] font-mono font-semibold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
          {{ number_format($cc->note, 2) }}
        </span>
      @endif
    </a>
    @endforeach

  </div>
</div>