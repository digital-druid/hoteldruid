<?php

switch ($messaggio) {

case "Aggiornamento eseguito con successo":  	$messaggio = "Updated successfully"; break;
case "Non si è potuto portare a termine l'<div style=\"display: inline; color: red;\">aggiornamento</div>":	$messaggio = "<div style=\"display: inline; color: red;\">Update</div> failed"; break;
case "OK":  					$messaggio = "OK"; break;
case "<div style=\"display: inline; color: red;\"><b>ATTENZIONE</b></div>: premendo su <b>\"<i>Continua</i>\"</b> tutti i dati del <i>database attuale</i> verranno <b>cancellati</b>":	$messaggio = "<div style=\"display: inline; color: red;\"><b>WARNING</b></div>: if you press <b>\"<i>Continue</i>\"</b> all data from <i>current database</i> will be <b>deleted</b>"; break;
case "Continua":  				$messaggio = "Continue"; break;
case "Subordinazione creata":  			$messaggio = "Subordination created"; break;
case "Impossibile effettuare il collegamento, controllare i dati immessi":	$messaggio = "Connection refused, check inserted data"; break;
case "Subordinazione cancellata":  		$messaggio = "Subordination deleted"; break;
case "Torna indietro":  			$messaggio = "Go back"; break;
case "Interconnessioni con sorgenti esterne di dati":	$messaggio = "Interconnections with external data sources"; break;
case "Cancella":  				$messaggio = "Delete"; break;
case "la subordinazione":  			$messaggio = "subordination"; break;
case "Subordinazione ad un'altra installazione di hoteldruid":	$messaggio = "Subordination to another installation of hoteldruid"; break;
case "Con la subordinazione non si potranno inserire nuovi dati o apportare modifiche, ma solo importare i dati dall'installazione principale":	$messaggio = "With subortdination you won't be able to insert new data or apply modifications, but only to import data from the master installation"; break;
case "Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo":	$messaggio = "Subordinate this installation of hoteldruid to the one that is at"; break;
case "Password":  				$messaggio = "Password"; break;
case "Commento da aggiungere al titolo di questa installazione":	$messaggio = "Comment to be added to the title of this installation"; break;
case "Crea la subordinazione":  		$messaggio = "Create subordination"; break;
case "Usa compressione":  			$messaggio = "Use compression"; break;
case "Utente per l'aggiornamento remoto delle interconnessioni":	$messaggio = "User to remotely update interconnections"; break;
case "Utente per l'aggiornamento remoto modificato":	$messaggio = "User for remote updates modified"; break;
case "Modifica":  				$messaggio = "Modify"; break;
case "Aggiornamento codice sorgente eseguito con successo":  	$messaggio = "Source code updated successfully"; break;
case "amministratore o utente con i privilegi per creare backup":	$messaggio = "administrator or user with privileges to create backups"; break;
case "nome utente":  				$messaggio = "username"; break;
case "Versione locale diversa da quella remota":	$messaggio = "Local version is different from the remote one"; break;
case "Non si è potuto scaricare il backup remoto":	$messaggio = "It was not possible to download the backup"; break;
case "Non si è potuto stabilire la connessione":	$messaggio = "It was not possible to establish the connection"; break;
case "Non ho i permassi di scrittura sulla cartella dati":	$messaggio = "I don't have write premissions on dati folder"; break;
case "Prova a mantenere i dati degli anni non presenti nell'installazione remota":	$messaggio = "Try to retain data of years not present in remote installation"; break;
case "nella pagina delle interconnessioni":  	$messaggio = "in interconnections page"; break;
case "Modifica la regola di assegnazione":  	$messaggio = "Modify the assignment rule"; break;
case "La prenotazione cancellata non era presente nel database":	$messaggio = "The deleted reservation was not present in the database"; break;
case "gruppo":  				$messaggio = "group"; break;
case "Attenzione":  				$messaggio = "Warning"; break;
case "carta di credito non salvata":  		$messaggio = "credit card not saved"; break;
case "":  		$messaggio = ""; break;
case "":  		$messaggio = ""; break;

} # fine switch ($messaggio)

?>