<?php

namespace App\Exports;

use App\Models\Note;
use Maatwebsite\Excel\Concerns\FromCollection;

class NotesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return Note::with([
            'stagiaire',
            'groupe',
            'filiere',
            'module'
        ])->get()->map(function ($note) {

            return [

                'Nom' => $note->stagiaire->nom,

                'Prenom' => $note->stagiaire->prenom,

                'Groupe' => $note->groupe->nom,

                'Filiere' => $note->filiere->titre,

                'Module' => $note->module->titre,

                'CC1' => $note->cc1,

                'CC2' => $note->cc2,

                'CC3' => $note->cc3,

                'Examen Final' => $note->examen_final,

                'Moyenne' => $note->moyenne,

                'Resultat' => $note->resultat,
            ];
        });
    }
}