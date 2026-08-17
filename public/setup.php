<?php
/**
 * Danosla Production Services & Setup Dashboard
 * Consolidates migrations, seeders, background queue workers, and Laravel Reverb websocket servers.
 */

// 1. Security Configuration
$securityKey = null;

// Attempt to read custom SETUP_KEY from .env
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/^SETUP_KEY=(.*)$/m', $envContent, $matches)) {
        $securityKey = trim(str_replace(['"', "'"], '', $matches[1]));
    }
}

// Fallback key if not set in .env
if (empty($securityKey)) {
    $securityKey = 'danosla_secure_setup';
}

$userKey = $_GET['key'] ?? $_POST['key'] ?? '';
$isAuthenticated = ($userKey === $securityKey);

// If not authenticated, render login form and terminate
if (!$isAuthenticated) {
    renderLoginForm($securityKey, $userKey !== '');
    exit;
}

// 2. Load Laravel Bootstrap
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 3. Process Management Configurations
$storageLogDir = dirname(__DIR__) . '/storage/logs';
$queuePidFile = $storageLogDir . '/queue_worker.pid';
$queueLogFile = $storageLogDir . '/queue_worker.log';
$reverbPidFile = $storageLogDir . '/reverb_server.pid';
$reverbLogFile = $storageLogDir . '/reverb_server.log';
$buildPidFile = $storageLogDir . '/frontend_build.pid';
$buildLogFile = $storageLogDir . '/frontend_build.log';
$buildStatusFile = $storageLogDir . '/frontend_build.status';

// Helper: Run Background Process
function startBackgroundProcess($command, $logFile, $pidFile) {
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    touch($logFile);
    
    $artisanPath = dirname(__DIR__) . '/artisan';
    $fullCommand = "php " . escapeshellarg($artisanPath) . " " . $command;
    
    if (substr(php_uname(), 0, 7) == "Windows") {
        // Windows execution using PowerShell
        $escapedArtisan = str_replace('\\', '/', $artisanPath);
        $escapedLog = str_replace('\\', '/', $logFile);
        $psCmd = "powershell -Command \"Start-Process php -ArgumentList '\"\"$escapedArtisan\"\" $command' -RedirectStandardOutput '\"$escapedLog\"' -RedirectStandardError '\"$escapedLog\"' -NoNewWindow -PassThru | Select-Object -ExpandProperty Id\"";
        
        $pid = shell_exec($psCmd);
        $pid = trim($pid);
        if (is_numeric($pid)) {
            file_put_contents($pidFile, $pid);
            return (int)$pid;
        }
    } else {
        // Linux execution using nohup
        $cmd = "nohup php " . escapeshellarg($artisanPath) . " $command > " . escapeshellarg($logFile) . " 2>&1 & echo \$!";
        $pid = shell_exec($cmd);
        $pid = trim($pid);
        if (is_numeric($pid)) {
            file_put_contents($pidFile, $pid);
            return (int)$pid;
        }
    }
    return false;
}

