<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class InitializeS3Bucket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:init-bucket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize S3 bucket for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Initializing S3 Bucket...');
        $this->newLine();

        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'region'  => config('filesystems.disks.s3.region'),
                'endpoint' => config('filesystems.disks.s3.endpoint'),
                'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint'),
                'credentials' => [
                    'key'    => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ]
            ]);

            $bucket = config('filesystems.disks.s3.bucket');

            // Check if bucket exists
            $this->info("Checking if bucket '{$bucket}' exists...");
            if (!$s3Client->doesBucketExist($bucket)) {
                $this->info("Creating bucket '{$bucket}'...");
                try {
                    $s3Client->createBucket([
                        'Bucket' => $bucket,
                    ]);
                    $this->info("✓ Bucket '{$bucket}' created successfully");
                } catch (AwsException $e) {
                    if ($e->getAwsErrorCode() === 'BucketAlreadyOwnedByYou') {
                        $this->info("✓ Bucket '{$bucket}' already exists and is owned by you");
                    } else {
                        throw $e;
                    }
                }
            } else {
                $this->info("✓ Bucket '{$bucket}' already exists");
            }

            // Set bucket policy to allow public read access
            $this->info("Configuring bucket policy for public read access...");
            $policy = [
                'Version' => '2012-10-17',
                'Statement' => [
                    [
                        'Effect' => 'Allow',
                        'Principal' => '*',
                        'Action' => 's3:GetObject',
                        'Resource' => "arn:aws:s3:::{$bucket}/*"
                    ]
                ]
            ];

            try {
                $s3Client->putBucketPolicy([
                    'Bucket' => $bucket,
                    'Policy' => json_encode($policy),
                ]);
                $this->info("✓ Bucket policy configured for public read access");
            } catch (AwsException $e) {
                $this->warn("Warning: Could not set bucket policy: " . $e->getMessage());
            }

            $this->newLine();
            $this->info('✓ S3 bucket initialization completed successfully!');
            return 0;

        } catch (AwsException $e) {
            $this->error('AWS Error: ' . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
