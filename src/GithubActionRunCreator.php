<?php

namespace PierreMiniggio\GithubActionRunCreator;

use PierreMiniggio\GithubActionRunCreator\Exception\NotFoundException;
use PierreMiniggio\GithubActionRunCreator\Exception\UnauthorizedException;
use PierreMiniggio\GithubActionRunCreator\Exception\UnknownException;
use PierreMiniggio\GithubUserAgent\GithubUserAgent;
use RuntimeException;

class GithubActionRunCreator
{

    /**
     * @param array<string, mixed> $inputs
     * 
     * @return GithubActionRun[]
     * 
     * @throws NotFoundException
     * @throws RuntimeException
     * @throws UnauthorizedException
     */
    public function create(
        string $token,
        string $owner,
        string $repo,
        string $workflowIdOrWorkflowFileName,
        array $inputs = [],
        string $ref = 'main'
    ): void
    {
        $curl = curl_init("https://api.github.com/repos/$owner/$repo/actions/workflows/$workflowIdOrWorkflowFileName/dispatches");
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_USERAGENT => GithubUserAgent::USER_AGENT,
            CURLOPT_HTTPHEADER => ['Authorization: token ' . $token],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode([
                'ref' => $ref,
                'inputs' => $inputs
            ])
        ]);

        $response = curl_exec($curl);

        if ($response === false) {
            throw new RuntimeException('Curl error' . curl_error($curl));
        }

        $jsonResponse = json_decode($response, true);

        if (is_array($jsonResponse) && ! empty($jsonResponse['message'])) {
            $message = $jsonResponse['message'];

            if (
                $message === 'Must have admin rights to Repository.'
                || $message === 'Bad credentials'
            ) {
                throw new UnauthorizedException();
            }

            if ($message === 'Not Found') {
                throw new NotFoundException();
            }

            throw new UnknownException($message);
        }
    }
}
