<?php
k
namespace App\Http\Controllers;
use App\Models\sendEmail;


use Illuminate\Http\Request;

class sendEmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $nom = $request->input('nom');
        $email = $request->input('email');
        $sendEmail = new sendEmail();
        $sendEmail->sendEmailAction('info@robert-schuman.eu','Fondation Robert Schuman',$email,$nom);
        return response()->json(['message' => 'Le formulaire a été soumis avec succès.']);
    }
}
