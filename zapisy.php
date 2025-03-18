<?php
$connection_error = false;
$success_message = null;
$error_message = null;

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = new mysqli("localhost", "root", "", "plywanie");
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $zawodnik_imie = filter_input(INPUT_POST, 'zawodnik-imie', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $zawodnik_nazwisko = filter_input(INPUT_POST, 'zawodnik-nazwisko', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $zawodnik_rok = filter_input(INPUT_POST, 'zawodnik-rok', FILTER_VALIDATE_INT);
        $opiekun_imie = filter_input(INPUT_POST, 'opiekun-imie', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $opiekun_nazwisko = filter_input(INPUT_POST, 'opiekun-nazwisko', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $wynik_czas = filter_input(INPUT_POST, 'wynik-czas', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $dystans = filter_input(INPUT_POST, 'dystans', FILTER_VALIDATE_INT);
        $data_plywania = filter_input(INPUT_POST, 'data-plywania', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $szkola_nazwa = filter_input(INPUT_POST, 'szkola', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $miejscowosc = filter_input(INPUT_POST, 'miejscowosc', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $styl = filter_input(INPUT_POST, 'styl', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$zawodnik_rok || $zawodnik_rok < 1900 || $zawodnik_rok > 2025) {
            throw new Exception("Nieprawidłowy rok urodzenia");
        }
        if (!$dystans || $dystans < 25 || $dystans > 1500) {
            throw new Exception("Nieprawidłowy dystans");
        }
        if (!preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/", $wynik_czas)) {
            throw new Exception("Nieprawidłowy format czasu");
        }
        if (!strtotime($data_plywania)) {
            throw new Exception("Nieprawidłowa data");
        }

        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT id_szkoly FROM szkola WHERE nazwa = ? AND miejscowosc = ?");
        $stmt->bind_param("ss", $szkola_nazwa, $miejscowosc);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_szkoly = $row['id_szkoly'];
        } else {
            $stmt = $conn->prepare("INSERT INTO szkola (nazwa, miejscowosc) VALUES (?, ?)");
            $stmt->bind_param("ss", $szkola_nazwa, $miejscowosc);
            $stmt->execute();
            $id_szkoly = $stmt->insert_id;
        }

        $stmt = $conn->prepare("INSERT INTO opiekun (imie, nazwisko, id_szkoly) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $opiekun_imie, $opiekun_nazwisko, $id_szkoly);
        $stmt->execute();
        $id_opiekuna = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO zawodnik (imie, nazwisko, id_szkoly, id_opiekuna, rok_urodzenia) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiis", $zawodnik_imie, $zawodnik_nazwisko, $id_szkoly, $id_opiekuna, $zawodnik_rok);
        $stmt->execute();
        $id_zawodnika = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO wynik (id_zawodnik, id_szkoly, czas, dystans, data_plywania, style) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $id_zawodnika, $id_szkoly, $wynik_czas, $dystans, $data_plywania, $styl);
        $stmt->execute();
        $id_wyniku = $stmt->insert_id;

        $stmt = $conn->prepare("INSERT INTO zgloszenie (id_zawodnik, id_szkoly, id_opiekuna, id_wyniku) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $id_zawodnika, $id_szkoly, $id_opiekuna, $id_wyniku);
        $stmt->execute();

        $conn->commit();
        $success_message = "Zapisano pomyślnie!";
        
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    if (isset($conn) && !$conn->connect_error) {
        $conn->rollback();
        $conn->close();
    }
    $connection_error = true;
    $error_message = "Wystąpił błąd: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kryta Pływalnia - Zapisy</title>
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
    </nav>
    <section id="zapisy" class="formularz-kontener">
        <h2>Formularz zapisów</h2>
        <?php if ($success_message): ?>
            <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="zawodnik-imie">Imię zawodnika</label>
                <input type="text" id="zawodnik-imie" name="zawodnik-imie" required maxlength="40">
            </div>
            <div>
                <label for="zawodnik-nazwisko">Nazwisko zawodnika</label>
                <input type="text" id="zawodnik-nazwisko" name="zawodnik-nazwisko" required maxlength="40">
            </div>
            <div>
                <label for="zawodnik-rok">Rok urodzenia</label>
                <input type="number" id="zawodnik-rok" name="zawodnik-rok" min="1900" max="2025" required>
            </div>
            <div>
                <label for="opiekun-imie">Imię opiekuna</label>
                <input type="text" id="opiekun-imie" name="opiekun-imie" required maxlength="40">
            </div>
            <div>
                <label for="opiekun-nazwisko">Nazwisko opiekuna</label>
                <input type="text" id="opiekun-nazwisko" name="opiekun-nazwisko" required maxlength="40">
            </div>
            <div>
                <label for="wynik-czas">Wynik (czas w formacie HH:MM:SS)</label>
                <input type="time" id="wynik-czas" name="wynik-czas" step="1" required>
            </div>
            <div>
                <label for="dystans">Dystans (w metrach)</label>
                <input type="number" id="dystans" name="dystans" min="25" max="1500" required>
            </div>
            <div>
                <label for="data-plywania">Data pływania</label>
                <input type="date" id="data-plywania" name="data-plywania" required>
            </div>
            <div>
                <label for="szkola">Szkoła</label>
                <input type="text" id="szkola" name="szkola" required maxlength="40">
            </div>
            <div>
                <label for="miejscowosc">Miejscowość</label>
                <input type="text" id="miejscowosc" name="miejscowosc" required maxlength="100">
            </div>
            <div>
                <label for="styl">Styl pływania</label>
                <select id="styl" name="styl" required>
                    <option value="kraul">Kraul</option>
                    <option value="styl klasyczny">Styl klasyczny</option>
                    <option value="motylkowy">Motylkowy</option>
                    <option value="grzbietowy">Grzbietowy</option>
                </select>
            </div>
            <button type="submit">Zapisz się</button>
        </form>
    </section>
    <footer>
    </footer>
</body>
</html>