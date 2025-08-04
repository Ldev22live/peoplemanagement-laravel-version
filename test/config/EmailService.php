<?php

namespace App;

class EmailService
{
    private $fromEmail;
    private $fromName;
    
    public function __construct(
        $fromEmail = 'noreply@peoplemanagementsystem.com', 
        $fromName = 'People Management System'
    ) {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }
    
    /**
     * Send an email to a person informing them they've been added to the system
     * 
     * @param array $person The person data
     * @return bool Whether the email was sent successfully
     */
    public function sendPersonRegistrationEmail($person)
    {
        // Set email subject
        $subject = 'You have been added to People Management System';
        
        // Set email headers
        $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
        // Create email body
        $body = "
            <html>
            <head>
                <title>Welcome to People Management System</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #a8daee; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .footer { font-size: 12px; text-align: center; margin-top: 20px; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Welcome to People Management System</h1>
                    </div>
                    <div class='content'>
                        <p>Hello {$person['name']} {$person['surname']},</p>
                        <p>We would like to inform you that your information has been added to the People Management System.</p>
                        <p>Your details are as follows:</p>
                        <ul>
                            <li>Name: {$person['name']} {$person['surname']}</li>
                            <li>Email: {$person['email']}</li>
                            <li>Mobile: {$person['mobile_number']}</li>
                        </ul>
                        <p>If you have any questions or need to update your information, please contact our support team.</p>
                        <p>Thank you for being part of our system!</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated message, please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
        // Send the email using PHP mail() function
        $result = mail($person['email'], $subject, $body, $headers);
        
        if (!$result) {
            // Log the error
            error_log('Email could not be sent using mail() function.');
            return false;
        }
        
        return true;
    }
}
