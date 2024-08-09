<?php

namespace App\Services;

use App\Models\Reminder;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ReminderService {
    private $db;
    private $reminderModel;

    public function __construct($db) {
        $this->db = $db;
        $this->reminderModel = new Reminder($db);
    }

    public function processReminders() {
        $reminders = $this->reminderModel->getPendingReminders();

        foreach ($reminders as $reminder) {
            if ($reminder['type'] === 'email') {
                $this->sendEmailReminder($reminder);
            } else {
                $this->sendNotification($reminder);
            }

            $this->reminderModel->markAsSent($reminder['id']);
        }
    }

    private function sendEmailReminder($reminder) {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'your_smtp_host';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_smtp_username';
            $mail->Password   = 'your_smtp_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('from@example.com', 'Mailer');
            $mail->addAddress($reminder['email']);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Event Reminder: ' . $reminder['event_title'];
            $mail->Body    = "This is a reminder for your event '{$reminder['event_title']}' starting at {$reminder['event_start']}.";

            $mail->send();
            error_log("Email sent to {$reminder['email']} for event {$reminder['event_title']}");
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    private function sendNotification($reminder) {
        // Implement in-app notification logic here
        // This could involve saving a notification to a database table
        // or using a push notification service
        error_log("Notification sent for event {$reminder['event_title']}");
    }
}