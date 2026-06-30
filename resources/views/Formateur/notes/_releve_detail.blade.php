@php
    /* ── Calcul Moy. Unités ── */
    $sumP = 0; $sumC = 0;
    $unitesRows = [];
    foreach($uniteNames as $u) {
        $moy  = $res['unites_moy'][$u] ?? null;
        $coef = (float) ($examRows->first(fn($r) => ($r['unite']->nom ?? '') === $u)['coef'] ?? 1);
        $pond = $moy !== null ? round($moy * $coef, 2) : null;
        if ($moy !== null) { $sumP += $moy * $coef; $sumC += $coef; }
        $unitesRows[] = ['nom' => $u, 'moy' => $moy, 'coef' => $coef, 'pond' => $pond];
    }
    $moyUnites  = $sumC > 0 ? round($sumP / $sumC, 2) : null;
    $partUnites = $moyUnites !== null ? round($moyUnites * 0.80, 2) : null;
    $notePfe    = $res['note_pfe'] ?? null;
    $partPfe    = $notePfe !== null ? round($notePfe * 0.20, 2) : null;
    $moyGen     = $res['moy_gen'] ?? null;
    $mention    = $res['mention'] ?? null;

    $stagNom    = $res['stagiaire']->nom . ' ' . $res['stagiaire']->prenom;
    $filiere    = $res['stagiaire']->groupe?->niveau?->filiere?->nom ?? '—';
    $niveau     = $res['stagiaire']->groupe?->niveau?->nom ?? '—';
    $groupe     = $res['stagiaire']->groupe?->nom ?? '—';
    $annee      = date('Y') . '/' . ((int)date('Y') + 1);

    $initials   = strtoupper(substr($res['stagiaire']->prenom,0,1).substr($res['stagiaire']->nom,0,1));
    $colors     = ['#dbeafe|#1e40af','#d1fae5|#065f46','#ede9fe|#4c1d95','#ffedd5|#9a3412','#fce7f3|#831843'];
    $color      = $colors[$res['stagiaire']->id % count($colors)];
    [$bg, $fg]  = explode('|', $color);

    $fmtCoef = fn($v) => (int)$v == $v ? (int)$v : $v;
@endphp

