<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Rapport des notes</title>
    <style>
        body{font-family:DejaVu Sans,sans-serif;color:#1f2937;font-size:11px;margin:24px;}
        h1{font-size:20px;margin:0 0 4px;color:#0f1f33;}
        .meta{color:#64748b;margin-bottom:18px;}
        .student{page-break-inside:avoid;margin-bottom:18px;border:1px solid #dbe4ee;border-radius:8px;overflow:hidden;}
        .student-head{background:#f1f5f9;padding:10px 12px;border-bottom:1px solid #dbe4ee;}
        .student-title{font-size:13px;font-weight:bold;color:#0f1f33;}
        .student-info{margin-top:3px;color:#64748b;}
        .rank{float:right;background:#1a3a5c;color:#fff;border-radius:14px;padding:3px 9px;font-weight:bold;}
        table{width:100%;border-collapse:collapse;}
        th{background:#f8fafc;color:#475569;text-transform:uppercase;font-size:9px;letter-spacing:.03em;}
        th,td{border-bottom:1px solid #edf2f7;padding:6px 8px;text-align:left;}
        tr:last-child td{border-bottom:0;}
        .num{text-align:center;font-weight:bold;}
        .summary{background:#f8fafc;font-weight:bold;}
        .empty{padding:28px;text-align:center;color:#94a3b8;border:1px solid #e2e8f0;border-radius:8px;}
    </style>
</head>
<body>
    <h1>Rapport des notes</h1>
    <div class="meta">
        Genere le {{ now()->format('d/m/Y H:i') }}
        @if($selectedFiliere) | Filiere: {{ $selectedFiliere->nom }} @endif
        @if($selectedGroupe) | Groupe: {{ $selectedGroupe->nom }} @endif
    </div>

    @forelse($results as $student)
        <section class="student">
            <div class="student-head">
                <span class="rank">#{{ $student['classement'] }}</span>
                <div class="student-title">{{ $student['full_name'] }}</div>
                <div class="student-info">
                    CIN: {{ $student['cin'] ?? '-' }} |
                    Groupe: {{ $student['groupe']?->nom ?? '-' }} |
                    Filiere: {{ $student['filiere']?->nom ?? '-' }}
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Evaluation</th>
                        <th class="num">Note</th>
                        <th class="num">Moy. module</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($student['modules'] as $moduleRow)
                        @foreach($moduleRow['notes'] as $note)
                            <tr>
                                <td>{{ $moduleRow['module']->titre ?? $moduleRow['module']->nom ?? '-' }}</td>
                                <td>{{ $note->evaluation?->nom ?? '-' }}</td>
                                <td class="num">{{ number_format((float) $note->note, 2) }}</td>
                                <td class="num">{{ number_format((float) $moduleRow['moyenne'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr class="summary">
                        <td colspan="3">Moyenne generale</td>
                        <td class="num">{{ number_format((float) $student['moyenne'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </section>
    @empty
        <div class="empty">Aucun resultat trouve pour les filtres selectionnes.</div>
    @endforelse
</body>
</html>
