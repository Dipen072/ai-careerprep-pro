@extends('layouts.auth')

@section('content')
<div class="glassmorphism rounded-3xl p-8 relative overflow-hidden transition-all duration-300">
    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between mb-2">
            <span class="text-sm font-semibold text-brandCyan" id="step-label">Step 1 of 3: Experience Level</span>
            <span class="text-sm font-medium text-gray-400" id="step-percentage">33%</span>
        </div>
        <div class="w-full bg-white/10 h-1.5 rounded-full overflow-hidden">
            <div id="progress-indicator" class="bg-gradient-to-r from-brandCyan to-brandPurple h-full w-1/3 transition-all duration-300"></div>
        </div>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-brandCyan/20 text-brandCyan border border-brandCyan/30 mb-3">
            <i class="fa-solid fa-user-astronaut text-2xl"></i>
        </div>
        <h2 class="text-2xl font-bold tracking-tight">Complete Your Profile</h2>
        <p class="text-gray-400 text-sm mt-1">Customize your career settings to enable tailored AI mock interviews</p>
    </div>

    <!-- Form -->
    <form id="onboardingForm" method="POST" action="{{ route('onboarding.post') }}" class="space-y-6">
        @csrf

        <!-- STEP 1: Experience Level -->
        <div id="step-1" class="space-y-5 transition-all duration-300">
            <label class="block text-sm font-semibold text-gray-300 mb-3 text-center">What is your current experience level?</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="relative flex flex-col items-center justify-center p-6 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-all duration-200 group active-scale" id="label-fresher">
                    <input type="radio" name="user_type" value="fresher" checked class="sr-only" onchange="toggleExperienceCard()">
                    <div class="w-12 h-12 rounded-xl bg-brandCyan/20 text-brandCyan flex items-center justify-center mb-3 border border-brandCyan/30 text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="font-bold text-white text-base">Fresher / Graduate</span>
                    <span class="text-xs text-gray-400 text-center mt-1">Starting career or seeking entry roles</span>
                </label>

                <label class="relative flex flex-col items-center justify-center p-6 bg-white/5 border border-white/10 rounded-2xl cursor-pointer hover:bg-white/10 transition-all duration-200 group active-scale" id="label-experienced">
                    <input type="radio" name="user_type" value="experienced" class="sr-only" onchange="toggleExperienceCard()">
                    <div class="w-12 h-12 rounded-xl bg-brandPurple/20 text-brandPurple flex items-center justify-center mb-3 border border-brandPurple/30 text-xl group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <span class="font-bold text-white text-base">Experienced Professional</span>
                    <span class="text-xs text-gray-400 text-center mt-1">Currently working or have industry experience</span>
                </label>
            </div>

            <button type="button" onclick="nextStep(2)" class="w-full py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center gap-2 mt-4">
                Continue <i class="fa-solid fa-arrow-right text-sm"></i>
            </button>
        </div>

        <!-- STEP 2: Career Path -->
        <div id="step-2" class="hidden space-y-5 transition-all duration-300">
            <label class="block text-sm font-semibold text-gray-300 mb-2 text-center">Select your desired Career Path</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto p-1">
                @php
                    $roles = [
                        'Backend Developer' => 'fa-server',
                        'Frontend Developer' => 'fa-laptop-code',
                        'Full Stack Developer' => 'fa-layer-group',
                        'Mobile App Developer' => 'fa-mobile-screen-button',
                        'DevOps Engineer' => 'fa-cloud-arrow-up',
                        'Data Analyst' => 'fa-chart-pie',
                        'QA Engineer' => 'fa-bug',
                        'Cyber Security Engineer' => 'fa-user-shield',
                        'AI/ML Engineer' => 'fa-brain'
                    ];
                @endphp
                @foreach($roles as $role => $icon)
                <label class="flex items-center gap-3 p-3 bg-white/5 border border-white/10 rounded-xl cursor-pointer hover:bg-white/10 transition-colors role-card" id="role-{{ Str::slug($role) }}">
                    <input type="radio" name="career_path" value="{{ $role }}" class="sr-only" onchange="selectRole('{{ $role }}')">
                    <div class="w-8 h-8 rounded-lg bg-white/5 text-gray-300 flex items-center justify-center text-sm border border-white/10 shrink-0">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <span class="text-sm font-semibold text-white">{{ $role }}</span>
                </label>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <button type="button" onclick="prevStep(1)" class="py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-gray-300 transition-colors flex justify-center items-center">
                    <i class="fa-solid fa-arrow-left mr-2 text-sm"></i> Back
                </button>
                <button type="button" id="btn-to-step-3" disabled onclick="nextStep(3)" class="py-3 bg-gradient-to-r from-brandCyan to-brandPurple hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    Next Step <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                </button>
            </div>
        </div>

        <!-- STEP 3: Technologies -->
        <div id="step-3" class="hidden space-y-6 transition-all duration-300">
            <div class="text-center">
                <h3 class="text-base font-semibold text-gray-300 mb-1">Select Technologies</h3>
                <p class="text-xs text-gray-400">Choose one or more technologies you specialize in or want to practice</p>
            </div>

            <div class="flex flex-wrap gap-2 justify-center max-h-60 overflow-y-auto p-1" id="tech-chips-container">
                <!-- Dynamically filled by JavaScript -->
            </div>

            <!-- Hidden inputs container for selected skills -->
            <div id="skills-hidden-container"></div>

            <div class="grid grid-cols-2 gap-4 mt-6">
                <button type="button" onclick="prevStep(2)" class="py-3 bg-white/10 hover:bg-white/15 rounded-xl font-semibold text-gray-300 transition-colors flex justify-center items-center">
                    <i class="fa-solid fa-arrow-left mr-2 text-sm"></i> Back
                </button>
                <button type="submit" id="btn-submit-onboarding" disabled class="py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:opacity-90 rounded-xl font-bold text-white shadow-lg transition-opacity flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-circle-check mr-2 text-sm"></i> Finish Setup
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    let selectedRole = '';
    const selectedTechs = new Set();

    const techStackOptions = {
        'Backend Developer': ['PHP', 'Laravel', 'Java', 'Spring Boot', 'Python', 'Django', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL'],
        'Frontend Developer': ['HTML', 'CSS', 'JavaScript', 'Bootstrap', 'Tailwind CSS', 'React', 'Angular', 'Vue.js'],
        'Full Stack Developer': ['HTML', 'CSS', 'JavaScript', 'React', 'Angular', 'Vue.js', 'PHP', 'Laravel', 'Node.js', 'Express.js', 'MySQL', 'PostgreSQL'],
        'Mobile App Developer': ['Swift', 'Objective-C', 'Java', 'Kotlin', 'Flutter', 'React Native', 'Xamarin'],
        'DevOps Engineer': ['Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP', 'CI/CD', 'Jenkins', 'Terraform', 'Ansible', 'Linux'],
        'Data Analyst': ['Python', 'R', 'SQL', 'PowerBI', 'Tableau', 'Excel', 'Pandas', 'NumPy'],
        'QA Engineer': ['Selenium', 'Cypress', 'JUnit', 'TestNG', 'JIRA', 'Postman', 'Manual Testing', 'Automation Testing'],
        'Cyber Security Engineer': ['Wireshark', 'Nmap', 'Metasploit', 'Cryptography', 'Network Security', 'Penetration Testing', 'OWASP'],
        'AI/ML Engineer': ['Python', 'TensorFlow', 'PyTorch', 'Scikit-Learn', 'Deep Learning', 'Machine Learning', 'NLP', 'Computer Vision']
    };

    function toggleExperienceCard() {
        const fresher = document.getElementById('label-fresher');
        const experienced = document.getElementById('label-experienced');
        const fresherInput = document.querySelector('input[name="user_type"][value="fresher"]');
        
        if (fresherInput.checked) {
            fresher.classList.add('bg-brandCyan/10', 'border-brandCyan');
            experienced.classList.remove('bg-brandPurple/10', 'border-brandPurple');
        } else {
            experienced.classList.add('bg-brandPurple/10', 'border-brandPurple');
            fresher.classList.remove('bg-brandCyan/10', 'border-brandCyan');
        }
    }

    function selectRole(role) {
        selectedRole = role;
        
        // Style highlights
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('bg-brandCyan/15', 'border-brandCyan');
        });
        
        const slug = role.toLowerCase().replace(/ /g, '-').replace(/\//g, '-');
        const selectedCard = document.getElementById(`role-${slug}`);
        if (selectedCard) {
            selectedCard.classList.add('bg-brandCyan/15', 'border-brandCyan');
        }

        // Enable continue button
        document.getElementById('btn-to-step-3').removeAttribute('disabled');
        
        // Reset and rebuild technologies
        selectedTechs.clear();
        document.getElementById('skills-hidden-container').innerHTML = '';
        document.getElementById('btn-submit-onboarding').setAttribute('disabled', 'true');
        
        rebuildTechChips(role);
    }

    function rebuildTechChips(role) {
        const container = document.getElementById('tech-chips-container');
        container.innerHTML = '';
        
        const techs = techStackOptions[role] || [];
        techs.forEach(tech => {
            const chip = document.createElement('div');
            chip.className = 'px-4 py-2 bg-white/5 border border-white/10 hover:border-brandCyan/50 rounded-xl cursor-pointer text-sm font-semibold text-gray-300 transition-all duration-200 select-none active-scale';
            chip.innerText = tech;
            chip.onclick = () => toggleTech(chip, tech);
            container.appendChild(chip);
        });
    }

    function toggleTech(element, tech) {
        if (selectedTechs.has(tech)) {
            selectedTechs.delete(tech);
            element.classList.remove('bg-brandCyan/25', 'border-brandCyan', 'text-brandCyan', 'shadow-md');
            element.classList.add('bg-white/5', 'border-white/10', 'text-gray-300');
        } else {
            selectedTechs.add(tech);
            element.classList.remove('bg-white/5', 'border-white/10', 'text-gray-300');
            element.classList.add('bg-brandCyan/25', 'border-brandCyan', 'text-brandCyan', 'shadow-md');
        }
        
        // Re-compile hidden inputs
        const container = document.getElementById('skills-hidden-container');
        container.innerHTML = '';
        selectedTechs.forEach(t => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'skills[]';
            input.value = t;
            container.appendChild(input);
        });

        // Toggle submit button state
        const submitBtn = document.getElementById('btn-submit-onboarding');
        if (selectedTechs.size >= 1) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'true');
        }
    }

    function nextStep(step) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        const progressIndicator = document.getElementById('progress-indicator');
        const stepLabel = document.getElementById('step-label');
        const stepPercentage = document.getElementById('step-percentage');
        
        if (step === 1) {
            progressIndicator.style.width = '33%';
            stepLabel.innerText = 'Step 1 of 3: Experience Level';
            stepPercentage.innerText = '33%';
        } else if (step === 2) {
            progressIndicator.style.width = '66%';
            stepLabel.innerText = 'Step 2 of 3: Career Path';
            stepPercentage.innerText = '66%';
        } else if (step === 3) {
            progressIndicator.style.width = '100%';
            stepLabel.innerText = 'Step 3 of 3: Technologies';
            stepPercentage.innerText = '100%';
        }
    }

    function prevStep(step) {
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`step-${step}`).classList.remove('hidden');
        currentStep = step;

        const progressIndicator = document.getElementById('progress-indicator');
        const stepLabel = document.getElementById('step-label');
        const stepPercentage = document.getElementById('step-percentage');
        
        if (step === 1) {
            progressIndicator.style.width = '33%';
            stepLabel.innerText = 'Step 1 of 3: Experience Level';
            stepPercentage.innerText = '33%';
        } else if (step === 2) {
            progressIndicator.style.width = '66%';
            stepLabel.innerText = 'Step 2 of 3: Career Path';
            stepPercentage.innerText = '66%';
        }
    }

    document.addEventListener("DOMContentLoaded", toggleExperienceCard);
</script>

<style>
    .active-scale:active {
        transform: scale(0.95);
    }
</style>
@endsection
