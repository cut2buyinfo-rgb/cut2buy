<?php
// File: /controllers/ContactController.php
// Description: Controller for handling the contact page.

// --- 1. Basic Setup ---
// No complex database queries are needed for a simple contact page display.
// We just need to set the title and load the view.

$pageTitle = "Contact Us";

// --- 2. Load the View ---
// This line tells the application to display the contact form's HTML structure.
// You will need to create the corresponding 'contact.view.php' file in your 'views' directory.
require 'views/contact.view.php';

?>