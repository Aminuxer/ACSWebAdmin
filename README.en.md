# ACSWebAdmin

<img src="https://img.icons8.com/emoji/24/000000/russia-emoji.png"/> [описание на русском](https://github.com/Aminuxer/ACSWebAdmin/blob/main/README.md)

Server-side Web-based system for mass-management Z5RWeb ACS controllers.

![ACSWebAdmin](https://user-images.githubusercontent.com/13812192/197417577-d8f43de0-e44c-4c1f-a1cb-e3614cf18826.png)

## Features
* Unlimited keys
* Unlimited controllers
* Events monitoring
* Proxify events over http
* Limit access by IP for managent actions and operators
* Access levels for operators
* Two-factor authentication (TOTP, Email, Bitcoin)
* HTTP-JSON API
* ACS controllers can work throught NAT
* Multilanguage (russian, english)

## Install and system requirements
* Prepare minimal web-server with PHP and MySQL support.
For example on this manual:

https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04-ru

Hardware requirements minimal. We recommend actual versionss Linux / BSD, nginx, php8, mysql
Windows also possible, but not tested.

* Install php-mysql and php-gd (for qr-codes totp), php-gmp (for bitcoin-2fa), php-mbstring, php-curl too
* Create mysql-user and assign DB-rights:

`CREATE USER 'z5r'@'localhost' IDENTIFIED BY 'z5r-my-good-password';`

`GRANT SELECT,UPDATE,INSERT,DELETE ON z5r.* TO 'z5r'@'localhost';

* Copy directory content from www to web server direcory (root or sub-dir like z5r)
* Restore dababase from dump `z5r.sql` , default database name z5r
* Edit config `www/options.php` on server and check database parameters. Edir option `$sess_secret_salt` to long random string.
Set localization language in `localization` option.
We greeting pull-requests with new language localizations.

* Open web-server path in browser and try login under login admin with empty password.
At first login your must change password.

## Usage
* Create operators logins an assign access rights.
* For connect controller open Z5RWeb web-interface, select work-mode WEB-JSON.
Input path to installed ACSWebAdmin as server url.
For example it can be http://acs-admin.local/z5r/
Dont type username or password near server url, current firmware do not working with this.
* !! Current verions of firmware do not support HTTPS !!
Connection Controller <--> ACS will be on port 80 over unencrypted http protocol.
* For encrypted operators connections to ACSWebAdmin configure HTTPS on web-server.

## Why ?
Software like Guard Commander not useful as server application, and i make new software for this.
I need normal server middleware for prevent direct access operators to controllers/vlans with hardware,
for proxify events over http-requests to another systems and for access rights manage/separation.

ACS controller model Z5RWeb has many management variants. Main - embedded web-interface with only basic options (connection type, password, time)and software like Guard Lite / Guard Commander for setting up all another (keys, access masks, schedules). But this soft а) need winodws and fat application б) work over serial-port redirection over network without any authentification. ACSWebAdmin use third work mode - Web-JSON.
In this mode controllers polling web-server any 10 seconds and fetch command for manage keys and access rights.
This software allow different users has different access levels.


##  FAQ
* Can i use this on mobile / tablet ?
  - Yes.

* I forget password or remove admin rights. How to reset password / recover access rights ?
  - Connect to server over SSH, input command `mysql` for connect to DB. User root-password for MySQL or requisits from config.
  - Select database:
     `USE z5r;`
  - View list of operators:
     `SELECT id, login FROM logins`
  - Assign empty password for operator, ex. admin:
     `UPDATE logins SET password = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', salt1 = '', salt2 = '', allowed_ip_range = '' WHERE login = 'admin' `
  - Enable login and access rights:
     `UPDATE logins SET enable = '1', allow_manage_logins = '1' WHERE login = 'admin' `
  - Login with empty password and install new one.

* How long this system exists ?
  - Created in 2021 for himself, since 2022 in public.

* What users create by default ?
  - Only admin with full rights

* How access limited ?
  - For each controller can be specified IP/subnets for whitelisting connections
  - For each operator can be specified IP/subnets for whitelisting login
  - Can be limited IP/subnets allowed for key management / door open.
  - For each operator can be enabled two-factor authentification.

* Does ACSWebAdmin will be accesible permanently ?
  - Not required. ACSWebAdmin do not switch controllers to online-check keys mode.
    When web-system inaccessible all early added keys will work.
    But commands for add / delete / change-access-mask for keys will do not appplied until controller appears online.
    For MEMORY-tables restart server after too long controllers offline can erase planned commands.
    In this case restore connection controllers and repeat configure actions.

* How setup time shedule over ACSWebAdmin ?
  - Nowtime only by specify TZ-parameter, it is bitmask. Current controller JSON-API do not allow manage shedules.
    Use 0 for deny access, and 255 - for anytime access.
  - If shedule really required. Switch controller to "Server" workmode in embedded web-interface.
    By Guard Commander connect to controller from trusted networrk and configure schedules. Max 8 can be.
  - Switch controller back to "Web-JSON" mode.
  - If needed upload TZ-values over web-interface.
    Mode detailed:
    https://ironlogic.ru/il_new.nsf/file/ru_Protocol_WEBJSON_v7.pdf/$FILE/Protocol_WEBJSON_v7.pdf

* Can i manage blocking / master keys ?
  - No. All keys manage as simple keys. But support another types of keys added to DB, this can be added in future.

* System will be developed more ?
  - Why not, if it will be interesting for me

* Why web ?
  - web is really cross-platform. ACS operators will not have access directly to controllers and tech subnets.
  - Operator with enroll permission can be created, but he can not view sensitive key data.
  - Operator with view-only permission can be created.

* Can i user many controllers separated by offices / departments ?
  - Yes.

* Does need cleaning / maintenance DB ?
  - On your selection, depends from events count. You can delete old records from tables `events` and `queue_commands` using `Adminer`.
  - If your need only management, without reports, your can alter tables `events` and `queue_commands` to MEMORY engine.
    In this case server restart will erase logs of events and sended commands.
  - More radical variant - alter tables `last_state` and `last_activity_keys` to MEMORY engine too.
    In this case server restart also clean data about last keys usage and controllers state.
    Data will be refreshed automatcally due future events processing.
  - Also your can clean old events by sheduled query:
    `DELETE FROM events WHERE ts < DATE_SUB(NOW(), INTERVAL 40 DAY)`

* How to check what controller correct interact with ACSWebAdmin ?
  - In web-interface on controllers tabs last activity time or last events time must renewed.
  - tcpdump -s 65000 -w /tmp/1.pcap 'tcp port http'
    In traffic dump JSON-data exhcnage over http must be present
    If web-server return bad-formed JSON controller can spam same events.
    In this case fix error and clean events.
  - Enable debug (option debug in config) and watch to file /tmp/z5r.txt  Inspect activity log.
  - View web-server logs. Exceptions and 5xx errors must do not appear.

* Where worktime reports ?
  - Not need for me. But your can proxify events "Input/output by key" to another web-server with parameters and collect data by another system.
  - Some reports can be added at future versions

* I need new option.
  - Button [Fork].
  - This soft created for me. If feature will be interesting - i possible make this in free time only.

