<?php

namespace App\Services;

use App\DTO\GepettoResponse;
use Exception;
use OpenAI;
use OpenAI\Client;

final class Gepetto
{
    private const string PROMPT = "
    Update contents of the provided CV data so that it matches the offer description giving highest chance of getting the interview.
    You can be very creative when it comes to description, however do not change the experience. Try to squeeze as much upside from each
    position as possible.

    I need the data to be returned in JSON format like this. Nothing else. Just the data. Do not include the prompt in the response:
    {
        'personal': {
            'name': '',
            'email': '',
            'linkedin': '',
            'summary': ''
        },
        'experience': [{
            'company': '',
            'position': '',
            'start_date': '',
            'end_date': '',
            'description': ''
        }],
        'education': [{
            'institution': '',
            'degree': '',
            'start_date': '',
            'end_date': ''
        }],
    }

    Offer description: %s
    Current CV content: %s";

    private Client $client;

    /**
     * TODO: At some point use Dependency Injection to inject the API key as a parameter
     * @throws \Throwable if the GPT_API_KEY is not set
     */
    public function __construct() {
        $key = env('GPT_API_KEY');

        if (empty($key)) {
            throw new Exception('GPT_API_KEY is not set');
        }

        $this->client = OpenAI::client($key);
    }

    /**
     * TODO: Consider retrying the request if the AI response is empty
     *
     * @param  string  $currentCVContent
     * @param  string  $offerDescription
     * @return GepettoResponse
     * @throws Exception if the AI response is empty
     */
    public function generateCVContent(string $currentCVContent, string $offerDescription): GepettoResponse
    {
        $prompt = $this->getPrompt($currentCVContent, $offerDescription);
        $result = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        // This could potentially be a problem if the AI is not intelligent enough, which it often isn't.
        // In that case, we should retry the request.
        // TODO: Implement retry mechanism
        if (empty($result->choices)) {
            throw new Exception('AI not intelligent enough to generate a response');
        }

        // Happy-go-lucky. Assume the first choice is the best one.
        $response = json_decode($result->choices[0]->message->content, associative: true);

        return GepettoResponse::fromArray($response);
    }

    /**
     * Fancy wrapper around the prompt.
     *
     * @param  string  $currentCVContent
     * @param  string  $offerDescription
     * @return string
     */
    private function getPrompt(string $currentCVContent, string $offerDescription): string
    {
        return sprintf(self::PROMPT, $offerDescription, $currentCVContent);
    }
}
