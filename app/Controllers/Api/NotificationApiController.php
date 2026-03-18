<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\MessageTemplateRenderer;
class NotificationApiController extends BaseController
{
    // public function index()
    // {
    //     $applicant = service('request')->applicant;
    //     $rows = db_connect()->table('applicant_notifications')
    //         ->where('applicant_id', (int) $applicant['id'])
    //         ->orderBy('id', 'DESC')
    //         ->get()
    //         ->getResultArray();

    //     return $this->response->setJSON([
    //         'status' => true,
    //         'count' => count($rows),
    //         'data' => $rows,
    //     ]);
    // }
public function index()
    {
        return $this->response->setJSON([
            'message' => 'Email controller is working'
        ]);
    }

    public static function send($email=null,  $param = [])
    {   
        // dd($param['password']);
        
        $email = service('email');
        $to      ='vivar.gari@gmail.com';
        $subject = 'Test Forgot Password';
        $message = $param['password'];

  
        // Library('MessageTemplateRenderer');
        $template = MessageTemplateRenderer::renderByKey($param['template'], $param);
        // dd($template['body']);
        $email->setFrom('ggvivar@joy-nostalg.com', 'JN Career');
        $email->setTo($to);
        $email->setSubject($template['subject']);
        $email->setMessage($template['body']);

        helper(['document_template', 'email_template']);
            // $file = render_document_template(
            //     'new_employee_decision_form',
            //     [
            //         'lastname' => 'Vivar',
            //         'firstname' => 'Gari',
            //         'middlename' => 'Geronimo',
                    
            //         'jobname' => 'Backend Developer',
            //         'Group' => 'IMG',
            //         'Unit' => 'DDU',

            //     ]
            // );
        // dd(file_exists($file));
        // $email->attach($file);
        if ($email->send()) {
            return [
                'status'  => 'success',
                'message' => 'Email sent successfully',
            ];
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status'  => 'error',
            'message' => 'Failed to send email',
            'debug'   => $email->printDebugger(['headers']),
        ]);
    }
    public function send_attachment()
    {
    helper(['document_template', 'email_template']);
    $file = render_document_template(
        'new_employee_decision_form',
        [
            'lastname' => 'Vivar',
            'firstname' => 'Gari',
            'middlename' => 'Geronimo',
            
            'jobname' => 'Backend Developer',
            'Group' => 'IMG',
            'Unit' => 'DDU',

        ]
    );
    // send_template_email(
    //     'app_notif_v1',
    //     [
    //         'lastname' => 'Vivar',
    //         'firstname' => 'Gari',
    //         'middlename' => 'Geronimo',
    //     ],
    //     'vivar.gari@gmail.com',
    //     [$file]
    // );
        $email = service('email');
        // $to = $this->request->getPost('to');
        // $name = $this->request->getPost('name');
        // $message = $this->request->getPost('message');
        $to = "vivar.gari@gmail.com";
        // $name = 
        $message = "Tehis is a test";
        $email->setFrom('ggvivar@joy-nostalg.com', 'JN Career');
        $email->setTo('test@email.com');
        $email->setSubject('Document Attachment');
        $email->setMessage('Attached is the requested document.');

        // attach document
        $email->attach(WRITEPATH . $file);

        if ($email->send()) {
            return $this->response->setJSON([
                "status" => "success",
                "message" => "Email sent with attachment"
            ]);
        }

        return $this->response->setJSON([
            "status" => "error",
            "debug" => $email->printDebugger(['headers'])
        ]);
    }

    public function markRead($id)
    {
        $applicant = service('request')->applicant;

        db_connect()->table('applicant_notifications')
            ->where('id', (int) $id)
            ->where('applicant_id', (int) $applicant['id'])
            ->update([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s'),
            ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Notification marked as read.',
        ]);
    }
}

