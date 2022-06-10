# Power Station

## Story
Elektrownia posiada 20 generatorów prądu oraz każdy poszczególny generator potrafi wygenerować prąd o maksymalnej mocy 1kW. Aplikacja dokonuje dwóch pomiarów w ciągu jednej sekundy dla każdego poszczególnego generatora prądu. Dane te są zbierane co każdą godzinę i wyliczana jest średnia moc wygenerowanego prądu dla każdego generatora w kW, w ciągu całej godziny. Codziennie zapisywany jest raport z dnia poprzedniego odnośnie wygenerowanego prądu w ciągu każdej godziny dla każdego poszczególnego generatora w MW. Aplikacja posiada GUI pozwalające na wyświetlanie danych dla poszczególnego generatora w podanym okresie.

## Główne narzędzia:
- PHP 8.1.6
- Symfony 6.1.0 [nowo generowane annotacje są cudowne]
- Composer 2.3.7
- MySql 8.0.29
- Redis 6.2.7

## Wybór bazy<br>
MySql służy do przechowywania stałych danych, do których będziemy chcieli "sięgnąć" w przyszłości. Natomiast użyłem Redis'a jako nierelacyjnej bazy danych, ponieważ danych których generujemy z pomiarów na bieżąco jest bardzo dużo. Przykładowo 1 milion hash'y z 5 polami zajmuje około 160MB. Wygenerowanych hash'y za cały rok będzie
1 261 440 000 co zajmie 201 830,4MB około 201GB. Dodatkowo Redis posiada bardzo użyteczną funkcjonalność jaką jest TTL, dzięki czemu generowane dane automatycznie po pewnym czasie zostaną usunięte.

## Instalacja i działanie aplikacji:

### Budowanie aplikacji

Aby uruchomić aplikację należy pobrać repozytorium: https://github.com/inari6735/power_station. Następnie budujemy projekt komendą:
```
docker compose up --build
```

### Utworzenie struktury bazy MySql

Po zbudowaniu projektu powinniśmy mieć możliwość wejść na stronę główną pod adresem: https://localhost/
Powinna ukazać nam się strona główna projektu ale bez możliwości wyboru generatora, ponieważ nie posiadamy żadnych wpisów na bazie. Aby to poprawić musimy wejść do kontenera php.<br>
Pobierzmy id naszego kontenera PHP komendą:<br>
```
docker ps
```
<br>

Wejdźmy do kontenera za pomocą:<br>
```
docker exec -it [container_id] sh
```

Aby zbudować strukturę bazy danych wykonujemy w konterze komendy:<br>
```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### Załadowanie danych do bazy MySql

Teraz możemy "zasiać" bazę danymi. Aby to zrobić wykonujemy komendę:<br>
```
php bin/console doctrine:fixtures:load
```
<br>
Załadowaliśmy właśnie dane 20 generatorów oraz przykładowe statystyki generatorów za rok 2019. Teraz wchodząc na stronę główną mamy możliwość filtrowania danych dla poszczególnych generatorów w podanym okresie.

### Zbieranie aktualnych danych z generatorów

Aby uruchomić zbieranie aktualnych statystyk z dwóch pomiarów w ciągu jednej sekundy dla każdego generatora należy najpierw uruchomić worker komendą:<br>
```
php bin/console messenger:consume
```
<br>
Następnie aby zacząć zbierać dane musimy wejść pod link: https://localhost/generator/stats/redis<br>
Od teraz dane zbierane są asynchronicznie i zapisywane na bazie Redis. Aby zakończyć zbieranie danych wystarczy zamknąć okno przeglądarki. Generowanych danych jest bardzo dużo dlatego też zostało to zrobione w takiej formie aby móc szybko zakończyć działanie.

Żeby móc zobaczyć wygenerowane dane musimy wejść do konteneru Redis'a. Wchodzimy do niego tak samo jak do konteneru PHP. Następnie musimy wejść do CLI Redis'a dlatego wywołujemy komendy:
```
redis-cli
```
Autoryzujemy się komendą:
```
AUTH Piotrek120
```
Wyświetlamy wszystkie klucze:
```
keys *
```
<br>

Dane te są zapisane w formie hash'a oraz ich TTL(czas życia) wynosi 2 godziny. Jest to zrobione w celu ograniczenia zużycia zasobów. Natomiast aplikacja co każdą godzinę zbiera dane z generatorów, oblicza ich średnią moc uzyskaną w ciągu ostatniej godziny i zapisuje dane do relacyjnej bazy danych.<br>

### Nowe komendy do przetwarzania danych z generatorów oraz generowania raportu

Utworzone zostały również dwie nowe komendy:
```
php bin/console app:collect-hourly-data:send
```
^^ Zbiera ona dane z Redis'a z poprzedniej godziny, oblicza średnią uzyskaną moc i zapisuje dane do MySql. <br>
```
php bin/console app:generate-daily-raport:send
```
^^ Służy do generowania raportu z danych uzyskanych z poprzedniego dnia dla każdego generatora z każdej godziny. Raport zapisywany jest w formie pdf w folderze daily_raports. Chciałem aby aplikacja wysyłała maile z raportem dla podanych email'i, ale niestety nie posiadam serwera SMTP :(

### Cron

Co każdą godzinę tj. [15:20, 16:20, 17:20 ...] wywoływany jest Cron wykonujący komendę: <code>php bin/console app:collect-hourly-data:send</code><br>
Codziennie o godzinie 01:00 wywoływany jest Cron wykonujący komendę: <code>php bin/console app:generate-daily-raport:send</code>

## Uwagi
Projekt najlepiej uruchomić na maszynie z procesorem o architekturze x86. Obraz Redis'a nie jest dobrze zoptymalizowany pod procesory o architekturze ARM. Redis po pewnym czasie zapisu danych na procesorach ARM potrafi "spaść z rowerka" i "wypluć" błędem o naruszeniu ochrony pamięci. Jest to spowodowane właśnie architekturą procesora. Przekonałem się o tym podczas robienia tego projektu. Później zmieniłem środowisko z procesorem x86 i nie wystąpiły żadne problemy.
