<?php

namespace Controllers;

use Models\ContactModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class ContactController 
{
    protected $model;

    public function __construct()
    {
        $this->model = new ContactModel();
    }

    public function sendEmail()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Retrieve and validate the email, object, and message from the request data
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : null;
        $object = isset($data['object']) ? strip_tags($data['object']) : null;
        $message = isset($data['message']) ? strip_tags($data['message']) : null;
        $userId = isset($data['userId']) ? $data['userId'] : null;

        // Check if the object and message fields are not empty
        if (empty($object) || empty($message)) {
            return ["success" => false, "message" => "Subject and message are required"];
        }

        // If the user is logged in, retrieve the user's email from the database using their userId
        if ($userId) {
            $userEmail = $this->model->getUserEmailById($userId);

            // If a user email is found, use it instead of the one provided in the request
            if ($userEmail) {
                $email = $userEmail;
            }
        }

        // If the email is still empty or invalid, return an error
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "A valid email is required"];
        }

        try {
            // Insert the contact message into the database
            $rowCount = $this->model->addContactMessage($email, $object, $message, $userId);

            // Check if the message was successfully inserted
            if ($rowCount > 0) {
                // Configure PHPMailer to send the email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'hokablese@gmail.com';
                $mail->Password = 'clfyuxcwcuddrgcu';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                // Set up the email content
                $mail->setFrom($email);
                $mail->addAddress('hokablese@gmail.com');
                $mail->addReplyTo($email);
                $mail->isHTML(true);
                $mail->Subject = $object;
                $mail->Body = nl2br($message);

                // Attempt to send the email
                if ($mail->send()) {
                    return ["success" => true, "message" => "Email sent successfully"];
                } else {
                    throw new PHPMailerException($mail->ErrorInfo);
                }
            } else {
                return ["success" => false, "message" => "Error occurred while saving the message"];
            }
        } catch (\PDOException $e) {
            return ["success" => false, "message" => "Database error: " . $e->getMessage()];
        } catch (PHPMailerException $e) {
            return ["success" => false, "message" => "Error sending email: " . $e->getMessage()];
        }
    }
}
