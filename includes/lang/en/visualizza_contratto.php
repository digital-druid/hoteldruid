<?php

switch ($messaggio) {

case "via":  				$messaggio = "street"; break;
case "Torna indietro":  		$messaggio = "Go back"; break;
case "Da":  				$messaggio = "From"; break;
case "A":  				$messaggio = "To"; break;
case "Oggetto":  			$messaggio = "Subject"; break;
case "Allega":  			$messaggio = "Attach"; break;
case "Spedisci":  			$messaggio = "Send"; break;
case "L'email a":  			$messaggio = "The email to"; break;
case "è stata inviata":  		$messaggio = "has been sent"; break;
case "bcc a":  				$messaggio = "bcc to"; break;
case "Non si è potuto inviare l'email a":	$messaggio = "It was not possible to send the email to"; break;
case "documento":  			$messaggio = "document"; break;
case "<span class=\"colblu\">salvato</span> come":	$messaggio = "<span class=\"colblu\">saved</span> as"; break;
case "Cancella il documento":  		$messaggio = "Delete this document"; break;
case "Cancella i documenti":  		$messaggio = "Delete these documents"; break;
case "Il documento":  			$messaggio = "The document"; break;
case "è stato <b style=\"font-weight: normal; color: blue;\">cancellato</b>":	$messaggio = "has been <b style=\"font-weight: normal; color: blue;\">deleted</b>"; break;
case "Transazione <font color=\"red\">scaduta</font>":	$messaggio = "<font color=\"red\">Expired</font> transaction"; break;
case "Sovrascrivi":  			$messaggio = "Overwrite"; break;
case "Documenti già esistenti riguardanti questa prenotazione":	$messaggio = "Already existing documents of this reservation"; break;
case "Documenti già esistenti riguardanti queste prenotazioni":	$messaggio = "Already existing documents of these reservations"; break;
case "Salva dei nuovi documenti":  	$messaggio = "Save new documents"; break;
case "Salva un nuovo documento":  	$messaggio = "Save a new document"; break;
case "<span class=\"colblu\">Attenzione</span>: una email con lo stesso oggetto è già stata inviata al cliente":	$messaggio = "<span class=\"colblu\">Warning</span>: an email with the same subject has already been sent to client"; break;
case "Errore":  			$messaggio = "Error"; break;
case "ha scritto":  			$messaggio = "wrote"; break;
case "":  		$messaggio = ""; break;
case "":  		$messaggio = ""; break;

} # fine switch ($messaggio)



?>