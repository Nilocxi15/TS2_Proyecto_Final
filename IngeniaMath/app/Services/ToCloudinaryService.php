<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class ToCloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
    }

    public function subirImagen(UploadedFile $imagen, string $carpeta = 'ejercicios'): string
    {
        $resultado = $this->cloudinary->uploadApi()->upload($imagen->getRealPath(), [
            'folder' => $carpeta
        ]);
        
        return $resultado['secure_url'];
    }

    public function eliminarImagen(string $urlPublica): void
    {
        $publicId = $this->extraerPublicId($urlPublica);
        if ($publicId) {
            $this->cloudinary->uploadApi()->destroy($publicId);
        }
    }

    private function extraerPublicId(string $url): ?string
    {
        $pattern = '/\/upload\/v\d+\/(.+)\./';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}