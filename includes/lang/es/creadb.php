<?php

switch ($messaggio) {

case "Inserimento dei dati permanenti":  	$messaggio = "Inserción de los datos permanentes."; break;
case "Inserisci questi dati per poi creare il database":	$messaggio = "Inserta estos datos para crear la base de datos"; break;
case "Nome del database da utilizzare":		$messaggio = "Nombre de la base de datos a utilizar"; break;
case "Nome del computer a cui collegarsi":  	$messaggio = "Nombre del ordenador al que conectarse"; break;
case "Numero della porta a cui collegarsi":  	$messaggio = "Número de la puerta a la que conectarse"; break;
case "Nome per l'autenticazione al database":  	$messaggio = "Nombre de usuario para la autenticación a la base de datos"; break;
case "Parola segreta per l'autenticazione al database":	$messaggio = "Contraseña para la autenticación a la base de datos"; break;
case "Caricare la libreria dinamica \"pgsql.so\" o \"mysql.so\"":	$messaggio = "Cargar la librería dinamica \"pgsql.so\" o \"mysql.so\""; break;
case "No":  					$messaggio = "No"; break;
case "Si":  					$messaggio = "Si"; break;
case "scegliere si se non viene caricata automaticamente da php":	$messaggio = "escoger si si no es cargada automaticamente por php"; break;
case "Nome del database a cui collegarsi temporaneamente":	$messaggio = "Nombre de la base de datos a la que conectarse temporaneamente"; break;
case "Numero di unità da gestire":  		$messaggio = "Número de unidades a gestionar"; break;
case "Crea il database":  			$messaggio = "Crea la base de datos"; break;
case "Database creato":  			$messaggio = "Base de datos creada"; break;
case "Massimo numero di occupanti":  		$messaggio = "Máximo número de personas que puede acoger"; break;
case "Numero (o nome) piano":  				$messaggio = "Número (o nombre) de piso"; break;
case "Numero (o nome) casa":  				$messaggio = "Número (o nombre) de casa"; break;
case "Non è stato possibile creare il database, controllare i privilegi dell' utente, il nome del database o se esiste già un database chiamato": 						$messaggio = "No ha sido posible crear la base de datos, controlar los privilegios del usuario, el nombre de la base de datos o si ya existe una base de datos llamada"; break;
case "I dati inseriti per il collegamento al database non sono esatti o il database non è in ascolto":	$messaggio = "Los datos insertados para la conexión a la base de datos no son exactos o la base de datos no está a la escucha"; break;
case "se postgres assicurarsi che venga avviato con -i e di avere i permessi giusti in pg_hba.conf":	$messaggio = "si es postgresql asegurarse que sea arrancado con -i y tener los permisos necesarios en pg_hba.conf"; break;
case "Torna indietro":  			$messaggio = "Vuelve atrás"; break;
case "Dati inseriti":  				$messaggio = "Datos insertados"; break;
case "Tutti i dati permanenti sono stati inseriti":	$messaggio = "Todos los datos permanentes han sido insertados"; break;
case "Non ho i permessi di scrittura sulla directory dati, cambiarli e reiniziare l'installazione":	$messaggio = "No tengo los permisos de escritura sobre el directorio dati, cambiarlos y volver a iniciar la instalación"; break;
case "Tipo di database":			$messaggio = "Tipo de base de datos"; break;
case "Database già esistente":			$messaggio = "Base de datos ya existente"; break;
case "Se già esistente e non vuoto usare un prefisso non presente nel database per il nome delle tabelle":	$messaggio = "Si existe ya y no está vacío utilizar un prefijo no presente en la base de datos para el nombre de las tablas"; break;
case "Normalmete 5432 o 5433 per Postgresql o 3306 per Mysql":	$messaggio = "Normalmente 5432 o 5433 para Postgresql o 3306 para Mysql"; break;
case "solo per Postgresql con database non esistente":	$messaggio = "solo para Postgresql sin base de datos ya existente"; break;
case "Prefisso nel nome delle tabelle":		$messaggio = "Prefijo en el nombre de las tablas"; break;
case "opzionale, utile per più installazioni di HotelDruid nello stesso database":	$messaggio = "opcionál, útil para más instalaciones de HotelDruid en la misma base de datos"; break;
case "Il prefisso del nome delle tabelle è sbagliato (accettate solo lettere minuscole, numeri e _ , primo carattere lettera)":	$messaggio = "El prefijo del nombre de las tablas está equivocado (permitidas solo letras minúsculas, numeros y _ , primer caracter letra)"; break;
case "Nome delle unità da gestire":		$messaggio = "Nombre de las unidades a gestionar"; break;
case "Euro":					$messaggio = "Euros"; break;
case "Benvenuto a HotelDruid!":			$messaggio = "Bienvenido a HotelDruid!"; break;
case "Questi sono alcuni semplici passi che puoi seguire per configurare le funzionalità di base di HotelDruid":	$messaggio = "Estos son algunos simples pasos a seguir para configurar las funciones de base de HotelDruid"; break;
case "utilizzando l'apposito tasto al di sotto di essa":	$messaggio = "utilizando el botón que está debajo de ella"; break;
case "pagina inserimento prezzi":		$messaggio = "página de inserción de precios"; break;
case "vedi passo successivo":			$messaggio = "mirar el paso siguiente"; break;
case "pagina inserimento regole":		$messaggio = "página de inserción de reglas"; break;
case "Se questo server web è pubblico si può abilitare il login e creare nuovi utenti dalla":	$messaggio = "Si este servidor web es público se puede habilitar el login y crear nuevos usuarios desde la"; break;
case "pagina gestione utenti":			$messaggio = "página de gestión de usuarios"; break;
case "Vai alla pagina":				$messaggio = "Ir a la página"; break;
case "configura e personalizza":		$messaggio = "configurar y personalizar"; break;
case "per cambiare il nome della valuta, abilitare la registrazione delle entrate, inserire i metodi di pagamento, ed impostare molte altre opzioni":	$messaggio = "para cambiar el nombre de la divisa, habilitar el registro de entradas, insertar los métodos de pago, y configurar muchas otras opciones"; break;
case "Inserisci il numero di tariffe, un nome per ciascuna di esse ed i prezzi corrispondenti dalla":	$messaggio = "Insertar el número de tarifas, un nombre para cada una de ellas y los precios correspondientes desde la"; break;
case "questo programma":			$messaggio = "este programa"; break;
case "Nome delle unità singole da gestire":	$messaggio = "Nombre de las unidades individuales a gestionar"; break;
case "Numero di unità singole da gestire":	$messaggio = "Número de las unidades individuales a gestionar"; break;
case "non incluse nelle unità normali":		$messaggio = "no incluídas en las unidades normales"; break;
case "":			$messaggio = ""; break;
case "":			$messaggio = ""; break;

} # fine switch ($messaggio)

?>