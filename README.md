#API PHP Feeder (cron worker)
and
#Anonymous mining user Interface with charts etc..

#About:
User interface portal is saving user data in fairly secured database system and shows data only to user that knows mining address. Made with help of varius free tools like: Bootstrap, Font Awesome, C3, D3, etc..

On frontpage it shows all pools (coins) with more info then regular pool. Global difficulty, global hash rate compared to local, etc..

Checkout how to install the uNOMP (Unified, Node Open Mining Portal) https://github.com/UNOMP/unified-node-open-mining-portal

#Good guide how to:
https://blockgen.net/setup-your-own-mining-pool/

#Read README in both local and public server folders.


#TO DO LIST: (Further development)
Secure db for injections from user inputs XD
User request for deleting of users history content.
Other implementations are welcome.

#Minimum shared hardware:
Local php server: 1 core, 348 - 512 MB RAM
Public server: >= 1 core, >= 512 MB RAM

Both can also run on the same server. Local server can be run in node js php environment. But for security reasons I prefer Nginx php server environment for the public server. This and other server environment solutions as VM or iso can be downloaded here: https://www.turnkeylinux.org/


#REQUIREMENTS:
* PHP >= 7.0
* MySQL >= 5.5.5

#THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.

License
----
MIT