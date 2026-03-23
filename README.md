# Hotel Booking & Reservation System

A web-based hotel booking and reservation system built with XAMPP. This project handles room reservations, customer management, and sends booking confirmation emails.  

---

## **Requirements**

- PHP
- Apache (XAMPP recommended)  
- Composer  
- PHPMailer  
- PHP Dotenv  
- Tailwind CSS

---

## **Installation**

### **1. Clone the Project**
```bash
git clone <your-repo-url> C:/xampp/htdocs/HMS
```

### Setup the root directory (required)

EDIT `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

ADD this line 

```text
<VirtualHost *:80>
    ServerName hms.local
    DocumentRoot "C:/xampp/htdocs/HMS/public"

    <Directory "C:/xampp/htdocs/HMS/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

EDIT `C:\xampp\apache\conf\httpd.conf`

FIND this line

```text
#Include conf/extra/httpd-vhosts.conf
```

REMOVE the '#'

```text
Include conf/extra/httpd-vhosts.conf
```

OPEN in Notepad as Administrator `C:\Windows\System32\drivers\etc\hosts`

ADD this line

```
127.0.0.1 hms.local
```

RESTART XAMPP

### Install Dependencies

Use Composer to install required packages:

```bash
composer install
composer require phpmailer/phpmailer
composer require vlucas/phpdotenv
```

Use NPM to install TailwindCSS Cli

### Configure Environment Variables

Populate `.env` with these values:

```text
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM=your-email@example.com
MAIL_FROM_NAME="Hotel Management System"
```