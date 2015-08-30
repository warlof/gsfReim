##gsfReim
GSF Reimbursement Application

##Setup
1. Edit `application/config/config.php` and set the base url the site will sit on as well as a random string in `$config['encryption_key']`.
2. Edit `application/config/database.php` and enter the db credentials
3. Import the `gsfReim.sql` file into the database you created, this will create the initial tables and set the base settings. **NOTE** If you do not have root on your database instance, you need to change the definer in the sql file. Something like:
```
sed -i 's/DEFINER=[^*]*\*/\*/g' gsfReim.sql
```
4. You need to create an .htaccess file containing the following and place it in the root:
```
RewriteEngine on
RewriteCond $1 !^(index\.php|assets|images|favicon.ico|robots\.txt)
RewriteRule ^(.*)$ /index.php/$1 [L]
```

##Authentication
Originally, this was written in a way that it only worked with CROWD. I have now written an internal authentication mechanism.

###To use the internal auth:
1. Make sure that in `application/config/config.php` `$config['AUTH_METHOD']` is set to `"INTERNAL"`. While you are in there, set an admin password in `$config['ADMIN_PASSWORD']`.
2. Point your browser (or cURL) to <base_url>/home/createAdmin, or run
```php
    php /path/to/site/index.php home createAdmin
```
This will create the initial admin account with full permissions. If the account already exists, then nothing will happen.

That is it! You should now be able to login using the username `admin` and whatever password you set (default is `asdqwe123`).

##Configuration
Not a lot of configuration is needed, aside from the initial population of payout types, payouts, and regions.

###Payouts
`Admin -> Payout Types`
These are the different types of payouts that can be used for each ship. For example, GSF has `Strategic [ST]` and `Peacetime [PT]`. Add all of the payout types you want on this page.

After payout types have been added, you can add the actual payout values. To do this, go to `Admin -> Payout Management`. This page is pretty easy to use, and relatively self-explanatory, but here is what you do:
    1. In the `Type Name` box, begin typing the name of a ship you would like to add a payout for. It has autocompletion, but requires 3 characters to autocomplete.
    2. Select the payout type from the `Payout Type` dropdown. This uses the payout types you created earlier.
    3. The last step is to add the payout amount (eg. 10000000).
Once you have done this, you will now have functioning payouts!

###Regions
`Admin -> Region Management`
These regions are peacetime specific regions. What it is used for is determining whether a loss was in a peacetime eligible region. When a loss is submitted, if the loss occurred in a region that is in this list, a flag will appear when the loss is paid out stating that it is eligible for peacetime. This is relatively minor, but was done to help increase efficiency.

###Preferences
`Admin -> Preferences`
These are the admin settings. You shouldn't need to change a whole lot here, aside from initial config. Here is a quick overview of what each setting does:
* `ptCap` - This is pretty self-explanatory. This is the cap for peacetime payouts. This number is included in determining whether someone is eligible for peacetime reimbursement or not. Default: **1000000000**
* `maxDayDiff` - This is used to set how old a loss can be. Default: **30**
* `waffeBonus` - This is a decimal value for a percent. Meaning if set to 1, the bonus is 100%. If it is 0.5, then it would be 50%. To turn it off, set to 0. Default: **1**
* `waffeBonusCap` - This is the cap for waffe bonus. Like the peacetime cap, this determines whether someone is eligible for the waffe bonus or not. Default: **300000000**
* `acceptLosses` - This setting will either allow or disallow posting of losses. If set to `1`, users can submit losses. If set to `0`, losses will not be accepted. Default: **1**
