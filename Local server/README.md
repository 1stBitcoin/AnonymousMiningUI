API PHP Feeder (cron worker)

Every engine needs credetinals to connect with rpc wallet server. Edit coin/index.php
Copy wholefolder with content and renameit to another coin your mining pool is running.

$bitcoin = new Bitcoin('RPCuser','Veryl0ngPassword','IP_ADDRESS','PORT');


$bitcoin = new Bitcoin('RPCuser','Veryl0ngPassword','192.168.0.51','1912');


#Setup: 
Edit index.php values to your own servers: $refreshTime of the feeder in seconds, link to mining pool server, link to user interface feeder and the userinterface webserver.

#Run it in:
Your favorite explorer, return will show & refresh with pool with diffuculty and hash rate. 
Try Elinks browsesr for this task.

(Installation of elinks in Ubuntu:)
sudo atp-get install elinks

(run:)
elinks https://192.168.0.102


#REQUIREMENTS:
* PHP >= 5.4

#THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND.

License
----
MIT