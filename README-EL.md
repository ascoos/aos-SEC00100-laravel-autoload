# 🧠 Οδηγός Προχωρημένης Ενσωμάτωσης: Laravel στο Ascoos OS

Αυτός ο οδηγός παρουσιάζει μια **προχωρημένη μορφή ενσωμάτωσης του Laravel Framework** στο **Ascoos OS**, αξιοποιώντας τις δυνατότητες του Web5 Kernel όπως macros, events, και global binding. Η υλοποίηση βασίζεται στο αρχείο `laravel_autoload.php` και επεκτείνει την απλή φόρτωση με διαγνωστικά, logging και event-driven παρακολούθηση.

---

## 🎯 Σκοπός

- Ενσωμάτωση του Laravel μέσω του `LibIn` upload system.
- Αρχικοποίηση με `TMacroHandler` και `TEventHandler`.
- Καταγραφή διαγνωστικών για core services και DB σύνδεση.
- Εκπομπή συμβάντων (`laravel_init`) με χρονική σήμανση.
- Παγκόσμια πρόσβαση στην εφαρμογή Laravel για μικτή χρήση.

---

## Προαπαιτούμενα

- **PHP 8.2.0+** με `strict_types=1`.
- **Ascoos OS** ή το [Ascoos Web Extended Studio 26](https://awes.ascoos.com).
- **Πακέτα Πλαισίων**: Ανεβάστε και αποσυμπιέστε το Laravel μέσω του συστήματος `LibIn`(στον υποφάκελο `/libs/laravel/` του **Ascoos OS**) ή χειροκίνητης εγκατάστασης στον ίδιο φάκελο. Δεν απαιτείται composer για την εγκατάσταση στο **Ascoos OS**.
- **Βάση Δεδομένων**: Μια βάση δεδομένων (π.χ. MySQL).


## 🧩 Κύριες Κλάσεις του Ascoos OS

| Κλάση | Ρόλος |
|-------|-------|
| `TMacroHandler` | Εκτέλεση macros, logging και deferred actions |
| `TEventHandler` | Καταγραφή και εκπομπή συμβάντων |
| `TLoggerHandler` | Εσωτερικός μηχανισμός logging |
| `LibIn` | Σύστημα upload αρχείων `.az` για frameworks και βιβλιοθήκες τρίτων |

---

## 🔄 Ροή Εκτέλεσης

1. **Ορισμός διαδρομής**: `LARAVEL_BASE_PATH = $AOS_LIBS_PATH . '/laravel'`
2. **Έλεγχος vendor**: Αν δεν υπάρχει `vendor/autoload.php`, γίνεται throw εξαίρεσης.
3. **Φόρτωση Laravel**: `require bootstrap/app.php` και bind στο `$GLOBALS['laravel_app']`.
4. **Logging αρχικοποίησης**: Μέσω Laravel logger και macro.
5. **Διαγνωστικά macro**:
   - Έλεγχος core services (`log`, `auth`, `db`, `router`)
   - Δοκιμή σύνδεσης DB
   - Καταγραφή επιτυχίας ή αποτυχίας
6. **Εκτέλεση όλων των macros**: **`runAll()`**
7. **Συμβάν `laravel_init`**: Καταχώριση και ενεργοποίηση με timestamp.
8. **Καθαρισμός πόρων**: **`Free()`** για macro και event handlers.

---

## 🧪 Διαγνωστικό Macro

```php
$macroHandler->addMacro(function () use ($laravel_app, $eventHandler) {
    $services = ['log', 'auth', 'db', 'router'];
    $missing = array_filter($services, fn($s) => !$laravel_app->bound($s));
    
    if (empty($missing)) {
        $eventHandler->logger->log("Laravel diagnostic passed", $eventHandler::DEBUG_LEVEL_INFO);
    } else {
        $eventHandler->logger->log("Missing services: " . implode(', ', $missing), $eventHandler::DEBUG_LEVEL_WARN);
    }

    if ($laravel_app->bound('db')) {
        $laravel_app->make('db')->connection()->getPdo();
    }
});
```

---

## 📂 Αρχείο Υλοποίησης

- [`laravel_autoload.php`](./laravel_autoload.php)

---

## 📈 Αναμενόμενα Logs

- **Laravel Logger**:
  ```
  [INFO] Laravel initialized with Ascoos OS
  [INFO] Laravel diagnostic passed: all core services available
  ```
- **Event Logger**:
  ```
  [INFO] Laravel integration successful at 2025-10-04 19:27:00
  ```

---

## 🧩 Επεκτάσεις

- Καταγραφή custom events (π.χ. `laravel.auth.success`)
- Υποστήριξη για Symfony/Yii μέσω κοινών macros (Δες το SEC00112: hybrid integration)

---

## 📚 Πόροι

- [Ascoos OS Documentation](https://os.ascoos.com/docs/)
- [AWES Kernel](https://awes.ascoos.com)
- [Laravel Framework](https://laravel.com)
- [Ascoos OS GitHub Repository](https://github.com/ascoos/os)

## Άδεια Χρήσης
Αυτή η μελέτη καλύπτεται από την Ascoos General License (AGL).