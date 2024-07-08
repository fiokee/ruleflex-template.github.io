<?php

/* =====================================================
 * Change this to the email you want the form to send to
 * ===================================================== */
$email_to = "you@company.pw";
$email_from = "webmaster@company.pw"; // must be different than $email_to
$email_subject = "Contact Form Submitted";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    function return_error($error) {
        echo json_encode(array('success' => 0, 'message' => $error));
        die();
    }

    // Check for empty required fields
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
        return_error('Please fill in all required fields.');
    }

    // Form field values
    $name = trim($_POST['name']); // required
    $email = trim($_POST['email']); // required
    $message = trim($_POST['message']); // required

    // Form validation
    $error_message = "";

    // Name validation
    $name_exp = "/^[a-z0-9 .\-]+$/i";
    if (!preg_match($name_exp, $name)) {
        $this_error = 'Please enter a valid name.';
        $error_message .= ($error_message == "") ? $this_error : "<br/>" . $this_error;
    }

    // Email validation
    $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($email_exp, $email)) {
        $this_error = 'Please enter a valid email address.';
        $error_message .= ($error_message == "") ? $this_error : "<br/>" . $this_error;
    }

    // If there are validation errors
    if (strlen($error_message) > 0) {
        return_error($error_message);
    }

    // Prepare email message
    $email_message = "Form details below.\n\n";

    function clean_string($string) {
        $bad = array("content-type", "bcc:", "to:", "cc:", "href");
        return str_replace($bad, "", $string);
    }

    $email_message .= "Name: " . clean_string($name) . "\n";
    $email_message .= "Email: " . clean_string($email) . "\n";
    $email_message .= "Message: " . clean_string($message) . "\n";

    // Create email headers
    $headers = 'From: ' . $email_from . "\r\n" .
        'Reply-To: ' . $email . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    // Send email
    if (mail($email_to, $email_subject, $email_message, $headers)) {
        echo json_encode(array('success' => 1, 'message' => 'Form submitted successfully.'));
    } else {
        echo json_encode(array('success' => 0, 'message' => 'An error occurred. Please try again later.'));
    }

} else {
    echo json_encode(array('success' => 0, 'message' => 'Invalid request method.'));
}
?>
