<?php

require_once '../../../vendor/autoload.php';

use WebFiori\Http\WebService;
use WebFiori\Http\Annotations\RestController;
use WebFiori\Http\Annotations\PostMapping;
use WebFiori\Http\Annotations\ResponseBody;
use WebFiori\Http\Annotations\RequestParam;
use WebFiori\Http\Annotations\AllowAnonymous;

/**
 * Service demonstrating file upload handling
 */
#[RestController('upload', 'File upload handling service')]
class UploadService extends WebService {
    
    private const UPLOAD_DIR = 'uploads/';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'text/plain', 'application/pdf'];
    
    public function __construct() {
        parent::__construct();
        
        // Ensure upload directory exists
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }
    }
    
    #[PostMapping]
    #[ResponseBody]
    #[AllowAnonymous]
    #[RequestParam('operation', 'string', true, 'single', 'Upload operation: single, multiple, with-metadata')]
    #[RequestParam('description', 'string', true, null, 'File description')]
    #[RequestParam('title', 'string', true, null, 'File title')]
    #[RequestParam('category', 'string', true, null, 'File category')]
    public function handleUpload(?string $operation = 'single', ?string $description = null, ?string $title = null, ?string $category = null): array {
        switch ($operation) {
            case 'single':
                return $this->handleSingleUpload($description);
            case 'multiple':
                return $this->handleMultipleUpload();
            case 'with-metadata':
                return $this->handleUploadWithMetadata($title, $category);
            default:
                throw new \InvalidArgumentException('Unknown upload operation');
        }
    }
    
    private function handleSingleUpload(?string $description): array {
        if (!isset($_FILES['file'])) {
            throw new \InvalidArgumentException('No file uploaded. Expected field: file');
        }
        
        $file = $_FILES['file'];
        
        $result = $this->processFile($file);
        
        if (!$result['success']) {
            throw new \InvalidArgumentException($result['error']);
        }
        
        return [
            'file_info' => $result['data'],
            'description' => $description,
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
    }
    
    private function handleMultipleUpload(): array {
        if (!isset($_FILES['files'])) {
            throw new \InvalidArgumentException('No files uploaded. Expected field: files[]');
        }
        
        $files = $_FILES['files'];
        $results = [];
        $errors = [];
        
        for ($i = 0; $i < count($files['name']); $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = $this->processFile($file);
            
            if ($result['success']) {
                $results[] = $result['data'];
            } else {
                $errors[] = [
                    'filename' => $file['name'],
                    'error' => $result['error']
                ];
            }
        }
        
        return [
            'uploaded_files' => $results,
            'errors' => $errors,
            'total_uploaded' => count($results),
            'total_errors' => count($errors)
        ];
    }
    
    private function handleUploadWithMetadata(?string $title, ?string $category): array {
        if (!isset($_FILES['file'])) {
            throw new \InvalidArgumentException('No file uploaded');
        }
        
        $file = $_FILES['file'];
        
        $result = $this->processFile($file);
        
        if (!$result['success']) {
            throw new \InvalidArgumentException($result['error']);
        }
        
        return [
            'title' => $title ?: $file['name'],
            'category' => $category ?: 'general',
            'uploaded_by' => 'anonymous',
            'upload_date' => date('Y-m-d H:i:s'),
            'file_info' => $result['data']
        ];
    }
    
    private function processFile(array $file): array {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'error' => 'File upload error',
                'details' => ['upload_error_code' => $file['error']]
            ];
        }
        
        // Validate file size
        if ($file['size'] > self::MAX_FILE_SIZE) {
            return [
                'success' => false,
                'error' => 'File too large',
                'details' => [
                    'max_size' => self::MAX_FILE_SIZE,
                    'file_size' => $file['size']
                ]
            ];
        }
        
        // Validate file type
        if (!in_array($file['type'], self::ALLOWED_TYPES)) {
            return [
                'success' => false,
                'error' => 'File type not allowed',
                'details' => [
                    'allowed_types' => self::ALLOWED_TYPES,
                    'file_type' => $file['type']
                ]
            ];
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = pathinfo($file['name'], PATHINFO_FILENAME);
        $uniqueFilename = $filename . '_' . date('Ymd_His') . '.' . $extension;
        $uploadPath = self::UPLOAD_DIR . $uniqueFilename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'data' => [
                    'original_name' => $file['name'],
                    'stored_name' => $uniqueFilename,
                    'size' => $file['size'],
                    'type' => $file['type'],
                    'upload_path' => $uploadPath,
                    'url' => '/' . $uploadPath
                ]
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Failed to save file',
                'details' => ['upload_path' => $uploadPath]
            ];
        }
    }
}
