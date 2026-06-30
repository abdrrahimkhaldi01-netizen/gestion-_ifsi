@if($valid)
    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-50 text-green-700 border border-green-200">
        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Validée
    </span>
@else
    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-700 border border-orange-200">
        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>En attente
    </span>
@endif