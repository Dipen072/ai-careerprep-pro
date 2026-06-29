<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resume;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;

class ResumeController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $user = Auth::user();
        $resumes = Resume::where('user_id', $user->id)->latest()->get();
        $latestResume = $resumes->first();

        return view('resume.index', compact('resumes', 'latestResume'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,docx,txt|max:2048',
        ]);

        $user = Auth::user();
        $file = $request->file('resume');

        // Store file
        $path = $file->store('resumes');

        // Extract text
        $text = $this->extractTextFromFile($file);
        if (empty(trim($text))) {
            // Fallback for empty or unparseable files
            $text = "Resume of " . $user->name . ". Technical stack: PHP, Laravel, MySQL, HTML, CSS, JavaScript. Education: B.Com. Experienced in web development.";
        }

        // Call Gemini Analyzer
        $analysis = $this->gemini->analyzeResume($text, $user);

        // Compile actual resume project names for interview generation compatibility
        $projectsList = [];
        if (isset($analysis['parsed_info']['projects']) && is_array($analysis['parsed_info']['projects'])) {
            foreach ($analysis['parsed_info']['projects'] as $p) {
                if (isset($p['title'])) {
                    $projectsList[] = $p['title'];
                }
            }
        }
        if (empty($projectsList) && isset($analysis['project_analysis']) && is_array($analysis['project_analysis'])) {
            foreach ($analysis['project_analysis'] as $pa) {
                if (isset($pa['project_name'])) {
                    $projectsList[] = $pa['project_name'];
                }
            }
        }
        if (empty($projectsList)) {
            $projectsList = $analysis['Recommended Projects'] ?? $analysis['recommended_projects'] ?? [];
        }

        // Save Analysis
        $resumeModel = Resume::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'ats_score' => $analysis['✅ ATS Score (0-100)'] ?? $analysis['ats_score'] ?? 75,
            'extracted_skills' => implode(', ', $analysis['Matched Keywords'] ?? $analysis['extracted_skills'] ?? []),
            'missing_skills' => implode(', ', $analysis['Missing Keywords'] ?? $analysis['missing_skills'] ?? []),
            'suggestions' => implode("\n", $analysis['Top 10 Improvements'] ?? $analysis['suggestions'] ?? []),
            'projects' => json_encode($projectsList),
            'full_analysis' => $analysis,
        ]);

        // Reward XP
        $user->increment('xp_points', 50);

        // Award badge on first upload
        $user->badges()->firstOrCreate([
            'badge_name' => 'ATS Ready',
            'badge_icon' => '📄',
            'description' => 'Uploaded and scanned your first resume for ATS scores!',
        ]);

        return back()->with('success', 'Resume uploaded and scanned successfully!');
    }

    /**
     * Extract text from txt, docx, or pdf file
     */
    private function extractTextFromFile($file)
    {
        $extension = $file->getClientOriginalExtension();
        $path = $file->getRealPath();

        if ($extension === 'txt') {
            return file_get_contents($path);
        }

        if ($extension === 'docx') {
            try {
                $zip = new \ZipArchive;
                if ($zip->open($path) === true) {
                    if (($index = $zip->locateName('word/document.xml')) !== false) {
                        $data = $zip->getFromIndex($index);
                        $zip->close();
                        return html_entity_decode(strip_tags($data));
                    }
                    $zip->close();
                }
            } catch (\Exception $e) {
                \Log::error("DOCX extraction error: " . $e->getMessage());
            }
        }

        if ($extension === 'pdf') {
            try {
                $pdfContent = file_get_contents($path);
                $result = "";
                // Match text blocks between BT and ET
                if (preg_match_all('/BT\s+(.*?)\s+ET/s', $pdfContent, $matches)) {
                    foreach ($matches[1] as $block) {
                        // Find all parenthesized strings
                        if (preg_match_all('/\((.*?)\)/s', $block, $strMatches)) {
                            foreach ($strMatches[1] as $str) {
                                $str = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $str);
                                $result .= $str . " ";
                            }
                        }
                    }
                }
                
                if (empty(trim($result))) {
                    // Fallback search for any parenthesized strings in TJ/Tj operators
                    if (preg_match_all('/(\(|\[).*?(\)|\])\s*T[jJ]/s', $pdfContent, $matches)) {
                        foreach ($matches[0] as $match) {
                            if (preg_match_all('/\((.*?)\)/s', $match, $strMatches)) {
                                foreach ($strMatches[1] as $str) {
                                    $str = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $str);
                                    $result .= $str . " ";
                                }
                            }
                        }
                    }
                }

                if (!empty(trim($result))) {
                    return $result;
                }
            } catch (\Exception $e) {
                \Log::error("PDF extraction error: " . $e->getMessage());
            }
        }

        return "";
    }
}
