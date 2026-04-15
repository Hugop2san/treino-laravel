<?php

namespace App\Agents\Tools;

use Illuminate\Support\Facades\File;

class ReadReferencesTool
{
    public function execute(): string
    {
        $referencePath = base_path('references');

        if (! File::isDirectory($referencePath)) {
            return 'Nenhum arquivo de referencia encontrado.';
        }

        $files = collect(File::files($referencePath))
            ->filter(fn ($file) => in_array($file->getExtension(), ['md', 'txt'], true))
            ->sortBy(fn ($file) => $file->getFilename())
            ->map(function ($file) {
                return 'Arquivo: ' . $file->getFilename() . "\n" . trim(File::get($file->getPathname()));
            });

        return $files->isNotEmpty()
            ? $files->implode("\n\n")
            : 'Nenhum arquivo de referencia encontrado.';
    }
}