// Helper: Run an arbitrary shell command in the background (used for npm ci / npm run build).
// Unlike startBackgroundProcess() this does not prefix the command with "php artisan" and
// runs with the given working directory. It also records the command's exit code to
// $statusFile so the dashboard can show success/failure after the process finishes.
function startShellBackgroundProcess($command, $logFile, $pidFile, $cwd, $statusFile) {
    $dir = dirname($logFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    touch($logFile);
    file_put_contents($statusFile, 'running');

    if (substr(php_uname(), 0, 7) == "Windows") {
        // Windows execution using PowerShell. Exit code of the last command in the
        // chain is written to $statusFile once the whole chain completes.
        $escapedLog = str_replace('\\', '/', $logFile);
        $escapedStatus = str_replace('\\', '/', $statusFile);
        $escapedCwd = str_replace('\\', '/', $cwd);
        $inner = "Set-Location -LiteralPath '$escapedCwd'; $command; " .
                 "if (\$?) { 'success' } else { 'failed' } | Set-Content -Path '$escapedStatus'";
        $psCmd = "powershell -Command \"Start-Process powershell -ArgumentList '-NoProfile','-Command','$inner' " .
                 "-RedirectStandardOutput '$escapedLog' -RedirectStandardError '$escapedLog' -NoNewWindow -PassThru | " .
                 "Select-Object -ExpandProperty Id\"";

        $pid = trim(shell_exec($psCmd));
        if (is_numeric($pid)) {
            file_put_contents($pidFile, $pid);
            return (int)$pid;
        }
    } else {
        // Linux execution using nohup + a subshell so we can capture the exit code
        // of the "&&" chain after it finishes, without blocking this HTTP request.
        $escapedCwd = escapeshellarg($cwd);
        $escapedStatus = escapeshellarg($statusFile);
        $inner = "cd $escapedCwd && ($command); echo \$? > $escapedStatus.tmp && " .
                 "(cat $escapedStatus.tmp | grep -q '^0$' && echo success > $escapedStatus || echo failed > $escapedStatus); " .
                 "rm -f $escapedStatus.tmp";
        $cmd = "nohup bash -c " . escapeshellarg($inner) . " > " . escapeshellarg($logFile) . " 2>&1 & echo \$!";
        $pid = trim(shell_exec($cmd));
        if (is_numeric($pid)) {
            file_put_contents($pidFile, $pid);
            return (int)$pid;
        }
    }
    file_put_contents($statusFile, 'failed');
    return false;
}

// Helper: Check if Process is Running
function isProcessRunning($pidFile) {
    if (!file_exists($pidFile)) return false;
    $pid = trim(file_get_contents($pidFile));
    if (empty($pid) || !is_numeric($pid)) return false;
    
    if (substr(php_uname(), 0, 7) == "Windows") {
        $output = [];
        exec("tasklist /FI \"PID eq $pid\" 2>&1", $output);
        foreach ($output as $line) {
            if (strpos($line, (string)$pid) !== false) {
                return true;
            }
        }
        return false;
    } else {
        if (file_exists("/proc/$pid")) {
            return true;
        }
        if (function_exists('posix_kill')) {
            return posix_kill((int)$pid, 0);
        }
        $output = [];
        exec("ps -p $pid 2>&1", $output);
        return count($output) > 1;
    }
}

// Helper: Stop Process
function stopProcess($pidFile) {
    if (!file_exists($pidFile)) return false;
    $pid = trim(file_get_contents($pidFile));
    if (empty($pid) || !is_numeric($pid)) {
        @unlink($pidFile);
        return false;
    }
    
    if (substr(php_uname(), 0, 7) == "Windows") {
        exec("taskkill /F /PID $pid 2>&1");
    } else {
        exec("kill -15 $pid 2>&1");
        usleep(300000);
        if (file_exists("/proc/$pid") || (function_exists('posix_kill') && posix_kill((int)$pid, 0))) {
            exec("kill -9 $pid 2>&1");
        }
    }
    @unlink($pidFile);
    return true;
}

// Helper: Socket check if Port is Open
function isPortOpen($host, $port) {
    if ($host === '0.0.0.0') {
        $host = '127.0.0.1';
    }
    $connection = @fsockopen($host, $port, $errno, $errstr, 0.5);
    if (is_resource($connection)) {
        fclose($connection);
        return true;
    }
    return false;
}

// 4. Handle Actions
$terminalOutput = '';
$actionMessage = '';
$actionStatus = 'success';

$action = $_GET['action'] ?? $_POST['action'] ?? '';
if (!empty($action)) {
    switch ($action) {
        case 'migrate':
            try {
                $kernel->call('migrate', ['--force' => true, '--no-interaction' => true]);
                $terminalOutput = $kernel->output();
                $actionMessage = "Migrations ran successfully!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Failed to run migrations.";
                $actionStatus = 'error';
            }
            break;
            
        case 'seed':
            try {
                $kernel->call('db:seed', ['--force' => true]);
                $terminalOutput = $kernel->output();
                $actionMessage = "Database seeding completed!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Database seeding failed.";
                $actionStatus = 'error';
            }
            break;
            
        case 'storage_link':
            try {
                $kernel->call('storage:link');
                $terminalOutput = $kernel->output();
                $actionMessage = "Storage link created successfully!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Failed to create storage link.";
                $actionStatus = 'error';
            }
            break;
            
        case 'key_generate':
            try {
                $kernel->call('key:generate', ['--force' => true]);
                $terminalOutput = $kernel->output();
                $actionMessage = "Application key generated successfully!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Failed to generate application key.";
                $actionStatus = 'error';
            }
            break;
            
        case 'optimize':
            try {
                $kernel->call('optimize');
                $terminalOutput = $kernel->output();
                $actionMessage = "Caching and optimization complete!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Optimization failed.";
                $actionStatus = 'error';
            }
            break;
            
        case 'optimize_clear':
            try {
                $kernel->call('optimize:clear');
                $terminalOutput = $kernel->output();
                $actionMessage = "Cache cleared successfully!";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Failed to clear cache.";
                $actionStatus = 'error';
            }
            break;
            
        case 'start_worker':
            if (isProcessRunning($queuePidFile)) {
                $actionMessage = "Queue worker is already running!";
                $actionStatus = 'warning';
            } else {
                $pid = startBackgroundProcess("queue:listen --tries=3", $queueLogFile, $queuePidFile);
                if ($pid) {
                    $actionMessage = "Background Queue Worker started (PID: $pid)";
                } else {
                    $actionMessage = "Failed to start background Queue Worker. Check permissions or PHP functions.";
                    $actionStatus = 'error';
                }
            }
            break;
            
        case 'stop_worker':
            if (stopProcess($queuePidFile)) {
                $actionMessage = "Background Queue Worker stopped.";
            } else {
                $actionMessage = "Queue worker was not running or could not be stopped.";
                $actionStatus = 'warning';
            }
            break;
            
        case 'run_worker_sync':
            try {
                $kernel->call('queue:work', ['--stop-when-empty' => true]);
                $terminalOutput = $kernel->output();
                $actionMessage = "Processed pending jobs (sync mode)";
            } catch (\Exception $e) {
                $terminalOutput = $e->getMessage();
                $actionMessage = "Sync worker execution failed.";
                $actionStatus = 'error';
            }
            break;
            
        case 'start_reverb':
            if (isProcessRunning($reverbPidFile)) {
                $actionMessage = "Reverb server is already running!";
                $actionStatus = 'warning';
            } else {
                $reverbHost = env('REVERB_SERVER_HOST', '0.0.0.0');
                $reverbPort = env('REVERB_SERVER_PORT', 8080);
                $pid = startBackgroundProcess("reverb:start --host=$reverbHost --port=$reverbPort", $reverbLogFile, $reverbPidFile);
                if ($pid) {
                    $actionMessage = "Background Reverb Server started (PID: $pid)";
                } else {
                    $actionMessage = "Failed to start background Reverb Server.";
                    $actionStatus = 'error';
                }
            }
            break;
            
        case 'stop_reverb':
            if (stopProcess($reverbPidFile)) {
                $actionMessage = "Background Reverb Server stopped.";
            } else {
                $actionMessage = "Reverb server was not running or could not be stopped.";
                $actionStatus = 'warning';
            }
            break;
            
        case 'build_frontend':
            if (isProcessRunning($buildPidFile)) {
                $actionMessage = "Frontend build is already running!";
                $actionStatus = 'warning';
            } else {
                $projectRoot = dirname(__DIR__);
                $pid = startShellBackgroundProcess('npm ci && npm run build', $buildLogFile, $buildPidFile, $projectRoot, $buildStatusFile);
                if ($pid) {
                    $actionMessage = "Frontend build started (PID: $pid). 'npm ci && npm run build' can take a minute or two — watch the log below and refresh this page.";
                } else {
                    $actionMessage = "Failed to start frontend build. Check that Node.js/npm is installed and reachable in PATH for the web server user.";
                    $actionStatus = 'error';
                }
            }
            break;

        case 'clear_build_log':
            if (file_exists($buildLogFile)) {
                file_put_contents($buildLogFile, '');
                $actionMessage = "Frontend build log cleared.";
            }
            break;

        case 'clear_worker_log':
            if (file_exists($queueLogFile)) {
                file_put_contents($queueLogFile, '');
                $actionMessage = "Queue worker log cleared.";
            }
            break;
            
        case 'clear_reverb_log':
            if (file_exists($reverbLogFile)) {
                file_put_contents($reverbLogFile, '');
                $actionMessage = "Reverb server log cleared.";
            }
            break;
            
        case 'run_custom':
            $customCommand = $_POST['custom_command'] ?? '';
            if (!empty($customCommand)) {
                // Sanitize/Restricted commands check
                $blacklist = ['tinker', 'serve', 'down'];
                $firstWord = explode(' ', trim($customCommand))[0];
                if (in_array($firstWord, $blacklist)) {
                    $actionMessage = "Command '$firstWord' is restricted for security reasons.";
                    $actionStatus = 'error';
                } else {
                    try {
                        // Split command and arguments
                        $parts = preg_split('/\s+/', $customCommand);
                        $cmd = array_shift($parts);
                        $args = [];
                        foreach ($parts as $part) {
                            if (strpos($part, '=') !== false) {
                                list($k, $v) = explode('=', $part, 2);
                                $args[$k] = $v;
                            } else {
                                $args[$part] = true;
                            }
                        }
                        $kernel->call($cmd, $args);
                        $terminalOutput = $kernel->output();
                        $actionMessage = "Command 'php artisan $customCommand' executed.";
                    } catch (\Exception $e) {
                        $terminalOutput = $e->getMessage();
                        $actionMessage = "Failed to execute custom command.";
                        $actionStatus = 'error';
                    }
                }
            }
            break;
    }
}

// 5. Gather Statuses
$queueRunning = isProcessRunning($queuePidFile);
$queuePid = $queueRunning ? trim(file_get_contents($queuePidFile)) : null;

$reverbRunning = isProcessRunning($reverbPidFile);
$reverbPid = $reverbRunning ? trim(file_get_contents($reverbPidFile)) : null;

$reverbHost = env('REVERB_SERVER_HOST', '0.0.0.0');
$reverbPort = env('REVERB_SERVER_PORT', 8080);
$reverbPortActive = isPortOpen($reverbHost, $reverbPort);

// Fast-failing commands (e.g. "npm: command not found") can exit in well under a
// second, which opens a race window where the OS reuses that PID for an unrelated
// process before this page reloads — a naive isProcessRunning() check would then
// report the build as still "running" forever. The background script always writes
// a terminal 'success'/'failed' line to $buildStatusFile before it exits, so trust
// that first; only fall back to the PID check while the status still says 'running'
// (which also correctly detects a build that was killed without finishing).
$buildStatusRaw = file_exists($buildStatusFile) ? trim(file_get_contents($buildStatusFile)) : 'never';
if ($buildStatusRaw === 'success' || $buildStatusRaw === 'failed') {
    $buildRunning = false;
    $buildLastStatus = $buildStatusRaw;
} else {
    $buildRunning = isProcessRunning($buildPidFile);
    $buildLastStatus = $buildRunning ? 'running' : 'failed';
}
$buildPid = $buildRunning ? trim(file_get_contents($buildPidFile)) : null;

// Detect whether Node/npm are even reachable for the web server user — a very common
// reason "npm ci && npm run build" silently fails to start on shared hosting.
$npmVersion = trim((string) @shell_exec('npm -v 2>&1'));
$nodeVersion = trim((string) @shell_exec('node -v 2>&1'));
$npmAvailable = !empty($npmVersion) && preg_match('/^\d+\.\d+\.\d+$/', $npmVersion);
$nodeAvailable = !empty($nodeVersion) && preg_match('/^v?\d+\.\d+\.\d+$/', $nodeVersion);

// Read logs
$queueLogLines = '';
if (file_exists($queueLogFile)) {
    $queueLogLines = getLastLines($queueLogFile, 35);
}
$reverbLogLines = '';
if (file_exists($reverbLogFile)) {
    $reverbLogLines = getLastLines($reverbLogFile, 35);
}
$buildLogLines = '';
if (file_exists($buildLogFile)) {
    $buildLogLines = getLastLines($buildLogFile, 60);
}

function getLastLines($filepath, $num = 35) {
    if (!file_exists($filepath)) return '';
    $file = fopen($filepath, 'r');
    $lines = [];
    while (($line = fgets($file)) !== false) {
        $lines[] = $line;
        if (count($lines) > $num) {
            array_shift($lines);
        }
    }
    fclose($file);
    return implode('', $lines);
}

// Check database connection status
$dbConnected = false;
$dbError = '';
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    $dbConnected = true;
} catch (\Exception $e) {
    $dbError = $e->getMessage();
}

