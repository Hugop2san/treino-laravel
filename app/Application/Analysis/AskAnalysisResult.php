<?php

namespace App\Application\Analysis;

use App\Models\Analysis;

class AskAnalysisResult
{
    public function __construct(
        public readonly ?string $answer,
        public readonly ?string $errorMessage,
        public readonly Analysis $analysis
    ) {
    }
}
