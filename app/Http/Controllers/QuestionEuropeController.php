<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\LtrSupplement;
use App\Traits\BanqueImageHelper;

class QuestionEurope extends Controller {

    public function getQuestionEuropePaths(){

        $question = LtrSupplement::where('id_type', 4)
            ->where('visible', '1')
            ->get([
                'id',
                "langue as code",
            ]);

        return response()->json($question);
    }

    public function getQuestionEurope($id = null) {
        if ($id == null) {
            return response()->json(array(
                'error' => 'Pas de ID',
            ), 404);
        }

        $question = LtrSupplement::where('id', $id)
            ->where('id_type', 4)
            ->first();

        if (!$question) {
            return response()->json(array(
                'error' => 'Not found',
            ), 404);
        }
        $question->personne = $question->personne();
        $question->rubrique = $question->rubrique();
        $question->image;
        $question->pdf = $question->pdf();
        $question->version = $question->version();
        $question->related = $question->related();
        $question->relatedTheme = $question->relatedTheme();
        $question->metas = null;
        $question->type = 'supplement';
        BanqueImageHelper::buildSingleFormats($question);
        return response()->json($question);
    }
}
