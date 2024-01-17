<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Middleware Throttle pour limiter le nombre de requêtes par IP (Protection DDOS)
Route::group([
    'middleware' => ThrottleRequestsWithIp::class,
], static function () {

    Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
        $request->user()->load('langueSite');
        return $request->user()->load('roleSite');
    });

   
    /* MEDIA   *va chercher tout les médias qui sont affilie à Rober shauman */
    Route::get('/media', [MediaController::class, 'getMediasCount']);
    Route::get('/media/{offset?}/{langue?}', [MediaController::class, 'getMedias']);

    /* QUESTION D'EUROPE  *Les questions sont en faite un article qui parle du sujet de la question*/
    Route::get('/question-europe', [QuestionEurope::class, 'getQuestionEuropePaths']);
    Route::get('/question-europe/{id?}', [QuestionEurope::class, 'getQuestionEurope']);

    //sendinblue  *l'api extern que j'ai utiliser pour envoyer des mail pour le formulaire de contacter 
    Route::post('/envoyer-email', [sendEmailController::class, 'sendEmail']);

    /* Contact  *une verification pour le formulaire de contact*/
    Route::get('/contact/captchaVerify/{token}', [ContactController::class, 'captchaVerify']);

// Partie HELPER
require __DIR__ . "/helper/forms.php";

