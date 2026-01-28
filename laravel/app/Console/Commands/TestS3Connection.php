<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestS3Connection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test S3 storage connection and file operations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing S3 Storage Connection...');
        $this->newLine();

        // Test 1: Check configuration
        $this->info('1. Checking S3 Configuration...');
        try {
            $disk = Storage::disk('s3');
            $this->info('   ✓ S3 disk configured and accessible');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to access S3 disk: ' . $e->getMessage());
            return 1;
        }

        // Test 2: Write a test file
        $this->info('2. Writing Test File to S3...');
        try {
            $testFileName = 'test-' . Str::random(8) . '.txt';
            $testContent = 'S3 Connection Test - ' . now()->toDateTimeString();
            
            Storage::disk('s3')->put($testFileName, $testContent);
            $this->info("   ✓ Successfully wrote test file: {$testFileName}");
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to write test file: ' . $e->getMessage());
            return 1;
        }

        // Test 3: Read the file back
        $this->info('3. Reading Test File from S3...');
        try {
            $readContent = Storage::disk('s3')->get($testFileName);
            if ($readContent === $testContent) {
                $this->info('   ✓ Successfully read test file and content matches');
            } else {
                $this->error('   ✗ File content does not match');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to read test file: ' . $e->getMessage());
            return 1;
        }

        // Test 4: List files
        $this->info('4. Listing Files in S3 Bucket...');
        try {
            $files = Storage::disk('s3')->files('/');
            $this->info('   ✓ Successfully listed files in bucket');
            $this->info("   Found " . count($files) . " file(s)");
            if (count($files) > 0) {
                $this->table(['File'], array_map(fn($f) => [$f], $files));
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to list files: ' . $e->getMessage());
            return 1;
        }

        // Test 5: Check file exists
        $this->info('5. Checking if Test File Exists...');
        try {
            if (Storage::disk('s3')->exists($testFileName)) {
                $this->info('   ✓ Test file exists in S3');
            } else {
                $this->error('   ✗ Test file not found in S3');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to check file existence: ' . $e->getMessage());
            return 1;
        }

        // Test 6: Get file size
        $this->info('6. Getting Test File Size...');
        try {
            $size = Storage::disk('s3')->size($testFileName);
            $this->info("   ✓ Test file size: {$size} bytes");
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to get file size: ' . $e->getMessage());
            return 1;
        }

        // Test 7: Delete test file
        $this->info('7. Deleting Test File...');
        try {
            Storage::disk('s3')->delete($testFileName);
            $this->info('   ✓ Successfully deleted test file');
        } catch (\Exception $e) {
            $this->error('   ✗ Failed to delete test file: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info('✓ All S3 connection tests passed!');
        return 0;
    }
}
