<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // High-quality MCQ Question Bank
    protected $questionsBank = [
        'Laravel' => [
            'Beginner' => [
                [
                    'id' => 1,
                    'question' => 'Which architectural pattern does Laravel follow?',
                    'options' => ['A' => 'MVVM', 'B' => 'MVC', 'C' => 'Singleton', 'D' => 'Observer'],
                    'correct' => 'B',
                    'explanation' => 'Laravel follows the Model-View-Controller (MVC) architectural pattern.'
                ],
                [
                    'id' => 2,
                    'question' => 'What is the command-line interface helper used in Laravel?',
                    'options' => ['A' => 'Composer', 'B' => 'Artisan', 'C' => 'NPM', 'D' => 'Vite'],
                    'correct' => 'B',
                    'explanation' => 'Artisan is the command-line interface helper included with Laravel.'
                ],
                [
                    'id' => 3,
                    'question' => 'Which folder in Laravel contains routes definition?',
                    'options' => ['A' => 'config', 'B' => 'app/Http', 'C' => 'routes', 'D' => 'resources'],
                    'correct' => 'C',
                    'explanation' => 'All routes are defined in files within the routes directory.'
                ]
            ],
            'Intermediate' => [
                [
                    'id' => 4,
                    'question' => 'Which of the following is the correct way to define a relationship in Laravel Eloquent?',
                    'options' => [
                        'A' => '$this->hasMany(Post::class)', 
                        'B' => '$this->belongsTo(Post::class)', 
                        'C' => '$this->hasOne(Post::class)', 
                        'D' => 'All of the above'
                    ],
                    'correct' => 'D',
                    'explanation' => 'Eloquent ORM supports hasMany, belongsTo, hasOne and other relationship bindings.'
                ],
                [
                    'id' => 5,
                    'question' => 'What is the purpose of database migrations in Laravel?',
                    'options' => [
                        'A' => 'To backup database data', 
                        'B' => 'To compile CSS & JS files', 
                        'C' => 'To define and share application database schemas', 
                        'D' => 'To run unit tests'
                    ],
                    'correct' => 'C',
                    'explanation' => 'Migrations are like version control for your database, allowing teams to modify schema easily.'
                ]
            ]
        ],
        'PHP' => [
            'Beginner' => [
                [
                    'id' => 6,
                    'question' => 'What does PHP stand for?',
                    'options' => [
                        'A' => 'Private Home Page', 
                        'B' => 'Hypertext Preprocessor', 
                        'C' => 'Personal Hypertext Processor', 
                        'D' => 'Preprocessed Home Page'
                    ],
                    'correct' => 'B',
                    'explanation' => 'PHP stands for PHP: Hypertext Preprocessor.'
                ],
                [
                    'id' => 7,
                    'question' => 'Which character is used to start a variable in PHP?',
                    'options' => ['A' => '&', 'B' => '#', 'C' => '$', 'D' => '!'],
                    'correct' => 'C',
                    'explanation' => 'All variables in PHP start with a dollar sign ($).'
                ]
            ]
        ],
        'Databases' => [
            'Beginner' => [
                [
                    'id' => 8,
                    'question' => 'Which SQL keyword is used to retrieve data from a database?',
                    'options' => ['A' => 'GET', 'B' => 'SELECT', 'C' => 'FETCH', 'D' => 'EXTRACT'],
                    'correct' => 'B',
                    'explanation' => 'The SELECT statement is used to select data from a database.'
                ],
                [
                    'id' => 9,
                    'question' => 'Which of the following is NOT a relational database system?',
                    'options' => ['A' => 'MySQL', 'B' => 'PostgreSQL', 'C' => 'MongoDB', 'D' => 'SQL Server'],
                    'correct' => 'C',
                    'explanation' => 'MongoDB is a NoSQL document-based database system, not relational.'
                ]
            ]
        ]
    ];

    public function index()
    {
        // Get leaderboard (users with highest XP)
        $leaderboard = User::orderBy('xp_points', 'desc')->take(5)->get();
        
        $userAttempts = QuizAttempt::where('user_id', Auth::id())->latest()->get();
        
        return view('quiz.index', compact('leaderboard', 'userAttempts'));
    }

    public function take($topic, $difficulty)
    {
        // Pull questions
        $questions = $this->questionsBank[$topic][$difficulty] ?? null;
        
        if (!$questions) {
            // Fallback default questions if not found
            $questions = [
                [
                    'id' => 99,
                    'question' => "What is the primary function of a database index in {$topic}?",
                    'options' => ['A' => 'To encrypt records', 'B' => 'To speed up data retrieval queries', 'C' => 'To enforce unique emails', 'D' => 'To write logs'],
                    'correct' => 'B',
                    'explanation' => 'Indexing improves SELECT query speeds at the expense of extra disk writes.'
                ]
            ];
        }

        return view('quiz.take', compact('topic', 'difficulty', 'questions'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'difficulty' => 'required|string',
            'answers' => 'required|array',
            'time_spent' => 'required|integer',
        ]);

        $topic = $request->topic;
        $difficulty = $request->difficulty;
        $userAnswers = $request->answers; // [question_id => selected_option]

        // Grade the quiz
        $totalQuestions = 0;
        $correctAnswers = 0;
        $score = 0;

        // Fetch questions for this topic and difficulty
        $questionsList = $this->questionsBank[$topic][$difficulty] ?? [
            [
                'id' => 99,
                'correct' => 'B'
            ]
        ];

        $results = [];

        foreach ($questionsList as $q) {
            $totalQuestions++;
            $qId = $q['id'];
            $selected = $userAnswers[$qId] ?? null;
            $isCorrect = ($selected === $q['correct']);

            if ($isCorrect) {
                $correctAnswers++;
                $score += 10; // +10 points for correct
            } else {
                $score -= 2.5; // -2.5 points negative marking!
            }

            $results[] = [
                'question' => $q['question'] ?? 'Question',
                'options' => $q['options'] ?? [],
                'selected' => $selected,
                'correct' => $q['correct'],
                'is_correct' => $isCorrect,
                'explanation' => $q['explanation'] ?? ''
            ];
        }

        // Save Attempt
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'topic' => $topic,
            'difficulty' => $difficulty,
            'score' => max(0, $score),
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'time_spent' => $request->time_spent,
        ]);

        // Reward XP
        $xpEarned = max(10, $score * 2);
        Auth::user()->increment('xp_points', $xpEarned);

        // Check 100% score badge
        if ($correctAnswers === $totalQuestions) {
            Auth::user()->badges()->firstOrCreate([
                'badge_name' => 'Brainiac',
                'badge_icon' => '🧠',
                'description' => "Scored 100% correct in {$topic} ({$difficulty}) Quiz!",
            ]);
        }

        return view('quiz.results', compact('attempt', 'results', 'xpEarned'));
    }
}
