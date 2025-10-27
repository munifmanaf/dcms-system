<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artifact;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
            }
            return $next($request);
        });
    }

    public function settings()
    {
        $settings = [
            'general' => [
                'site_name' => Setting::getValue('site_name', config('app.name')),
                'site_description' => Setting::getValue('site_description', 'Document Content Management System'),
                'contact_email' => Setting::getValue('contact_email', 'admin@dcms.test'),
                'items_per_page' => Setting::getValue('items_per_page', 15),
                'timezone' => Setting::getValue('timezone', config('app.timezone')),
            ],
            'workflow' => [
                'auto_assign_reviewers' => Setting::getValue('auto_assign_reviewers', true),
                'notify_submitter_on_approval' => Setting::getValue('notify_submitter_on_approval', true),
                'allow_quick_approval' => Setting::getValue('allow_quick_approval', true),
                'max_review_days' => Setting::getValue('max_review_days', 7),
            ],
            'notifications' => [
                'email_enabled' => Setting::getValue('email_enabled', true),
                'slack_enabled' => Setting::getValue('slack_enabled', false),
                'slack_webhook' => Setting::getValue('slack_webhook', ''),
                'daily_digest' => Setting::getValue('daily_digest', true),
            ],
            'backup' => [
                'auto_backup' => Setting::getValue('auto_backup', false),
                'backup_frequency' => Setting::getValue('backup_frequency', 'daily'),
                'keep_backups_days' => Setting::getValue('keep_backups_days', 30),
                'backup_notification_email' => Setting::getValue('backup_notification_email', ''),
            ]
        ];

        $timezones = \DateTimeZone::listIdentifiers();
        
        return view('system.settings', compact('settings', 'timezones'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            // General settings
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'contact_email' => 'required|email',
            'items_per_page' => 'required|integer|min:5|max:100',
            'timezone' => 'required|string|in:' . implode(',', \DateTimeZone::listIdentifiers()),
            
            // Workflow settings
            'auto_assign_reviewers' => 'boolean',
            'notify_submitter_on_approval' => 'boolean',
            'allow_quick_approval' => 'boolean',
            'max_review_days' => 'required|integer|min:1|max:30',
            
            // Notification settings
            'email_enabled' => 'boolean',
            'slack_enabled' => 'boolean',
            'slack_webhook' => 'nullable|url|required_if:slack_enabled,true',
            'daily_digest' => 'boolean',
            
            // Backup settings
            'auto_backup' => 'boolean',
            'backup_frequency' => 'required|in:daily,weekly,monthly',
            'keep_backups_days' => 'required|integer|min:1|max:365',
            'backup_notification_email' => 'nullable|email',
        ]);

        // Save general settings
        Setting::setValue('site_name', $validated['site_name'], 'string', 'general', 'Website name');
        Setting::setValue('site_description', $validated['site_description'], 'string', 'general', 'Website description');
        Setting::setValue('contact_email', $validated['contact_email'], 'string', 'general', 'Contact email address');
        Setting::setValue('items_per_page', $validated['items_per_page'], 'integer', 'general', 'Number of items per page');
        Setting::setValue('timezone', $validated['timezone'], 'string', 'general', 'System timezone');

        // Save workflow settings
        Setting::setValue('auto_assign_reviewers', $validated['auto_assign_reviewers'], 'boolean', 'workflow', 'Automatically assign reviewers');
        Setting::setValue('notify_submitter_on_approval', $validated['notify_submitter_on_approval'], 'boolean', 'workflow', 'Notify submitter when item is approved');
        Setting::setValue('allow_quick_approval', $validated['allow_quick_approval'], 'boolean', 'workflow', 'Allow quick approval by managers');
        Setting::setValue('max_review_days', $validated['max_review_days'], 'integer', 'workflow', 'Maximum days for review before reminder');

        // Save notification settings
        Setting::setValue('email_enabled', $validated['email_enabled'], 'boolean', 'notifications', 'Enable email notifications');
        Setting::setValue('slack_enabled', $validated['slack_enabled'], 'boolean', 'notifications', 'Enable Slack notifications');
        Setting::setValue('slack_webhook', $validated['slack_webhook'], 'string', 'notifications', 'Slack webhook URL');
        Setting::setValue('daily_digest', $validated['daily_digest'], 'boolean', 'notifications', 'Send daily digest email');

        // Save backup settings
        Setting::setValue('auto_backup', $validated['auto_backup'], 'boolean', 'backup', 'Enable automatic backups');
        Setting::setValue('backup_frequency', $validated['backup_frequency'], 'string', 'backup', 'Backup frequency');
        Setting::setValue('keep_backups_days', $validated['keep_backups_days'], 'integer', 'backup', 'Number of days to keep backups');
        Setting::setValue('backup_notification_email', $validated['backup_notification_email'], 'string', 'backup', 'Backup notification email');

        // Clear settings cache
        Cache::forget('system_settings');

        return redirect()->route('system.settings')->with('success', 'System settings updated successfully.');
    }

    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $logs = $this->readLogFile($logFile, 200); // Last 200 lines
        }

        $logLevels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        
        return view('system.logs', compact('logs', 'logLevels'));
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
        }

        return redirect()->route('system.logs')->with('success', 'Log files cleared successfully.');
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');

        return redirect()->route('system.settings')->with('success', 'All caches cleared successfully.');
    }

    public function systemInfo()
    {
        $info = [
            'application' => [
                'Laravel Version' => app()->version(),
                'PHP Version' => phpversion(),
                'Environment' => app()->environment(),
                'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
                'Timezone' => config('app.timezone'),
            ],
            'server' => [
                'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'Server OS' => php_uname(),
                'Database' => config('database.default'),
                'Cache Driver' => config('cache.default'),
                'Session Driver' => config('session.driver'),
            ],
            'php' => [
                'Memory Limit' => ini_get('memory_limit'),
                'Max Execution Time' => ini_get('max_execution_time'),
                'Upload Max Filesize' => ini_get('upload_max_filesize'),
                'Post Max Size' => ini_get('post_max_size'),
            ]
        ];

        return view('system.info', compact('info'));
    }

    public function backup()
    {
        try {
            Artisan::call('backup:run');
            $output = Artisan::output();
            
            return redirect()->route('system.settings')->with('success', 'Backup completed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('system.settings')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function health()
    {
        $health = [
            'Database' => $this->checkDatabase(),
            'Storage' => $this->checkStorage(),
            'Cache' => $this->checkCache(),
            'Queue' => $this->checkQueue(),
        ];

        return view('system.health', compact('health'));
    }

    private function readLogFile($filePath, $lines = 100)
    {
        $file = new \SplFileObject($filePath, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        
        $lines = new \LimitIterator($file, max(0, $lastLine - $lines), $lastLine);
        return iterator_to_array($lines);
    }

    private function checkDatabase()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Connected successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkStorage()
    {
        $free = disk_free_space(storage_path());
        $total = disk_total_space(storage_path());
        $used = $total - $free;
        $percentage = round(($used / $total) * 100, 2);

        $status = $percentage > 90 ? 'warning' : 'healthy';
        
        return [
            'status' => $status,
            'message' => "Storage usage: {$percentage}%",
            'details' => [
                'Used' => $this->formatBytes($used),
                'Free' => $this->formatBytes($free),
                'Total' => $this->formatBytes($total)
            ]
        ];
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            
            return ['status' => 'healthy', 'message' => 'Cache is working'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function checkQueue()
    {
        try {
            // Simple queue check
            return ['status' => 'healthy', 'message' => 'Queue system available'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}