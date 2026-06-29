<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewSession;
use App\Models\InterviewQuestion;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;

class InterviewController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $user = Auth::user();
        $sessions = InterviewSession::where('user_id', $user->id)->latest()->get();
        return view('interview.index', compact('sessions'));
    }

    public function setup()
    {
        $user = Auth::user();
        if (empty($user->career_path)) {
            return redirect()->route('onboarding');
        }

        $techStackOptions = [
            'Backend Developer' => ['PHP', 'Laravel', 'Java', 'Spring Boot', 'Python', 'Django', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL'],
            'Frontend Developer' => ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Tailwind CSS', 'React', 'Angular', 'Vue.js'],
            'Full Stack Developer' => ['HTML', 'CSS', 'JavaScript', 'React', 'Angular', 'Vue.js', 'PHP', 'Laravel', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL'],
            'Mobile App Developer' => ['Swift', 'Objective-C', 'Java', 'Kotlin', 'Flutter', 'React Native', 'Xamarin'],
            'DevOps Engineer' => ['Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP', 'CI/CD', 'Jenkins', 'Terraform', 'Ansible', 'Linux'],
            'Data Analyst' => ['Python', 'R', 'SQL', 'PowerBI', 'Tableau', 'Excel', 'Pandas', 'NumPy'],
            'QA Engineer' => ['Selenium', 'Cypress', 'JUnit', 'TestNG', 'JIRA', 'Postman', 'Manual Testing', 'Automation Testing'],
            'Cyber Security Engineer' => ['Wireshark', 'Nmap', 'Metasploit', 'Cryptography', 'Network Security', 'Penetration Testing', 'OWASP'],
            'AI/ML Engineer' => ['Python', 'TensorFlow', 'PyTorch', 'Scikit-Learn', 'Deep Learning', 'Machine Learning', 'NLP', 'Computer Vision']
        ];

        $availableTechs = $techStackOptions[$user->career_path] ?? [];

        return view('interview.setup', compact('user', 'availableTechs'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:HR,Technical',
            'technology' => 'nullable|required_if:type,Technical|array|min:1',
            'difficulty' => 'required|string',
            'language' => 'required|string',
        ]);

        $user = Auth::user();

        $techsString = null;
        if ($request->type === 'Technical') {
            $techsString = implode(', ', $request->technology);
        }

        // Create session
        $session = InterviewSession::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'technology' => $techsString,
            'difficulty' => $request->difficulty,
            'language' => $request->language,
            'status' => 'pending',
            'score' => 0,
        ]);

        // Fetch resume projects if any
        $latestResume = $user->resumes()->latest()->first();
        $projects = $latestResume ? json_decode($latestResume->projects, true) : null;
        $projectsString = is_array($projects) ? implode("\n", $projects) : null;

        // Generate first question
        $questionText = $this->gemini->generateQuestion(
            $request->type,
            $techsString,
            $request->difficulty,
            [],
            $request->language,
            $projectsString
        );

        $session->questions()->create([
            'question_text' => $questionText
        ]);

        return redirect()->route('interviews.arena', $session->id);
    }

    public function arena(InterviewSession $session)
    {
        // Get the current question (the last one created for this session)
        $currentQuestion = $session->questions()->whereNull('user_answer')->first();
        
        // If all questions are answered, complete session
        if (!$currentQuestion) {
            $answeredCount = $session->questions()->whereNotNull('user_answer')->count();
            if ($answeredCount >= 5) {
                // Calculate average scores & call Gemini completion report
                $questions = $session->questions()->whereNotNull('user_answer')->get();
                $report = $this->gemini->generateCompletionReport($session, $questions);
                
                $session->update([
                    'status' => 'completed',
                    'score' => $report['technical_score'] ?? round($session->questions()->avg('ai_score')),
                    'communication_score' => $report['communication_score'] ?? rand(70, 90),
                    'confidence_score' => $report['confidence_score'] ?? rand(75, 95),
                    'strong_areas' => $report['strong_areas'] ?? [],
                    'weak_areas' => $report['weak_areas'] ?? [],
                    'recommended_topics' => $report['recommended_topics'] ?? [],
                ]);

                // Reward XP
                $user = Auth::user();
                $user->increment('xp_points', 150);

                // Check for streak badge
                if ($user->interviews()->count() === 1) {
                    $user->badges()->firstOrCreate([
                        'badge_name' => 'First Blood',
                        'badge_icon' => '🏆',
                        'description' => 'Completed your very first AI mock interview session!',
                    ]);
                }

                return redirect()->route('interviews.report', $session->id);
            } else {
                // Determine adaptive difficulty based on previous question score
                $lastQuestion = $session->questions()->whereNotNull('user_answer')->latest()->first();
                $lastScore = $lastQuestion ? $lastQuestion->ai_score : null;
                
                $dynamicDifficulty = $session->difficulty; // 'Fresher' or 'Experienced'
                if ($lastScore !== null) {
                    if ($lastScore >= 75) {
                        if ($session->difficulty === 'Fresher') {
                            if ($answeredCount === 1) {
                                $dynamicDifficulty = 'Medium / Intermediate';
                            } else {
                                $dynamicDifficulty = 'Hard / Advanced';
                            }
                        } else {
                            if ($answeredCount === 1) {
                                $dynamicDifficulty = 'Hard / Advanced';
                            } else {
                                $dynamicDifficulty = 'Expert / Architectural';
                            }
                        }
                    } elseif ($lastScore < 50) {
                        if ($session->difficulty === 'Experienced') {
                            $dynamicDifficulty = 'Medium / Intermediate';
                        } else {
                            $dynamicDifficulty = 'Fresher';
                        }
                    }
                }

                // Generate next question
                $history = [];
                foreach ($session->questions as $q) {
                    $history[] = [
                        'question' => $q->question_text,
                        'answer' => $q->user_answer
                    ];
                }

                $user = Auth::user();
                $latestResume = $user->resumes()->latest()->first();
                $projects = $latestResume ? json_decode($latestResume->projects, true) : null;
                $projectsString = is_array($projects) ? implode("\n", $projects) : null;

                $questionText = $this->gemini->generateQuestion(
                    $session->type,
                    $session->technology,
                    $dynamicDifficulty,
                    $history,
                    $session->language,
                    $projectsString
                );
                
                $currentQuestion = $session->questions()->create([
                    'question_text' => $questionText
                ]);
            }
        }

        $questionNumber = $session->questions()->count();

        return view('interview.arena', compact('session', 'currentQuestion', 'questionNumber'));
    }

    public function getHint(InterviewQuestion $question)
    {
        $session = $question->session;
        $hint = $this->gemini->generateHint($question->question_text, $session->language);
        return response()->json([
            'success' => true,
            'hint' => $hint
        ]);
    }

    public function submitAnswer(Request $request, InterviewQuestion $question)
    {
        $request->validate([
            'user_answer' => 'required|string',
        ]);

        $session = $question->session;

        // Call AI evaluation
        $eval = $this->gemini->evaluateAnswer(
            $question->question_text,
            $request->user_answer,
            $session->language
        );

        $question->update([
            'user_answer' => $request->user_answer,
            'ai_score' => $eval['score'] ?? 0,
            'ai_feedback_positive' => implode("\n", $eval['positive_points'] ?? []),
            'ai_feedback_missing' => implode("\n", $eval['missing_points'] ?? []),
            'ai_feedback_suggestions' => implode("\n", $eval['suggestions'] ?? []),
            'ai_improved_answer' => $eval['improved_answer'] ?? '',
            'grammar_feedback' => $eval['grammar_feedback'] ?? '',
            'voice_analysis' => $eval['voice_analysis'] ?? '',
            'camera_analysis' => $eval['camera_analysis'] ?? '',
        ]);

        return response()->json([
            'success' => true,
            'eval' => $eval,
            'redirect' => route('interviews.arena', $session->id)
        ]);
    }

    public function report(InterviewSession $session)
    {
        $questions = $session->questions()->whereNotNull('user_answer')->get();
        return view('interview.report', compact('session', 'questions'));
    }
}
