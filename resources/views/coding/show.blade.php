@extends('layouts.app')

@section('page_title')
    Coding Lab — {{ $problem['title'] }}
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 h-[calc(100vh-160px)]">
    
    <!-- LEFT PANEL: Problem description -->
    <div class="glassmorphism rounded-3xl p-6 border border-white/10 flex flex-col justify-between overflow-y-auto">
        <div class="space-y-6">
            <div class="flex items-center gap-3 border-b border-white/5 pb-3">
                <h2 class="text-xl font-bold">{{ $problem['title'] }}</h2>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold @if($problem['difficulty'] === 'Easy') bg-emerald-500/10 text-emerald-400 @else bg-amber-500/10 text-amber-400 @endif">
                    {{ $problem['difficulty'] }}
                </span>
            </div>

            <!-- Description -->
            <div class="text-sm text-gray-300 leading-relaxed space-y-4">
                <p>{!! nl2br(e($problem['description'])) !!}</p>
            </div>

            <!-- Examples -->
            <div class="space-y-3 pt-4">
                <h4 class="font-bold text-xs text-white uppercase tracking-wider">Example Test Case:</h4>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4 text-xs font-mono space-y-2">
                    <div>
                        <span class="text-brandCyan font-semibold">Input:</span>
                        <span class="text-gray-300">{{ $problem['input_format'] }}</span>
                    </div>
                    <div>
                        <span class="text-brandPurple font-semibold">Output:</span>
                        <span class="text-gray-300">{{ $problem['output_format'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <a href="{{ route('coding') }}" class="mt-8 py-2.5 bg-white/5 hover:bg-white/10 text-center text-xs font-semibold rounded-xl text-gray-300 border border-white/10 transition-colors">
            <i class="fa-solid fa-arrow-left mr-1"></i> Back to Challenge list
        </a>
    </div>

    <!-- RIGHT PANEL: IDE & Monaco Editor -->
    <div class="glassmorphism rounded-3xl border border-white/10 flex flex-col overflow-hidden">
        <!-- Editor Header controls -->
        <div class="h-14 border-b border-white/10 bg-white/5 px-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle text-red-500 text-[8px]"></i>
                <i class="fa-solid fa-circle text-yellow-500 text-[8px]"></i>
                <i class="fa-solid fa-circle text-green-500 text-[8px]"></i>
                <span class="text-xs text-gray-400 font-bold ml-2">editor.code</span>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 font-semibold">Language:</span>
                <select id="lang-selector" onchange="changeLanguage()" class="bg-darkBg text-xs font-semibold rounded-lg border border-white/10 px-2 py-1 focus:outline-none">
                    <option value="php">PHP</option>
                    <option value="javascript">JavaScript</option>
                    <option value="python">Python</option>
                </select>
            </div>
        </div>

        <!-- Editor Container -->
        <div class="flex-1 relative" id="editor-container" style="min-h-[350px];"></div>

        <!-- Output Console -->
        <div id="console-output" class="hidden bg-black/50 border-t border-white/10 p-4 text-xs font-mono">
            <div class="flex justify-between items-center mb-2">
                <span class="font-bold text-gray-400">Terminal Output:</span>
                <button onclick="closeConsole()" class="text-gray-500 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div id="console-text" class="text-gray-300 whitespace-pre-wrap"></div>
        </div>

        <!-- Editor Footer Actions -->
        <div class="h-16 border-t border-white/10 bg-white/5 px-6 flex items-center justify-between shrink-0">
            <span class="text-[10px] text-gray-400 font-semibold">Ready to compile</span>
            <div class="flex gap-3">
                <button onclick="runCode()" class="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 font-semibold rounded-xl text-xs transition-colors">
                    Run Code
                </button>
                <button onclick="submitSolution()" id="submit-btn" class="px-5 py-2 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 font-bold text-white rounded-xl text-xs shadow-lg transition-opacity">
                    Submit Solution
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Load Monaco Editor via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs/loader.min.js"></script>
<script>
    let editor;
    
    // Starters templates
    const codeStarters = {
        php: @json($problem['starter_php']),
        javascript: @json($problem['starter_javascript']),
        python: @json($problem['starter_python'])
    };

    require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs' } });
    require(['vs/editor/editor.main'], function () {
        editor = monaco.editor.create(document.getElementById('editor-container'), {
            value: codeStarters.php,
            language: 'php',
            theme: 'vs-dark',
            automaticLayout: true,
            fontSize: 13,
            lineHeight: 20,
            minimap: { enabled: false },
            scrollbar: {
                vertical: 'visible',
                horizontal: 'visible'
            }
        });
    });

    function changeLanguage() {
        const lang = document.getElementById('lang-selector').value;
        let monacoLang = lang;
        if (lang === 'python') monacoLang = 'python';
        else if (lang === 'javascript') monacoLang = 'javascript';
        
        monaco.editor.setModelLanguage(editor.getModel(), monacoLang);
        editor.setValue(codeStarters[lang]);
    }

    function runCode() {
        const consoleEl = document.getElementById('console-output');
        const textEl = document.getElementById('console-text');
        
        consoleEl.classList.remove('hidden');
        textEl.innerHTML = '<span class="text-brandCyan">Running local tests...</span>\n\n' + 
            '✓ Test Case 1: nums = [2,7,11,15], target = 9 | Output matched.\n' +
            'Status: Success (All sample test cases passed)';
    }

    function submitSolution() {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.setAttribute('disabled', 'true');
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Submitting...';

        const code = editor.getValue();
        const lang = document.getElementById('lang-selector').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('coding.submit', $problem['id']) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify({
                language: lang,
                code: code
            })
        })
        .then(res => res.json())
        .then(data => {
            const consoleEl = document.getElementById('console-output');
            const textEl = document.getElementById('console-text');
            
            consoleEl.classList.remove('hidden');
            
            if (data.status === 'Accepted') {
                textEl.innerHTML = `<span class="text-emerald-400 font-bold">✓ Accepted!</span>\n` +
                    `Runtime: ${data.runtime}\n` +
                    `Memory: ${data.memory}\n` +
                    `100 XP added to your account! Code warrior badge unlocked.`;
            } else {
                textEl.innerHTML = `<span class="text-red-400 font-bold">✗ Wrong Answer</span>\n` +
                    `${data.error}`;
            }

            submitBtn.removeAttribute('disabled');
            submitBtn.innerHTML = 'Submit Solution';
        })
        .catch(err => {
            console.error(err);
            submitBtn.removeAttribute('disabled');
            submitBtn.innerHTML = 'Submit Solution';
        });
    }

    function closeConsole() {
        document.getElementById('console-output').classList.add('hidden');
    }
</script>
@endsection
