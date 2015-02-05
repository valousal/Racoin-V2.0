Depot GIT du projet : https://github.com/valousal/Racoin-V2.0

URL projet en ligne : http://racoin.lucas-arcuri.fr/

Donnees utiles : 
	Pour s'authentifier au compte PRO : 
		login : azerty@azerty.com
		password : azerty

	Pour modifier/supprimer une annonce :
		password : azerty


	API : 
		APIKey :

		GET :
			http://racoin.valentin-salvestroni.fr/api/annonces?apiKey=
			Filtrer les annonces avec query, exemple :
			http://localhost/Racoin/api/annonces?apiKey=&cat=Informatique&price=100&tag=Go
			Valeurs spécifiques : noTags, allPrice, noTags

			http://racoin.valentin-salvestroni.fr/api/annonces/:id?apiKey=
			http://racoin.valentin-salvestroni.fr/api/categories?apiKey=
			http://racoin.valentin-salvestroni.fr/api/categorie/:categorie/annonces?apiKey=

		POST :
			http://racoin.valentin-salvestroni.fr/api/annonces?apiKey=

		PUT :
			http://racoin.valentin-salvestroni.fr/api/annonces/id?apiKey=



