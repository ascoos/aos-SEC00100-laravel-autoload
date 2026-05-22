<?php
/*
dobu {
    file:id(`example-00000100`),name(`laravelAutoload`) {
        ascoos {
            logo {`
                  __ _  ___  ___ ___   ___   ___     ___   ___
                 / _' |/  / / __/ _ \ / _ \ /  /    / _ \ /  /
                | (_| |\  \| (_| (_) | (_) |\  \   | (_) |\  \
                 \__,_|/__/ \___\___/ \___/ /__/    \___/ /__/
            `},
            name {`ASCOOS OS`},
            version {`1.0.0`},
            category {`Web OS`},
            subcategory {`Web5 / WebAI`},
            description {`A Web 5.0 and Web AI Kernel for decentralized web and IoT applications`},
            creator {`Drogidis Christos`},
            website {`https://www.ascoos.com`},
            issues {`https://support.ascoos.com`},
            support {`support@ascoos.com`},
            license {`[Commercial] http://docs.ascoos.com/lics/ascoos/AGL.html`},
            copyright {`Copyright (c) 2007 - 2026, AlexSoft Software.`},
        },
        example {
            case-study {`SEC00100`},
            source {`libs/laravel/autoload.php`},
            category:langs {
                en {`Framework Integration`},
                el {`Ενσωμάτωση Framework`}
            },
            subcategory:langs {
                en {`Laravel Autoloading & Kernel Bootstrapping`},
                el {`Φόρτωση Laravel & Εκκίνηση Kernel`}
            },
            summary:langs {
                en {`Autoloading, bootstrapping and diagnostic integration of Laravel inside Ascoos OS Web5 Kernel.`},
                el {`Φόρτωση, εκκίνηση και διαγνωστική ενσωμάτωση του Laravel μέσα στο Ascoos OS Web5 Kernel.`}
            },
            desc:langs {
                en {`This example demonstrates the advanced integration of the Laravel framework into Ascoos OS using the LibIn autoloader. 
                    It initializes the framework, binds the application globally, executes diagnostic macros, validates core services, 
                    tests database connectivity, and emits structured events through TMacroHandler and TEventHandler. 
                    The implementation ensures seamless coexistence between Laravel and the Web5 Kernel, enabling mixed usage scenarios 
                    and unified logging across both environments.
                `},
                el {`Το παράδειγμα παρουσιάζει την προχωρημένη ενσωμάτωση του Laravel στο Ascoos OS μέσω του LibIn autoloader. 
                    Αρχικοποιεί το framework, το δεσμεύει στο global scope, εκτελεί διαγνωστικά macros, 
                    επαληθεύει βασικές υπηρεσίες, δοκιμάζει τη σύνδεση στη βάση δεδομένων και εκπέμπει δομημένα συμβάντα 
                    μέσω των TMacroHandler και TEventHandler. 
                    Η υλοποίηση εξασφαλίζει ομαλή συνύπαρξη του Laravel με το Web5 Kernel, επιτρέποντας μικτή χρήση 
                    και ενοποιημένο logging και στα δύο περιβάλλοντα.
                `}
            },
            keywords:langs {
                en {`Laravel, Ascoos OS, Web5 Kernel, LibIn, autoload, macros, events, diagnostics, integration`},
                el {`Laravel, Ascoos OS, Web5 Kernel, LibIn, autoload, macros, events, diagnostics, ενσωμάτωση`}
            },
            created {`2025-10-20 07:00:00`},
            updated {`2026-05-22 14:27:00`}
            author {`Christos Drogidis`},
            since {`1.0.0`},
            sincePHP {`8.4.0`}
        }
    }
}
*/
declare(strict_types=1);

use ASCOOS\OS\Kernel\{
    Core\TError,
    Arrays\Macros\TMacroHandler,
    Arrays\Events\TEventHandler
};

// <EN> Loading via Ascoos OS autoloader
// <EL> Φόρτωση μέσω Ascoos OS autoloader
global $conf, $AOS_LOGS_PATH, $AOS_LIBS_PATH, $utf8;

// <EN> Settings for logging and events to manage logs, reports, and event triggers
// <EL> Ρυθμίσεις για logging και συμβάντα για τη διαχείριση logs, αναφορών και εκπομπής συμβάντων
$properties = [
    'cache' => $conf['cache'],
    'logs' => [
        'useLogger' => true,
        'dir' => $AOS_LOGS_PATH . '/',
        'file' => 'laravel_loads.log'
    ]
];

// <EN> Initialize Ascoos OS macro handler for Laravel tasks
// <EL> Αρχικοποίηση του Ascoos OS macro handler για tasks του Laravel
$macroHandler =& TMacroHandler::getInstance([], $properties);

// <EN> Initialize Ascoos OS event handler for logging
// <EL> Αρχικοποίηση του Ascoos OS event handler για logging
$eventHandler =& TEventHandler::getInstance([], $properties);

