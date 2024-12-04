<?php

namespace App\Services;

use App\DTO\GepettoResponse;
use Exception;
use OpenAI;
use OpenAI\Client;

final class Gepetto
{
    private const int RETRY_LIMIT = 3;
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
     * @param  string  $currentCVContent
     * @param  string  $offerDescription
     * @return GepettoResponse
     * @throws Exception if the AI response is empty
     */
    public function generateCVContent(string $currentCVContent, string $offerDescription): GepettoResponse
    {
        $retry = 0;
        while ($retry < self::RETRY_LIMIT) {
            $result = $this->sendRequest($currentCVContent, $offerDescription);
            if (!empty($result->choices)) {
                break;
            }
            $retry++;
        }

        if (empty($result->choices)) {
            throw new Exception('AI not intelligent enough to generate a response');
        }

        // Happy-go-lucky. Assume the first choice is the best one.
        $response = json_decode($result->choices[0]->message->content, associative: true);

        return GepettoResponse::fromArray($response);
    }

    /**
     * @param $currentCVContent
     * @param $offerDescription
     * @return OpenAI\Responses\Chat\CreateResponse
     */
    private function sendRequest($currentCVContent, $offerDescription): OpenAI\Responses\Chat\CreateResponse
    {

        $prompt = $this->getPrompt($currentCVContent, $offerDescription);
        return $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);
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
