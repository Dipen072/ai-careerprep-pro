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
        // 1. Detect which JSON file to load based on $tech
        $jsonFile = null;
        if (!empty($tech)) {
            if (stripos($tech, 'Laravel') !== false || stripos($tech, 'PHP') !== false) {
                $jsonFile = 'php.json';
            } elseif (stripos($tech, 'Python') !== false || stripos($tech, 'Django') !== false || stripos($tech, 'Flask') !== false) {
                $jsonFile = 'python.json';
            } elseif (stripos($tech, 'Java') !== false || stripos($tech, 'Spring') !== false) {
                $jsonFile = 'java.json';
            } elseif (stripos($tech, 'MySQL') !== false || stripos($tech, 'PostgreSQL') !== false || stripos($tech, 'SQL') !== false) {
                $jsonFile = 'sql.json';
            } elseif (stripos($tech, 'Node') !== false || stripos($tech, 'Express') !== false) {
                $jsonFile = 'node.json';
            }
        }

        // If it's technical and we have a predefined JSON file, load it
        if ($type === 'Technical' && $jsonFile !== null) {
            $path = resource_path('interview_questions/' . $jsonFile);
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if ($data) {
                    $fresherPool = $data['fresher'] ?? [];
                    $experiencedPool = $data['experienced'] ?? [];

                    // Determine difficulty category
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

                    // Filter out already asked questions
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

                    if (!$nextQuestionData && !empty($pool)) {
                        $nextQuestionData = $pool[count($history) % count($pool)];
                    }

                    if ($nextQuestionData) {
                        $langKey = $language;
                        if (!in_array($langKey, ['en', 'hi', 'gu', 'hi_en', 'gu_en'])) {
                            $langKey = 'en';
                        }

                        $questionText = $nextQuestionData['question'][$langKey] ?? $nextQuestionData['question']['en'];

                        // Translate dynamically using Gemini if key is present and language is not English
                        if ($language !== 'en' && !empty($this->apiKey)) {
                            $translatePrompt = "Translate this technical interview question to " . $this->getLanguageName($language) . ". "
                                . "Keep technical terms in English or in parenthetical English format if translated, so it is easy for developers to understand. "
                                . "Only return the translated question, do not include any other text.\nQuestion: " . $nextQuestionData['question']['en'];
                            
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
                }
            }
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
        $qTexts = isset($q['question']) ? $q['question'] : $q;
        foreach (['en', 'hi', 'gu', 'hi_en', 'gu_en'] as $langKey) {
            if (isset($qTexts[$langKey]) && (stripos($text, $qTexts[$langKey]) !== false || stripos($qTexts[$langKey], $text) !== false)) {
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
    /**
     * Generate answer hint based on question and language
     */
    public function generateHint($questionText, $language = 'en')
    {
        // Check predefined questions in JSON files first
        $jsonFiles = ['php.json', 'python.json', 'java.json', 'sql.json', 'node.json'];
        foreach ($jsonFiles as $file) {
            $path = resource_path('interview_questions/' . $file);
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if ($data) {
                    $pools = array_merge($data['fresher'] ?? [], $data['experienced'] ?? []);
                    foreach ($pools as $q) {
                        if ($this->questionMatches($questionText, $q)) {
                            $langKey = $language;
                            if (!in_array($langKey, ['en', 'hi', 'gu', 'hi_en', 'gu_en'])) {
                                $langKey = 'en';
                            }
                            $hint = $q['hint'][$langKey] ?? $q['hint']['en'] ?? null;
                            if ($hint) {
                                return $hint;
                            }
                        }
                    }
                }
            }
        }

        $prompt = "You are an AI interview coach. Provide a short, one-sentence hint or starting pointer to help the candidate answer this question: \"" . $questionText . "\". Keep the hint brief and written in the preferred language: " . $language . ". Only return the hint text itself, no intro, no quote marks.";
        return $this->callGemini($prompt);
    }

    public function evaluateAnswer($question, $answer, $languagePreference = 'en')
    {
        // If API key is empty (mock mode), look up predefined evaluation from JSON files
        if (empty($this->apiKey)) {
            $jsonFiles = ['php.json', 'python.json', 'java.json', 'sql.json', 'node.json'];
            foreach ($jsonFiles as $file) {
                $path = resource_path('interview_questions/' . $file);
                if (file_exists($path)) {
                    $data = json_decode(file_get_contents($path), true);
                    if ($data) {
                        $pools = array_merge($data['fresher'] ?? [], $data['experienced'] ?? []);
                        foreach ($pools as $q) {
                            if ($this->questionMatches($question, $q)) {
                                if (isset($q['evaluation'])) {
                                    return $q['evaluation'];
                                }
                            }
                        }
                    }
                }
            }
        }

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
    \"hr_readiness\": integer,
    \"communication_readiness\": integer,
    \"coding_readiness\": integer,
    \"overall_readiness\": integer
  }
- \"ats_score_breakdown\": {
    \"structure_score\": integer,
    \"keywords_score\": integer,
    \"skills_score\": integer,
    \"experience_score\": integer,
    \"projects_score\": integer,
    \"education_score\": integer,
    \"formatting_score\": integer,
    \"readability_score\": integer
  }
- \"missing_skills_analysis\": [{\"skill\": \"string\", \"importance_explanation\": \"string\"}]
- \"project_analysis\": [{\"project_name\": \"string\", \"strengths\": \"string\", \"improvements\": [\"string\"], \"suggested_missing_features\": [\"string\"]}]
- \"personalized_interview_questions\": [\"string\"]
- \"personalized_learning_roadmap\": [{\"period\": \"string\", \"topic\": \"string\", \"description\": \"string\", \"key_actions\": [\"string\"]}]
- \"improvement_suggestions\": [{\"suggestion\": \"string\", \"reason_why\": \"string\"}]
- \"resume_summary\": \"string\"
- \"strengths\": [\"string\"]
- \"weaknesses\": [\"string\"]
- \"resume_sections_missing\": [\"string\"]
- \"recommended_certifications\": [\"string\"]
- \"recommended_skills\": [\"string\"]
- \"recommended_projects\": [\"string\"]
- \"final_hiring_decision\": \"string\"

Only return a valid JSON object. No markdown wrapping, no trailing commas, no extra text.";

        $response = $this->callGemini($prompt, true);
        $cleaned = trim($response);
        if (strpos($cleaned, '```json') === 0) {
            $cleaned = substr($cleaned, 7);
        }
        $cleaned = trim($cleaned, "`\n\r\t ");
        
        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Inject legacy keys for full backward compatibility
            $decoded['âœ… ATS Score (0-100)'] = $decoded['readiness_scores']['ats_score'] ?? $decoded['âœ… ATS Score (0-100)'] ?? 75;
            $decoded['Reason for Score'] = $decoded['target_role_detection']['reason'] ?? $decoded['Reason for Score'] ?? '';
            if (!isset($decoded['Missing Keywords']) && isset($decoded['missing_skills_analysis'])) {
                $decoded['Missing Keywords'] = array_column($decoded['missing_skills_analysis'], 'skill');
            }
            if (!isset($decoded['Matched Keywords']) && isset($decoded['parsed_info']['tech_stack'])) {
                $decoded['Matched Keywords'] = $decoded['parsed_info']['tech_stack'];
            }
            if (!isset($decoded['Top 10 Improvements']) && isset($decoded['improvement_suggestions'])) {
                $decoded['Top 10 Improvements'] = array_map(function($item) {
                    return ($item['suggestion'] ?? '') . ': ' . ($item['reason_why'] ?? '');
                }, $decoded['improvement_suggestions']);
            }
            if (!isset($decoded['Strengths']) && isset($decoded['strengths'])) {
                $decoded['Strengths'] = $decoded['strengths'];
            }
            if (!isset($decoded['Weaknesses']) && isset($decoded['weaknesses'])) {
                $decoded['Weaknesses'] = $decoded['weaknesses'];
            }
            if (!isset($decoded['Resume Sections Missing']) && isset($decoded['resume_sections_missing'])) {
                $decoded['Resume Sections Missing'] = $decoded['resume_sections_missing'];
            }
            if (!isset($decoded['Recommended Certifications']) && isset($decoded['recommended_certifications'])) {
                $decoded['Recommended Certifications'] = $decoded['recommended_certifications'];
            }
            if (!isset($decoded['Recommended Skills']) && isset($decoded['recommended_skills'])) {
                $decoded['Recommended Skills'] = $decoded['recommended_skills'];
            }
            if (!isset($decoded['Recommended Projects']) && isset($decoded['recommended_projects'])) {
                $decoded['Recommended Projects'] = $decoded['recommended_projects'];
            }
            if (!isset($decoded['Final Hiring Decision']) && isset($decoded['final_hiring_decision'])) {
                $decoded['Final Hiring Decision'] = $decoded['final_hiring_decision'];
            }
            if (!isset($decoded['Resume Summary']) && isset($decoded['resume_summary'])) {
                $decoded['Resume Summary'] = $decoded['resume_summary'];
            }
            return $decoded;
        }

        return json_decode($this->getMockResponse($prompt, true), true);
    }

    /**
     * Generate completion report from session and questions
     */
    public function generateCompletionReport($session, $questions)
    {
        $history = [];
        foreach ($questions as $q) {
            $history[] = [
                'question' => $q->question_text,
                'answer' => $q->user_answer,
                'score' => $q->ai_score,
            ];
        }

        $prompt = "Evaluate the candidate's complete performance across these interview questions:
" . json_encode($history) . "

Generate a final interview completion report.
Return a JSON object containing:
- technical_score (0 to 100)
- communication_score (0 to 100)
- confidence_score (0 to 100)
- strong_areas (array of strings, e.g. ['Laravel Basics', 'Database Concepts'])
- weak_areas (array of strings, e.g. ['API Development', 'Design Patterns'])
- recommended_topics (array of strings, e.g. ['Laravel APIs', 'Authentication', 'Queues', 'Service Container'])

Only return valid JSON.";

        $response = $this->callGemini($prompt, true);
        
        $cleaned = trim($response);
        if (strpos($cleaned, '```json') === 0) {
            $cleaned = substr($cleaned, 7);
        }
        $cleaned = trim($cleaned, "`\n\r\t ");
        
        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return json_decode($this->getMockResponse($prompt, true), true);
    }

    /**
     * Generate AI career roadmap
     */
    public function generateRoadmap($role, $skills, $experience = 'fresher')
    {
        $skillsList = is_array($skills) ? implode(', ', $skills) : $skills;
        $prompt = "Generate a 5-month learning roadmap for a person wanting to become a {$role}.
Their current skills: {$skillsList}.
Experience level: {$experience}.

Return a JSON object with a timeline of month nodes. Example format:
{
  \"timeline\": [
    {\"month\": \"Month 1\", \"topic\": \"Topic name\", \"description\": \"Details\"},
    ...
  ],
  \"skill_gap\": [
     {\"skill\": \"Skill name\", \"gap_percentage\": 60}
  ]
}

Only return valid JSON.";

        $response = $this->callGemini($prompt, true);
        $cleaned = trim($response);
        if (strpos($cleaned, '```json') === 0) {
            $cleaned = substr($cleaned, 7);
        }
        $cleaned = trim($cleaned, "`\n\r\t ");
        
        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return json_decode($this->getMockResponse($prompt, true), true);
    }

    /**
     * Intelligent Mock fallback for Offline/Demo mode
     */
    protected function getMockResponse($prompt, $jsonResponse)
    {
        if (!$jsonResponse) {
            // Check if this is a hint request
            if (strpos($prompt, 'AI interview coach') !== false || strpos($prompt, 'hint') !== false) {
                // Determine preferred language
                $lang = 'en';
                if (stripos($prompt, 'Hindi') !== false || stripos($prompt, 'hi') !== false) {
                    $lang = 'hi';
                } elseif (stripos($prompt, 'Gujarati') !== false || stripos($prompt, 'gu') !== false) {
                    $lang = 'gu';
                } elseif (stripos($prompt, 'Hinglish') !== false || stripos($prompt, 'hi_en') !== false) {
                    $lang = 'hi_en';
                } elseif (stripos($prompt, 'Gujlish') !== false || stripos($prompt, 'gu_en') !== false) {
                    $lang = 'gu_en';
                }

                $jsonFiles = ['php.json', 'python.json', 'java.json', 'sql.json', 'node.json'];
                foreach ($jsonFiles as $file) {
                    $path = resource_path('interview_questions/' . $file);
                    if (file_exists($path)) {
                        $data = json_decode(file_get_contents($path), true);
                        if ($data) {
                            $pools = array_merge($data['fresher'] ?? [], $data['experienced'] ?? []);
                            foreach ($pools as $q) {
                                foreach (['en', 'hi', 'gu', 'hi_en', 'gu_en'] as $langKey) {
                                    $qText = $q['question'][$langKey] ?? '';
                                    if (!empty($qText) && stripos($prompt, $qText) !== false) {
                                        return $q['hint'][$lang] ?? $q['hint']['en'];
                                    }
                                }
                            }
                        }
                    }
                }
                return "Hint: Try defining the core technical concept and give a clear practical example.";
            }
        }

        if ($jsonResponse) {
            // Determine language from prompt
            $lang = 'en';
            // Force $lang to be 'en' so that all mock responses return improved_answer in English.

            if (strpos($prompt, 'evaluateAnswer') !== false || strpos($prompt, 'Evaluate the following answer') !== false) {
                // Check if the question belongs to any of our predefined JSON files
                $jsonFiles = ['php.json', 'python.json', 'java.json', 'sql.json', 'node.json'];
                foreach ($jsonFiles as $file) {
                    $path = resource_path('interview_questions/' . $file);
                    if (file_exists($path)) {
                        $data = json_decode(file_get_contents($path), true);
                        if ($data) {
                            $pools = array_merge($data['fresher'] ?? [], $data['experienced'] ?? []);
                            foreach ($pools as $q) {
                                foreach (['en', 'hi', 'gu', 'hi_en', 'gu_en'] as $langKey) {
                                    $qText = $q['question'][$langKey] ?? '';
                                    if (!empty($qText) && stripos($prompt, $qText) !== false) {
                                        return json_encode($q['evaluation']);
                                    }
                                }
                            }
                        }
                    }
                }

                // Fallback generic mock evaluation
                return json_encode([
                    'score' => 85,
                    'communication_score' => 87,
                    'confidence_score' => 86,
                    'positive_points' => [
                        'You explained the core concept correctly.',
                        'Good volume and clarity.'
                    ],
                    'missing_points' => [
                        'Could provide more detailed technical examples.'
                    ],
                    'suggestions' => [
                        'Use bullet points to structure your answer.'
                    ],
                    'improved_answer' => "A professional candidate should cover the definition, explain the internal workings, and list the practical usage in projects with confidence.",
                    'grammar_feedback' => 'Clear syntax and structure.',
                    'voice_analysis' => 'Speaking speed: 120 WPM. Clarity: 92%.',
                    'camera_analysis' => 'Eye contact: 88%. Posture: Steady.'
                ]);
            }

            if (strpos($prompt, 'completion report') !== false || strpos($prompt, 'complete performance') !== false) {
                return json_encode([
                    'technical_score' => 85,
                    'communication_score' => 78,
                    'confidence_score' => 80,
                    'strong_areas' => ['Laravel Basics', 'Database Concepts', 'CRUD Operations'],
                    'weak_areas' => ['API Development', 'Design Patterns', 'Queue Systems'],
                    'recommended_topics' => ['Laravel APIs', 'Authentication', 'Queues & Redis', 'Service Container']
                ]);
            }
        }

        // Text mock fallbacks (Default is a list of dynamic interview questions)
        if (strpos($prompt, 'Technical') !== false) {
            // Check if project details are present in prompt
            if (stripos($prompt, 'Car Rental') !== false || stripos($prompt, 'project') !== false) {
                $projectQuestions = [
                    "Can you explain the overall architecture of your Car Rental Website project?",
                    "What database tables did you create for the booking functionality in the Car Rental Website?",
                    "How did you implement the vehicle booking and calendar functionalities in your Car Rental Website?",
                    "What technical challenges did you face during the implementation of the Car Rental Website?",
                    "How did you solve the database and concurrency challenges in your Car Rental Website?"
                ];
                // Select question based on history count if possible, or random
                if (strpos($prompt, 'history') !== false) {
                    // Try to count questions already asked
                    preg_match_all('/"question"/', $prompt, $matches);
                    $count = count($matches[0] ?? []);
                    return $projectQuestions[$count % count($projectQuestions)];
                }
                return $projectQuestions[0];
            }

            // Adaptive difficulty checks
            $isExpert = stripos($prompt, 'Expert') !== false || stripos($prompt, 'Architectural') !== false;
            $isHard = stripos($prompt, 'Hard') !== false || stripos($prompt, 'Advanced') !== false;
            $isMedium = stripos($prompt, 'Medium') !== false || stripos($prompt, 'Intermediate') !== false;

            if (stripos($prompt, 'Python') !== false || stripos($prompt, 'Django') !== false || stripos($prompt, 'Flask') !== false) {
                if ($isExpert) {
                    return "How does Python's Global Interpreter Lock (GIL) affect multi-threaded applications, and how do you achieve concurrency?";
                }
                if ($isHard) {
                    return "What are Python decorators, and how would you implement a custom decorator to log function execution time?";
                }
                if ($isMedium) {
                    return "What is the difference between Django's select_related and prefetch_related, and how do they optimize database queries?";
                }
                return "What are the key differences between Python lists and tuples, and when would you use each?";
            }

            if (stripos($prompt, 'React') !== false || stripos($prompt, 'JavaScript') !== false || stripos($prompt, 'JS') !== false || stripos($prompt, 'Angular') !== false || stripos($prompt, 'Vue') !== false) {
                if ($isExpert) {
                    return "How do you optimize React rendering performance to avoid unnecessary re-renders in deep component trees?";
                }
                if ($isHard) {
                    return "What is code splitting and lazy loading in React, and how do they improve the Initial Page Load time?";
                }
                if ($isMedium) {
                    return "What is the difference between Context API and Redux for state management, and when would you choose one over the other?";
                }
                return "What is the difference between virtual DOM and real DOM in React?";
            }

            if (stripos($prompt, 'Java') !== false || stripos($prompt, 'Spring') !== false) {
                if ($isExpert) {
                    return "How do you resolve memory leaks or thread contention issues in a high-concurrency Spring Boot application?";
                }
                if ($isHard) {
                    return "How do you configure database transaction management using @Transactional in Spring Boot, and what are propagation levels?";
                }
                if ($isMedium) {
                    return "Can you explain Dependency Injection and how Spring's ApplicationContext resolves bean dependencies?";
                }
                return "What is the difference between an Abstract Class and an Interface in Java?";
            }

            if (stripos($prompt, 'Node') !== false || stripos($prompt, 'Express') !== false) {
                if ($isExpert) {
                    return "How would you design a secure, distributed session management system using Node.js and Redis?";
                }
                if ($isHard) {
                    return "What are streams in Node.js, and how do you use them to read or write large files efficiently?";
                }
                if ($isMedium) {
                    return "What is the role of the Event Loop in Node.js, and how does it execute callbacks?";
                }
                return "What is Node.js, and how does its non-blocking event-driven architecture work?";
            }

            if (stripos($prompt, 'MySQL') !== false || stripos($prompt, 'PostgreSQL') !== false || stripos($prompt, 'SQL') !== false) {
                if ($isExpert) {
                    return "How would you design a database sharding and replication strategy for a write-heavy application?";
                }
                if ($isHard) {
                    return "How do you identify and optimize a slow query using EXPLAIN in PostgreSQL or MySQL?";
                }
                if ($isMedium) {
                    return "What are database indexes, and how do they speed up search queries?";
                }
                return "What is the difference between INNER JOIN and LEFT JOIN in SQL?";
            }

            if (stripos($prompt, 'DevOps') !== false || stripos($prompt, 'Docker') !== false || stripos($prompt, 'AWS') !== false || stripos($prompt, 'Kubernetes') !== false) {
                if ($isExpert) {
                    return "How would you design a highly available, multi-region disaster recovery architecture on AWS?";
                }
                if ($isHard) {
                    return "What is Infrastructure as Code (IaC), and how do you manage infrastructure state using Terraform?";
                }
                if ($isMedium) {
                    return "How does a Kubernetes Pod differ from a Docker Container?";
                }
                return "What is Docker, and what is the difference between an Image and a Container?";
            }

            // Default / Laravel
            if ($isExpert) {
                return "How do you design a Repository Pattern or handle Service Classes in Laravel for large-scale applications?";
            }
            if ($isHard) {
                return "What is dependency injection and how does the Service Container resolve bindings in Laravel?";
            }
            if ($isMedium) {
                return "Can you explain the lifecycle of a request in a Laravel application from entry to response?";
            }

            // Default / Fresher
            $defaultTechQuestions = [
                "What is the difference between Laravel seeders and factories, and when would you use each?",
                "Can you explain the lifecycle of a request in a Laravel application from entry to response?",
                "What is dependency injection and how does the Service Container resolve bindings in Laravel?",
                "How do you design a Repository Pattern or handle Service Classes in Laravel for large-scale applications?",
                "How does middleware work in Laravel, and how do you register it?"
            ];
            preg_match_all('/"question"/', $prompt, $matches);
            $count = count($matches[0] ?? []);
            return $defaultTechQuestions[$count % count($defaultTechQuestions)];
        }

        // Default HR
        preg_match_all('/"question"/', $prompt, $matches);
        $count = count($matches[0] ?? []);

        $fresherHR = [
            "Tell me about yourself.",
            "Walk me through your resume.",
            "Why do you want to work in the IT industry?",
            "Why are you interested in this company?",
            "Why should we hire you?",
            "What are your strengths?",
            "What is your biggest weakness?",
            "What makes you different from other candidates?",
            "Where do you see yourself in 5 years?",
            "What are your long-term career goals?"
        ];

        $experiencedHR = [
            "Walk me through your professional journey.",
            "What has been your biggest achievement so far?",
            "What projects are you currently working on?",
            "Describe your role in your current project.",
            "What technologies do you use daily.",
            "What challenges have you faced in your current role?",
            "Why do you want to leave your current company?",
            "What new skills have you learned recently?"
        ];

        if (stripos($prompt, 'Experienced') !== false || stripos($prompt, 'Experience') !== false) {
            $index = $count % count($experiencedHR);
            return $experiencedHR[$index];
        }

        // Default to Fresher HR
        $index = $count % count($fresherHR);
        return $fresherHR[$index];
    }
}

