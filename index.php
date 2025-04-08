<?php
$wyniki = [];
$najlepsi_plywacy = [];
$blad_polaczenia = false;
$wiadomosc_bledu = '';

try {
    $polaczenie = new mysqli("localhost", "root", "", "plywanie");

    if ($polaczenie->connect_error) {
        throw new Exception("Błąd połączenia z bazą danych");
    }

    $sql = "SELECT 
                z.id_zawodnik AS id_zawodnika,
                CONCAT(z.imie, ' ', z.nazwisko) AS zawodnik_imie_nazwisko,
                IFNULL(CONCAT(TIME_FORMAT(w.czas, '%H:%i:%s'), ' / ', w.dystans, 'm'), 'Brak wyniku') AS zawodnik_wynik,
                w.style AS styl_plywania,
                o.id_opiekuna,
                CONCAT(o.imie, ' ', o.nazwisko) AS opiekun_imie_nazwisko,
                z.id_szkoly,
                s.nazwa AS nazwa_szkoly
            FROM zawodnik z
            LEFT JOIN wynik w ON z.id_zawodnik = w.id_zawodnik
            INNER JOIN opiekun o ON z.id_opiekuna = o.id_opiekuna
            INNER JOIN szkola s ON z.id_szkoly = s.id_szkoly
            ORDER BY w.czas ASC";

    $sql_najlepsi = "SELECT 
                        CONCAT(z.imie, ' ', z.nazwisko) AS zawodnik_imie_nazwisko,
                        TIME_FORMAT(w.czas, '%H:%i:%s') AS czas,
                        w.dystans,
                        w.style AS styl_plywania
                    FROM zawodnik z
                    INNER JOIN wynik w ON z.id_zawodnik = w.id_zawodnik
                    WHERE w.czas IS NOT NULL
                    ORDER BY w.czas ASC
                    LIMIT 3";

    $sql_zawody = "SELECT 
                        z.rok_urodzenia,
                        z.imie,
                        z.nazwisko,
                        TIME_FORMAT(w.czas, '%i:%s.%f') AS wynik,
                        w.dystans,
                        w.style,
                        s.nazwa AS szkola
                    FROM zawodnik z
                    INNER JOIN wynik w ON z.id_zawodnik = w.id_zawodnik
                    INNER JOIN szkola s ON z.id_szkoly = s.id_szkoly
                    ORDER BY z.rok_urodzenia DESC, w.czas ASC";

$sql_zawodnicy_bez_wynikow = "SELECT 
                             z.id_zawodnik AS id_zawodnika,
                             CONCAT(z.imie, ' ', z.nazwisko) AS zawodnik_imie_nazwisko,
                             CONCAT(o.imie, ' ', o.nazwisko) AS opiekun_imie_nazwisko,
                             z.id_szkoly,
                             s.nazwa AS nazwa_szkoly
                         FROM zawodnik z
                         INNER JOIN opiekun o ON z.id_opiekuna = o.id_opiekuna
                         INNER JOIN szkola s ON z.id_szkoly = s.id_szkoly
                         LEFT JOIN wynik w ON z.id_zawodnik = w.id_zawodnik
                         WHERE w.id_wyniku IS NULL
                         ORDER BY z.nazwisko, z.imie";

$zawodnicy_bez_wynikow = [];
if ($result = $polaczenie->query($sql_zawodnicy_bez_wynikow)) {
    while ($row = $result->fetch_assoc()) {
        $zawodnicy_bez_wynikow[] = $row;
    }
    $result->close();
} else {
    throw new Exception("Błąd podczas pobierania listy zawodników bez wyników");
}
    if ($result = $polaczenie->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $wyniki[] = $row;
        }
        $result->close();
    } else {
        throw new Exception("Błąd podczas pobierania listy pływaków");
    }

    if ($result = $polaczenie->query($sql_najlepsi)) {
        while ($row = $result->fetch_assoc()) {
            $najlepsi_plywacy[] = $row;
        }
        $result->close();
    } else {
        throw new Exception("Błąd podczas pobierania najlepszych pływaków");
    }

    $wyniki_zawodow = [];
    if ($result = $polaczenie->query($sql_zawody)) {
        while ($row = $result->fetch_assoc()) {
            $wyniki_zawodow[] = $row;
        }
        $result->close();
    } else {
        throw new Exception("Błąd podczas pobierania wyników zawodów");
    }

    $wyniki_grupowane_dystans = [];
    foreach ($wyniki_zawodow as $wynik) {
        $rocznik = $wynik['rok_urodzenia'];
        $dystans = $wynik['dystans'];
        
        if (!isset($wyniki_grupowane_dystans[$dystans])) {
            $wyniki_grupowane_dystans[$dystans] = [];
        }
        
        if (!isset($wyniki_grupowane_dystans[$dystans][$rocznik])) {
            $wyniki_grupowane_dystans[$dystans][$rocznik] = [];
        }
        
        $wyniki_grupowane_dystans[$dystans][$rocznik][] = $wynik;
    }
    
    $polaczenie->close();

} catch (Exception $e) {
    $blad_polaczenia = true;
    $wiadomosc_bledu = $e->getMessage();
}