<div class="rld-wrap" id="rld-{{ $res['stagiaire']->id }}">

    {{-- ══ HEADER ══ --}}
    <div class="rld-header">
        <div class="rld-header-left">
            <div class="rld-avatar" style="background:{{ $bg }};color:{{ $fg }};">{{ $initials }}</div>
            <div>
                <div class="rld-name">{{ $stagNom }}</div>
                <div class="rld-meta">{{ $filiere }} &nbsp;·&nbsp; {{ $groupe }} &nbsp;·&nbsp; {{ $annee }}</div>
            </div>
        </div>
        <div class="rld-header-actions">
            <button class="rld-btn-print" onclick="rldPrint({{ $res['stagiaire']->id }})">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Imprimer / PDF
            </button>
            <button class="rld-btn-close" onclick="rldClose()">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Fermer
            </button>
        </div>
    </div>

    {{-- ══ BODY (printable) ══ --}}
    <div class="rld-body" id="rld-print-{{ $res['stagiaire']->id }}">

        {{-- ── INFO STAGIAIRE ── --}}
        <div class="rv-info-grid">
            <div class="rv-info-card">
                <div class="rv-info-label">Stagiaire</div>
                <div class="rv-info-val">{{ $stagNom }}</div>
            </div>
            <div class="rv-info-card">
                <div class="rv-info-label">Filière</div>
                <div class="rv-info-val">{{ $filiere }}</div>
            </div>
            <div class="rv-info-card">
                <div class="rv-info-label">Niveau</div>
                <div class="rv-info-val">{{ $niveau }}</div>
            </div>
            <div class="rv-info-card">
                <div class="rv-info-label">Groupe</div>
                <div class="rv-info-val">{{ $groupe }}</div>
            </div>
            <div class="rv-info-card">
                <div class="rv-info-label">Année</div>
                <div class="rv-info-val">{{ $annee }}</div>
            </div>
        </div>

        {{-- ── TABLEAU UNITÉS ── --}}
        <div class="rv-section">
            <div class="rv-section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                Détail par unité
            </div>
            <table class="rv-table">
                <thead>
                    <tr>
                        <th>Unité</th>
                        <th class="tc">Moyenne</th>
                        <th class="tc">Coef.</th>
                        <th class="tc">Note Pond.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unitesRows as $ur)
                    <tr>
                        <td class="rv-td-name">{{ $ur['nom'] }}</td>
                        <td class="tc">
                            @if($ur['moy'] !== null)
                                <span class="rnp {{ $ur['moy'] >= 10 ? 'rnp-ok' : 'rnp-fail' }}">{{ number_format($ur['moy'],2) }}</span>
                            @else
                                <span class="rndash">—</span>
                            @endif
                        </td>
                        <td class="tc rv-coef">{{ $fmtCoef($ur['coef']) }}</td>
                        <td class="tc">
                            @if($ur['pond'] !== null)
                                <span class="rnp rnp-neutral">{{ number_format($ur['pond'],2) }}</span>
                            @else
                                <span class="rndash">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="rv-total-label">Total</td>
                        <td></td>
                        <td class="tc rv-coef rv-total-coef">{{ $fmtCoef($sumC) }}</td>
                        <td class="tc">
                            <span class="rnp rnp-total">{{ number_format($sumP,2) }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- ── CALCUL BREAKDOWN ── --}}
        <div class="rv-section">
            <div class="rv-section-title">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="12" y2="14"/></svg>
                Récapitulatif du calcul
            </div>
            <div class="rv-calc-box">
                <div class="rv-calc-row">
                    <span class="rv-calc-label">Moyenne des unités</span>
                    <span class="rv-calc-val {{ $moyUnites !== null && $moyUnites >= 10 ? 'rvc-ok' : 'rvc-fail' }}">
                        {{ $moyUnites !== null ? number_format($moyUnites,2) : '—' }}
                    </span>
                </div>
                <div class="rv-calc-row rv-calc-row-sub">
                    <span class="rv-calc-label">Part unités (80%)</span>
                    <span class="rv-calc-val">{{ $partUnites !== null ? number_format($partUnites,2) : '—' }}</span>
                </div>
                @if($notePfe !== null)
                <div class="rv-calc-row rv-calc-sep">
                    <span class="rv-calc-label">Note PFE</span>
                    <span class="rv-calc-val {{ $notePfe >= 10 ? 'rvc-ok' : 'rvc-fail' }}">{{ number_format($notePfe,2) }}</span>
                </div>
                <div class="rv-calc-row rv-calc-row-sub">
                    <span class="rv-calc-label">Part PFE (20%)</span>
                    <span class="rv-calc-val">{{ $partPfe !== null ? number_format($partPfe,2) : '—' }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- ── MOYENNE GÉNÉRALE ── --}}
        <div class="rv-moy-gen-box {{ $moyGen !== null && $moyGen >= 10 ? 'rv-mg-ok' : 'rv-mg-fail' }}">
            <div class="rv-mg-left">
                <div class="rv-mg-label">MOYENNE GÉNÉRALE</div>
                @if($moyGen !== null)
                <div class="rv-mg-formula">
                    @if($partUnites !== null)({{ number_format($moyUnites,2) }} × 0.80)@endif
                    @if($partPfe !== null) + ({{ number_format($notePfe,2) }} × 0.20)@endif
                    = <strong>{{ number_format($moyGen,2) }}</strong>
                </div>
                @endif
            </div>
            <div class="rv-mg-score">
                {{ $moyGen !== null ? number_format($moyGen,2) : '—' }}
                <span class="rv-mg-denom">/ 20</span>
            </div>
            @if($mention)
            <div class="rv-mg-mention">
                <span class="mention-badge mention-{{ Str::slug($mention['label']) }}">{{ $mention['label'] }}</span>
            </div>
            @endif
        </div>

    </div>{{-- end rld-body --}}
