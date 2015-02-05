Depot GIT du projet : https://github.com/valousal/Racoin-V2.0

URL projet en ligne : http://racoin.lucas-arcuri.fr/

Donnees utiles : 
	Pour s'authentifier au compte PRO : 
		name : azerty
		login : azerty@azerty.com
		password : azerty

	Pour modifier/supprimer une annonce :
		password : azerty


	API : 
		APIKey : Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4

		GET :
			Filtrer les Annonces :
				http://localhost/Racoin/api/annonces?apiKeyCftr3kGFTxWcgYRPXVHN8D72yp6BJ4=&cat=Informatique&price=100&tag=Go
				Valeurs spécifiques : noTags, allPrice, noTags
				Exemple : si l'on souhaite toutes les annonces : 
				http://localhost/Racoin/api/annonces?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4&cat=all&price=allPrice&tag=noTags
				Exemple : si l'on souhaite les annonces pour categorie Informatique
				http://localhost/Racoin/api/annonces?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4&cat=Informatique&price=allPrice&tag=noTags

			http://racoin.valentin-salvestroni.fr/api/annonces/:id?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4
			http://racoin.valentin-salvestroni.fr/api/categories?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4
			http://racoin.valentin-salvestroni.fr/api/categorie/:categorie/annonces?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4

		POST :
			http://racoin.valentin-salvestroni.fr/api/annonces?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4

		PUT :
			http://racoin.valentin-salvestroni.fr/api/annonces/id?apiKey=Cftr3kGFTxWcgYRPXVHN8D72yp6BJ4



