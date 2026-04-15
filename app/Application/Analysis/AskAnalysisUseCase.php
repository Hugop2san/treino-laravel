<?php

namespace App\Application\Analysis;

use App\Agents\AnalysisAgent;

class AskAnalysisUseCase
{
    public function __construct(
        private readonly AnalysisAgent $analysisAgent
    ) {
    }

    public function execute(string $prompt): AskAnalysisResult
    {
        $result = $this->analysisAgent->run($prompt);

        return new AskAnalysisResult(
            answer: $result->answer,
            errorMessage: $result->errorMessage,
            analysis: $result->analysis
        );
    }
}
