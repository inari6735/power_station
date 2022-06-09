# Power Station
Story
Elektrownia posiada 20 generatorów prądu oraz każdy poszczególny generator potrafi wygenerować prąd o maksymalnej mocy 1kW. Aplikacja dokonuje dwóch pomiarów w ciągu jednej sekundy dla każdego poszczególnego generatora prądu. Dane te są zbierane co każdą godzinę i wyliczana jest średnia moc wygenerowanego prądu dla każdego generatora w kW, w ciągu całej godziny. Codziennie zapisywany jest raport z dnia poprzedniego odnośnie wygenerowanego prądu w ciągu każdej godziny dla każdego poszczególnego generatora w MW. Aplikacja posiada GUI pozwalające na wyświetlanie danych dla poszczególnego generatora w podanym okresie.

Główne narzędzia:
- PHP 8.1.6
- Symfony 6.1.0 [nowo generowane annotacje są cudowne]
- Composer 2.3.7
- MySql 8.0.29
- Redis 6.2.7

Instalacja i działanie aplikacji:

Aby uruchomić aplikację należy pobrać repozytorium: https://github.com/inari6735/power_station
Następnie budujemy projekt: docker compose up --build

Po zbudowaniu projektu powinniśmy mieć możliwość wejść na stronę główną pod adresem: https://localhost/
Powinna ukazać nam się strona główna projektu ale bez możliwości wyboru generatora, ponieważ nie posiadamy żadnych wpisów na bazie. Aby to poprawić musimy wejść do kontenera php.<br>
Pobierzmy id naszego kontenera php komendą: docker ps<br>
Wejdźmy do kontenera za pomocą: docker exec -it [container_id] sh

Aby zbudować strukturę bazy danych wykonujemy w konterze komendy:<br>
php bin/console make:migration<br>
php bin/console doctrine:migrations:migrate

Teraz możemy "zasiać" bazę danymi. Aby to zrobić wykonujemy komendę:<br>
php bin/console doctrine:fixtures:load<br>
Załadowaliśmy właśnie dane 20 generatorów oraz przykładowe statystyki generatorów za rok 2019. Teraz wchodząc na stronę główną mamy możliwość filtrowania danych dla poszczególnych generatorów w podanym okresie.

Aby uruchomić zbieranie aktualnych statystyk z dwóch pomiarów w ciągu jednej sekundy dla każdego generatora należy najpierw uruchomić worker komendą:<br>
php bin/console messenger:consume<br>
Następnie aby zacząć zbierać dane musimy wejść pod link: https://localhost/generator/stats/redis<br>
Od teraz dane zbierane są asynchronicznie i zapisywane na bazie Redis. Aby zakończyć zbieranie danych wystarczy zamknąć okno przeglądarki. Generowanych danych jest bardzo dużo dlatego też zostało to zrobione w takiej formie aby móc szybko zakończyć działanie.

Żeby móc zobaczyć wygenerowane dane musimy wejść do konteneru Redis'a. Wchodzimy do niego tak samo jak do konteneru PHP. Następnie musimy wejść do CLI Redis'a dlatego wywołujemy komendę:
redis-cli<br>
Autoryzujemy się komendą: AUTH Piotrek120<br>
Wyświetlamy wszystkie klucze: keys *<br>

Dane te są zapisane w formie hash'a oraz ich TTL(czas życia) wynosi 2 godziny. Jest to zrobione w celu ograniczenia zużycia zasobów. Przykładowo 1 milion hash'y z 5 polami zajmuje około 160MB. Natomiast aplikacja co każdą godzinę zbiera dane z generatorów, oblicza ich średnią moc uzyskaną w ciągu ostatniej godziny i zapisuje dane do relacyjnej bazy danych.<br>

Utworzone zostały również dwie nowe komendy:
php bin/console app:collect-hourly-data:send - Zbiera ona dane z Redis'a z poprzedniej godziny, oblicza średnią uzyskaną moc i zapisuje dane do MySql. <br>
php bin/console app:generate-daily-raport:send - Służy do generowania raportu z danych uzyskanych z poprzedniego dnia dla każdego generatora z każej godziny. Raport zapisywany jest w formie pdf w folderze daily_raports. Chciałem aby aplikacja wysyłała maile z raportem dla podanych email'i, ale niestety nie posiadam serwera SMTP :(

Co każdą godzinę tj. [15:20, 16:20, 17:20 ...] wywoływany jest Cron wykonujący komendę: php bin/console app:collect-hourly-data:send
Codziennie o godzinie 01:00 wywoływany jest Cron wykonujący komendę: php bin/console app:generate-daily-raport:send
