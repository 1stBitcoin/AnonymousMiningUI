Anonymous mining user Interface with charts etc..

#Setup: 

Upload all files in public folder to your public webserver. Edit credentials to MySQL server in: cfg/config.php

You will also need to add ports for each pool (not automatized yet). There are three examples between lines 410 - 428 in index.php.
This front page works after the cron has run once and targeted the public feed.php just once.

#REQUIREMENTS:
* PHP >= 7.0
* MySQL >= 5.5.5

#THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.

License
----
MIT