<?php

namespace App\Services;

use App\DTO\GepettoResponse;
use OpenAI;
use OpenAI\Client;

final class Gepetto
{
    private const string PROMPT = "
    Update contents of the provided CV data so that it matches the offer description giving highest chance of getting the interview.
    You can be very creative when it comes to description, however do not change the experience. Try to squeeze as much upside from each
    position as possible.

    I need the data to be returned in JSON format like this:
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
    public function __construct() {
        $key = env('GPT_API_KEY');
        if (empty($key)) {
            throw new \Exception('GPT_API_KEY is not set');
        }

        $this->client = OpenAI::client($key);
    }

    public function generateCVContent(string $currentCVContent, string $offerDescription): GepettoResponse
    {
        $prompt = $this->getPrompt($currentCVContent, $offerDescription);
        $result = $this->client->chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $response = json_decode($result->choices[0]->message->content, associative: true);

        return GepettoResponse::fromArray($response);
    }

    private function getPrompt(string $currentCVContent, string $offerDescription): string
    {
        return sprintf(self::PROMPT, $offerDescription, $currentCVContent);
    }
}
