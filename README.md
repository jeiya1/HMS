# Setup the root directory (required)

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
