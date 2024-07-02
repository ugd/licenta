# Platforma de gestionat evenimente muzicale si resurse

Acest repository contine solutiile de backend si frontend ale proiectului, fiecare dintre solutii avand doua variante de utilizare:

1. deploy in Container Registry folosind GitLab.
2. rulare locala

## Solutia Backend

### Variante de Utilizare

#### 1. Deploy in Container Registry

1. **Creare Repository**:

   - Creeaza un repository separat pentru backend in GitLab.
   - Incarca solutia din directorul backend in branch-ul `main`.
   - Creeaza un branch `docker` din branch-ul `main`.

2. **Pipeline Automat**:

   - Pipeline-ul va rula automat in branch-ul `docker`.

3. **Configurare pe Host**:

   - Creeaza un folder pentru solutia backend pe host.
   - Creeaza un fisier `.env` si copiaza variabilele de sistem din fisierul `.env.docker-example` din repository-ul GitLab sau GitHub din directorul backend. Modifica urmatoarele variabile:
     ```env
     # Variabile ce trebuie modificate
     export APP_NAME='<NUME-APLICATIE>'
     export APP_KEY=<APP-KEY-LARAVEL>
     export APP_API_URL="<URL-API>"
     export APP_FRONTEND_URL="<URL-INTRARI>"

     export SESSION_DOMAIN=<DOMENIU-COOKIE>
     export SANCTUM_STATEFUL_DOMAINS=<DOMENIU-COOKIE>

     export DB_DATABASE=<NUME-BAZA-DATE>
     export DB_USERNAME=<USER-BAZA-DATE>
     export DB_PASSWORD=<PAROLA-BAZA-DATE>

     export MAIL_HOST=<HOST-MAIL>
     export MAIL_PORT=<PORT-MAIL>
     export MAIL_USERNAME=<USER-MAIL>
     export MAIL_PASSWORD=<PAROLA-MAIL>
     export MAIL_ENCRYPTION=<ENCRYPTIE-MAIL>
     export MAIL_FROM_ADDRESS=<ADRESA-MAIL>
     export MAIL_FROM_NAME="<NUME_EXPEDITOR_MAIL>"

     export CERTIFICATE_PASS="<PAROLA-CERTIFICAT>"
     export PASS_DESCRIPTION="<DESCRIERE_PASS>"
     export PASS_ORGANIZATION_NAME="<NUME_ORGANIZATIE>"
     export PASS_TYPE_IDENTIFIER="<IDENTIFICATOR_PASS>"
     export PASS_TEAM_IDENTIFIER="<IDENTIFICATOR_ECHIPA>"

     export LISTMONK_API_BASE_URL="<URL-API-LISTMONK>"
     export LISTMONK_URL="<URL-LISTMONK>"
     export LISTMONK_API_USERNAME="<USER-API-LISTMONK>"
     export LISTMONK_API_PASSWORD="<PAROLA-API-LISTMONK>"
     export LISTMONK_TEMPLATE_ID="<ID-TEMPLATE-LISTMONK>"
     ```
   - Creeaza un fisier `docker-compose.yml` si copiaza continutul din `docker-compose.yml` din repository-ul GitLab sau GitHub din directorul backend si se modifica path-ul imaginii docker:
   ```image: <your-gitlab-container-registry-url>/<your-gitlab-group>/<your-gitlab-project>/<your-gitlab-container-name>:latest```

4. **Rularea Stack-ului**:
   ```sh
   docker compose pull && docker compose up -d
   ```
