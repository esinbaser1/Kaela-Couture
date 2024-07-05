<?php
namespace Models;

use App\Database;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

require_once "variables.php";

class Contact 
{
    protected $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function sendEmail()
    {
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);

        $email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : null;
        $object = isset($data['object']) ? strip_tags($data['object']) : null;
        $message = isset($data['message']) ? strip_tags($data['message']) : null;

        if (empty($email) || empty($object) || empty($message)) {
            return ["success" => false, "message" => "All fields are required"];
        }

        try {
            $request = "INSERT INTO send_email (email, object, message) VALUES (?,?,?)";
            $pdo = $this->db->prepare($request);
            $pdo->execute([$email, $object, $message]);

            $mail = new PHPMailer(true); 
            $mail->isSMTP(); 
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USERNAME');
            $mail->Password = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587; 
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($email); 
            $mail->addAddress('hokablese@gmail.com'); 
            $mail->addReplyTo($email);

            $mail->isHTML(true);
            $mail->Subject = $object;
            $mail->Body = nl2br($message);

            if ($mail->send()) 
            {
                return ["success" => true, "message" => "Mail sent successfully"];
            } 
            else 
            {
                throw new PHPMailerException($mail->ErrorInfo);
            }
        } 
        catch (\PDOException $e) 
        {
            error_log("PDOException when sending email: " . $e->getMessage());
            return ["success" => false, "message" => "An error has occurred while processing your request"];
        } 
        catch (PHPMailerException $e) 
        {
            error_log("PHPMailerException when sending email: " . $e->getMessage());
            return ["success" => false, "message" => "Error sending email: " . $e->getMessage()];
        }
    }
}
