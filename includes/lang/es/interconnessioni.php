<?php

switch ($messaggio) {

case "Aggiornamento eseguito con successo":  	$messaggio = "Actualización ejecutada con éxito"; break;
case "Non si è potuto portare a termine l'<div style=\"display: inline; color: red;\">aggiornamento</div>":	$messaggio = "No se ha podido llevar a cabo la <div style=\"display: inline; color: red;\">actualización</div>"; break;
case "OK":  					$messaggio = "OK"; break;
case "<div style=\"display: inline; color: red;\"><b>ATTENZIONE</b></div>: premendo su <b>\"<i>Continua</i>\"</b> tutti i dati del <i>database attuale</i> verranno <b>cancellati</b>":	$messaggio = "<div style=\"display: inline; color: red;\"><b>ATENCION</b></div>: al pulsar <b>\"<i>Continua</i>\"</b> todos los datos de la <i>actual base de datos</i> serán <b>borrados</b>"; break;
case "Continua":  				$messaggio = "Continuar"; break;
case "Subordinazione creata":  			$messaggio = "Subordinación creada"; break;
case "Impossibile effettuare il collegamento, controllare i dati immessi":	$messaggio = "Imposible efectuar la conexión, controlar los datos insertados"; break;
case "Subordinazione cancellata":  		$messaggio = "Subordinación eliminada"; break;
case "Torna indietro":  			$messaggio = "Volver atrás"; break;
case "Interconnessioni con sorgenti esterne di dati":	$messaggio = "Interconexiones con fuentes exteriores de datos"; break;
case "Cancella":  				$messaggio = "Elimina"; break;
case "la subordinazione":  			$messaggio = "la subordinación"; break;
case "Subordinazione ad un'altra installazione di hoteldruid":	$messaggio = "Subordinación a otra instalación de hoteldruid"; break;
case "Con la subordinazione non si potranno inserire nuovi dati o apportare modifiche, ma solo importare i dati dall'installazione principale":	$messaggio = "Con la subordinación no se podrán insertar nuevos datos y efectuar modificaciones, solo importar datos desde la instalación principal"; break;
case "Subordina questa installazione di hoteldruid a quella che si trova all'indirizzo":	$messaggio = "Subordina esta instalación de hoteldruid a la que se encuentra a la dirección"; break;
case "Password":  				$messaggio = "Contraseña"; break;
case "Commento da aggiungere al titolo di questa installazione":	$messaggio = "Comentario a añadir a esta instalación"; break;
case "Crea la subordinazione":  		$messaggio = "Crear la subordinación"; break;
case "Usa compressione":  			$messaggio = "Utilizar compresión"; break;
case "Utente per l'aggiornamento remoto delle interconnessioni":	$messaggio = "Usuario para la actualización remota de las interconexiones"; break;
case "Utente per l'aggiornamento remoto modificato":	$messaggio = "Usuario para la actualización remota modificado"; break;
case "Modifica":  				$messaggio = "Modificar"; break;
case "Aggiornamento codice sorgente eseguito con successo":	$messaggio = "Actualización código fuente ejecutada con éxito"; break;
case "amministratore o utente con i privilegi per creare backup":	$messaggio = "administrador o usuario con los privilegios para crear backups"; break;
case "nome utente":  				$messaggio = "Nombre de usuario"; break;
case "Versione locale diversa da quella remota":	$messaggio = "Versión local diferente de la remota"; break;
case "Non si è potuto scaricare il backup remoto":	$messaggio = "No se ha podido descargar el backup remoto"; break;
case "Non si è potuto stabilire la connessione":	$messaggio = "No se ha podido establecer la conexión"; break;
case "Non ho i permassi di scrittura sulla cartella dati":	$messaggio = "No tengo los permisos para escribir en el directorio dati"; break;
case "Prova a mantenere i dati degli anni non presenti nell'installazione remota":	$messaggio = "Intentar mantener los datos de los años no presentes en la instalación remota"; break;
case "nella pagina delle interconnessioni":  	$messaggio = "en la página de las interconexiones"; break;
case "Modifica la regola di assegnazione":  	$messaggio = "Modificar la regla de asignación"; break;
case "La prenotazione cancellata non era presente nel database":	$messaggio = "La reserva borrada no estaba presente en la base de datos"; break;
case "gruppo":  				$messaggio = "grupo"; break;
case "Attenzione":  				$messaggio = "Atención"; break;
case "carta di credito non salvata":  		$messaggio = "la tarjeta de crédito no ha sido salvada"; break;
case "":  		$messaggio = ""; break;
case "":  		$messaggio = ""; break;

} # fine switch ($messaggio)

?>