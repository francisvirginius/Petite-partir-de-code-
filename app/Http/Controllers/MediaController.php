<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Traits\BanqueImageHelper;

class MediaController extends Controller
{
    public function getMedias($offset = 1, string $langue = "fr")
    {
        //if ($langue!='fr'){$langue='en';}

        $arr = [];
        $medias = Article::select("articles.url",
            "articles.date_publication",
            "articles.titre",
            "articles.texte",
            "articles.metas",
            "bi.fichier",
            "p.nom",
            "p.prenom",
            'articles.id',
            "metas->lien as lien",
        )
            ->join('langues as l', 'l.id', '=', 'langue_id')
            ->leftJoin('banque_images as bi', 'bi.id', '=', 'image_id')
            ->leftJoin('personne_relations as pr', 'pr.type_id', '=', 'articles.id')
            ->leftJoin('personnes as p', 'p.id', '=', 'pr.personne_id')
            ->where('l.code', $langue)
            ->where('articles.type', "media")
            ->where('pr.type', "media") 
            ->whereJsonContains('metas->visible', '1')
            ->orderBy('date_publication', 'desc')
            ->offset(($offset * 16 )- 16)
            ->limit(16)
            ->get();
        BanqueImageHelper::buildFormats($medias);
     
        $arr[] = $medias;

        $number_Media = Article::select("articles.id")
            ->join('langues as l', 'l.id', '=', 'langue_id')
            ->leftJoin('banque_images as bi', 'bi.id', '=', 'image_id')
            ->leftJoin('personne_relations as pr', 'pr.type_id', '=', 'articles.id')
            ->leftJoin('personnes as p', 'p.id', '=', 'pr.personne_id')
            ->where('pr.type', "media") 
            ->where('l.code', $langue)
            ->where('articles.type', "media")
            ->whereJsonContains('metas->visible', '1')
            ->count();

        $arr[] = $number_Media;


        return response()->json($arr);
    }

    public function getMediasCount(string $langue = "fr")
    {

        $number_Media = Article::select("*")
            ->join('langues as l', 'l.id', '=', 'langue_id')
            ->leftJoin('banque_images as bi', 'bi.id', '=', 'image_id')
            ->join('personne_relations as pr', 'pr.type_id', '=', 'articles.id')
            ->join('personnes as p', 'p.id', '=', 'pr.personne_id')
            ->where('pr.type', "media")
            ->where('l.code', $langue)
            ->where('articles.type', "media")
            ->whereJsonContains('metas->visible', '1')
            ->count();

        $total_page = ceil($number_Media / 16);

        $arr = [];
        for ($i = 1; $i <= $total_page; $i++) {
            $arr[] = ["page" => $i, "code" => $langue];
        }

        return response()->json($arr);
    }

}
