##gsfReim
GSF Reimbursement Application

##Setup
1. Edit config/config.php and set the base url the site will sit on as well as a random string in $config['encryption_key'].
2. Edit config/database.php and enter the db credentials
3. Import the gsfReim.sql file and adminSettings.sql file into the database you created, this will create the initial tables and set the base settings.

##Authentication
Originally, this was written in a way that it only worked with CROWD. I have now written an internal authentication mechanism.

###To use the internal auth:
1. Make sure that in config/config.php `$config['AUTH_METHOD']` is set to `"INTERNAL"`. While you are in there, set an admin password in `$config['ADMIN_PASSWORD']`.
2. Point your browser (or cURL) to <base_url>/home/createInternalAuth, or run
```php
    php /path/to/site/index.php home createInternalAuth
```
This creates the initial users table (not created by default because :effort:) and creates the admin account with full permissions.

That is it! You should now be able to login using the username `admin` and whatever password you set (default is `asdqwe123`).
