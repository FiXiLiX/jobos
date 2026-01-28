<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Carbon\Carbon;

class ExportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:export {--output=} {--compress}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export the database to a SQL file with SSL workaround';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $database = Config::get('database.connections.mysql.database');
        $username = Config::get('database.connections.mysql.username');
        $password = Config::get('database.connections.mysql.password');
        $host = Config::get('database.connections.mysql.host');
        $port = Config::get('database.connections.mysql.port', 3306);

        // Determine output file
        $timestamp = Carbon::now()->format('Y-m-d-H-i-s');
        $filename = $this->option('output') ?? storage_path("backups/database-{$timestamp}.sql");
        
        // Create backup directory if it doesn't exist
        $directory = dirname($filename);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $this->info("Exporting database '{$database}' to {$filename}...");

        // Build mysqldump command with --skip-ssl flag
        $command = [
            'mysqldump',
            '--skip-ssl',
            "--host={$host}",
            "--port={$port}",
            "--user={$username}",
            "--password={$password}",
            $database,
        ];

        try {
            // Execute mysqldump
            $process = new Process($command);
            $process->setTimeout(300); // 5 minute timeout
            
            // Capture output
            $output = '';
            $process->run(function ($type, $buffer) use (&$output) {
                $output .= $buffer;
            });

            if (!$process->isSuccessful()) {
                $this->error('Database export failed!');
                $this->error($process->getErrorOutput());
                return Command::FAILURE;
            }

            // Write to file
            file_put_contents($filename, $output);
            $filesize = human_filesize(filesize($filename));

            $this->info("✓ Database exported successfully! ({$filesize})");

            // Optionally compress
            if ($this->option('compress')) {
                $this->info('Compressing export...');
                $this->compressFile($filename);
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Compress a file using gzip
     */
    protected function compressFile(string $filename): void
    {
        $command = new Process(['gzip', '-9', $filename]);
        $command->run();

        if ($command->isSuccessful()) {
            $compressedSize = human_filesize(filesize($filename . '.gz'));
            $this->info("✓ Compressed to {$filename}.gz ({$compressedSize})");
        } else {
            $this->warn('Compression failed: ' . $command->getErrorOutput());
        }
    }
}

/**
 * Human readable file size
 */
function human_filesize($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}
