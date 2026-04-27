<?php

namespace App\Services;

use Cloudinary\Api\ApiResponse;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CloudinaryService
{
	private Cloudinary $cloudinary;

	public function __construct(?Cloudinary $cloudinary = null)
	{
		$this->cloudinary = $cloudinary ?? new Cloudinary([
			'cloud' => [
				'cloud_name' => config('services.cloudinary.cloud_name'),
				'api_key' => config('services.cloudinary.api_key'),
				'api_secret' => config('services.cloudinary.api_secret'),
			],
			'url' => [
				'secure' => (bool) config('services.cloudinary.secure', true),
			],
		]);
	}

	public function subirImagen(UploadedFile|string $archivo, array $opciones = []): array
	{
		$opcionesBase = [
			'resource_type' => 'image',
		];

		return $this->subirArchivo($archivo, array_merge($opcionesBase, $opciones));
	}

	public function subirVideo(UploadedFile|string $archivo, array $opciones = []): array
	{
		$opcionesBase = [
			'resource_type' => 'video',
		];

		return $this->subirArchivo($archivo, array_merge($opcionesBase, $opciones));
	}

	public function subirPdf(UploadedFile|string $archivo, array $opciones = []): array
	{
		$opcionesBase = [
			'resource_type' => 'raw',
			'format' => 'pdf',
			'allowed_formats' => ['pdf'],
		];

		return $this->subirArchivo($archivo, array_merge($opcionesBase, $opciones));
	}

	public function subirArchivo(UploadedFile|string $archivo, array $opciones = []): array
	{
		$fuente = $this->resolverFuente($archivo);
		$opcionesHttp = $this->resolverOpcionesSslCloudinary();

		$respuesta = $this->cloudinary
			->uploadApi()
			->upload($fuente, array_merge($opcionesHttp, $opciones));

		return $this->normalizarRespuesta($respuesta);
	}

	public function subirContenidoTutor(UploadedFile $archivo, array $opciones = []): array
	{
		$mime = (string) $archivo->getMimeType();
		$nombreOriginal = $archivo->getClientOriginalName();
		$extension = strtolower((string) $archivo->getClientOriginalExtension());

		$opcionesBase = [
			'folder' => $opciones['folder'] ?? 'ingeniamath/recursos',
			'use_filename' => true,
			'unique_filename' => true,
			'filename_override' => $opciones['filename_override']
				?? Str::slug(pathinfo($nombreOriginal, PATHINFO_FILENAME)),
		];

		if (str_starts_with($mime, 'video/')) {
			return $this->subirVideo($archivo, array_merge($opcionesBase, $opciones, [
				'resource_type' => 'video',
			]));
		}

		if ($mime === 'application/pdf' || $extension === 'pdf') {
			return $this->subirPdf($archivo, array_merge($opcionesBase, $opciones, [
				'resource_type' => 'raw',
				'allowed_formats' => ['pdf'],
			]));
		}

		if (str_starts_with($mime, 'image/')) {
			return $this->subirImagen($archivo, array_merge($opcionesBase, $opciones, [
				'resource_type' => 'image',
			]));
		}

		throw new InvalidArgumentException('Formato no soportado. Solo se permiten imagenes, videos o PDF.');
	}

	public function eliminarArchivo(
		string $publicId,
		string $resourceType = 'image',
		string $deliveryType = 'upload',
	): array {
		$respuesta = $this->cloudinary
			->uploadApi()
			->destroy($publicId, [
				'resource_type' => $resourceType,
				'type' => $deliveryType,
			]);

		return $respuesta->getArrayCopy();
	}

	public function generarUrl(string $publicId, string $resourceType = 'image'): string
	{
		return match ($resourceType) {
			'video' => $this->cloudinary->video($publicId)->toUrl(),
			'raw' => $this->cloudinary->raw($publicId)->toUrl(),
			default => $this->cloudinary->image($publicId)->toUrl(),
		};
	}

	private function resolverFuente(UploadedFile|string $archivo): string
	{
		if ($archivo instanceof UploadedFile) {
			$rutaReal = $archivo->getRealPath();

			if ($rutaReal === false) {
				throw new InvalidArgumentException('No fue posible leer el archivo temporal para subirlo a Cloudinary.');
			}

			return $rutaReal;
		}

		if (trim($archivo) === '') {
			throw new InvalidArgumentException('Debes proporcionar una ruta local o URL valida para el archivo.');
		}

		return $archivo;
	}

	private function normalizarRespuesta(ApiResponse $respuesta): array
	{
		$data = $respuesta->getArrayCopy();

		return [
			'public_id' => $data['public_id'] ?? null,
			'resource_type' => $data['resource_type'] ?? null,
			'format' => $data['format'] ?? null,
			'bytes' => $data['bytes'] ?? null,
			'url' => $data['url'] ?? null,
			'secure_url' => $data['secure_url'] ?? null,
			'version' => $data['version'] ?? null,
			'raw' => $data,
		];
	}

	private function resolverOpcionesSslCloudinary(): array
	{
		$caBundle = trim((string) config('services.cloudinary.ca_bundle', ''));

		if ($caBundle !== '') {
			return ['verify' => $caBundle];
		}

		$verifySsl = (bool) config('services.cloudinary.verify_ssl', true);

		return ['verify' => $verifySsl];
	}
}
