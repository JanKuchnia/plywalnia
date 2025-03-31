<?php
$wyniki = [];
$najlepsi_plywacy = [];
$blad_polaczenia = false;

try {
    $polaczenie = new mysqli("sql308.infinityfree.com", "if0_38587063", "gaVmPArJ1XJn", "if0_38587063_plywanie");

    if ($polaczenie->connect_error) {
        throw new Exception("Błąd połączenia: " . $polaczenie->connect_error);
    }

    $sql = "
        SELECT 
            z.id_zawodnik AS id_zawodnika,
            CONCAT(z.imie, ' ', z.nazwisko) AS zawodnik_imie_nazwisko,
            IFNULL(CONCAT(TIME_FORMAT(w.czas, '%H:%i:%s'), ' / ', w.dystans, 'm'), 'Brak wyniku') AS zawodnik_wynik,
            w.style AS styl_plywania,
            o.id_opiekuna,
            CONCAT(o.imie, ' ', o.nazwisko) AS opiekun_imie_nazwisko,
            z.id_szkoly,
            s.nazwa AS nazwa_szkoly
        FROM 
            zawodnik z
        LEFT JOIN 
            wynik w ON z.id_zawodnik = w.id_zawodnik
        INNER JOIN 
            opiekun o ON z.id_opiekuna = o.id_opiekuna
        INNER JOIN 
            szkola s ON z.id_szkoly = s.id_szkoly
        ORDER BY 
            w.czas ASC
    ";

    $sql_najlepsi_plywacy = "
        SELECT 
            CONCAT(z.imie, ' ', z.nazwisko) AS zawodnik_imie_nazwisko,
            TIME_FORMAT(w.czas, '%H:%i:%s') AS czas,
            w.dystans,
            w.style AS styl_plywania
        FROM 
            zawodnik z
        INNER JOIN 
            wynik w ON z.id_zawodnik = w.id_zawodnik
        WHERE 
            w.czas IS NOT NULL
        ORDER BY 
            w.czas ASC
        LIMIT 3
    ";

    if (!($wynik = $polaczenie->query($sql))) {
        throw new Exception("Błąd zapytania: " . $polaczenie->error);
    }
    
    while ($wiersz = $wynik->fetch_assoc()) {
        $wyniki[] = $wiersz;
    }

    if (!($wynik_najlepsi_plywacy = $polaczenie->query($sql_najlepsi_plywacy))) {
        throw new Exception("Błąd zapytania: " . $polaczenie->error);
    }

    while ($wiersz = $wynik_najlepsi_plywacy->fetch_assoc()) {
        $najlepsi_plywacy[] = $wiersz;
    }

    $wynik->close();
    $wynik_najlepsi_plywacy->close();
    $polaczenie->close();

} catch (Exception $e) {
    $blad_polaczenia = true;
    $wiadomosc_bledu = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kryta Pływalnia - Zawody i Więcej</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="najlepszyPlywak.jpeg">
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
    </section>
    <section id="basen" class="zawartosc">
        <div class="karta">
            <h3>Nasze Obiekty</h3>
            <p>Oferujemy baseny o różnej głębokości i nowoczesne zaplecze.</p>
        </div>
        <div class="karta">
            <h3>Zajęcia Dodatkowe</h3>
            <p>Zapraszamy na lekcje pływania i zajęcia z aqua fitness.</p>
        </div>
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
                        <td><?php echo htmlspecialchars($plywak['styl_plywania']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
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
                        <th>ID Zawodnika</th>
                        <th>Imię i nazwisko zawodnika</th>
                        <th>Wynik i dystans</th>
                        <th>Styl pływania</th>
                        <th>ID Opiekuna</th>
                        <th>Imię i nazwisko opiekuna</th>
                        <th>Szkoła</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wyniki)): ?>
                        <tr>
                            <td colspan="7">Brak danych w tabeli</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($wyniki as $wiersz): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($wiersz['id_zawodnika']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['zawodnik_imie_nazwisko']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['zawodnik_wynik']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['styl_plywania']); ?></td>
                                <td><?php echo htmlspecialchars($wiersz['id_opiekuna']); ?></td>
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
