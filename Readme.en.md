# ACSWebAdmin

<img src="https://img.icons8.com/emoji/24/000000/russia-emoji.png"/> [описание на русском](https://github.com/Aminuxer/ACSWebAdmin/blob/main/README.md)

Server-side Web-based system for mass-management Z5RWeb ACS controllers.

Features:
* Unlimited keys count
* Unlimited controllers count
* мониторинг событий
* proxify events over http-requests
* Limit access per IP for actions and for logins
* Different access rights for logins 
* Two-factor authentication (TOTP, Email, Bitcoin)
* HTTP-JSON API

Installation:
* Install web-server with php + mysql supprt
* Install mysql and create mysql-user
* Unpack web-scripts
* Create database from dump
* In file config.php change database requisits and session encryption key

Log in to system under user admin and empty password. Change password at first login.
