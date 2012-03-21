<?php

$finnish = array(
	'admin:userexport' => 'Käyttäjien vienti',
	'userexport:fields:select'  =>  "Valitse vientiin mukaan otettavat kentät",
	'userexport:type:select'  =>  "Valitse tiedoston tyyppi",
	'userexport:type:csv'  =>  "csv",
	'userexport:type:excel'  =>  "xsl",
	'userexport:file:generate'  =>  "Generoi tiedosto",
	'admin' => 'Ylläpito-oikeudet',
	
	// Error messages
	'userexport:error:nofields' => 'Valitse vähintään yksi vietävä tieto!',
	'userexport:error:nofiledir' => 'Userexport-hakemisto puuttuu Elggin datahakemistosta. Hakemisto koitettiin luoda, mutta luominen epäonnistui. Tarkista hakemiston tiedosto-oikeudet.',
);

add_translation('fi', $finnish); 
