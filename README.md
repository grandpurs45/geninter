# Generateur de fiche d'intervention

Application PHP simple pour creer une fiche de depart manoeuvre pompiers au format A4, inspiree du PDF de reference fourni.

Les champs de l'intervention demarrent vides. Les dates et heures de depart/appel sont remplies automatiquement au moment de la generation de la fiche.

## Utilisation avec XAMPP

1. Placer ce dossier dans `htdocs`.
2. Demarrer Apache dans XAMPP.
3. Ouvrir `http://geninter.local/`.
4. Remplir la fiche puis utiliser `Imprimer / PDF`.

## Utilisation avec Docker en local

Cette commande sert surtout a tester l'image Docker sur un poste de developpement. Pour le developpement quotidien local, XAMPP reste possible via `http://geninter.local/`.

### Prerequis

- Docker Engine et Docker Compose v2 installes.
- Le port `8080` libre sur la machine.

### Demarrage

```bash
docker compose up --build
```

Puis ouvrir `http://localhost:8080/`.

### Arret

```bash
docker compose down
```

### Rebuild apres modification du code

```bash
docker compose up -d --build
```

## Production Docker avec Traefik

Le fichier `docker-compose.prod.yml` est prevu pour un serveur ou Traefik tourne deja comme reverse proxy Docker.

### Prerequis serveur

- Docker Engine et Docker Compose v2 installes.
- Traefik deja lance sur le serveur.
- Un reseau Docker externe partage avec Traefik, par defaut `traefik`.
- Un nom de domaine public pointe vers le serveur.
- Le resolver Let's Encrypt existe dans la configuration Traefik, par defaut `letsencrypt`.

### Verification du reseau Traefik

Lister les reseaux :

```bash
docker network ls
```

Si le reseau Traefik n'existe pas encore :

```bash
docker network create traefik
```

Le nom du reseau doit correspondre a `TRAEFIK_NETWORK` dans `.env`.

### Installation

Cloner le depot :

```bash
git clone https://github.com/grandpurs45/geninter.git
cd geninter
```

Creer le fichier d'environnement :

```bash
cp .env.example .env
```

Modifier `.env` :

```env
APP_HOST=geninter.votre-domaine.fr
TRAEFIK_NETWORK=traefik
TRAEFIK_ENTRYPOINT=websecure
TRAEFIK_CERT_RESOLVER=letsencrypt
```

Demarrer l'application :

```bash
docker compose -f docker-compose.prod.yml --env-file .env up -d --build
```

Le conteneur expose Apache en interne sur le port `80`. Traefik route le trafic HTTPS via les labels du service.

### Mise a jour

Depuis le dossier du projet :

```bash
git pull
docker compose -f docker-compose.prod.yml --env-file .env up -d --build
```

### Logs et diagnostic

Voir les logs de l'application :

```bash
docker compose -f docker-compose.prod.yml --env-file .env logs -f
```

Verifier les conteneurs :

```bash
docker compose -f docker-compose.prod.yml --env-file .env ps
```

Verifier que Traefik voit le conteneur :

```bash
docker inspect geninter-geninter-1
```

Points a controler si le site ne repond pas :

- `APP_HOST` correspond exactement au domaine appele.
- Le domaine pointe vers l'IP du serveur.
- Le reseau `TRAEFIK_NETWORK` est le meme que celui utilise par Traefik.
- L'entrypoint `TRAEFIK_ENTRYPOINT` existe dans Traefik.
- Le certresolver `TRAEFIK_CERT_RESOLVER` existe dans Traefik.
- Le service Traefik a acces au socket Docker ou au provider Docker.

## Export PDF

Le bouton `Imprimer / PDF` utilise la fonction d'impression du navigateur. Choisir ensuite `Enregistrer au format PDF` pour generer le fichier final.
