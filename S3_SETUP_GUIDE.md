# S3 Storage Setup with MinIO - Complete Guide

## What was installed:
- **MinIO** - S3-compatible object storage service (running in Docker)
- **AWS SDK for PHP** - AWS PHP SDK for S3 operations
- **Flysystem S3 Adapter** - Laravel file system adapter for AWS S3

## Configuration

### Environment Variables (.env)
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=admin
AWS_SECRET_ACCESS_KEY=admin_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=jobos
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
```

### Available Commands

#### 1. Initialize S3 Bucket
Create the S3 bucket if it doesn't exist:
```bash
php artisan s3:init-bucket
```

#### 2. Test S3 Connection
Test file operations (write, read, list, delete):
```bash
php artisan s3:test
```

This command will:
- Check S3 configuration
- Write a test file to the bucket
- Read the file back and verify content
- List all files in the bucket
- Check if the file exists
- Get the file size
- Delete the test file

## MinIO Web Console
Access MinIO's web interface at:
- **URL**: http://localhost:9001
- **Username**: admin
- **Password**: admin_secret

## Docker Service
The MinIO service runs as `minio` and is accessible from Laravel at:
- **Endpoint**: http://minio:9000

## Using S3 Storage in Laravel

### Store a file
```php
use Illuminate\Support\Facades\Storage;

Storage::disk('s3')->put('path/to/file.txt', 'contents');
```

### Retrieve a file
```php
$contents = Storage::disk('s3')->get('path/to/file.txt');
```

### Delete a file
```php
Storage::disk('s3')->delete('path/to/file.txt');
```

### Check if file exists
```php
if (Storage::disk('s3')->exists('path/to/file.txt')) {
    // File exists
}
```

## Troubleshooting

If you get connection errors:
1. Ensure MinIO container is running: `docker compose ps`
2. Check MinIO logs: `docker compose logs minio`
3. Verify credentials in .env file match docker-compose configuration
4. Ensure the bucket exists: `php artisan s3:init-bucket`