function przetlumaczStyl($styl) {
    switch ($styl) {
        case 'kraul':
            return 'stylem dowolnym (kraul)';
        case 'styl klasyczny':
            return 'stylem klasycznym';
        case 'motylkowy':
            return 'stylem motylkowym';
        case 'grzbietowy':
            return 'stylem grzbietowym';
        default:
            return $styl;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kryta Pływalnia - Zawody i Więcej</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <a href="index.php"><h1>Kryta Pływalnia</h1></a>
        <p>Sport, emocje, rywalizacja</p>
    </header>
    <nav>
        <a href="index.php">Strona Główna</a>
        <a href="zapisy.php">Zapisy</a>
        <a href="zawody.php">Zawody</a>
    </nav>
    <section class="glowna">
        <h2>Najlepsze miejsce dla pływaków</h2>
        <p>Dołącz do naszych zawodów i poczuj prawdziwą rywalizację!</p>
    </section>
    <section id="zawody" class="zawartosc">
        <div class="karta">
            <h3>Najbliższe zawody</h3>
            <p>Zapraszamy na turniej, który odbędzie się 20 lutego 2025 roku.</p>
        </div>
        <div class="karta">
            <h3>Regulamin</h3>
            <p>Zapoznaj się z zasadami uczestnictwa i przepisami zawodów.</p>
        </div>
        <div class="karta">
            <h3>Zapisy</h3>
            <p>Rejestracja uczestników otwarta do 15 lutego 2025 roku.</p>
        </div>
        <div class="karta">
            <h3>Nasze Obiekty</h3>
            <p>Oferujemy baseny o różnej głębokości i nowoczesne zaplecze.</p>
        </div>
        <div class="karta">
            <h3>Zajęcia Dodatkowe</h3>
            <p>Zapraszamy na lekcje pływania i zajęcia z aqua fitness.</p>
        </div>
    </section>
    <section class="wyniki-zawodow">
        <h1>WYNIKI GMINNYCH ZAWODÓW PŁYWACKICH</h1>
        <h2>Szkoły Podstawowe, Myślenice <?php echo date('d.m.Y'); ?></h2>
        
        <?php if ($blad_polaczenia): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($wiadomosc_bledu); ?>
            </div>
        <?php else: ?>
            <div class="roczniki-container">
                <div class="roczniki-row">
                    <div class="rocznik-column">
                        <h3 class="dystans-naglowek">50m</h3>
                        <?php if (isset($wyniki_grupowane_dystans['50'])): ?>
                            <?php krsort($wyniki_grupowane_dystans['50']); ?>
                            <?php foreach ($wyniki_grupowane_dystans['50'] as $rocznik => $zawodnicy): ?>
                                <h4>Rocznik <?php echo htmlspecialchars($rocznik); ?></h4>
                                <table class="compact-table">
                                    <thead>
                                        <tr>
                                            <th class="miejsce">m-ce</th>
                                            <th class="czas">czas</th>
                                            <th>nazwisko</th>
                                            <th>szkoła</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        usort($zawodnicy, function($a, $b) {
                                            return $a['wynik'] <=> $b['wynik'];
                                        });
                                        
                                        foreach ($zawodnicy as $index => $wynik): 
                                            $miejsce = $index + 1;
                                            $rzymskie = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
                                            $miejsce_display = $miejsce <= count($rzymskie) ? $rzymskie[$miejsce-1] : $miejsce;
                                        ?>
                                            <tr>
                                                <td class="miejsce"><?php echo $miejsce_display; ?></td>
                                                <td class="czas"><?php echo htmlspecialchars($wynik['wynik']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['nazwisko']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['szkola']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-results">Brak wyników dla dystansu 50m</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="rocznik-column">
                        <h3 class="dystans-naglowek">100m</h3>
                        <?php if (isset($wyniki_grupowane_dystans['100'])): ?>
                            <?php krsort($wyniki_grupowane_dystans['100']); ?>
                            <?php foreach ($wyniki_grupowane_dystans['100'] as $rocznik => $zawodnicy): ?>
                                <h4>Rocznik <?php echo htmlspecialchars($rocznik); ?></h4>
                                <table class="compact-table">
                                    <thead>
                                        <tr>
                                            <th class="miejsce">m-ce</th>
                                            <th class="czas">czas</th>
                                            <th>nazwisko</th>
                                            <th>szkoła</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        usort($zawodnicy, function($a, $b) {
                                            return $a['wynik'] <=> $b['wynik'];
                                        });
                                        
                                        foreach ($zawodnicy as $index => $wynik): 
                                            $miejsce = $index + 1;
                                            $rzymskie = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
                                            $miejsce_display = $miejsce <= count($rzymskie) ? $rzymskie[$miejsce-1] : $miejsce;
                                        ?>
                                            <tr>
                                                <td class="miejsce"><?php echo $miejsce_display; ?></td>
                                                <td class="czas"><?php echo htmlspecialchars($wynik['wynik']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['nazwisko']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['szkola']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-results">Brak wyników dla dystansu 100m</p>
                        <?php endif; ?>
                    </div>
                    

                    <div class="rocznik-column">
                        <h3 class="dystans-naglowek">150m</h3>
                        <?php if (isset($wyniki_grupowane_dystans['150'])): ?>
                            <?php krsort($wyniki_grupowane_dystans['150']); ?>
                            <?php foreach ($wyniki_grupowane_dystans['150'] as $rocznik => $zawodnicy): ?>
                                <h4>Rocznik <?php echo htmlspecialchars($rocznik); ?></h4>
                                <table class="compact-table">
                                    <thead>
                                        <tr>
                                            <th class="miejsce">m-ce</th>
                                            <th class="czas">czas</th>
                                            <th>nazwisko</th>
                                            <th>szkoła</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        usort($zawodnicy, function($a, $b) {
                                            return $a['wynik'] <=> $b['wynik'];
                                        });
                                        
                                        foreach ($zawodnicy as $index => $wynik): 
                                            $miejsce = $index + 1;
                                            $rzymskie = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
                                            $miejsce_display = $miejsce <= count($rzymskie) ? $rzymskie[$miejsce-1] : $miejsce;
                                        ?>
                                            <tr>
                                                <td class="miejsce"><?php echo $miejsce_display; ?></td>
                                                <td class="czas"><?php echo htmlspecialchars($wynik['wynik']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['nazwisko']); ?></td>
                                                <td><?php echo htmlspecialchars($wynik['szkola']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-results">Brak wyników dla dystansu 150m</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div style="display:none;">
            <?php 
            krsort($wyniki_grupowane_dystans); 
            
            foreach ($wyniki_grupowane_dystans as $dystans => $roczniki): 
                krsort($roczniki);
                
                foreach ($roczniki as $rocznik => $zawodnicy):
                    $style = [];
                    foreach ($zawodnicy as $zawodnik) {
                        $styl = $zawodnik['style'];
                        if (!isset($style[$styl])) {
                            $style[$styl] = [];
                        }
                        $style[$styl][] = $zawodnik;
                    }
            ?>
                <h3>Rocznik <?php echo htmlspecialchars($rocznik); ?> - <?php echo htmlspecialchars($dystans); ?>m</h3>
                
                <?php foreach ($style as $styl => $zawodnicy_styl): ?>
                    <h4><?php echo htmlspecialchars(przetlumaczStyl($styl)); ?></h4>
                    
                    <table>
                        <thead>
                            <tr>
                                <th class="miejsce">miejsce</th>
                                <th class="czas">wynik</th>
                                <th>nazwisko</th>
                                <th>imię</th>
                                <th>szkoła</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            usort($zawodnicy_styl, function($a, $b) {
                                return $a['wynik'] <=> $b['wynik'];
                            });
                            
                            foreach ($zawodnicy_styl as $index => $wynik): 
                                $miejsce = $index + 1;
                                $rzymskie = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII'];
                                $miejsce_display = $miejsce <= count($rzymskie) ? $rzymskie[$miejsce-1] : $miejsce;
                            ?>
                                <tr>
                                    <td class="miejsce"><?php echo $miejsce_display; ?></td>
                                    <td class="czas"><?php echo htmlspecialchars($wynik['wynik']); ?></td>
                                    <td><?php echo htmlspecialchars($wynik['nazwisko']); ?></td>
                                    <td><?php echo htmlspecialchars($wynik['imie']); ?></td>
                                    <td><?php echo htmlspecialchars($wynik['szkola']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <section id="basen" class="zawartosc">
    </section>
    <section id="najlepszyPływak">
        <h1>Pływak Sezonu 2024</h1>
        <img src="najlepszyPływak.jpeg" alt="Bartosz Szuba - Najlepszy Pływak">
        <h3>Bartosz Szuba</h3>
        <p class="osiagniecia">
            Mistrz województwa w stylu motylkowym • Rekordzista klubu na dystansie 100m • 
            Reprezentant Polski juniorów
        </p>
        <h2>Top 3 Pływaków</h2>
        <table class="tabela-najlepsi-plywacy">
            <thead>
                <tr>
                    <th>Imię i Nazwisko</th>
                    <th>Czas</th>
                    <th>Dystans</th>
                    <th>Styl Pływania</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($najlepsi_plywacy)): ?>
                    <tr>
                        <td colspan="4">Brak danych o najlepszych pływakach.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($najlepsi_plywacy as $index => $plywak): ?>
                        <tr>
                            <td>
                                <?php echo $index === 0 ? '&#129351; ' : ($index === 1 ? '&#129352; ' : ($index === 2 ? '&#129353; ' : '')); ?>
                                <?php echo htmlspecialchars($plywak['zawodnik_imie_nazwisko']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($plywak['czas']); ?></td>
                            <td><?php echo htmlspecialchars($plywak['dystans']); ?>m</td>
                            <td><?php echo htmlspecialchars(przetlumaczStyl($plywak['styl_plywania'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
    <section class="tabela-sekcja">
    <h1>Lista Zawodników Bez Wyników</h1>
    <?php if ($blad_polaczenia): ?>
        <div class="alert error">
            <?php echo htmlspecialchars($wiadomosc_bledu); ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Imię i nazwisko zawodnika</th>
                    <th>Imię i nazwisko opiekuna</th>
                    <th>Szkoła</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($zawodnicy_bez_wynikow)): ?>
                    <tr>
                        <td colspan="3">Wszyscy zawodnicy mają wyniki</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($zawodnicy_bez_wynikow as $zawodnik): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($zawodnik['zawodnik_imie_nazwisko']); ?></td>
                            <td><?php echo htmlspecialchars($zawodnik['opiekun_imie_nazwisko']); ?></td>
                            <td><?php echo htmlspecialchars($zawodnik['id_szkoly'] . ' - ' . $zawodnik['nazwa_szkoly']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
    <section class="tabela-sekcja">
        <h1>Lista Rejestracji</h1>
        <?php if ($blad_polaczenia): ?>
            <div class="alert error">
                <?php echo htmlspecialchars($wiadomosc_bledu); ?>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Imię i nazwisko zawodnika</th>
                        <th>Wynik i dystans</th>
                        <th>Styl pływania</th>
                        <th>Imię i nazwisko opiekuna</th>
                        <th>Szkoła</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wyniki)): ?>
                        <tr>
                            <td colspan="5">Brak danych w tabeli</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($wyniki as $wiersz): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($wiersz['zawodnik_imie_nazwisko']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['zawodnik_wynik']); ?></td>
                                <td><?php echo htmlspecialchars(przetlumaczStyl($wiersz['styl_plywania'])); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['opiekun_imie_nazwisko']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['id_szkoly'] . ' - ' . $wiersz['nazwa_szkoly']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
    <footer>
    </footer>
</body>
</html>