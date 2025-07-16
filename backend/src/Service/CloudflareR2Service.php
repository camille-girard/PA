<?php

namespace App\Service;

use Aws\Exception\AwsException;
use Aws\S3\S3Client;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudflareR2Service
{
    private S3Client $s3Client;
    private ImageManager $imageManager;
    private string $bucketName;
    private string $publicUrl;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private LoggerInterface $logger,
    ) {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->parameterBag->get('cloudflare_r2.region'),
            'endpoint' => $this->parameterBag->get('cloudflare_r2.endpoint'),
            'credentials' => [
                'key' => $this->parameterBag->get('cloudflare_r2.access_key'),
                'secret' => $this->parameterBag->get('cloudflare_r2.secret_key'),
            ],
            'use_path_style_endpoint' => false,
        ]);

        $this->imageManager = new ImageManager(new Driver());
        $this->bucketName = $this->parameterBag->get('cloudflare_r2.bucket_name');
        $this->publicUrl = $this->parameterBag->get('cloudflare_r2.public_url');
    }

    public function uploadAvatar(UploadedFile $file, int $userId): string
    {
        $this->validateImageFile($file);

        // Process and resize image
        $processedImage = $this->processAvatarImage($file);

        // Generate unique filename
        $filename = sprintf(
            'avatars/%d/%s.webp',
            $userId,
            uniqid()
        );

        return $this->uploadToR2($processedImage, $filename, 'image/webp');
    }

    public function uploadAccommodationImage(UploadedFile $file, int $accommodationId): string
    {
        $this->validateImageFile($file);

        // Process and resize image
        $processedImage = $this->processAccommodationImage($file);

        // Generate unique filename
        $filename = sprintf(
            'accommodations/%d/%s.webp',
            $accommodationId,
            uniqid()
        );

        return $this->uploadToR2($processedImage, $filename, 'image/webp');
    }

    public function deleteFile(string $url): bool
    {
        try {
            $key = $this->extractKeyFromUrl($url);

            $this->s3Client->deleteObject([
                'Bucket' => $this->bucketName,
                'Key' => $key,
            ]);

            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    private function validateImageFile(UploadedFile $file): void
    {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \InvalidArgumentException('Invalid file type. Only JPEG, PNG, and WebP are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB limit
            throw new \InvalidArgumentException('File size too large. Maximum size is 5MB.');
        }
    }

    private function processAvatarImage(UploadedFile $file): string
    {
        $image = $this->imageManager->read($file->getPathname());

        // Resize to 200x200 and convert to WebP
        $image->resize(200, 200);

        return $image->toWebp(85)->toString();
    }

    private function processAccommodationImage(UploadedFile $file): string
    {
        $image = $this->imageManager->read($file->getPathname());

        // Resize to max 1200px width maintaining aspect ratio
        $image->resize(1200, null);

        return $image->toWebp(85)->toString();
    }

    private function uploadToR2(string $imageData, string $filename, string $contentType): string
    {
        try {
            // Debug information
            $this->logger->info('Upload to R2', [
                'bucket' => $this->bucketName,
                'key' => $filename,
                'publicUrl' => $this->publicUrl,
            ]);

            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key' => $filename,
                'Body' => $imageData,
                'ContentType' => $contentType,
                'CacheControl' => 'max-age=31536000', // 1 year
            ]);

            $finalUrl = $this->publicUrl.'/'.$filename;
            $this->logger->info('Image uploaded successfully', ['finalUrl' => $finalUrl]);

            return $finalUrl;
        } catch (AwsException $e) {
            $this->logger->error('Failed to upload image to R2', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to upload image: '.$e->getMessage());
        }
    }

    private function extractKeyFromUrl(string $url): string
    {
        return str_replace($this->publicUrl.'/', '', $url);
    }
}