try {
    // <EN> Define Laravel base path
    // <EL> Ορισμός της βασικής διαδρομής του Laravel
    define('LARAVEL_BASE_PATH', $AOS_LIBS_PATH . '/laravel');

    // <EN> If the Laravel vendors code autoload file does not exist
    // <EL> Εάν το αρχείο αυτόματης φόρτωσης κώδικα των προμηθευτών Laravel δεν υπάρχει
    if (!file_exists(LARAVEL_BASE_PATH . '/vendor/autoload.php')) {
        new TError('Laravel vendor not found. Ensure archive is uploaded via LibIn.');
    }

    // <EN> Load Laravel vendor autoloader, included in the archive uploaded via LibIn
    // <EL> Φόρτωση του Laravel vendor autoloader, που περιλαμβάνεται στο archive που ανέβηκε μέσω LibIn
    require_once LARAVEL_BASE_PATH . '/vendor/autoload.php';

    // <EN> Bootstrap Laravel application
    // <EL> Εκκίνηση εφαρμογής Laravel
    $laravel_app = require_once LARAVEL_BASE_PATH . '/bootstrap/app.php';

    // <EN> Optionally bind Laravel to global scope for mixed usage
    // <EL> Προαιρετική δέσμευση της εφαρμογής Laravel στο global scope για μικτή χρήση
    $GLOBALS['laravel_app'] = $laravel_app;

    // <EN> Log successful initialization using Laravel logger
    // <EL> Καταγραφή επιτυχούς αρχικοποίησης χρησιμοποιώντας το logger του Laravel
    $macroHandler->addMacro(fn() => $laravel_app->make('log')->info('Laravel initialized with Ascoos OS'));

    // <EN> Diagnostic macro to check core services and DB connection
    // <EL> Διαγνωστικό macro για έλεγχο βασικών υπηρεσιών και σύνδεσης DB
    $macroHandler->addMacro(function () use ($laravel_app, $eventHandler) {
        try {
            // <EN> Verify Laravel app instance
            // <EL> Επαλήθευση instance της Laravel app
            if (!($laravel_app instanceof \Illuminate\Contracts\Foundation\Application)) {
                new TError('Laravel app is not a valid instance of \Illuminate\Contracts\Foundation\Application');
            }

            // <EN> Check core services
            // <EL> Έλεγχος βασικών υπηρεσιών
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
                $eventHandler->logger->log("Laravel diagnostic warning: missing services → " . $utf8->implode(', ', $missing), $eventHandler::DEBUG_LEVEL_WARN);
                $laravel_app->make('log')->warning('Laravel diagnostic warning: missing services → ' . $utf8->implode(', ', $missing));
            }

            // <EN> Test DB connection if configured
            // <EL> Δοκιμή σύνδεσης DB αν έχει ρυθμιστεί
            if ($laravel_app->bound('db')) {
                $laravel_app->make('db')->connection()->getPdo();
                $eventHandler->logger->log("Laravel DB connection test successful", $eventHandler::DEBUG_LEVEL_INFO);
                $laravel_app->make('log')->info('Laravel DB connection test successful');
            }

        } catch (Exception $e) {
            // <EN> Log diagnostic failure
            // <EL> Καταγραφή αποτυχίας διαγνωστικού ελέγχου
            $eventHandler->logger->log("Laravel diagnostic failed: " . $e->getMessage(), $eventHandler::DEBUG_LEVEL_ERROR);
            $laravel_app->make('log')->error('Laravel diagnostic failed: ' . $e->getMessage());
        }
    });

    // <EN> Execute all macros in queue
    // <EL> Εκτέλεση όλων των μακροεντολών στην ουρά
    $macroHandler->runAll();

    // <EN> Register and trigger Laravel integration event
    // <EL> Καταχώριση και ενεργοποίηση συμβάντος ενσωμάτωσης Laravel
    $eventHandler->setTargets(['laravel_init']);
    $eventHandler->register('laravel_init', 'framework', fn() =>
        $eventHandler->logger->log("Laravel integration successful at " . date('Y-m-d H:i:s'), $eventHandler::DEBUG_LEVEL_INFO)
    );
    $eventHandler->trigger('laravel_init', 'framework');

    // <EN> You may also register Laravel services or facades here
    // <EL> Μπορείτε επίσης να καταχωρήσετε εδώ τις υπηρεσίες ή τα facades του Laravel
} catch (Exception $e) {
    // <EN> Handle and log the exception
    // <EL> Διαχείριση και καταγραφή της εξαίρεσης
    $macroHandler->Free();
    $eventHandler->Free();    
    new TError("Error: {$e->getMessage()}");
}

// <EN> Resource cleanup and memory release
// <EL> Εκκαθάριση πόρων και απελευθέρωση μνήμης
$macroHandler->Free();
$eventHandler->Free();
?>