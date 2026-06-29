                            'Could mention handling failed jobs or supervisor configurations'
                        ],
                        'suggestions' => [
                            'State the commands used to start background workers.',
                            'Explain how Supervisor keeps `queue:work` process running in production.'
                        ],
                        'improved_answer' => $improved,
                        'grammar_feedback' => 'Highly technical explanation.',
                        'voice_analysis' => 'Speaking speed: 123 WPM. Filler words: None. Voice clarity: 92%.',
                        'camera_analysis' => 'Eye contact: 87%. Smiling: Yes. Posture: Steady.'
                    ]);
                }

                // 14. How to optimize Laravel performance?
                if (stripos($prompt, 'optimize') !== false && (stripos($prompt, 'performance') !== false || stripos($prompt, 'Laravel') !== false)) {
                    $improved = "Laravel applications can be optimized by caching configurations (`config:cache`, `route:cache`), using eager loading (`with()`) to prevent N+1 queries, indexing database columns, offloading heavy tasks to background queues, utilizing Redis for caching, and running Laravel Octane.";
                    if ($lang === 'gu') {
                        $improved = "àª²àª¾àª°àª¾àªµà«‡àª² àª�àªªà«�àª²àª¿àª•à«‡àª¶àª¨ àªªàª°àª«à«‹àª°à«�àª®àª¨à«�àª¸ àª“àªªà«�àªŸàª¿àª®àª¾àª‡àª� àª•àª°àªµàª¾ àª®àª¾àªŸà«‡ àª•à«‹àª¨à«�àª«àª¿àª—/àª°àª¾àª‰àªŸ àª•à«‡àª¶à«€àª‚àª—, N+1 àª•à«�àªµà«‡àª°à«€àª� àª…àªŸàª•àª¾àªµàªµàª¾ àª‡àª—àª° àª²à«‹àª¡àª¿àª‚àª— (`with()`), àª¡à«‡àªŸàª¾àª¬à«‡àª� àª‡àª¨à«�àª¡à«‡àª•à«�àª¸àª¿àª‚àª—, àª¬à«‡àª•àª—à«�àª°àª¾àª‰àª¨à«�àª¡ àª•à«�àª¯à«�àª�, àª…àª¨à«‡ àª²àª¾àª°àª¾àªµà«‡àª² àª“àª•à«�àªŸà«‡àª¨àª¨à«‹ àª‰àªªàª¯à«‹àª— àª•àª°àªµà«‹ àªœà«‹àªˆàª�.";
                    } elseif ($lang === 'hi') {
                        $improved = "à¤²à¤¾à¤°à¤µà¥‡à¤² à¤�à¤ªà¥�à¤²à¤¿à¤•à¥‡à¤¶à¤¨ à¤•à¤¾ à¤ªà¤°à¤«à¥‰à¤°à¥�à¤®à¥‡à¤‚à¤¸ à¤‘à¤ªà¥�à¤Ÿà¤¿à¤®à¤¾à¤‡à¤œà¤¼ à¤•à¤°à¤¨à¥‡ à¤•à¥‡ à¤²à¤¿à¤� à¤•à¥‰à¤¨à¥�à¤«à¤¼à¤¿à¤—/à¤°à¥‚à¤Ÿ à¤•à¥ˆà¤¶à¤¿à¤‚à¤—, N+1 à¤•à¥�à¤µà¥‡à¤°à¥€ à¤¸à¥‡ à¤¬à¤šà¤¨à¥‡ à¤•à¥‡ à¤²à¤¿à¤� à¤ˆà¤—à¤° à¤²à¥‹à¤¡à¤¿à¤‚à¤— (`with()`), à¤¡à¥‡à¤Ÿà¤¾à¤¬à¥‡à¤¸ à¤‡à¤‚à¤¡à¥‡à¤•à¥�à¤¸à¤¿à¤‚à¤—, à¤¬à¥ˆà¤•à¤—à¥�à¤°à¤¾à¤‰à¤‚à¤¡ à¤•à¥�à¤¯à¥‚ à¤”à¤° à¤²à¤¾à¤°à¤µà¥‡à¤² à¤‘à¤•à¥�à¤Ÿà¥‡à¤¨ à¤•à¤¾ à¤‰à¤ªà¤¯à¥‹à¤— à¤•à¤¿à¤¯à¤¾ à¤œà¤¾à¤¤à¤¾ à¤¹à¥ˆà¥¤";
                    }

                    return json_encode([
                        'score' => 89,
                        'communication_score' => 90,
                        'confidence_score' => 88,
                        'positive_points' => [
                            'Mentioned caching strategies (config, routes, views)',
                            'Highlighted query optimizations (eager loading to solve N+1 problem, indexing)'
                        ],
                        'missing_points' => [
                            'Did not mention PHP OPcache configurations',
                            'Could mention high-performance servers like Laravel Octane (using Swoole/RoadRunner)'
                        ],
                        'suggestions' => [
                            'Mention Laravel Octane for high concurrency applications.',
                            'Explain N+1 problem and how lazy vs eager loading works.'
                        ],
                        'improved_answer' => $improved,
                        'grammar_feedback' => 'Structured points, easy to read.',
                        'voice_analysis' => 'Speaking speed: 126 WPM. Filler words: None. Voice clarity: 90%.',
                        'camera_analysis' => 'Eye contact: 85%. Smiling: Friendly. Posture: Solid.'
                    ]);
                }

                // 15. Explain Redis usage
                if (stripos($prompt, 'Redis') !== false) {
                    $improved = "Redis is an open-source, in-memory key-value data structure store used as a database, cache, and message broker. In Laravel, it is commonly configured for ultra-fast session storage, application caching, and as a backend driver for high-performance queue processing.";
                    if ($lang === 'gu') {
                        $improved = "Redis àª� àª‡àª¨-àª®à«‡àª®àª°à«€ àª•à«€-àªµà«‡àª²à«�àª¯à«� àª¡à«‡àªŸàª¾ àª¸à«�àªŸà«‹àª° àª›à«‡ àªœà«                }à¤°à¤¾ à¤¸à¤°à¥�à¤µà¤¿à¤¸ à¤•à¤‚à¤Ÿà¥‡à¤¨à¤° à¤‡à¤¸à¥‡ à¤¸à¥�à¤µà¤šà¤¾à¤²à¤¿à¤¤ à¤°à¥‚à¤ª à¤¸à¥‡ à¤¹à¤² à¤•à¤°à¤¤à¤¾ à¤¹à¥ˆà¥¤";
                    }

                    return json_encode([
                        'score' => 90,
                        'communication_score' => 91,
                        'confidence_score' => 88,
                        'positive_points' => [
                            'Defined Dependency Injection as passing dependencies from outside rather than manual creation',
                            'Identified constructor injection and the role of Service Container automatic resolution'
                        ],
                        'missing_points' => [
                            'Could mention interface bindings promoting loose coupling',
                            'Could state benefits in unit testing (mocking dependencies easily)'
                        ],
                        'suggestions' => [
                            'Explain how dependency injection improves unit testability.',
                            'Show difference between manual class instantiation and container resolution.'
                        ],
                        'improved_answer' => $improved,
                        'grammar_feedback' => 'Very strong architectural comprehension.',
                        'voice_analysis' => 'Speaking speed: 125 WPM. Filler words: None. Voice clarity: 94%.',
                        'camera_analysis' => 'Eye contact: 89%. Smiling: Warm. Posture: Steady.'
                    ]);
                }

                // 17. Explain Design Patterns used in Laravel
                if (stripos($prompt, 'Design Patterns') !== false) {
                    $improved = "Laravel utilizes several design patterns: Active Record (Eloquent ORM), MVC (separation of logic/views), Dependency Injection, Facades (provides a static interface to classes in container), Service Providers (Factory pattern for bootstrapping services), and Chain of Responsibility (Middleware).";
                    if ($lang === 'gu') {
                        $improved = "àª²àª¾àª°àª¾àªµà«‡àª² àªµàª¿àªµàª¿àª§ àª¡àª¿àª�àª¾àª‡à�            if (strpos($prompt, 'Applicant Tracking System') !== false || strpos($prompt, 'ATS') !== false || strpos($prompt, 'Resume:') !== false || strpos($prompt, 'ATS scoring') !== false) {
                // Parse facts from the prompt to make the mock response dynamic
                $hasBulletPoints = strpos($prompt, 'Bullet points present: Yes') !== false;
                $hasExperience = strpos($prompt, 'Experience section present: Yes') !== false;
                $hasProjects = strpos($prompt, 'Projects section present: Yes') !== false;
                $hasEducation = strpos($prompt, 'Education section present: Yes') !== false;
                $hasSkills = strpos($prompt, 'Skills section present: Yes') !== false;
                
                // Extract email, phone and name from prompt/resume text
                $email = 'candidate@careerprep.com';
                if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $prompt, $matches)) {
                    $email = $matches[0];
                }
                $phone = '+91 9876543210';
                if (preg_match('/(?:\+?\d{1,3}[- ]?)?\d{10}/', $prompt, $matches)) {
                    $phone = $matches[0];
                }
                $name = 'Jane Doe';
                if ($email !== 'candidate@careerprep.com') {
                    $name = ucwords(str_replace(['.', '_'], ' ', explode('@', $email)[0]));
                }

                // Detect skills
                $detectedSkills = [];
                $potentialSkills = ['PHP', 'Laravel', 'MySQL', 'HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Tailwind', 'Git', 'React', 'Vue', 'Docker', 'AWS', 'TypeScript', 'Python', 'Java', 'Django', 'Spring Boot', 'C++', 'Node.js', 'Express', 'SQL', 'PostgreSQL'];
                foreach ($potentialSkills as $skill) {
                    if (stripos($prompt, $skill) !== false) {
                        // $detectedSkills[] = $skill;
                    }
                }
                if (empty($detectedSkills)) {
                    $detectedSkills = ['PHP', 'Laravel', 'MySQL', 'JavaScript'];
                }

                // Determine target role dynamically
                $detectedRole = 'Backend Developer';
                $roleReason = 'Detected based on backend technologies like PHP and database skills.';
                if (in_array('Python', $detectedSkills) || stripos($prompt, 'Data') !== false) {
                    $detectedRole = 'Python Data Analyst';
                    $roleReason = 'Detected based on Python data analysis stack.';
                } elseif (in_array('React', $detectedSkills) && !in_array('Laravel', $detectedSkills)) {
                    $detectedRole = 'React Frontend Developer';
                    $roleReason = 'Detected based on React frontend skills.';
                } elseif (in_array('Node.js', $detectedSkills) && in_array('React', $detectedSkills)) {
                    $detectedRole = 'MERN Stack Developer';
                    $roleReason = 'Detected based on fullstack JavaScript skills.';
                } elseif (in_array('Docker', $detectedSkills) || in_array('Kubernetes', $detectedSkills) || in_array('AWS', $detectedSkills)) {
                    $detectedRole = 'DevOps Cloud Engineer';
                    $roleReason = 'Detected based on cloud and containerization skills.';
                }

                // Calculate base score
                $score = 82;
                if (!$hasBulletPoints) $score -= 15;
                if (!$hasExperience) $score -= 10;
                if (!$hasProjects) $score -= 10;
                if (!$hasEducation) $score -= 10;
                if (!$hasSkills) $score -= 10;
                $score = max(50, min(100, $score));

                $atsBreakdown = [
                    'structure_score' => $hasExperience && $hasEducation ? 90 : 60,
                    'keywords_score' => count($detectedSkills) * 8 + 40,
                    'skills_score' => count($detectedSkills) * 9 + 30,
                    'experience_score' => $hasExperience ? 85 : 50,
                    'projects_score' => $hasProjects ? 80 : 40,
                    'education_score' => $hasEducation ? 90 : 50,
                    'formatting_score' => $hasBulletPoints ? 85 : 55,
                    'readability_score' => $hasBulletPoints ? 88 : 60
                ];

                // Missing skills based on role
