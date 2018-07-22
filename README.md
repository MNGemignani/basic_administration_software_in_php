# Basic Administration Software in PHP PDO with Prepared Statements

This is a simple administration software written in PHP 7 PDO with prepared statements to get data from database, a bit of javascript, with jQuery, including AJAX calls, basic CSS and heavy Bootstrap. Also I make use of `PHPmailer` for send emails in PHP and `pdf master` for pdf templates.

## Functionalities:

1. Login

2. Adds users and admins

3. Edit users and admins

4. Generate invoices

5. Show all invoices, with payment dates

6. Send email to users

7. Check if invoice due date is passed

8. Send warning email to customer

## Installation:

1. Clone the repository or download zip and add to your local server

2. Create database with name `barao` and import the sql files from `simple_admin.sql` folder

3. Change the database information if necessary at `core/Database.php`

4. Username: `gemignaniholland@gmail.com`

5. Password: `password`

6. If you want to use the email functionality you need to change inside `admin/User.php` the email function that uses `PHPmailer`, using your email provider and password (as sugestion, use gmail and disable the security from google).

## Information:

Please do not expect any help or bug fixing from me, this is only a trial module for learning proposes. I'm always open for tips or improvements in my code.