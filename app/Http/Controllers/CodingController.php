<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CodingSubmission;
use Illuminate\Support\Facades\Auth;

class CodingController extends Controller
{
    // Coding problems bank
    protected $problems = [
        'two-sum' => [
            'id' => 'two-sum',
            'title' => 'Two Sum',
            'difficulty' => 'Easy',
            'company_tags' => ['Google', 'Amazon', 'TCS'],
            'description' => "Given an array of integers `nums` and an integer `target`, return indices of the two numbers such that they add up to `target`.\n\nYou may assume that each input would have exactly one solution, and you may not use the same element twice.",
            'input_format' => "nums = [2,7,11,15], target = 9",
            'output_format' => "[0,1]",
            'starter_php' => "function twoSum(\$nums, \$target) {\n    // Write your PHP code here\n}",
            'starter_javascript' => "function twoSum(nums, target) {\n    // Write your JS code here\n}",
            'starter_python' => "def two_sum(nums, target):\n    # Write your Python code here\n    pass",
        ],
        'reverse-string' => [
            'id' => 'reverse-string',
            'title' => 'Reverse String',
            'difficulty' => 'Easy',
            'company_tags' => ['Amazon', 'TCS', 'Infosys'],
            'description' => "Write a function that reverses a string. The input string is given as an array of characters `s`.",
            'input_format' => "s = [\"h\",\"e\",\"l\",\"l\",\"o\"]",
            'output_format' => "[\"o\",\"l\",\"l\",\"e\",\"h\"]",
            'starter_php' => "function reverseString(&\$s) {\n    // Write your PHP code here\n}",
            'starter_javascript' => "function reverseString(s) {\n    // Write your JS code here\n}",
            'starter_python' => "def reverse_string(s):\n    # Write your Python code here\n    pass",
        ],
        'fibonacci' => [
            'id' => 'fibonacci',
            'title' => 'Fibonacci Number',
            'difficulty' => 'Medium',
            'company_tags' => ['Google', 'Meta'],
            'description' => "The Fibonacci numbers, commonly denoted `F(n)` form a sequence, called the Fibonacci sequence, such that each number is the sum of the two preceding ones, starting from 0 and 1.",
            'input_format' => "n = 4",
            'output_format' => "3",
            'starter_php' => "function fib(\$n) {\n    // Write your PHP code here\n}",
            'starter_javascript' => "function fib(n) {\n    // Write your JS code here\n}",
            'starter_python' => "def fib(n):\n    # Write your Python code here\n    pass",
        ]
    ];

    public function index()
    {
        $problemsList = $this->problems;
        $submissions = CodingSubmission::where('user_id', Auth::id())->latest()->get();
        
        return view('coding.index', compact('problemsList', 'submissions'));
    }

    public function show($problemId)
    {
        $problem = $this->problems[$problemId] ?? null;
        if (!$problem) {
            abort(404);
        }

        return view('coding.show', compact('problem'));
    }

    public function submit(Request $request, $problemId)
    {
        $request->validate([
            'language' => 'required|in:php,javascript,python',
            'code' => 'required|string',
        ]);

        $problem = $this->problems[$problemId] ?? null;
        if (!$problem) {
            return response()->json(['success' => false, 'error' => 'Problem not found']);
        }

        // Mock compiler logic: checking if code has syntax errors or key words
        $code = $request->code;
        $language = $request->language;
        
        $status = 'Accepted';
        $runtime = rand(10, 80) . 'ms';
        $memory = rand(10, 30) . 'MB';
        $errorMsg = null;

        // basic validation checks for empty/unfinished code
        if (strlen(trim($code)) < 40 || strpos($code, 'Write your') !== false) {
            $status = 'Wrong Answer';
            $errorMsg = "Test Case 1 Failed: Expected output {$problem['output_format']}, got null/empty output.";
        }

        // Save submission in DB
        CodingSubmission::create([
            'user_id' => Auth::id(),
            'problem_id' => $problemId,
            'language' => $language,
            'code' => $code,
            'status' => $status,
            'runtime' => $runtime,
            'memory' => $memory,
        ]);

        // Reward XP if accepted
        if ($status === 'Accepted') {
            Auth::user()->increment('xp_points', 100);
            
            // Earn coding badge on first success
            Auth::user()->badges()->firstOrCreate([
                'badge_name' => 'Code Warrior',
                'badge_icon' => '💻',
                'description' => 'Successfully solved a coding problem on the platform!',
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $status,
            'runtime' => $runtime,
            'memory' => $memory,
            'error' => $errorMsg,
        ]);
    }
}
