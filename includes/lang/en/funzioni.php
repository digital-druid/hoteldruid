<?php

switch ($messaggio) {

case "<div style=\"display: inline; color: red;\">ERRORE</div> di scrittura del file":  		$messaggio = "<div style=\"display: inline; color: red;\">ERROR</div> writing file"; break;
case "Nome utente":  				$messaggio = "Username"; break;
case "Password":  				$messaggio = "Password"; break;
case "Entra":  					$messaggio = "Login"; break;
case "Login per HotelDruid":  			$messaggio = "Login for HotelDruid"; break;
case "Sessione <div style=\"display: inline; color: red;\">scaduta</div>":	$messaggio = "<div style=\"display: inline; color: red;\">Expired</div> session"; break;
case "Nome utente o password <div style=\"display: inline; color: red;\">errati</div>":	$messaggio = "<div style=\"display: inline; color: red;\">Incorrect</div> username or password"; break;
case "Numero eccesivo di login <div style=\"display: inline; color: red;\">errati</div> negli ultimi":	$messaggio = "Excessive number of <div style=\"display: inline; color: red;\">incorrect</div> logins in the last"; break;
case "minuti":  				$messaggio = "minutes"; break;
case "Dopo un login <div style=\"display: inline; color: red;\">errato</div> si devono attendere":	$messaggio = "After an <div style=\"display: inline; color: red;\">incorrect</div> login you must wait"; break;
case "secondi":  				$messaggio = "seconds"; break;
case "Il database deve essere aggiornato":  	$messaggio = "Database must be updated"; break;
case "Aggiorna":  				$messaggio = "Update"; break;
case "Mancano solo":  				$messaggio = "Only"; break;
case "tentativi prima del blocco dei login": 	$messaggio = "attempts remaining before locking the login"; break;
case "Manca solo":  				$messaggio = "Only"; break;
case "tentativo prima del blocco dei login":	$messaggio = "attempt remaining before locking the login"; break;
case "È possibile reimpostare la password dal proprio account di hosting":	$messaggio = "You can reset the password from your hosting account"; break;
case "":  		$messaggio = ""; break;
case "":  		$messaggio = ""; break;

} # fine switch ($messaggio)

?>