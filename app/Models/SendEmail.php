<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailSender;

class sendEmail extends Model
{
    public function sendEmailAction($fromEmail, $fromNom, $toEmail, $toNom)
    {
        $configuration = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('SENDINBLUE_API_KEY'));
        $emailApi = new TransactionalEmailsApi(null, $configuration);

        // Créez un objet SendSmtpEmail pour définir les détails de l'email
        $email = new SendSmtpEmail();
        
        $senderData = array('name' => $fromNom, 'email' => $fromEmail);
        $sender = new SendSmtpEmailSender($senderData);

        $email['sender'] = $sender;
        $email['subject'] = 'Contact Schumman';
        $email['to'] = array(array('email' => 'francisvirginius@gmail.com', 'name' => 'francis'), array('email' => $toEmail, 'name' => $toNom));

        $html = view('emails.contact', ['user' => "Francis"])->render();
        $email['htmlContent'] = $html;

        try {
            // Envoyez l'email en utilisant la méthode sendTransacEmail de l'API
            $response = $emailApi->sendTransacEmail($email);
            // Vérifiez la réponse de l'API
            //if ($response->getCode() === 'success') {
            //    var_dump("L'email a été envoyé avec succès");
            //} else {
            //    var_dump("Il y a eu une erreur lors de l'envoi de l'email");
            //}
        } catch (\Exception $e) {
            var_dump("Il y a eu une erreur lors de l'envoi de l'email");
            var_dump("Voici l'erreur : " . $e);
        }
    }
}
