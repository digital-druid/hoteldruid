<?php

switch ($messaggio) {

case "Inserimento dei dati permanenti":  	$messaggio = "Insert the permanent data."; break;
case "Inserisci questi dati per poi creare il database":	$messaggio = "Insert this data to create the database"; break;
case "Nome del database da utilizzare":		$messaggio = "Name of the database to be used"; break;
case "Nome del computer a cui collegarsi":  	$messaggio = "Name of the computer to connect to"; break;
case "Numero della porta a cui collegarsi":  	$messaggio = "Number of the port to connect to"; break;
case "Nome per l'autenticazione al database":  	$messaggio = "Username for database authentication"; break;
case "Parola segreta per l'autenticazione al database":	$messaggio = "Password for database authentication"; break;
case "Caricare la libreria dinamica \"pgsql.so\" o \"mysql.so\"":	$messaggio = "Load the dinamic library \"pgsql.so\" or \"mysql.so\""; break;
case "No":  					$messaggio = "No"; break;
case "Si":  					$messaggio = "Yes"; break;
case "scegliere si se non viene caricata automaticamente da php":	$messaggio = "choose yes if it is not loaded automatically by php"; break;
case "Nome del database a cui collegarsi temporaneamente":	$messaggio = "Name of the database to temporarely connect to"; break;
case "Numero di unità da gestire":  		$messaggio = "Number of units to be managed"; break;
case "Crea il database":  			$messaggio = "Create database"; break;
case "Database creato":  			$messaggio = "Database created"; break;
case "Massimo numero di occupanti":  		$messaggio = "Maximum number of people it can host"; break;
case "Numero (o nome) piano":  				$messaggio = "Floor number (or name)"; break;
case "Numero (o nome) casa":  				$messaggio = "House number (or name)"; break;
case "Non è stato possibile creare il database, controllare i privilegi dell' utente, il nome del database o se esiste già un database chiamato":
						$messaggio = "It hasn't been possible to create the database, check the user privileges, the name of the database or if already exists a database called"; break;
case "I dati inseriti per il collegamento al database non sono esatti o il database non è in ascolto":	$messaggio = "Data inserted for database connection are not correct or the database isn't listening"; break;
case "se postgres assicurarsi che venga avviato con -i e di avere i permessi giusti in pg_hba.conf":	$messaggio = "if it's postgres make sure it is started with -i and you have right permissions in pg_hba.conf"; break;
case "Torna indietro":  			$messaggio = "Go back"; break;
case "Dati inseriti":  				$messaggio = "Data inserted"; break;
case "Tutti i dati permanenti sono stati inseriti":	$messaggio = "All the permanent data has been inserted"; break;
case "Non ho i permessi di scrittura sulla directory dati, cambiarli e reiniziare l'installazione":	$messaggio = "I don't have write permissions on dati folder, change them and begin again installation"; break;
case "Tipo di database":			$messaggio = "Database type"; break;
case "Database già esistente":			$messaggio = "Existing database"; break;
case "Se già esistente e non vuoto usare un prefisso non presente nel database per il nome delle tabelle":	$messaggio = "If already existing and not empty use a prefix not present in the database for tables names"; break;
case "Normalmete 5432 o 5433 per Postgresql o 3306 per Mysql":	$messaggio = "Normally 5432 or 5433 for Postgresql or 3306 for Mysql"; break;
case "solo per Postgresql con database non esistente":	$messaggio = "only for Postgresql with database not existing"; break;
case "Prefisso nel nome delle tabelle":		$messaggio = "Prefix in tables name"; break;
case "opzionale, utile per più installazioni di HotelDruid nello stesso database":	$messaggio = "optional, useful for more installations of HotelDruid in the same database"; break;
case "Il prefisso del nome delle tabelle è sbagliato (accettate solo lettere minuscole, numeri e _ , primo carattere lettera)":	$messaggio = "The prefix of tables names is wrong (only accepted lower case letters, numbers and _ , first character letter)"; break;
case "Nome delle unità da gestire":		$messaggio = "Name of the units to be managed"; break;
case "Euro":					$messaggio = "Euros"; break;
case "Benvenuto a HotelDruid!":			$messaggio = "Welcome to HotelDruid!"; break;
case "Questi sono alcuni semplici passi che puoi seguire per configurare le funzionalità di base di HotelDruid":	$messaggio = "These are some simple steps you can follow to set up the basic functionality of HotelDruid"; break;
case "utilizzando l'apposito tasto al di sotto di essa":	$messaggio = "using the specific button below it"; break;
case "pagina inserimento prezzi":		$messaggio = "page to insert prices"; break;
case "vedi passo successivo":			$messaggio = "view next step"; break;
case "pagina inserimento regole":		$messaggio = "page to insert rules"; break;
case "Se questo server web è pubblico si può abilitare il login e creare nuovi utenti dalla":	$messaggio = "If this is a public web server you can enable the login and create new users from the"; break;
case "pagina gestione utenti":			$messaggio = "users management page"; break;
case "Vai alla pagina":				$messaggio = "Go to the"; break;
case "configura e personalizza":		$messaggio = "configure and customize"; break;
case "per cambiare il nome della valuta, abilitare la registrazione delle entrate, inserire i metodi di pagamento, ed impostare molte altre opzioni":	$messaggio = "page to change currency name, enable registration of check-in, insert payment methods, and set up much more options"; break;
case "Inserisci il numero di tariffe, un nome per ciascuna di esse ed i prezzi corrispondenti dalla":	$messaggio = "Insert the number of rates, a name for each one of them and the corresponding prices from the"; break;
case "questo programma":			$messaggio = "this program"; break;
case "Nome delle unità singole da gestire":	$messaggio = "Name of single units to be managed"; break;
case "Numero di unità singole da gestire":	$messaggio = "Number of single units to be managed"; break;
case "non incluse nelle unità normali":		$messaggio = "not included in normal units"; break;
case "":			$messaggio = ""; break;
case "":			$messaggio = ""; break;

} # fine switch ($messaggio)

?>