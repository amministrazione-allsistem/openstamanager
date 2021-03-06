<?php

namespace API\App\v1;

use API\Interfaces\CreateInterface;
use API\Interfaces\RetrieveInterface;
use API\Resource;
use Modules\Emails\Mail;
use Modules\Emails\Template;
use Notifications\EmailNotification;

class RapportinoIntervento extends Resource implements RetrieveInterface, CreateInterface
{
    public function retrieve($request)
    {
        $database = database();
        $id_record = $request['id'];

        $template = Template::where('name', 'Rapportino intervento')->first();
        $module = $template->module;

        $body = $module->replacePlaceholders($id_record, $template['body']);
        $subject = $module->replacePlaceholders($id_record, $template['subject']);
        $email = $module->replacePlaceholders($id_record, '{email}');

        $prints = $database->fetchArray('SELECT id, title, EXISTS(SELECT id_print FROM em_print_template WHERE id_template = '.prepare($template['id']).' AND em_print_template.id_print = zz_prints.id) AS selected FROM zz_prints WHERE id_module = '.prepare($module->id).' AND enabled = 1');

        return [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
            'prints' => $prints,
            'read_notify' => $template->read_notify,
        ];
    }

    public function create($request)
    {
        $data = $request['data'];
        $id_record = $data['id'];

        $template = Template::where('name', 'Rapportino intervento')->first();
        $mail = Mail::build($this->getUser(), $template, $id_record);

        // Rimozione allegati predefiniti
        $mail->resetPrints();

        // Destinatari
        $receivers = $data['receivers'];
        foreach ($receivers as $receiver) {
            $mail->addReceiver($receiver['email'], $receiver['tipo']);
        }

        // Contenuti
        $mail->subject = $data['subject'];
        $mail->content = $data['body'];
        $mail->read_notify = $data['read_notify'];

        // Stampe da allegare
        $prints = $data['prints'];
        foreach ($prints as $print_id) {
            $mail->addPrint($print_id);
        }

        $mail->save();

        $email = EmailNotification::build($mail);
        try {
            $email_success = $email->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $email_success = false;
        }

        return [
            'sent' => $email_success,
        ];
    }
}
