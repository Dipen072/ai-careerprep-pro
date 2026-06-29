@extends('layouts.app')

@section('page_title')
    AI Interview Arena — Session #{{ $session->id }}
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative">
    
    <!-- LEFT PANEL: AI Interviewer Avatar & Question Box -->
    <div class="flex flex-col gap-6">
        <!-- Question Box -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 relative">
            <div class="flex items-center justify-between mb-4">
                <span class="px-3 py-1 bg-brandCyan/15 border border-brandCyan/30 text-brandCyan rounded-full text-xs font-bold">
                    Active Question (Q {{ $questionNumber }} of 5)
                </span>
                <span class="text-xs text-gray-400 font-semibold" id="timer">Time elapsed: 00:00</span>
            </div>
            
            <!-- AI Avatar & Bubble -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brandPurple/20 border border-brandPurple/30 text-brandPurple flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-robot text-xl"></i>
                </div>
                <div class="flex-1 bg-white/5 border border-white/10 rounded-2xl p-4">
                    <h4 class="font-bold text-brandPurple text-sm mb-1">AI Interviewer</h4>
                    <p class="text-white text-base leading-relaxed" id="question-text">
                        {{ $currentQuestion->question_text }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Animated Avatar representation -->
        <div class="glassmorphism rounded-3xl p-8 border border-white/10 flex flex-col items-center justify-center relative flex-1 min-h-[300px]">
            <div class="relative w-48 h-48 rounded-full bg-brandPurple/5 border-2 border-brandPurple/20 flex items-center justify-center shadow-2xl">
                <!-- Glowing pulses -->
                <div class="absolute inset-0 rounded-full border border-brandCyan/30 animate-ping opacity-25"></div>
                <div class="absolute inset-4 rounded-full border border-brandPurple/50 animate-pulse opacity-40"></div>
                
                <!-- Avatar image/icon representation -->
                <div class="w-36 h-36 rounded-full bg-gradient-to-tr from-brandCyan/20 to-brandPurple/20 border border-white/10 flex items-center justify-center text-8xl shadow-inner select-none" id="avatar-container">
                    👩‍💼
                </div>
            </div>
            <h3 class="font-bold text-lg text-white mt-6">HR Coach — Priya</h3>
            <p class="text-xs text-gray-400 mt-1">Listening and analyzing speech patterns...</p>
        </div>
    </div>

    <!-- RIGHT PANEL: User Webcam & Speech Input -->
    <div class="flex flex-col gap-6">
        <!-- Camera Preview Screen -->
        <div class="glassmorphism rounded-3xl overflow-hidden border border-white/10 bg-black relative min-h-[300px] flex items-center justify-center">
            <video id="webcam" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover opacity-75"></video>
            
            <!-- Safe Overlay Frame -->
            <div class="absolute inset-6 border border-dashed border-white/20 rounded-2xl pointer-events-none flex flex-col justify-between p-4">
                <div class="flex justify-between items-start">
                    <span class="px-2 py-1 bg-black/60 rounded-lg text-[10px] font-bold text-red-500 uppercase tracking-widest flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> Live
                    </span>
                    <span class="px-2 py-1 bg-black/60 rounded-lg text-[10px] font-semibold text-gray-300">
                        Camera Stream
                    </span>
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-[10px] text-gray-400 font-semibold" id="voice-detection-status">Mic active</span>
                    <span class="text-[10px] text-gray-400 font-semibold">1080p 30fps</span>
                </div>
            </div>
            
            <!-- Camera Disabled Placeholder -->
            <div class="z-10 text-center space-y-3 p-4" id="camera-placeholder">
                <div class="w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-gray-400 mx-auto">
                    <i class="fa-solid fa-video-slash text-2xl"></i>
                </div>
                <button onclick="startCamera()" class="px-4 py-2 bg-brandCyan/20 hover:bg-brandCyan/30 border border-brandCyan/30 text-brandCyan text-xs font-bold rounded-xl transition-all">
                    Enable Webcam Access
                </button>
            </div>
        </div>

        <!-- User Answer Input Arena -->
        <div class="glassmorphism rounded-3xl p-6 border border-white/10 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold">Your Response</span>
                <div class="flex gap-2">
                    <button type="button" onclick="triggerSpeechRecognition()" id="mic-btn" class="px-3.5 py-1.5 bg-brandCyan/10 hover:bg-brandCyan/20 border border-brandCyan/30 text-brandCyan text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-microphone"></i> <span id="mic-btn-text">Speak response</span>
                    </button>
                    <button type="button" onclick="getAIHint()" class="px-3.5 py-1.5 bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 text-xs font-bold rounded-xl transition-all">
                        Get AI Hint
                    </button>
                </div>
            </div>

            <!-- Answer input -->
            <form id="answerForm" class="space-y-4">
                @csrf
                <textarea id="answer-text" name="user_answer" rows="4" required class="block w-full p-4 bg-white/5 border border-white/10 rounded-2xl focus:outline-none focus:border-brandCyan text-white text-sm transition-colors" placeholder="Speak or type your response here..."></textarea>
                
                <button type="submit" id="submit-btn" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-2xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center gap-2">
                    Submit Response <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- AI COACHING MODAL / DRAWER (Revealed on submission) -->
<div id="coaching-modal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 hidden">
    <div class="glassmorphism w-full max-w-2xl rounded-3xl border border-white/10 p-6 md:p-8 space-y-6 max-h-[90vh] overflow-y-auto relative">
        <div class="flex justify-between items-start">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-brandPurple/20 text-brandPurple border border-brandPurple/30 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold">AI Response Evaluation</h3>
                    <p class="text-xs text-gray-400">Priya's immediate feedback & analysis</p>
                </div>
            </div>
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-brandCyan to-brandPurple p-0.5 shadow-lg">
                <div class="w-full h-full bg-darkBg rounded-full flex items-center justify-center font-extrabold text-white text-lg" id="modal-score">
                    65%
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Positive -->
            <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-2xl p-4">
                <h4 class="font-bold text-emerald-400 text-sm mb-2 flex items-center gap-1.5">
                    <i class="fa-regular fa-circle-check"></i> Positive Points
                </h4>
                <ul class="text-xs text-gray-300 space-y-1.5 list-disc pl-4" id="modal-positive"></ul>
            </div>
            
            <!-- Missing -->
            <div class="bg-red-500/5 border border-red-500/10 rounded-2xl p-4">
                <h4 class="font-bold text-red-400 text-sm mb-2 flex items-center gap-1.5">
                    <i class="fa-regular fa-circle-xmark"></i> Missing Points
                </h4>
                <ul class="text-xs text-gray-300 space-y-1.5 list-disc pl-4" id="modal-missing"></ul>
            </div>
        </div>

        <!-- suggestions -->
        <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
            <h4 class="font-bold text-brandCyan text-sm mb-2 flex items-center justify-between">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-lightbulb"></i> Priya's Suggestions</span>
                <button onclick="speakSuggestions()" class="text-xs text-brandCyan hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-volume-high"></i> Listen Suggestions
                </button>
            </h4>
            <ul class="text-xs text-gray-300 space-y-1.5 list-disc pl-4" id="modal-suggestions"></ul>
        </div>

        <!-- AI Improved Generator -->
        <div class="bg-gradient-to-tr from-brandCyan/5 to-brandPurple/5 border border-white/10 rounded-2xl p-4">
            <h4 class="font-bold text-white text-sm mb-2 flex items-center justify-between">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Improved Answer Generator</span>
                <div class="flex items-center gap-3">
                    <button onclick="speakImprovedAnswer()" class="text-xs text-brandCyan hover:underline flex items-center gap-1">
                        <i class="fa-solid fa-volume-high"></i> Listen Answer
                    </button>
                    <span class="text-gray-500">|</span>
                    <button onclick="copyImprovedAnswer()" class="text-xs text-brandCyan hover:underline">Copy Answer</button>
                </div>
            </h4>
            <p class="text-xs text-gray-300 leading-relaxed" id="modal-improved"></p>
        </div>

        <button onclick="closeCoachingModal()" class="w-full py-3 bg-brandPurple hover:bg-brandPurple/90 text-white font-bold rounded-2xl transition-colors flex justify-center items-center gap-1">
            Proceed to Next Question <i class="fa-solid fa-circle-play text-sm"></i>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Timer counters
    let elapsedSeconds = 0;
    setInterval(() => {
        elapsedSeconds++;
        const mins = String(Math.floor(elapsedSeconds / 60)).padStart(2, '0');
        const secs = String(elapsedSeconds % 60).padStart(2, '0');
        document.getElementById('timer').innerText = `Time elapsed: ${mins}:${secs}`;
    }, 1000);

    // Speak initial question using Web Speech API synthesis
    document.addEventListener("DOMContentLoaded", function() {
        const textToSpeak = "{{ $currentQuestion->question_text }}";
        speak(textToSpeak);
        startCamera(); // try auto load camera
    });

    function speak(text) {
        if ('speechSynthesis' in window) {
            // Cancel any current speaking
            window.speechSynthesis.cancel();
            
            const utterance = new SpeechSynthesisUtterance(text);
            
            // Auto match voice language preference
            const prefLang = "{{ $session->language }}";
            if (prefLang.startsWith('hi')) {
                utterance.lang = 'hi-IN';
            } else if (prefLang.startsWith('gu')) {
                utterance.lang = 'gu-IN'; // If not supported, falls back
            } else {
                utterance.lang = 'en-US';
            }
            
            window.speechSynthesis.speak(utterance);
        }
    }

    // Speech-to-text recognition
    let recognition;
    let isRecognizing = false;

    function triggerSpeechRecognition() {
        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            alert("Speech recognition is not supported in this browser. Please type your response.");
            return;
        }

        const SpeechRec = window.SpeechRecognition || window.webkitSpeechRecognition;
        
        if (!recognition) {
            recognition = new SpeechRec();
            recognition.continuous = true;
            recognition.interimResults = true;
            
            const prefLang = "{{ $session->language }}";
            if (prefLang.startsWith('hi')) {
                recognition.lang = 'hi-IN';
            } else if (prefLang.startsWith('gu')) {
                recognition.lang = 'gu-IN';
            } else {
                recognition.lang = 'en-US';
            }

            recognition.onstart = function() {
                isRecognizing = true;
                document.getElementById('mic-btn-text').innerText = 'Listening...';
                document.getElementById('mic-btn').classList.add('bg-red-500/20', 'border-red-500/30', 'text-red-400');
            };

            recognition.onend = function() {
                isRecognizing = false;
                document.getElementById('mic-btn-text').innerText = 'Speak response';
                document.getElementById('mic-btn').classList.remove('bg-red-500/20', 'border-red-500/30', 'text-red-400');
            };

            recognition.onresult = function(event) {
                let interimTranscript = '';
                let finalTranscript = '';
                
                for (let i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        finalTranscript += event.results[i][0].transcript;
                    } else {
                        interimTranscript += event.results[i][0].transcript;
                    }
                }
                
                const textArea = document.getElementById('answer-text');
                if (finalTranscript) {
                    textArea.value += (textArea.value ? ' ' : '') + finalTranscript;
                }
            };
        }

        if (isRecognizing) {
            recognition.stop();
        } else {
            recognition.start();
        }
    }

    // HTML5 camera capture
    function startCamera() {
        const video = document.getElementById('webcam');
        const placeholder = document.getElementById('camera-placeholder');
        
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(stream => {
                video.srcObject = stream;
                placeholder.classList.add('hidden');
                video.classList.remove('opacity-0');
            })
            .catch(err => {
                console.error("Camera access blocked: ", err);
            });
    }

    function getAIHint() {
        const hintBtn = document.querySelector('button[onclick="getAIHint()"]');
        const originalText = hintBtn.innerText;
        hintBtn.setAttribute('disabled', 'true');
        hintBtn.innerText = 'Fetching Hint...';

        fetch("{{ route('interviews.hint', $currentQuestion->id) }}")
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Priya's Hint: " + data.hint);
            } else {
                alert("Could not load hint.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Error fetching hint.");
        })
        .finally(() => {
            hintBtn.removeAttribute('disabled');
            hintBtn.innerText = originalText;
        });
    }

    // AJAX Form submit for answer
    document.getElementById('answerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isRecognizing) {
            recognition.stop();
        }

        const submitBtn = document.getElementById('submit-btn');
        submitBtn.setAttribute('disabled', 'true');
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Analyzing...';

        const formData = new FormData(this);
        
        fetch("{{ route('interviews.answer', $currentQuestion->id) }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Populate modal
                document.getElementById('modal-score').innerText = `${data.eval.score}%`;
                
                const posList = document.getElementById('modal-positive');
                posList.innerHTML = '';
                data.eval.positive_points.forEach(p => {
                    posList.innerHTML += `<li>${p}</li>`;
                });

                const missList = document.getElementById('modal-missing');
                missList.innerHTML = '';
                data.eval.missing_points.forEach(p => {
                    missList.innerHTML += `<li>${p}</li>`;
                });

                const sugList = document.getElementById('modal-suggestions');
                sugList.innerHTML = '';
                data.eval.suggestions.forEach(p => {
                    sugList.innerHTML += `<li>${p}</li>`;
                });

                document.getElementById('modal-improved').innerText = data.eval.improved_answer;

                // Show modal
                document.getElementById('coaching-modal').classList.remove('hidden');
            }
        })
        .catch(err => {
            console.error(err);
            alert("Errors evaluating answer.");
            submitBtn.removeAttribute('disabled');
            submitBtn.innerHTML = 'Submit Response <i class="fa-solid fa-arrow-right"></i>';
        });
    });

    function closeCoachingModal() {
        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
        window.location.reload();
    }

    function copyImprovedAnswer() {
        const text = document.getElementById('modal-improved').innerText;
        navigator.clipboard.writeText(text);
        alert("Copied to clipboard!");
    }

    function speakSuggestions() {
        const listItems = document.querySelectorAll('#modal-suggestions li');
        let text = "Here are my suggestions for your answer: ";
        listItems.forEach((li, index) => {
            text += li.innerText + ". ";
        });
        speak(text);
    }

    function speakImprovedAnswer() {
        const text = document.getElementById('modal-improved').innerText;
        speak(text);
    }
</script>
@endsection
