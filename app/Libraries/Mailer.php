<?php

namespace App\Libraries;

class ApplicantMailer
{
    public function send(string $to, string $subject, string $html): bool
    {
        $email = service('email');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($html);

        return $email->send();
    }

    public function applicationSubmitted(string $to, string $name, string $jobTitle): bool
    {
        $subject = 'Application Received';
        $html = view('emails/applicant/application_submitted', [
            'name' => $name,
            'jobTitle' => $jobTitle,
        ]);

        return $this->send($to, $subject, $html);
    }

    public function passwordReset(string $to, string $name, string $resetToken): bool
    {
        $resetUrl = site_url('reset-password?token=' . urlencode($resetToken));
        $subject = 'Reset Your Password';
        $html = view('emails/applicant/password_reset', [
            'name' => $name,
            'resetUrl' => $resetUrl,
        ]);

        return $this->send($to, $subject, $html);
    }

    public function applicationStatusUpdated(string $to, string $name, string $jobTitle, string $status): bool
    {
        $subject = 'Application Status Update';
        $html = view('emails/applicant/application_status_updated', [
            'name' => $name,
            'jobTitle' => $jobTitle,
            'status' => $status,
        ]);

        return $this->send($to, $subject, $html);
    }
}