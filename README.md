# ratp
plugin jeedom pour récupérer les passages aux arrêts des transports parisiens

plugin basé sur l'API de pgrimaud ("https://api-ratp.pierre-grimaud.fr/v4")

URL de base de l'API utilisée dans le plugin est: https://api-ratp.pierre-grimaud.fr/v4/schedules/

Le seul paramêtre du plugin est l'URI correspondant à l'arret souhaité de la forme: {type}/{code}/{station}/{way}

Exemple: https://api-ratp.pierre-grimaud.fr/v4/schedules/buses/121/Fort%20de%20Rosny/R
Donnera les 2 prochains passages de l'arret de bus "Fort de Rosny" de la ligne de bus 121 en direction de mairie de montreuil
