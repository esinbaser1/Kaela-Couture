<?php
namespace Models;

use App\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Contact 
{
    protected $db;

    // Constructor to initialize the database connection
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Method to send email and store contact information
    public function sendEmail()
    {
        // Retrieve the raw input (JSON) sent in the HTTP request
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        // Validate and sanitize the input data (email, subject, and message)
        $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : null;
        $object = isset($data['object']) ? strip_tags($data['object']) : null;
        $message = isset($data['message']) ? strip_tags($data['message']) : null;

        // If any required field is missing or invalid, return an error message
        if (empty($email) || empty($object) || empty($message)) 
        {
            return ["success" => false, "message" => "All fields are required"];
        }

        try 
        {
            // Insert the contact details into the database
            $request = "INSERT INTO contact (email, object, message) VALUES (?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email, $object, $message]);

            // Configure PHPMailer to send an email
            $mail = new PHPMailer(true);  // Create a new PHPMailer instance
            $mail->isSMTP();  // Use SMTP for sending emails
            $mail->Host = 'smtp.gmail.com';  // Set the SMTP server (Gmail in this case)
            $mail->SMTPAuth = true;  // Enable SMTP authentication
            $mail->Username = 'hokablese@gmail.com';  // Your SMTP username (email address)
            $mail->Password = 'clfyuxcwcuddrgcu';  // Your SMTP password (use a real app-specific password)
            $mail->SMTPSecure = 'tls';  // Enable TLS encryption
            $mail->Port = 587;  // Set the SMTP port
            $mail->CharSet = 'UTF-8';  // Set the character encoding to UTF-8

            // Set up the email content
            $mail->setFrom($email);  // The email sender
            $mail->addAddress('hokablese@gmail.com');  // The recipient (your email address)
            $mail->addReplyTo($email);  // Add a reply-to address

            $mail->isHTML(true);  // Enable HTML in the email content
            $mail->Subject = $object;  // Set the email subject
            $mail->Body = nl2br($message);  // Set the email body, converting newlines to <br> for HTML formatting

            // Attempt to send the email
            if ($mail->send()) 
            {
                // Return success message if email is sent successfully
                return ["success" => true, "message" => "Mail sent successfully"];
            } 
            else 
            {
                // Throw an exception if the email fails to send
                throw new PHPMailerException($mail->ErrorInfo);
            }
        } 
        catch (\PDOException $e) 
        {
            // Return an error message
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        } 
        catch (PHPMailerException $e) 
        {
            // Return an error message
            return ["success" => false, "message" => "Error sending email: " . $e->getMessage()];
        }
    }
}
