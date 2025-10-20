# 🧠 Advanced Integration Guide: Laravel in Ascoos OS

This guide presents an **advanced integration of the Laravel Framework** into **Ascoos OS**, leveraging the capabilities of the Web5 Kernel such as macros, events, and global binding. The implementation is based on the `laravel_autoload.php` file and extends basic loading with diagnostics, logging, and event-driven monitoring.

---

## 🎯 Purpose

- Integrate Laravel via the `LibIn` upload system.
- Initialize using `TMacroHandler` and `TEventHandler`.
- Log diagnostics for core services and database connection.
- Emit events (`laravel_init`) with timestamp.
- Provide global access to the Laravel application for mixed usage.

---

## Requirements

- **PHP 8.2.0+** with `strict_types=1`.
- **Ascoos OS** or [Ascoos Web Extended Studio 26](https://awes.ascoos.com).
- **Framework Packages**: Upload and extract Laravel via the `LibIn` system (into `/libs/laravel/` subfolder of **Ascoos OS**) or install manually in the same folder. Composer is not required for installation in **Ascoos OS**.
- **Database**: A database (e.g., MySQL).

---

## 🧩 Core Classes of Ascoos OS

| Class             | Role                                                  |
|------------------|--------------------------------------------------------|
| `TMacroHandler`  | Executes macros, handles logging and deferred actions  |
| `TEventHandler`  | Records and emits events                               |
| `TLoggerHandler` | Internal logging mechanism                             |
| `LibIn`          | Upload system for `.az` archives of third-party frameworks and libraries |

---

## 🔄 Execution Flow

1. **Define path**: `LARAVEL_BASE_PATH = $AOS_LIBS_PATH . '/laravel'`
2. **Vendor check**: If `vendor/autoload.php` is missing, throw exception.
3. **Load Laravel**: `require bootstrap/app.php` and bind to `$GLOBALS['laravel_app']`.
4. **Initialization logging**: Via Laravel logger and macro.
5. **Diagnostic macro**:
   - Check core services (`log`, `auth`, `db`, `router`)
   - Test DB connection
   - Log success or failure
6. **Execute all macros**: **`runAll()`**
7. **Emit `laravel_init` event**: Register and trigger with timestamp.
8. **Resource cleanup**: **`Free()`** for macro and event handlers.

---

## 🧪 Diagnostic Macro

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

## 📂 Implementation File

- [`laravel_autoload.php`](./laravel_autoload.php)

---

## 📈 Expected Logs

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

## 🧩 Extensions

- Log custom events (e.g. `laravel.auth.success`)
- Support for Symfony/Yii via shared macros (see SEC00112: hybrid integration)

---

## 📚 Resources

- [Ascoos OS Documentation](https://os.ascoos.com/docs/)
- [AWES Kernel](https://awes.ascoos.com)
- [Laravel Framework](https://laravel.com)
- [Ascoos OS GitHub Repository](https://github.com/ascoos/os)

## License
This case study is covered under the Ascoos General License (AGL).
