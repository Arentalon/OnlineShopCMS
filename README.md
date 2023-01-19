## instalacja obrazu
```
    docker-compose up -d
```
* Strona `localhost:81`
* PhpMyAdmin `localhost:83`
    - login: `root`
    - hasło: `qwerty`

## Wgrywanie danych do bazy
Schemat tabel bazy znajduje się w `migrations/cms.sql`.
Plik można zaimportować przy użyciu PhpMyAdmin `localhost:83`

## Konto admina
- login: `cms@gmail.com`
- hasło: `testtest`

## Manualna aktywacja konta użytkownia
W przypadku braku maila z linkiem weryfikacyjnym, należy ustawić wartość kolumny `is_verified` w tabeli `user` na 1. 

## Skrypt dodająy fejkowe dane
Skrypt odpala się przy użyciu komendy: `php bin/console doctrine:fixtures:load --append`
