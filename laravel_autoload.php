<?php
/**
 * @ASCOOS-NAME        : Ascoos OS
 * @ASCOOS-VERSION     : 26.0.0
 * @ASCOOS-SUPPORT     : support@ascoos.com
 * @ASCOOS-BUGS        : https://issues.ascoos.com
 * 
 * @CASE-STUDY          : laravel_autoload.php
 * @fileNo              : ASCOOS-OS-CASESTUDY-SEC00100
 * 
 * @desc <English> Integration of Laravel framework into Ascoos OS (PHP Web 5.0 Kernel) via LibIn autoloader, leveraging TMacroHandler and TEventHandler for logging, event management, and seamless bootstrapping with optional global binding for mixed usage.
 * @desc <Greek> Ενσωμάτωση του Laravel framework στο Ascoos OS (PHP Web 5.0 Kernel) μέσω LibIn autoloader, αξιοποιώντας TMacroHandler και TEventHandler για logging, διαχείριση συμβάντων και απρόσκοπτη εκκίνηση με προαιρετική δέσμευση στο global scope για μικτή χρήση.
 * 
 * @since PHP 8.2.0+
 */
declare(strict_types=1);

use ASCOOS\OS\Kernel\{
    Arrays\Macros\TMacroHandler,
    Arrays\Events\TEventHandler
};
use Exception;

// <English> Loading via Ascoos OS autoloader
// <Greek> Φόρτωση μέσω Ascoos OS autoloader
global $conf, $AOS_LOGS_PATH, $AOS_LIBS_PATH;

// <English> Settings for logging and events to manage logs, reports, and event triggers
// <Greek> Ρυθμίσεις για logging και συμβάντα για τη διαχείριση logs, αναφορών και εκπομπής συμβάντων
$properties = [
    'cache' => $conf['cache'],
    'logs' => [
        'useLogger' => true,
        'dir' => $AOS_LOGS_PATH . '/',
        'file' => 'laravel_loads.log'
    ]
];

// <English> Initialize Ascoos OS macro handler for Laravel tasks
// <Greek> Αρχικοποίηση του Ascoos OS macro handler για tasks του Laravel
$macroHandler =& TMacroHandler::getInstance([], $properties);

// <English> Initialize Ascoos OS event handler for logging
// <Greek> Αρχικοποίηση του Ascoos OS event handler για logging
$eventHandler =& TEventHandler::getInstance([], $properties);

try {
    // <English> Define Laravel base path
    // <Greek> Ορισμός της βασικής διαδρομής του Laravel
    define('LARAVEL_BASE_PATH', $AOS_LIBS_PATH . '/laravel');

    // <English> If the Laravel vendors code autoload file does not exist
    // <Greek> Εάν το αρχείο αυτόματης φόρτωσης κώδικα των προμηθευτών Laravel δεν υπάρχει
    if (!file_exists(LARAVEL_BASE_PATH . '/vendor/autoload.php')) {
        throw new Exception('Laravel vendor not found. Ensure archive is uploaded via LibIn.');
    }

    // <English> Load Laravel vendor autoloader, included in the archive uploaded via LibIn
    // <Greek> Φόρτωση του Laravel vendor autoloader, που περιλαμβάνεται στο archive που ανέβηκε μέσω LibIn
    require_once LARAVEL_BASE_PATH . '/vendor/autoload.php';

    // <English> Bootstrap Laravel application
    // <Greek> Εκκίνηση εφαρμογής Laravel
    $laravel_app = require_once LARAVEL_BASE_PATH . '/bootstrap/app.php';

    // <English> Optionally bind Laravel to global scope for mixed usage
    // <Greek> Προαιρετική δέσμευση της εφαρμογής Laravel στο global scope για μικτή χρήση
    $GLOBALS['laravel_app'] = $laravel_app;

    // <English> Log successful initialization using Laravel logger
    // <Greek> Καταγραφή επιτυχούς αρχικοποίησης χρησιμοποιώντας το logger του Laravel
    $macroHandler->addMacro(fn() => $laravel_app->make('log')->info('Laravel initialized with Ascoos OS'));

    // <English> Diagnostic macro to check core services and DB connection
    // <Greek> Διαγνωστικό macro για έλεγχο βασικών υπηρεσιών και σύνδεσης DB
    $macroHandler->addMacro(function () use ($laravel_app, $eventHandler) {
        try {
            // <English> Verify Laravel app instance
            // <Greek> Επαλήθευση instance της Laravel app
            if (!($laravel_app instanceof \Illuminate\Contracts\Foundation\Application)) {
                throw new Exception('Laravel app is not a valid instance of \Illuminate\Contracts\Foundation\Application');
            }

            // <English> Check core services
            // <Greek> Έλεγχος βασικών υπηρεσιών
            $services = ['log', 'auth', 'db', 'router'];
            $missing = [];

            foreach ($services as $service) {
                if (!$laravel_app->bound($service)) {
                    $missing[] = $service;
                }
            }

            if (empty($missing)) {
                $eventHandler->logger->log("Laravel diagnostic passed: all core services are available", $eventHandler::DEBUG_LEVEL_INFO);
                $laravel_app->make('log')->info('Laravel diagnostic passed: all core services available');
            } else {
                $eventHandler->logger->log("Laravel diagnostic warning: missing services → " . implode(', ', $missing), $eventHandler::DEBUG_LEVEL_WARN);
                $laravel_app->make('log')->warning('Laravel diagnostic warning: missing services → ' . implode(', ', $missing));
            }

            // <English> Test DB connection if configured
            // <Greek> Δοκιμή σύνδεσης DB αν έχει ρυθμιστεί
            if ($laravel_app->bound('db')) {
                $laravel_app->make('db')->connection()->getPdo();
                $eventHandler->logger->log("Laravel DB connection test successful", $eventHandler::DEBUG_LEVEL_INFO);
                $laravel_app->make('log')->info('Laravel DB connection test successful');
            }

        } catch (Exception $e) {
            // <English> Log diagnostic failure
            // <Greek> Καταγραφή αποτυχίας διαγνωστικού ελέγχου
            $eventHandler->logger->log("Laravel diagnostic failed: " . $e->getMessage(), $eventHandler::DEBUG_LEVEL_ERROR);
            $laravel_app->make('log')->error('Laravel diagnostic failed: ' . $e->getMessage());
        }
    });

    // <English> Execute all macros in queue
    // <Greek> Εκτέλεση όλων των μακροεντολών στην ουρά
    $macroHandler->runAll();

    // <English> Register and trigger Laravel integration event
    // <Greek> Καταχώριση και ενεργοποίηση συμβάντος ενσωμάτωσης Laravel
    $eventHandler->setTargets(['laravel_init']);
    $eventHandler->register('laravel_init', 'framework', fn() =>
        $eventHandler->logger->log("Laravel integration successful at " . date('Y-m-d H:i:s'), $eventHandler::DEBUG_LEVEL_INFO)
    );
    $eventHandler->trigger('laravel_init', 'framework');

    // <English> You may also register Laravel services or facades here
    // <Greek> Μπορείτε επίσης να καταχωρήσετε εδώ τις υπηρεσίες ή τα facades του Laravel
} catch (Exception $e) {
    // <English> Handle and log the exception
    // <Greek> Διαχείριση και καταγραφή της εξαίρεσης
    error_log("Error: {$e->getMessage()}");
    echo "Error: {$e->getMessage()}\n";
}

// <English> Resource cleanup and memory release
// <Greek> Εκκαθάριση πόρων και απελευθέρωση μνήμης
$macroHandler->Free();
$eventHandler->Free();
?>