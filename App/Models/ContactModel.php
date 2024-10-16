<?php
namespace Models;

use App\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class ContactModel 
{
    protected $db;

   // Initializes the database connection 
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection(); 
    }

    // Method to send email and store contact information
    public function sendEmail()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Retrieve and validate the email, object, and message from the request data
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : null;
        $object = isset($data['object']) ? strip_tags($data['object']) : null;
        $message = isset($data['message']) ? strip_tags($data['message']) : null;
        $userId = isset($data['userId']) ? $data['userId'] : null; // Retrieve userId if the user is logged in

        // Check if the object and message fields are not empty
        if (empty($object) || empty($message)) 
        {
            return ["success" => false, "message" => "Subject and message are required"];
        }

        // If the user is logged in, retrieve the user's email from the database using their userId
        if ($userId) 
        {
            $request = "SELECT email FROM user WHERE id = ?";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$userId]);
            $userEmail = $pdo->fetchColumn(); // Fetch the user's email from the database

            // If a user email is found, use it instead of the one provided in the request
            if ($userEmail) {
                $email = $userEmail;
            }
        }

        // If the email is still empty return an error
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            return ["success" => false, "message" => "A valid email is required"];
        }

        try 
        {
            // Insert the contact message into the database
            $request = "INSERT INTO contact (email, object, message, user_id) VALUES (?,?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email, $object, $message, $userId]);

            // Configure PHPMailer to send the email
            $mail = new PHPMailer(true);
            $mail->isSMTP(); // Use SMTP for email
            $mail->Host = 'smtp.gmail.com'; // SMTP server host
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'hokablese@gmail.com'; // SMTP username
            $mail->Password = 'clfyuxcwcuddrgcu'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Use TLS encryption
            $mail->Port = 587; // SMTP port
            $mail->CharSet = 'UTF-8'; // Set character encoding to UTF-8

            // Set up the email content
            $mail->setFrom($email); // The email sender
            $mail->addAddress('hokablese@gmail.com'); // The email recipient
            $mail->addReplyTo($email); // Set the reply-to email address
            $mail->isHTML(true); // Enable HTML in the email
            $mail->Subject = $object; // The subject of the email
            $mail->Body = nl2br($message); // The body of the email, converting newlines to <br> tags

            // Attempt to send the email
            if ($mail->send()) 
            {
                return ["success" => true, "message" => "Email sent successfully"];
            } 
            else 
            {
                // Throw an exception if the email fails to send
                throw new PHPMailerException($mail->ErrorInfo);
            }
        } 
        catch (\PDOException $e) 
        {
            // Return an error message if there is a database error
            return ["success" => false, "message" => "Error occurred while saving the message"];
        } 
        catch (PHPMailerException $e) 
        {
            // Return an error message if there is an error sending the email
            return ["success" => false, "message" => "Error sending email: " . $e->getMessage()];
        }
    }
}
