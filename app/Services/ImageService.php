<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageService
{
    /**
     * Faz upload ou atualização de uma imagem diretamente na pasta public
     *
     * @param UploadedFile|null $file
     * @param string|null $oldPath
     * @param string $folder
     * @param array $allowedMimes
     * @param int $maxSizeEmKb
     * @return string|null
     *
     * @throws ValidationException
     */
    public static function handleUpload(
        ?UploadedFile $file,
        ?string $oldPath = null,
        string $folder = 'uploads',
        array $allowedMimes = ['png', 'jpg', 'jpeg', 'svg'],
        int $maxSizeEmKb = 2048
    ): ?string {
        if (!$file) {
            return $oldPath;
        }

        // Validação de extensão
        $extension = Str::lower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedMimes)) {
            throw ValidationException::withMessages([
                'icon' => "O arquivo deve ser do tipo: " . implode(', ', $allowedMimes) . "."
            ]);
        }

        // Validação de tamanho
        if ($file->getSize() / 1024 > $maxSizeEmKb) {
            throw ValidationException::withMessages([
                'icon' => "O arquivo não pode ser maior que {$maxSizeEmKb} KB."
            ]);
        }

        // Remove imagem antiga
        if ($oldPath && file_exists(public_path($oldPath))) {
            unlink(public_path($oldPath));
        }

        // Gera um nome único para evitar conflito
        $filename = uniqid() . '.' . $extension;

        // Cria a pasta se não existir
        $path = public_path($folder);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        // Move o arquivo
        $file->move($path, $filename);

        // Retorna o caminho relativo para uso na view
        return "{$folder}/{$filename}";
    }
}
