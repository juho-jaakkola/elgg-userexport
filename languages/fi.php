<?php

$finnish = array(
	'admin:userexport' => 'Käyttäjien vienti',
	'userexport:fields:select'  =>  "Valitse vientiin mukaan otettavat kentät",
	'userexport:file:generate'  =>  "Generoi tiedosto",
	'userexport:progress' => 'Käsitelty %s/%s käyttäjää', 
	'userexport:redo' => 'Generoi uudelleen',
	'is_admin' => 'Ylläpito-oikeudet',
	'guid' => 'GUID',
	'download' => 'Lataa',
	'profile:language' => 'Kieli',

	// Error messages
	'userexport:error:nofields' => 'Valitse vähintään yksi kenttä!',
	'userexport:error:nofiledir' => 'Userexport-hakemisto puuttuu Elggin datahakemistosta. Hakemisto koitettiin luoda, mutta luominen epäonnistui. Tarkista hakemiston tiedosto-oikeudet.',
);

add_translation('fi', $finnish);