5. **Incarcarea certificatului Apple**:

   - Se inchide containerul utilizand comanda `docker compose down` in interiorul directorului backend pe host.
   - Urmand pasii din [Documentatie](https://developer.apple.com/help/account/configure-app-capabilities/create-wallet-identifiers-and-certificates/), se genereaza un fisier `Certificates.p12`, fisier ce este un certificat necesar pentru semnarea Invitatiilor in formatul Apple Wallet Event Pass.
   - Se copiaza pe host certificatul in directorul `api-app-resources/certificates`.
   - Se actualizeaza containerul prin repornirea acestuia utilizand comanda `docker compose up -d`.

6. **Optional: Configureaza reverse proxy pentru solutia de Backend**

#### 2. Rulare Locala

1. **Creare fisier `.env`**:
   - Copiaza local solutia backend in folderul backend.
   - Creeaza un fisier `.env` in directorul backend, copiaza variabilele de sistem din fisierul `.env.local-example` si modifica variabilele necesare.
     ```env
     # Variabile ce trebuie modificate
     APP_NAME='<NUME-APLICATIE>'
     APP_KEY=<APP-KEY-LARAVEL>
     APP_API_URL="<URL-API>"
     APP_FRONTEND_URL="<URL-INTRARI>"

     DB_HOST=localhost
     DB_DATABASE=<NUME-BAZA-DATE>
     DB_USERNAME=<USER-BAZA-DATE>
     DB_PASSWORD=<PAROLA-BAZA-DATE>

     SESSION_DOMAIN=<DOMENIU-COOKIE>
     SANCTUM_STATEFUL_DOMAINS=<DOMENIU-COOKIE>

     MAIL_HOST=<HOST-MAIL>
     MAIL_PORT=<PORT-MAIL>
     MAIL_USERNAME=<USER-MAIL>
     MAIL_PASSWORD=<PAROLA-MAIL>
     MAIL_ENCRYPTION=<ENCRYPTIE-MAIL>
     MAIL_FROM_ADDRESS=<ADRESA-MAIL>
     MAIL_FROM_NAME="<NUME EXPEDITOR MAIL>"
     
     CERTIFICATE_PASS="<PAROLA-CERTIFICAT>"
     PASS_DESCRIPTION="<DESCRIERE_PASS>"
     PASS_ORGANIZATION_NAME="<NUME_ORGANIZATIE>"
     PASS_TYPE_IDENTIFIER="<IDENTIFICATOR_PASS>"
     PASS_TEAM_IDENTIFIER="<IDENTIFICATOR_ECHIPA>"

     LISTMONK_API_BASE_URL="<URL-API-LISTMONK>"
     LISTMONK_URL="<URL-LISTMONK>"
     LISTMONK_API_USERNAME="<USER-API-LISTMONK>"
     LISTMONK_API_PASSWORD="<PAROLA-API-LISTMONK>"
     LISTMONK_TEMPLATE_ID="<ID-TEMPLATE-LISTMONK>"
     ```
2. **Instalare Dependente**:
   ```sh
   composer install
   npm install
   npm run dev
   ```
3. **Incarcarea certificatului Apple**:

   - Urmand pasii din [Documentatie](https://developer.apple.com/help/account/configure-app-capabilities/create-wallet-identifiers-and-certificates/), se genereaza un fisier `Certificates.p12`, fisier ce este un certificat necesar pentru semnarea Invitatiilor in formatul Apple Wallet Event Pass.
   - Se copiaza certificatul in directorul `resources/certificates` din radacina solutiei.

4. **Migrare si Pornirea Aplicatiei**:
   ```sh
   php artisan cache:clear
   php artisan migrate
   php artisan serve
   ```

## Solutia Frontend

### Variante de Utilizare

#### 1. Deploy in Container Registry

1. **Creare Repository**:

   - Creeaza un repository separat pentru frontend in GitLab.
   - Incarca solutia din directorul frontend in branch-ul `main`.
   - Creeaza un branch `docker` din branch-ul `main`.

2. **Pipeline Automat**:

   - Pipeline-ul va rula automat in branch-ul `docker`.

3. **Configurare pe Host**:

   - Creeaza un folder pentru solutia frontend pe host.
   - Creeaza un fisier `.env` si copiaza variabilele de sistem din fisierul `.env.docker-example` din repository-ul GitLab sau GitHub din directorul frontend. Modifica urmatoarele variabile:
     ```env
     # Variabile ce trebuie modificate
     export VITE_API='<API_URL>/api'
     export VITE_API_SERVER='<API_URL>'

     export VITE_APP_NAME='<APP_NAME>'

     export VITE_ENV_NOTIFICATION_TOPIC='<NOTIFICATION_TOPIC>'
     export VITE_ENV_NOTIFICATION_SERVER='<NOTIFICATION_SERVER>'
     ```
   - Creeaza un fisier `docker-compose.yml` si copiaza continutul din `docker-compose.yml` din repository-ul GitLab sau GitHub din directorul frontend si se modifica path-ul imaginii docker:
   ```image: <your-gitlab-container-registry-url>/<your-gitlab-group>/<your-gitlab-project>/<your-gitlab-container-name>:latest```

1. **Rularea Containerelor**:

   ```sh
   docker compose pull && docker compose up -d
   ```

2. **Optional: Configureaza reverse proxy pentru solutia de Frontend**

#### 2. Rulare Locala

1. **Creare fisier `.env`**:
   - Copiaza local solutia frontend in folderul frontend.
   - Creeaza un fisier `.env` in directorul frontend si modifica variabilele necesare.

   ```env
    # Variabile ce trebuie modificate
    VITE_API='<API_URL>/api'
    VITE_API_SERVER='<API_URL>'

    VITE_APP_NAME='<APP_NAME>'

    VITE_ENV_NOTIFICATION_TOPIC='<NOTIFICATION_TOPIC>'
    VITE_ENV_NOTIFICATION_SERVER='<NOTIFICATION_SERVER>'
   ```

2. **Instalare Dependente**:

   ```sh
   npm install
   npm install -g @quasar/cli
   ```

3. **Servirea Aplicatiei**:
   ```sh
   quasar dev
   ```

## Descriere variabile .env

### Backend

- `APP_NAME`: Numele aplicatiei Backend.(ex: `'Backend Aplicatie'`)
- `APP_KEY`: Cheia aplicatiei pentru criptare in Laravel. Aceasta se poate genera utilizand un [generator online](https://generate-random.org/laravel-key-generator) sau ruland local in solutia backend `php artisan key:generate`. (ex: `base64:YjNqMzRuZm1zc3B0YjZuN2o4NHFhbnZuaTJ0NjVlOXM=`)
- `APP_API_URL`: URL-ul API-ului aplicatiei.(ex: `https://api.domeniu.tld` sau `http://localhost:8000/` in cazul in care este rulat local)
- `APP_FRONTEND_URL`: URL-ul frontend-ului aplicatiei.(ex: `"https://domeniu.tld"` sau `"http://localhost:9000/"` in cazul in care este rulat local)
- `SESSION_DOMAIN`: Domeniul/Domeniile pentru sesiuni si cookie-uri.(ex: daca este rulat local `localhost`, daca este rulat prin intermediul unui reverse proxy, in cazul docker `.domeniu.tld`, se pot adauga mai multe domenii folosind virgula, de exemplu `frontend.domeniu.tld,frontend2.domeniu.tld`)
- `SANCTUM_STATEFUL_DOMAINS`: Domeniul/domeniile pentru cookie-uri de autentificare.(ex: daca este rulat local `localhost`, daca este rulat prin intermediul unui reverse proxy, in cazul docker `.domeniu.tld`, se pot adauga mai multe domenii folosind virgula, de exemplu `frontend.domeniu.tld,frontend2.domeniu.tld`)
- `DB_DATABASE`: Numele bazei de date.(ex: `tabel_rmdbs`)
- `DB_USERNAME`: Numele de utilizator pentru baza de date.(ex: `user_rmdbs`)
- `DB_PASSWORD`: Parola pentru baza de date.(ex: `parola_rmdbs`)
- `MAIL_HOST`: Host-ul serverului de email.(ex: `mail.domeniu.tld`)
- `MAIL_PORT`: Portul serverului de email.(ex: `576`)
- `MAIL_USERNAME`: Numele de utilizator pentru serverul de email.(ex: `utilizator_mail`)
- `MAIL_PASSWORD`: Parola pentru serverul de email.(ex: `parola_mail`)
- `MAIL_ENCRYPTION`: Tipul de criptare utilizat pentru email.(ex: `TLS`)
- `MAIL_FROM_ADDRESS`: Adresa de email de la care vor fi trimise emailurile de system.(ex: `mail@domeniu.tld`)
- `MAIL_FROM_NAME`: Numele expeditorului emailurilor.(ex: `"Marian-Bogdan Muntean"`)
- `CERTIFICATE_PASS`: Parola pentru certificatul Apple.(ex: `"parola"`)
- `PASS_DESCRIPTION`: Descrierea pentru Apple Wallet Pass.(ex: `"Aceasta este o descriere a Apple Wallet Pass"`)
- `PASS_ORGANIZATION_NAME`: Numele organizatiei pentru Apple Wallet Pass.(ex: `"Licenta Org"`)
- `PASS_TYPE_IDENTIFIER`: Identificatorul de pass pentru Apple Wallet.(ex: `"pass.tld.domeniu.subdomeniu"`)
- `PASS_TEAM_IDENTIFIER`: Identificatorul echipei Apple Developer pentru Apple Wallet.(ex: `"CODIDENTIFICARE"`)
- `LISTMONK_API_BASE_URL`: URL-ul bazei API-ului Listmonk.(ex: `"https://listmonk.domeniu.tld/api"`)
- `LISTMONK_URL`: URL-ul Listmonk.(ex: `"https://listmonk.domeniu.tld"`)
- `LISTMONK_API_USERNAME`: Numele de utilizator pentru API-ul Listmonk.(ex: `"utilizator"`)
- `LISTMONK_API_PASSWORD`: Parola pentru API-ul Listmonk.(ex: `"parola"`)
- `LISTMONK_TEMPLATE_ID`: ID-ul template-ului Listmonk.(ex: `"5"`)

### Frontend

- `VITE_API_SERVER`: URL-ul serverului API.(ex: `"https://api.domeniu.tld"` si este identic cu cel definit in solutia backend la `APP_API_URL`)
- `VITE_API`: URL-ul API-ului aplicatiei.(ex: `"https://api.domeniu.tld/api"`, poate sa fie identic cu `VITE_API_SERVER` dar se adauga `/api` dupa url)
- `VITE_APP_NAME`: Numele aplicatiei Frontend.
- `VITE_ENV_NOTIFICATION_TOPIC`: Subiectul pentru notificari.(ex: `"canal-notificari"`)
- `VITE_ENV_NOTIFICATION_SERVER`: Serverul pentru notificari.(ex: `"https://notificari.domeniu.tld"`)

## Date de autentificare
- Utilizatorul implicit este `licenta@exemplu.ro`.
- Parola implicita este `ParolaLICENTA!2@24_`.
- Adresa URL a frontend-ului in cazul rularii locale este `http://localhost:9000/`.
- Adresa URL a backend-ului in cazul rularii locale este `http://localhost:8000/`.
- Adresele in cazul utilizarii prin intermediul unui reverse-proxy in cazul containerelor docker, nu vor mai contine portul in adresa si vor fi sub forma `https://domeniu.tld` respectiv `https://api.domeniu.tld` sau `https://subdomeniu.domeniu.tld` respectiv `https://api.domeniu.tld` sau `https://api.subdomeniu.domeniu.tld`, dupa cum se doreste.