// HTML View Helper Functions
function renderLoginForm($securityKey, $failed = false) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Authorization Required - Danosla Admin</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --bg: #0b0f19;
                --card-bg: rgba(17, 24, 39, 0.7);
                --text: #f3f4f6;
                --primary: #6366f1;
                --primary-glow: rgba(99, 102, 241, 0.4);
                --error: #ef4444;
            }
            body {
                font-family: 'Outfit', sans-serif;
                background-color: var(--bg);
                color: var(--text);
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background-image: radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                                  radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 40%);
            }
            .login-card {
                background: var(--card-bg);
                backdrop-filter: blur(16px);
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 24px;
                padding: 40px;
                width: 100%;
                max-width: 420px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), 0 0 80px rgba(99, 102, 241, 0.05);
                text-align: center;
            }
            h1 {
                font-size: 28px;
                margin-bottom: 8px;
                font-weight: 700;
                background: linear-gradient(135deg, #a5b4fc, #6366f1);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            p {
                color: #9ca3af;
                font-size: 14px;
                margin-bottom: 30px;
            }
            .input-group {
                margin-bottom: 20px;
                text-align: left;
            }
            label {
                display: block;
                font-size: 13px;
                font-weight: 600;
                color: #cbd5e1;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            input[type="password"] {
                width: 100%;
                padding: 14px;
                box-sizing: border-box;
                background: rgba(15, 23, 42, 0.6);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                color: white;
                font-size: 16px;
                transition: all 0.3s ease;
            }
            input[type="password"]:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 15px var(--primary-glow);
            }
            button {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #6366f1, #4f46e5);
                border: none;
                border-radius: 12px;
                color: white;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            }
            button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            }
            button:active {
                transform: translateY(0);
            }
            .alert {
                background: rgba(239, 68, 68, 0.15);
                border: 1px solid rgba(239, 68, 68, 0.3);
                padding: 12px;
                border-radius: 12px;
                color: #fca5a5;
                font-size: 14px;
                margin-bottom: 20px;
            }
            .hint {
                font-size: 11px;
                color: #64748b;
                margin-top: 20px;
                line-height: 1.5;
            }
            code {
                background: rgba(255, 255, 255, 0.05);
                padding: 2px 6px;
                border-radius: 4px;
                color: #e2e8f0;
            }
        </style>
    </head>
    <body>
        <div class="login-card">
            <h1>Danosla Portal</h1>
            <p>Enter your authorization setup key to manage servers.</p>
            
            <?php if ($failed): ?>
                <div class="alert">Invalid authorization key!</div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="input-group">
                    <label for="key">Authorization Key</label>
                    <input type="password" id="key" name="key" required placeholder="••••••••••••••••" autofocus>
                </div>
                <button type="submit">Unlock Dashboard</button>
            </form>
            
            <div class="hint">
                By default, this panel looks for <code>SETUP_KEY</code> in your <code>.env</code> file. 
                If not set, it defaults to <code>danosla_secure_setup</code>.
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Manager - Danosla Console</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #090d16;
            --card-bg: rgba(20, 27, 45, 0.65);
            --border: rgba(255, 255, 255, 0.07);
            --text: #f3f4f6;
            --text-muted: #9ca3af;
            --primary: #6366f1;
            --primary-gradient: linear-gradient(135deg, #818cf8, #6366f1);
            --success: #10b981;
            --success-glow: rgba(16, 185, 129, 0.2);
            --warning: #f59e0b;
            --danger: #ef4444;
            --terminal-bg: #070a12;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background-image: radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.08) 0%, transparent 40%),
                              radial-gradient(circle at 100% 100%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .header-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            background: linear-gradient(135deg, #a5b4fc, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-title p {
            margin: 4px 0 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .header-badge {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .sys-badge {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            color: #cbd5e1;
        }

        .sys-badge span {
            color: var(--primary);
            font-weight: 600;
        }

        /* Toast Notifications */
        .toast {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid transparent;
            animation: fadeIn 0.4s ease;
        }
        .toast-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.25);
            color: #34d399;
        }
        .toast-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
            color: #f87171;
        }
        .toast-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.25);
            color: #fbbf24;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Overview Stats Row */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .stat-card .value {
            font-size: 20px;
            font-weight: 700;
            margin: 10px 0 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active {
            background-color: var(--success);
            box-shadow: 0 0 10px var(--success-glow);
        }

        .status-inactive {
            background-color: var(--text-muted);
        }

        .status-alert {
            background-color: var(--danger);
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 768px) {
            .services-grid {
                grid-template-columns: 1fr;
            }
        }

        .service-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .service-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .service-title h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .service-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-inactive {
            background: rgba(156, 163, 175, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(156, 163, 175, 0.3);
        }

        .service-meta {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 20px;
            background: rgba(0, 0, 0, 0.15);
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.03);
        }

        .service-meta span {
            color: white;
            font-weight: 500;
        }

        .actions-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.08);
        }

        .log-section {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .log-header span {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .log-clear {
            font-size: 11px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .log-clear:hover {
            text-decoration: underline;
        }

        .log-box {
            background: var(--terminal-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            font-family: 'Fira Code', monospace;
            font-size: 11px;
            color: #94a3b8;
            height: 180px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.5;
        }

        /* Setup & Core Actions Card */
        .control-panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        .control-panel h2 {
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: 600;
        }

        .setup-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .action-card {
            background: rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .action-card:hover {
            border-color: rgba(99, 102, 241, 0.25);
            background: rgba(99, 102, 241, 0.02);
        }

        .action-card h4 {
            margin: 0 0 6px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .action-card p {
            margin: 0 0 16px 0;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .action-card .btn {
            width: 100%;
            box-sizing: border-box;
        }

        /* Terminal Window */
        .terminal-container {
            background: var(--terminal-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .terminal-header {
            background: rgba(255, 255, 255, 0.02);
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .terminal-dots {
            display: flex;
            gap: 6px;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .terminal-title {
            font-family: 'Fira Code', monospace;
            font-size: 12px;
            color: var(--text-muted);
        }

        .terminal-body {
            padding: 20px;
        }

        .terminal-output {
            background: #000;
            border-radius: 8px;
            padding: 16px;
            font-family: 'Fira Code', monospace;
            font-size: 13px;
            color: #34d399;
            height: 250px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            line-height: 1.6;
        }

        .terminal-input-form {
            display: flex;
            gap: 12px;
        }

        .terminal-input-wrapper {
            position: relative;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .terminal-prefix {
            position: absolute;
            left: 14px;
            font-family: 'Fira Code', monospace;
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
        }

        .terminal-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 12px 12px 12px 125px;
            box-sizing: border-box;
            color: white;
            font-family: 'Fira Code', monospace;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .terminal-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(99, 102, 241, 0.15);
        }

        .footer {
            text-align: center;
            margin-top: 60px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container">
        
        <!-- Header -->
        <header>
            <div class="header-title">
                <h1>Danosla Controller</h1>
                <p>Manage system processes, database updates, and Laravel console command tasks without CLI access.</p>
            </div>
            <div class="header-badge">
                <div class="sys-badge">Environment: <span><?php echo ucfirst(app()->environment()); ?></span></div>
                <div class="sys-badge">OS: <span><?php echo php_uname('s'); ?></span></div>
            </div>
        </header>

        <!-- Notification Toast -->
        <?php if (!empty($actionMessage)): ?>
            <div class="toast toast-<?php echo $actionStatus; ?>">
                <div class="status-dot <?php echo $actionStatus === 'success' ? 'status-active' : ($actionStatus === 'warning' ? 'status-inactive' : 'status-alert'); ?>"></div>
                <span><strong>System Notification:</strong> <?php echo htmlspecialchars($actionMessage); ?></span>
            </div>
        <?php endif; ?>

        <!-- System Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Database State</h3>
                <div class="value">
                    <span class="status-dot <?php echo $dbConnected ? 'status-active' : 'status-alert'; ?>"></span>
                    <?php echo $dbConnected ? 'Connected' : 'Disconnected'; ?>
                </div>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <?php echo $dbConnected ? 'Active connection to mysql database' : htmlspecialchars(substr($dbError, 0, 45) . '...'); ?>
                </small>
            </div>
            
            <div class="stat-card">
                <h3>Queue Status</h3>
                <div class="value">
                    <span class="status-dot <?php echo $queueRunning ? 'status-active' : 'status-inactive'; ?>"></span>
                    <?php echo $queueRunning ? 'Active' : 'Stopped'; ?>
                </div>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <?php echo $queueRunning ? "Running on PID: <strong>$queuePid</strong>" : 'No background worker running'; ?>
                </small>
            </div>
            
            <div class="stat-card">
                <h3>Reverb websocket</h3>
                <div class="value">
                    <span class="status-dot <?php echo $reverbRunning ? 'status-active' : 'status-inactive'; ?>"></span>
                    <?php echo $reverbRunning ? 'Active' : 'Stopped'; ?>
                </div>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <?php echo $reverbRunning ? "Running on PID: <strong>$reverbPid</strong>" : 'No websocket server running'; ?>
                </small>
            </div>

            <div class="stat-card">
                <h3>Reverb Port (<?php echo $reverbPort; ?>)</h3>
                <div class="value">
                    <span class="status-dot <?php echo $reverbPortActive ? 'status-active' : 'status-inactive'; ?>"></span>
                    <?php echo $reverbPortActive ? 'Listening' : 'Offline'; ?>
                </div>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <?php echo $reverbPortActive ? 'WebSocket port is open and responding' : 'Port is closed'; ?>
                </small>
            </div>

            <div class="stat-card">
                <h3>Node / NPM</h3>
                <div class="value">
                    <span class="status-dot <?php echo ($nodeAvailable && $npmAvailable) ? 'status-active' : 'status-alert'; ?>"></span>
                    <?php echo ($nodeAvailable && $npmAvailable) ? "node $nodeVersion" : 'Not found'; ?>
                </div>
                <small style="color: var(--text-muted); font-size: 11px;">
                    <?php echo ($nodeAvailable && $npmAvailable)
                        ? "npm $npmVersion available to the web server user"
                        : 'Frontend build will fail — Node.js/npm is not in PATH for this process'; ?>
                </small>
            </div>
        </div>

        <!-- Setup Panel -->
        <div class="control-panel">
            <h2>Core Database & System Setup</h2>
            <div class="setup-actions">
                
                <div class="action-card">
                    <h4>Run Migrations</h4>
                    <p>Applies pending schema migrations using force flag.</p>
                    <a href="?action=migrate&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary">Run migrate</a>
                </div>
                
                <div class="action-card">
                    <h4>Run Seeders</h4>
                    <p>Populates database tables with default seed values.</p>
                    <a href="?action=seed&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary">Run db:seed</a>
                </div>
                
                <div class="action-card">
                    <h4>Create Storage Link</h4>
                    <p>Creates symbolic link from public/storage to storage/app/public.</p>
                    <a href="?action=storage_link&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary">Create Link</a>
                </div>
                
                <div class="action-card">
                    <h4>Generate APP_KEY</h4>
                    <p>Generates a new secure application key in the env file.</p>
                    <a href="?action=key_generate&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary" onclick="return confirm('WARNING: Regenerating APP_KEY will invalidate all encrypted sessions and passwords. Proceed?')">Generate Key</a>
                </div>
                
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid var(--border); padding-top: 20px;">
                <a href="?action=optimize&key=<?php echo urlencode($securityKey); ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">Optimize & Cache Config</a>
                <a href="?action=optimize_clear&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px;">Clear Application Cache</a>
            </div>
        </div>

        <!-- Background Services Dashboard -->
        <div class="services-grid">
            
            <!-- Queue Worker Card -->
            <div class="service-card">
                <div class="service-header">
                    <div class="service-title">
                        <h2>Queue Worker</h2>
                        <span class="service-badge <?php echo $queueRunning ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo $queueRunning ? 'Running' : 'Stopped'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="service-meta">
                    Queue Connection: <span><?php echo config('queue.default'); ?></span><br>
                    Process PID: <span><?php echo $queuePid ?? 'None'; ?></span><br>
                    Log File: <span>storage/logs/queue_worker.log</span>
                </div>
                
                <div class="actions-group">
                    <?php if ($queueRunning): ?>
                        <a href="?action=stop_worker&key=<?php echo urlencode($securityKey); ?>" class="btn btn-danger">Stop Worker Daemon</a>
                    <?php else: ?>
                        <a href="?action=start_worker&key=<?php echo urlencode($securityKey); ?>" class="btn btn-primary">Start Worker Daemon</a>
                    <?php endif; ?>
                    <a href="?action=run_worker_sync&key=<?php echo urlencode($securityKey); ?>" class="btn btn-secondary">Run Sync (Process once)</a>
                </div>
                
                <div class="log-section">
                    <div class="log-header">
                        <span>Worker Console Logs</span>
                        <a href="?action=clear_worker_log&key=<?php echo urlencode($securityKey); ?>" class="log-clear" onclick="return confirm('Clear logs?')">Clear Log</a>
                    </div>
                    <div class="log-box"><?php echo htmlspecialchars($queueLogLines ?: 'No logs recorded yet. Start daemon or process jobs.'); ?></div>
                </div>
            </div>

            <!-- Reverb Server Card -->
            <div class="service-card">
                <div class="service-header">
                    <div class="service-title">
                        <h2>Laravel Reverb WebSockets</h2>
                        <span class="service-badge <?php echo $reverbRunning ? 'badge-active' : 'badge-inactive'; ?>">
                            <?php echo $reverbRunning ? 'Running' : 'Stopped'; ?>
                        </span>
                    </div>
                </div>
                
                <div class="service-meta">
                    Server Address: <span><?php echo $reverbHost; ?>:<?php echo $reverbPort; ?></span><br>
                    Process PID: <span><?php echo $reverbPid ?? 'None'; ?></span><br>
                    Log File: <span>storage/logs/reverb_server.log</span>
                </div>
                
                <div class="actions-group">
                    <?php if ($reverbRunning): ?>
                        <a href="?action=stop_reverb&key=<?php echo urlencode($securityKey); ?>" class="btn btn-danger">Stop Reverb Daemon</a>
                    <?php else: ?>
                        <a href="?action=start_reverb&key=<?php echo urlencode($securityKey); ?>" class="btn btn-primary">Start Reverb Daemon</a>
                    <?php endif; ?>
                </div>
                
                <div class="log-section">
                    <div class="log-header">
                        <span>Reverb Server Logs</span>
                        <a href="?action=clear_reverb_log&key=<?php echo urlencode($securityKey); ?>" class="log-clear" onclick="return confirm('Clear logs?')">Clear Log</a>
                    </div>
                    <div class="log-box"><?php echo htmlspecialchars($reverbLogLines ?: 'No logs recorded yet. Start daemon to begin streaming.'); ?></div>
                </div>
            </div>

            <!-- Frontend Build Card -->
            <div class="service-card">
                <div class="service-header">
                    <div class="service-title">
                        <h2>Frontend Build</h2>
                        <span class="service-badge <?php
                            echo $buildRunning ? 'badge-active' : ($buildLastStatus === 'failed' ? 'badge-inactive' : 'badge-inactive');
                        ?>" style="<?php echo (!$buildRunning && $buildLastStatus === 'failed') ? 'background: rgba(239,68,68,0.15); color:#f87171; border-color: rgba(239,68,68,0.3);' : ''; ?>">
                            <?php
                                if ($buildRunning) echo 'Running';
                                elseif ($buildLastStatus === 'success') echo 'Built';
                                elseif ($buildLastStatus === 'failed') echo 'Failed';
                                else echo 'Never run';
                            ?>
                        </span>
                    </div>
                </div>

                <div class="service-meta">
                    Command: <span>npm ci &amp;&amp; npm run build</span><br>
                    Process PID: <span><?php echo $buildPid ?? 'None'; ?></span><br>
                    Log File: <span>storage/logs/frontend_build.log</span>
                    <?php if (!$nodeAvailable || !$npmAvailable): ?>
                        <br><span style="color:#f87171;">⚠ Node.js/npm not detected in PATH — build will fail to start.</span>
                    <?php endif; ?>
                </div>

                <div class="actions-group">
                    <?php if ($buildRunning): ?>
                        <span class="btn btn-secondary" style="cursor: default; opacity: 0.7;">Build in progress…</span>
                    <?php else: ?>
                        <a href="?action=build_frontend&key=<?php echo urlencode($securityKey); ?>" class="btn btn-primary" onclick="return confirm('Run npm ci && npm run build now? This rebuilds public/build from resources/js and resources/css.')">Run npm ci &amp;&amp; npm run build</a>
                    <?php endif; ?>
                </div>

                <div class="log-section">
                    <div class="log-header">
                        <span>Build Output</span>
                        <a href="?action=clear_build_log&key=<?php echo urlencode($securityKey); ?>" class="log-clear" onclick="return confirm('Clear logs?')">Clear Log</a>
                    </div>
                    <div class="log-box"><?php echo htmlspecialchars($buildLogLines ?: 'No build has been run yet from this panel.'); ?></div>
                </div>
            </div>

        </div>

        <!-- Terminal Command Box -->
        <div class="terminal-container">
            <div class="terminal-header">
                <div class="terminal-dots">
                    <div class="terminal-dot" style="background: #ff5f56;"></div>
                    <div class="terminal-dot" style="background: #ffbd2e;"></div>
                    <div class="terminal-dot" style="background: #27c93f;"></div>
                </div>
                <div class="terminal-title">Artisan Console Interface</div>
                <div style="width: 42px;"></div> <!-- spacer -->
            </div>
            <div class="terminal-body">
                <div class="terminal-output" id="terminal-out"><?php 
                    if (!empty($terminalOutput)) {
                        echo htmlspecialchars($terminalOutput);
                    } else {
                        echo "Danosla Command Console ready.\nType any artisan command below (e.g. \"route:list\", \"migrate:status\", \"db:seed --class=UserSeeder\")\n\nRestrictions: down, serve, tinker are disabled.";
                    }
                ?></div>
                <form method="POST" class="terminal-input-form">
                    <input type="hidden" name="key" value="<?php echo htmlspecialchars($securityKey); ?>">
                    <input type="hidden" name="action" value="run_custom">
                    <div class="terminal-input-wrapper">
                        <span class="terminal-prefix">php artisan</span>
                        <input type="text" name="custom_command" class="terminal-input" placeholder="route:list" required autocomplete="off" autofocus>
                    </div>
                    <button type="submit" class="btn btn-primary">Run Command</button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Danosla Platform Console Panel &bull; Powered by Antigravity AI</p>
        </div>

    </div>
    
    <script>
        // Scroll terminal output to bottom
        const term = document.getElementById('terminal-out');
        term.scrollTop = term.scrollHeight;
        
        // Scroll log boxes to bottom
        const logs = document.querySelectorAll('.log-box');
        logs.forEach(log => {
            log.scrollTop = log.scrollHeight;
        });
    </script>
</body>
</html>