</div>

<style>
/* ── INFO GRID ── */
.rv-info-grid{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;}
.rv-info-card{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px 14px;min-width:110px;flex:1;}
.rv-info-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:3px;}
.rv-info-val{font-size:13px;font-weight:600;color:#1e293b;}

/* ── SECTION ── */
.rv-section{margin-bottom:18px;}
.rv-section-title{display:flex;align-items:center;gap:7px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:10px;padding-bottom:7px;border-bottom:1px solid #f1f5f9;}

/* ── TABLE ── */
.rv-table{width:100%;border-collapse:collapse;font-size:12.5px;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;}
.rv-table thead tr{background:#f1f5f9;}
.rv-table th{padding:8px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;border-bottom:1.5px solid #e2e8f0;text-align:left;}
.rv-table th.tc{text-align:center;}
.rv-table td{padding:9px 12px;border-bottom:1px solid #f1f5f9;color:#334155;}
.rv-table tbody tr:last-child td{border-bottom:none;}
.rv-table tfoot td{background:#f8fafc;border-top:2px solid #e2e8f0;padding:9px 12px;font-weight:700;color:#0f1f33;}
.tc{text-align:center;}
.rv-td-name{font-weight:500;color:#1e293b;}
.rv-coef{color:#64748b;font-size:12px;}
.rv-total-label{font-weight:700;color:#475569;font-size:12px;}
.rv-total-coef{font-weight:700;}

/* ── NOTE PILLS (relevé) ── */
.rnp{display:inline-flex;align-items:center;justify-content:center;min-width:46px;height:22px;padding:0 7px;border-radius:20px;font-size:11.5px;font-weight:700;font-family:'DM Mono',monospace;border:1.5px solid;}
.rnp-ok     {background:#f0fdf4;color:#15803d;border-color:#86efac;}
.rnp-fail   {background:#fff1f2;color:#be123c;border-color:#fda4af;}
.rnp-neutral{background:#f1f5f9;color:#475569;border-color:#cbd5e1;}
.rnp-total  {background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;min-width:54px;height:24px;font-size:12px;}
.rndash     {color:#cbd5e1;}

/* ── CALCUL BOX ── */
.rv-calc-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;}
.rv-calc-row{display:flex;align-items:center;justify-content:space-between;padding:9px 14px;border-bottom:1px dashed #e2e8f0;}
.rv-calc-row:last-child{border-bottom:none;}
.rv-calc-row-sub{background:#fff;padding-left:26px;}
.rv-calc-sep{border-top:1px solid #e2e8f0;}
.rv-calc-label{font-size:12.5px;color:#475569;}
.rv-calc-val{font-size:13px;font-weight:700;font-family:'DM Mono',monospace;color:#1e293b;}
.rvc-ok  {color:#15803d;}
.rvc-fail{color:#be123c;}

/* ── MOY. GÉNÉRALE BOX ── */
.rv-moy-gen-box{display:flex;align-items:center;gap:16px;padding:18px 22px;border-radius:12px;margin-top:6px;}
.rv-mg-ok  {background:linear-gradient(135deg,#1a3a5c,#2563a8);}
.rv-mg-fail{background:linear-gradient(135deg,#7f1d1d,#b91c1c);}
.rv-mg-left{flex:1;}
.rv-mg-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.6);margin-bottom:4px;}
.rv-mg-formula{font-size:11px;color:rgba(255,255,255,.55);}
.rv-mg-formula strong{color:#fff;font-family:'DM Mono',monospace;}
.rv-mg-score{font-size:32px;font-weight:700;font-family:'DM Mono',monospace;color:#fff;white-space:nowrap;}
.rv-mg-denom{font-size:16px;font-weight:400;opacity:.5;margin-left:3px;}
.rv-mg-mention{flex-shrink:0;}
</style>