<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    }

    /**
     * Call Gemini API or fall back to local AI mock
     */
    protected function callGemini($prompt, $jsonResponse = false)
    {
        if (empty($this->apiKey)) {
            return $this->getMockResponse($prompt, $jsonResponse);
        }

        try {
            $response = Http::post("{$this->endpoint}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => $jsonResponse ? [
                    'responseMimeType' => 'application/json'
                ] : null
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            }
            
            Log::error('Gemini API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Gemini connection error: ' . $e->getMessage());
        }

        return $this->getMockResponse($prompt, $jsonResponse);
    }

    /**
     * Generate interview question based on history and settings
     */
    public function generateQuestion($type, $tech, $difficulty, $history = [], $language = 'en', $projects = null)
    {
        $isPhpLaravel = $type === 'Technical' && !empty($tech) && (stripos($tech, 'Laravel') !== false || stripos($tech, 'PHP') !== false);

        if ($isPhpLaravel) {
            $fresherPool = [
                [
                    'id' => 'what_is_php',
                    'en' => 'What is PHP?',
                    'hi' => 'PHP Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'PHP Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'PHP kya hai?',
                    'gu_en' => 'PHP shu chhe?',
                ],
                [
                    'id' => 'what_is_laravel',
                    'en' => 'What is Laravel?',
                    'hi' => 'Laravel Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Laravel Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Laravel kya hai?',
                    'gu_en' => 'Laravel shu chhe?',
                ],
                [
                    'id' => 'mvc_architecture',
                    'en' => 'Explain MVC Architecture.',
                    'hi' => 'MVC Ã Â¤â€ Ã Â¤Â°Ã Â¥ï¿½Ã Â¤â€¢Ã Â¤Â¿Ã Â¤Å¸Ã Â¥â€¡Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Å¡Ã Â¤Â° (MVC Architecture) Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'MVC Ã Âªâ€ Ã ÂªÂ°Ã Â«ï¿½Ã Âªâ€¢Ã ÂªÂ¿Ã ÂªÅ¸Ã Â«â€¡Ã Âªâ€¢Ã Â«ï¿½Ã ÂªÅ¡Ã ÂªÂ° (MVC Architecture) Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'MVC Architecture ko explain karein.',
                    'gu_en' => 'MVC Architecture ne explain karo.',
                ],
                [
                    'id' => 'get_vs_post',
                    'en' => 'Difference between GET and POST.',
                    'hi' => 'GET Ã Â¤â€Ã Â¤Â° POST Ã Â¤â€¢Ã Â¥â€¡ Ã Â¤Â¬Ã Â¥â‚¬Ã Â¤Å¡ Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤â€¦Ã Â¤â€šÃ Â¤Â¤Ã Â¤Â° Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'GET Ã Âªâ€¦Ã ÂªÂ¨Ã Â«â€¡ POST Ã ÂªÂµÃ ÂªÅ¡Ã Â«ï¿½Ã ÂªÅ¡Ã Â«â€¡ Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã ÂªÂ¤Ã ÂªÂ«Ã ÂªÂ¾Ã ÂªÂµÃ ÂªÂ¤ Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'GET aur POST ke beech kya difference hai?',
                    'gu_en' => 'GET ane POST vachhe shu difference chhe?',
                ],
                [
                    'id' => 'what_is_middleware',
                    'en' => 'What is Middleware?',
                    'hi' => 'Middleware Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Middleware Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Middleware kya hai?',
                    'gu_en' => 'Middleware shu chhe?',
                ],
                [
                    'id' => 'what_is_route',
                    'en' => 'What is Route?',
                    'hi' => 'Route Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Route Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Route kya hai?',
                    'gu_en' => 'Route shu chhe?',
                ],
                [
                    'id' => 'what_is_migration',
                    'en' => 'What is Migration?',
                    'hi' => 'Migration Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Migration Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Migration kya hai?',
                    'gu_en' => 'Migration shu chhe?',
                ],
                [
                    'id' => 'what_is_eloquent_orm',
                    'en' => 'What is Eloquent ORM?',
                    'hi' => 'Eloquent ORM Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Eloquent ORM Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Eloquent ORM kya hai?',
                    'gu_en' => 'Eloquent ORM shu chhe?',
                ],
                [
                    'id' => 'explain_rest_api',
                    'en' => 'Explain REST API.',
                    'hi' => 'REST API Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'REST API Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'REST API ko explain karein.',
                    'gu_en' => 'REST API ne explain karo.',
                ],
                [
                    'id' => 'what_is_authentication',
                    'en' => 'What is Authentication?',
                    'hi' => 'Authentication Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Authentication Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Authentication kya hai?',
                    'gu_en' => 'Authentication shu chhe?',
                ],
            ];

            $experiencedPool = [
                [
                    'id' => 'laravel_service_container',
                    'en' => 'Explain Laravel Service Container.',
                    'hi' => 'Laravel Service Container Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'Laravel Service Container Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'Laravel Service Container ko explain karein.',
                    'gu_en' => 'Laravel Service Container ne explain karo.',
                ],
                [
                    'id' => 'events_and_listeners',
                    'en' => 'What are Events and Listeners?',
                    'hi' => 'Events Ã Â¤â€Ã Â¤Â° Listeners Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†Ã Â¤â€š?',
                    'gu' => 'Events Ã Âªâ€¦Ã ÂªÂ¨Ã Â«â€¡ Listeners Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Events aur Listeners kya hain?',
                    'gu_en' => 'Events ane Listeners shu chhe?',
                ],
                [
                    'id' => 'queue_system',
                    'en' => 'Explain Queue System.',
                    'hi' => 'Queue System Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'Queue System Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'Queue System ko explain karein.',
                    'gu_en' => 'Queue System ne explain karo.',
                ],
                [
                    'id' => 'optimize_laravel_performance',
                    'en' => 'How to optimize Laravel performance?',
                    'hi' => 'Laravel Ã Â¤ÂªÃ Â¤Â°Ã Â¤Â«Ã Â¥â€°Ã Â¤Â°Ã Â¥ï¿½Ã Â¤Â®Ã Â¥â€¡Ã Â¤â€šÃ Â¤Â¸ Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤â€¢Ã Â¥Ë†Ã Â¤Â¸Ã Â¥â€¡ Ã Â¤â€˜Ã Â¤ÂªÃ Â¥ï¿½Ã Â¤Å¸Ã Â¤Â¿Ã Â¤Â®Ã Â¤Â¾Ã Â¤â€¡Ã Â¤Å“Ã Â¤Â¼ (optimize) Ã Â¤â€¢Ã Â¤Â°Ã Â¥â€¡Ã Â¤â€š?',
                    'gu' => 'Laravel Ã ÂªÂªÃ ÂªÂ°Ã Â«ï¿½Ã ÂªÂ«Ã Â«â€¹Ã ÂªÂ°Ã Â«ï¿½Ã ÂªÂ®Ã ÂªÂ¨Ã Â«ï¿½Ã ÂªÂ¸Ã ÂªÂ¨Ã Â«â€¡ Ã Âªâ€¢Ã Â«â€¡Ã ÂªÂµÃ Â«â‚¬ Ã ÂªÂ°Ã Â«â‚¬Ã ÂªÂ¤Ã Â«â€¡ Ã Âªâ€œÃ ÂªÂªÃ Â«ï¿½Ã ÂªÅ¸Ã ÂªÂ¿Ã ÂªÂ®Ã ÂªÂ¾Ã Âªâ€¡Ã Âªï¿½ (optimize) Ã Âªâ€¢Ã ÂªÂ°Ã ÂªÂµÃ Â«ï¿½Ã Âªâ€š?',
                    'hi_en' => 'Laravel performance ko kaise optimize karein?',
                    'gu_en' => 'Laravel performance ne kevi rite optimize karvu?',
                ],
                [
                    'id' => 'redis_usage',
                    'en' => 'Explain Redis usage.',
                    'hi' => 'Redis Ã Â¤â€¢Ã Â¥â€¡ Ã Â¤â€°Ã Â¤ÂªÃ Â¤Â¯Ã Â¥â€¹Ã Â¤â€” Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'Redis Ã ÂªÂ¨Ã ÂªÂ¾ Ã Âªâ€°Ã ÂªÂªÃ ÂªÂ¯Ã Â«â€¹Ã Âªâ€”Ã ÂªÂ¨Ã Â«â€¡ Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'Redis usage ko explain karein.',
                    'gu_en' => 'Redis usage ne explain karo.',
                ],
                [
                    'id' => 'dependency_injection',
                    'en' => 'What is Dependency Injection?',
                    'hi' => 'Dependency Injection Ã Â¤â€¢Ã Â¥ï¿½Ã Â¤Â¯Ã Â¤Â¾ Ã Â¤Â¹Ã Â¥Ë†?',
                    'gu' => 'Dependency Injection Ã ÂªÂ¶Ã Â«ï¿½Ã Âªâ€š Ã Âªâ€ºÃ Â«â€¡?',
                    'hi_en' => 'Dependency Injection kya hai?',
                    'gu_en' => 'Dependency Injection shu chhe?',
                ],
                [
                    'id' => 'design_patterns_in_laravel',
                    'en' => 'Explain Design Patterns used in Laravel.',
                    'hi' => 'Laravel Ã Â¤Â®Ã Â¥â€¡Ã Â¤â€š Ã Â¤â€°Ã Â¤ÂªÃ Â¤Â¯Ã Â¥â€¹Ã Â¤â€” Ã Â¤Â¹Ã Â¥â€¹Ã Â¤Â¨Ã Â¥â€¡ Ã Â¤ÂµÃ Â¤Â¾Ã Â¤Â²Ã Â¥â€¡ Design Patterns Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤Â¸Ã Â¤Â®Ã Â¤ï¿½Ã Â¤Â¾Ã Â¤â€¡Ã Â¤ï¿½Ã Â¥Â¤',
                    'gu' => 'Laravel Ã ÂªÂ®Ã ÂªÂ¾Ã Âªâ€š Ã Âªâ€°Ã ÂªÂªÃ ÂªÂ¯Ã Â«â€¹Ã Âªâ€” Ã ÂªÂ¥Ã ÂªÂ¤Ã ÂªÂ¾ Design Patterns Ã ÂªÂ¸Ã ÂªÂ®Ã ÂªÅ“Ã ÂªÂ¾Ã ÂªÂµÃ Â«â€¹.',
                    'hi_en' => 'Laravel mein use hone wale Design Patterns ko explain karein.',
                    'gu_en' => 'Laravel ma use thata Design Patterns ne explain karo.',
                ],
                [
                    'id' => 'handle_large_scale_applications',
                    'en' => 'How do you handle large-scale applications?',
                    'hi' => 'Ã Â¤â€ Ã Â¤Âª Ã Â¤Â¬Ã Â¤Â¡Ã Â¤Â¼Ã Â¥â€¡ Ã Â¤ÂªÃ Â¥Ë†Ã Â¤Â®Ã Â¤Â¾Ã Â¤Â¨Ã Â¥â€¡ Ã Â¤â€¢Ã Â¥â€¡ Ã Â¤ï¿½Ã Â¤ÂªÃ Â¥ï¿½Ã Â¤Â²Ã Â¤Â¿Ã Â¤â€¢Ã Â¥â€¡Ã Â¤Â¶Ã Â¤Â¨Ã Â¥ï¿½Ã Â¤Â¸ (large-scale applications) Ã Â¤â€¢Ã Â¥â€¹ Ã Â¤â€¢Ã Â¥Ë†Ã Â¤Â¸Ã Â¥â€¡ Ã Â¤Â¹Ã Â¥Ë†Ã Â¤â€šÃ Â¤Â¡Ã Â¤Â² Ã Â¤â€¢Ã Â¤Â°Ã Â¤Â¤Ã Â¥â€¡ Ã Â¤Â¹Ã Â¥Ë†Ã Â¤â€š?',
                    'gu' => 'Ã ÂªÂ¤Ã ÂªÂ®Ã Â«â€¡ Ã ÂªÂ®Ã Â«â€¹Ã ÂªÅ¸Ã ÂªÂ¾ Ã ÂªÂªÃ ÂªÂ¾Ã ÂªÂ¯Ã ÂªÂ¾Ã ÂªÂ¨Ã ÂªÂ¾ Ã Âªï¿½Ã ÂªÂªÃ Â«ï¿½Ã ÂªÂ²Ã ÂªÂ¿Ã Âªâ€¢Ã Â«â€¡Ã ÂªÂ¶Ã ÂªÂ¨Ã Â«ï¿½Ã ÂªÂ¸ (large-scale applications) Ã ÂªÂ¨Ã Â«â€¡ Ã Âªâ€¢Ã Â«â€¡Ã ÂªÂµÃ Â«â‚¬ Ã ÂªÂ°Ã Â«â‚¬Ã ÂªÂ¤Ã Â«â€¡ Ã ÂªÂ¹Ã Â«â€¡Ã ÂªÂ¨Ã Â«ï¿½Ã ÂªÂ¡Ã ÂªÂ² Ã Âªâ€¢Ã ÂªÂ°Ã Â«â€¹ Ã Âªâ€ºÃ Â«â€¹?',
                    'hi_en' => 'Aap large-scale applications ko kaise handle karte hain?',
                    'gu_en' => 'Tame large-scale applications ne kevi rite handle karo chho?',
                ],
            ];

            // Determine if Experienced based on difficulty or history
            $isExperienced = false;
            if (stripos($difficulty, 'Experienced') !== false || stripos($difficulty, 'Expert') !== false || stripos($difficulty, 'Architectural') !== false) {
                $isExperienced = true;
            } else {
                foreach ($history as $h) {
                    $qText = $h['question'] ?? '';
                    foreach ($experiencedPool as $q) {
                        if ($this->questionMatches($qText, $q)) {
                            $isExperienced = true;
                            break 2;
                        }
                    }
                }
            }

            $pool = $isExperienced ? $experiencedPool : $fresherPool;

            $nextQuestionData = null;
            foreach ($pool as $q) {
                $alreadyAsked = false;
                foreach ($history as $h) {
                    if (isset($h['question']) && $this->questionMatches($h['question'], $q)) {
                        $alreadyAsked = true;
                        break;
                    }
                }
                if (!$alreadyAsked) {
                    $nextQuestionData = $q;
                    break;
                }
            }

            if (!$nextQuestionData) {
                $nextQuestionData = $pool[count($history) % count($pool)];
            }

            $langKey = $language;
            if (!in_array($langKey, ['en', 'hi', 'gu', 'hi_en', 'gu_en'])) {
                $langKey = 'en';
            }

            $questionText = $nextQuestionData[$langKey] ?? $nextQuestionData['en'];

            // Translate using Gemini dynamically if key is present (but fallback to predefined is always active)
            if ($language !== 'en' && !empty($this->apiKey)) {
                $translatePrompt = "Translate this technical interview question to " . $this->getLanguageName($language) . ". "
                    . "Keep technical terms (like PHP, Laravel, GET, POST, Middleware, Route, Migration, Eloquent ORM, REST API, Authentication, Service Container, Events, Listeners, Queue, Redis, Dependency Injection, Design Patterns) in English or in parenthetical English format if translated, so it is easy for developers to understand. "
                    . "Only return the translated question, do not include any other text.\nQuestion: " . $nextQuestionData['en'];
                
                try {
                    $translated = $this->callGemini($translatePrompt);
                    if (!empty($translated)) {
                        return trim($translated);
                    }
                } catch (\Exception $e) {
                    Log::error("Translation error: " . $e->getMessage());
                }
            }

            return $questionText;
        }

        $prompt = "You are a professional IT interviewer. Generate the next single question for a {$type} interview. ";
        if ($type === 'Technical') {
            $prompt .= "The technologies are: {$tech}. ";
        }
        $prompt .= "Difficulty: {$difficulty}. Preferred language: {$language}. ";
        
        if (!empty($projects)) {
            $prompt .= "The candidate has the following project(s) on their resume:\n{$projects}\n"
                     . "You MUST ask questions related to this project(s) (e.g. explain project architecture, database tables, how booking/specific feature was implemented, challenges faced and how they solved them). ";
        }
        
        if (!empty($history)) {
            $prompt .= "Here is the conversation history so far: " . json_encode($history) . ". ";
        }
        
        $prompt .= "Ask the next relevant, context-aware question directly. Keep your reply to ONLY the question text. Do not add intro or outro.";

        return $this->callGemini($prompt);
    }

    /**
     * Check if a question text matches one of the predefined questions
     */
    protected function questionMatches($text, $q)
    {
        foreach (['en', 'hi', 'gu', 'hi_en', 'gu_en'] as $langKey) {
            if (isset($q[$langKey]) && (stripos($text, $q[$langKey]) !== false || stripos($q[$langKey], $text) !== false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get language name helper
     */
    protected function getLanguageName($code)
    {
        $names = [
            'en' => 'English',
            'hi' => 'Hindi',
            'gu' => 'Gujarati',
            'hi_en' => 'Hinglish (Hindi + English)',
            'gu_en' => 'Gujlish (Gujarati + English)',
        ];
        return $names[$code] ?? 'English';
    }

    /**
     * Evaluate the answer provided by user
     */
    public function evaluateAnswer($question, $answer, $languagePreference = 'en')
    {
        $prompt = "Evaluate the following answer to this interview question:
Question: \"{$question}\"
User's Answer: \"{$answer}\"

Preferred response language is: {$languagePreference}. Note: All parts of the evaluation (positive_points, missing_points, suggestions, grammar_feedback, etc.) should match this preferred language, BUT the 'improved_answer' MUST be written strictly in English.
Analyze grammar, completeness, technical accuracy. Return a JSON object containing:
- score (0 to 100)
- communication_score (0 to 100)
- confidence_score (0 to 100)
- positive_points (array of strings showing what the user got right)
- missing_points (array of strings showing what they missed)
- suggestions (array of suggestions for improvement)
- improved_answer (an improved professional answer written strictly in English)
- grammar_feedback (short critique of their grammar and tone)
- voice_analysis (mock speed and clarity analysis)
- camera_analysis (mock posture and eye contact analysis)

Only return a valid JSON object. No backticks, no text wrappers.";

        $response = $this->callGemini($prompt, true);
        
        // Clean JSON formatting from Gemini wrappers
        $cleaned = trim($response);
        if (strpos($cleaned, '```json') === 0) {
            $cleaned = substr($cleaned, 7);
        }
        if (strpos($cleaned, '```') === 0) {
            $cleaned = substr($cleaned, 3);
        }
        if (strrpos($cleaned, '```') === strlen($cleaned) - 3) {
            $cleaned = substr($cleaned, 0, -3);
        }
        $cleaned = trim($cleaned);

        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Return a structured array if decoding fails
        return json_decode($this->getMockResponse($prompt, true), true);
    }

    /**
     * Analyze uploaded resume text
     */
    public function analyzeResume($text, $user = null)
    {
        $hasBulletPoints = preg_match('/(?:[â€¢\-\*â–ªâ—¦]|\d+\.)\s+\w+/', $text);
        $hasExperience = stripos($text, 'experience') !== false || stripos($text, 'work') !== false || stripos($text, 'history') !== false;
        $hasProjects = stripos($text, 'project') !== false;
        $hasEducation = stripos($text, 'education') !== false || stripos($text, 'academic') !== false || stripos($text, 'degree') !== false;
        $hasSkills = stripos($text, 'skill') !== false;

        $courseTitles = [];
        if ($user) {
            $courseTitles = $user->enrollments()->with('course')->get()->map(function($e) {
                return $e->course->title;
            })->toArray();
        }
        
        $courseInfo = "None specified";
        if (!empty($courseTitles)) {
            $courseInfo = implode(', ', $courseTitles);
        } elseif ($user && $user->career_path) {
            $courseInfo = $user->career_path;
        }

        $prompt = "You are a professional ATS (Applicant Tracking System) software used by corporate companies.
Analyze the uploaded resume text below and generate a highly personalized, structured evaluation report based on the candidate's actual details.

Resume: \"{$text}\"

Metadata facts for analysis:
- Bullet points present: " . ($hasBulletPoints ? "Yes" : "No") . "
- Experience section present: " . ($hasExperience ? "Yes" : "No") . "
- Projects section present: " . ($hasProjects ? "Yes" : "No") . "
- Education section present: " . ($hasEducation ? "Yes" : "No") . "
- Skills section present: " . ($hasSkills ? "Yes" : "No") . "
- User Enrolled Course(s) / Career Path: {$courseInfo}

Instructions:
1. Parse the resume to extract: Name, Email, Phone Number, Education, Experience, Skills, Projects, Certifications, Internships, Technical Stack, Soft Skills, Languages, GitHub Profile, LinkedIn Profile.
2. Automatically detect the target role (e.g. Backend Developer, Frontend Developer, MERN Developer, Python Data Analyst, DevOps Engineer, QA Engineer, Cyber Security Specialist) based on the technologies present in the resume. Explain why this role fits the candidate.
3. Calculate ATS Score out of 100 based on structure, keywords, skills, experience, projects, education, formatting, and readability.
4. Calculate readiness scores (0-100) for: ATS Readiness, Technical Readiness, HR Readiness, Communication Readiness, Coding Readiness, and Overall Interview Readiness.
5. Identify all technologies mentioned in the resume and list matched keywords.
6. Compare the resume against the detected role's industry requirements to detect missing skills. List each missing skill along with a short explanation of why it is important.
7. Analyze each project in the resume. Detail its strengths and suggest specific missing features or enhancements to make it production-ready.
8. Generate 5 personalized mock interview questions tailored directly to the candidate's actual skills, projects, and work experience.
9. Generate a week-by-week or month-by-month learning plan/roadmap.
10. Suggest improvements (add achievements, links, etc.) and explain why they improve the resume.

Return a JSON object with the following exact keys:
- \"parsed_info\": {
    \"name\": \"string\",
    \"email\": \"string\",
    \"phone\": \"string\",
    \"github\": \"string\",
    \"linkedin\": \"string\",
    \"education\": [{\"degree\": \"string\", \"institution\": \"string\", \"year\": \"string\"}],
    \"experience\": [{\"role\": \"string\", \"company\": \"string\", \"duration\": \"string\", \"responsibilities\": \"string\"}],
    \"projects\": [{\"title\": \"string\", \"tech_stack\": [\"string\"], \"description\": \"string\"}],
    \"certifications\": [\"string\"],
    \"internships\": [\"string\"],
    \"tech_stack\": [\"string\"],
    \"soft_skills\": [\"string\"],
    \"languages\": [\"string\"]
  }
- \"target_role_detection\": {\"role\": \"string\", \"reason\": \"string\"}
- \"readiness_scores\": {
    \"ats_score\": integer,
    \"technical_readiness\": integer,
