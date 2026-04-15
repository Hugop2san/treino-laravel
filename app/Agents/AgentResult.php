<?php

namespace App\Agents;

use App\Models\Analysis;

class AgentResult
{
    public function __construct(
        public readonly ?string $answer,
        public readonly ?string $errorMessage,
        public readonly Analysis $analysis
    ) {
    }
}
