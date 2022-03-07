<?php
$password = $_REQUEST['pass'];
$encryptionKey = "8017aa25b6c6ba0c56110e3544718361af905f83f151f4f3af8f029cd36ee84d";  
$encryptionKeyBytes = pack('H*', $encryptionKey);
$rawHmac = hash_hmac('sha256', $password, $encryptionKeyBytes, true);
return bin2hex($rawHmac);