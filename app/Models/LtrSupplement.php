<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LtrSupplement extends Model
{
    use HasFactory;

    /**
     * Get the picture of this article.
     */
    public function image()
    {
        return $this->hasOne(BanqueImage::class, 'id', 'id_image');
    }

    public function personne(){
        return DB::table('personne_relations')
            ->join('personnes', 'personnes.id', '=', 'personne_relations.personne_id')
            ->where('type_id', $this->id)
            ->where('type', 'supplement')
            ->get(['personnes.id', 'personnes.nom', 'personnes.prenom', 'personnes.photo', 'personnes.biographie_fr', 'personnes.biographie_en', 'personne_relations.role', 'personne_relations.ordre']);
    }

    public function rubrique()
    {
        return DB::table('rubrique_relations')
            ->join('rubriques', 'rubriques.id', '=', 'rubrique_relations.rubrique_id')
            ->where('rubrique_relations.type_id', $this->id)
            ->where('rubrique_relations.type', "supplement")
            ->get(['rubriques.id', 'rubriques.nom_fr', 'rubriques.nom_en', 'rubriques.illustration', 'rubriques.ordre']);
    }

    public function version()
    {
        return DB::table('ltr_supplements')
            ->where('id_lettre', $this->id_lettre)
            ->where('numero', $this->numero)
            ->where('visible', '1')
            ->get(['id', 'langue as code',]);
    }

    public function related()
    {
        $related = LtrSupplement::where('langue', $this->langue)
            ->where('id_type', $this->id_type)
            ->where('visible', '1')
            ->where('id', "!=", $this->id)
            ->limit(4)
            ->orderBy("id", "desc")
            ->get([
                'id',
                'langue',
                'id_image',
                'date_publication',
                'titre',
                DB::raw('SUBSTRING(`texte`, 1, 1000) as texte'),
            ]);

        foreach ($related as $data){
            $data->titre = "« " . rtrim(ltrim($data->titre, '"'), '"') . " »";
            $data->image = $data->image();
            $data->personne = $data->personne();
            $data->rubrique = $data->rubrique();
        }

        return $related;
    }

    public function pdf(){
        $pdf = DB::table('pdfs')
            ->where('pdfs.type_id', $this->id)
            ->where('pdfs.type', 'supplement')
            ->get(['pdfs.id', 'pdfs.url', 'pdfs.file', 'pdfs.langue_id']);
        if (!$pdf->isEmpty()) {
            return $pdf;
        }
        return null;
    }

    public function relatedTheme()
    {
        $rubriques = DB::table('rubrique_relations')
            ->join('rubriques', 'rubriques.id', '=', 'rubrique_relations.rubrique_id')
            ->where('rubrique_relations.type_id', $this->id)
            ->where('rubrique_relations.type', 'supplement')
            ->where('rubriques.visible', 1)
            ->get(['rubriques.id', 'rubriques.nom_fr', 'rubriques.nom_en', 'rubriques.illustration', 'rubriques.ordre']);

        return $rubriques;
    }
}
