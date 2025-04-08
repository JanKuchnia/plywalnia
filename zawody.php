<?php
$connection_error = false;
$success_message = null;
$error_message = null;

try {
    $polaczenie = new mysqli("localhost", "root", "", "plywanie");

    if ($polaczenie->connect_error) {
        throw new Exception("Błąd połączenia z bazą danych");
    }

    $zawodnicy_query = "SELECT id_zawodnik, CONCAT(imie, ' ', nazwisko) AS zawodnik_name FROM zawodnik";
    $zawodnicy_result = $polaczenie->query($zawodnicy_query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_zawodnika = filter_input(INPUT_POST, 'zawodnik', FILTER_VALIDATE_INT);
        $dystans = filter_input(INPUT_POST, 'dystans', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $czas = filter_input(INPUT_POST, 'czas', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $styl = filter_input(INPUT_POST, 'styl', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!$id_zawodnika || !$dystans || !$czas || !$styl) {
            throw new Exception("Proszę wypełnić wszystkie pola.");
        }

        $stmt = $polaczenie->prepare("SELECT id_szkoly FROM zawodnik WHERE id_zawodnik = ?");
        $stmt->bind_param("i", $id_zawodnika);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $id_szkoly = $row['id_szkoly'];

        $stmt = $polaczenie->prepare("INSERT INTO wynik (id_zawodnik, id_szkoly, czas, dystans, style) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $id_zawodnika, $id_szkoly, $czas, $dystans, $styl);
        $stmt->execute();

        $success_message = "Wynik został dodany pomyślnie!";
        $stmt->close();
    }
} catch (Exception $e) {
    $connection_error = true;
    $error_message = "Wystąpił błąd: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kryta Pływalnia - Zawody</title>
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
    <section id="zawody" class="formularz-kontener">
        <h2>Dodaj Wynik Zawodnika</h2>
        <?php if ($success_message): ?>
            <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="zawodnik">Wybierz Zawodnika</label>
                <select id="zawodnik" name="zawodnik" required>
                    <option value="">Wybierz zawodnika</option>
                    <?php while ($zawodnik = $zawodnicy_result->fetch_assoc()): ?>
                        <option value="<?php echo $zawodnik['id_zawodnik']; ?>">
                            <?php echo htmlspecialchars($zawodnik['zawodnik_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="dystans">Dystans</label>
                <select id="dystans" name="dystans" required>
                    <option value="">Wybierz dystans</option>
                    <option value="50">50m</option>
                    <option value="100">100m</option>
                    <option value="150">150m</option>
                </select>
            </div>
            <div>
                <label for="czas">Czas</label>
                <input type="time" id="czas" name="czas" step="1" required>
            </div>
            <div>
                <label for="styl">Styl Pływania</label>
                <select id="styl" name="styl" required>
                    <option value="">Wybierz styl</option>
                    <option value="kraul">Kraul</option>
                    <option value="styl klasyczny">Styl klasyczny</option>
                    <option value="motylkowy">Motylkowy</option>
                    <option value="grzbietowy">Grzbietowy</option>
                </select>
            </div>
            <button type="submit">Dodaj Wynik</button>
        </form>
    </section>
    <footer>
    </footer>
</body>
</html>