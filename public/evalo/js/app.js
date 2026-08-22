/**
 * Evalo - Frontend Application Logic
 * No backend - Data is static/mocked
 */

// --- Mock Data ---

const MOCK_DATA = {
    plans: [
        { id: 'free', name: 'Starting', price: '0', features: ['Basic Evaluation', '1 Department', 'Email Support'] },
        { id: 'pro', name: 'Professional', price: '49', features: ['AI Insights', '5 Departments', 'Priority Support', 'Full Analytics'] },
        { id: 'enterprise', name: 'Enterprise', price: '199', features: ['Unlimited Everything', 'Custom AI Training', 'Dedicated Manager', 'API Access'] }
    ],
    employees: [
        { id: 1, name: 'Ahmed Ali', role: 'employee', dept: 'IT', evaluation: 85, report: 'Outstanding performance in frontend tasks.' },
        { id: 2, name: 'Sara Kamel', role: 'employee', dept: 'HR', evaluation: 72, report: 'Good communication skills, needs improvement in technical documentation.' },
        { id: 3, name: 'Mohamed Hassan', role: 'manager', dept: 'IT', evaluation: 90, report: 'Excellent leadership in the IT department.' },
        { id: 4, name: 'Mona Zaki', role: 'hr', dept: 'HR', evaluation: 88, report: 'Great talent acquisition results this quarter.' },
        { id: 5, name: 'Khaled Omar', role: 'gm', dept: 'Management', evaluation: 95, report: 'Strategic vision is driving company growth.' }
    ],
    roles: {
        employee: { name: 'Employee', permissions: ['view_profile', 'view_report', 'chat_ai'] },
        manager: { name: 'Department Manager', permissions: ['view_profile', 'view_report', 'view_dept_employees', 'chat_ai'] },
        hr: { name: 'HR specialist', permissions: ['view_profile', 'view_all_employees', 'evaluate_employees', 'chat_ai'] },
        gm: { name: 'General Manager', permissions: ['view_profile', 'view_all_access', 'evaluate_hr', 'chat_ai'] }
    }
};

// --- Auth Handling ---

const Auth = {
    login: (email, role) => {
        const user = MOCK_DATA.employees.find(e => e.role === role); // Simplified role-based mock login
        if (user) {
            localStorage.setItem('qt_user', JSON.stringify(user));
            window.location.href = 'dashboard.html';
        } else {
            showToast('User not found for this role.', 'danger');
        }
    },
    logout: () => {
        localStorage.removeItem('qt_user');
        window.location.href = 'index.html';
    },
    getUser: () => {
        return JSON.parse(localStorage.getItem('qt_user'));
    },
    checkAuth: () => {
        // Disabled for Laravel migration
        /*
        if (!localStorage.getItem('qt_user') && window.location.pathname.includes('dashboard')) {
            window.location.href = 'login.html';
        }
        */
    }
};

// --- UI Rendering ---

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `qt-toast bg-${type}`;
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    Auth.checkAuth();

    // Login Form
    const loginForm = document.getElementById('loginFormLegacy'); // Avoid conflict with Laravel login
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            // ...
        });
    }

    // Subscription Form
    const subForm = document.getElementById('subscriptionForm');
    if (subForm) {
        subForm.addEventListener('submit', (e) => {
            e.preventDefault();
            showToast('Company registered successfully! Redirecting to login...');
            setTimeout(() => window.location.href = 'login.html', 2000);
        });
    }

    // Floating Chat Initialization
    initFloatingChat();
});

function initFloatingChat() {
    const container = document.querySelector('.floating-chat-container');
    const floatingChatBtn = document.getElementById('floatingChatBtn');
    const floatingChatWindow = document.getElementById('floatingChatWindow');
    const aiMessageInput = document.getElementById('aiMessageInput');
    const maximizeBtn = document.getElementById('maximizeChat');
    const aiChatForm = document.getElementById('aiChatForm');

    if (!container || !floatingChatBtn || !floatingChatWindow) return;

    let isDragging = false;
    let startX, startY, initialRight, initialBottom;

    floatingChatBtn.addEventListener('click', (e) => {
        if (isDragging) return;
        e.stopPropagation();
        floatingChatWindow.classList.toggle('active');
        if (floatingChatWindow.classList.contains('active') && aiMessageInput) {
            aiMessageInput.focus();
        }
    });

    if (maximizeBtn) {
        maximizeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            floatingChatWindow.classList.toggle('maximized');
            const icon = maximizeBtn.querySelector('i');
            if (floatingChatWindow.classList.contains('maximized')) {
                icon.classList.replace('bi-arrows-fullscreen', 'bi-fullscreen-exit');
            } else {
                icon.classList.replace('bi-fullscreen-exit', 'bi-arrows-fullscreen');
            }
        });
    }

    container.addEventListener('mousedown', startDrag);
    container.addEventListener('touchstart', startDrag, { passive: false });

    function startDrag(e) {
        if (!e.target.closest('#floatingChatBtn') && !e.target.closest('.chat-header')) return;
        if (e.target.closest('button')) return; // Don't drag when clicking buttons

        isDragging = false;
        const event = e.type === 'touchstart' ? e.touches[0] : e;
        startX = event.clientX;
        startY = event.clientY;
        const style = window.getComputedStyle(container);
        initialRight = parseInt(style.right);
        initialBottom = parseInt(style.bottom);

        document.addEventListener('mousemove', drag);
        document.addEventListener('touchmove', drag, { passive: false });
        document.addEventListener('mouseup', stopDrag);
        document.addEventListener('touchend', stopDrag);
    }

    function drag(e) {
        const event = e.type === 'touchmove' ? e.touches[0] : e;
        const dx = startX - event.clientX;
        const dy = startY - event.clientY;
        if (Math.abs(dx) > 5 || Math.abs(dy) > 5) isDragging = true;
        if (isDragging) {
            if (e.cancelable) e.preventDefault();
            container.style.right = (initialRight + dx) + 'px';
            container.style.bottom = (initialBottom + dy) + 'px';
            container.style.left = 'auto';
        }
    }

    function stopDrag() {
        document.removeEventListener('mousemove', drag);
        document.removeEventListener('touchmove', drag);
        document.removeEventListener('mouseup', stopDrag);
        document.removeEventListener('touchend', stopDrag);
        setTimeout(() => { isDragging = false; }, 0);
    }

    document.addEventListener('click', (e) => {
        if (!floatingChatWindow.contains(e.target) && !floatingChatBtn.contains(e.target)) {
            floatingChatWindow.classList.remove('active');
        }
    });

    // Handle Chat Form Submission
    if (aiChatForm) {
        aiChatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = aiMessageInput.value.trim();
            if (!message) return;

            const chatMessages = document.getElementById('chatMessages');

            // Add user message
            const userBubble = document.createElement('div');
            userBubble.className = 'chat-bubble user animate-fade-in shadow-sm';
            userBubble.textContent = message;
            chatMessages.appendChild(userBubble);

            aiMessageInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Add loading bubble
            const loadingBubble = document.createElement('div');
            loadingBubble.className = 'chat-bubble ai animate-fade-in shadow-sm loading';
            loadingBubble.textContent = window.EvaloConfig._i18n.thinking;
            chatMessages.appendChild(loadingBubble);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const response = await fetch(window.EvaloConfig.routes.aiChat, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.EvaloConfig.csrfToken
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                chatMessages.removeChild(loadingBubble);

                const aiBubble = document.createElement('div');
                aiBubble.className = 'chat-bubble ai animate-fade-in shadow-sm';
                const rawResponse = data.response || window.EvaloConfig._i18n.errorMsg;
                // Use marked to parse markdown, but keep it safe
                aiBubble.innerHTML = typeof marked !== 'undefined' ? marked.parse(rawResponse) : rawResponse;
                chatMessages.appendChild(aiBubble);
            } catch (error) {
                console.error('[Evalo AI Chat Error]', error);
                if (loadingBubble.parentNode) {
                    chatMessages.removeChild(loadingBubble);
                }
                const errorBubble = document.createElement('div');
                errorBubble.className = 'chat-bubble ai animate-fade-in shadow-sm';
                errorBubble.textContent = window.EvaloConfig._i18n.errorMsg;
                chatMessages.appendChild(errorBubble);
            }
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    }
}

function renderDashboard() {
    const user = Auth.getUser();
    if (!user) return;

    // Update Role Badge & Name
    const roleBadge = document.getElementById('roleBadge');
    const userName = document.getElementById('userName');
    if (roleBadge) roleBadge.innerText = MOCK_DATA.roles[user.role].name;
    if (userName) userName.innerText = user.name;

    // Adapt Navigation
    const navLinks = document.getElementById('sidebarNav');
    if (navLinks) {
        const links = getRoleLinks(user.role);
        navLinks.innerHTML = links.map(link => `
            <a href="#" class="nav-link ${link.active ? 'active' : ''}" onclick="switchView('${link.view}')">
                <span class="nav-icon">${link.icon}</span>
                ${link.label}
            </a>
        `).join('');
    }

    // Default View
    switchView('profile');
}

function getRoleLinks(role) {
    const baseLinks = [
        { label: 'My Profile', icon: '👤', view: 'profile', active: true },
        { label: 'AI Advisory', icon: '🤖', view: 'chat' }
    ];

    if (role === 'manager') {
        baseLinks.push({ label: 'My Department', icon: '🏢', view: 'department' });
    } else if (role === 'hr') {
        baseLinks.push({ label: 'HR Overview', icon: '👥', view: 'hr_all' });
    } else if (role === 'gm') {
        baseLinks.push({ label: 'Global Monitor', icon: '🌍', view: 'gm_global' });
    }

    return baseLinks;
}

function switchView(view) {
    const content = document.getElementById('mainContent');
    const user = Auth.getUser();

    // Highlight active link
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
    event?.currentTarget?.classList.add('active');

    switch (view) {
        case 'profile':
            content.innerHTML = renderProfile(user);
            break;
        case 'chat':
            content.innerHTML = renderChat();
            initChat();
            break;
        case 'department':
            content.innerHTML = renderDepartmentView(user.dept);
            break;
        case 'hr_all':
            content.innerHTML = renderHRView();
            break;
        case 'gm_global':
            content.innerHTML = renderGMView();
            break;
    }
}

// --- View Generators ---

function renderProfile(user) {
    return `
        <div class="animate-up">
            <h2 class="page-title">Employee Profile</h2>
            <p class="page-subtitle">Your personal performance evaluation</p>
            <div class="glass-card">
                <div class="profile-header">
                    <div class="profile-avatar">${user.name.charAt(0)}</div>
                    <h3>${user.name}</h3>
                    <span class="badge-role badge-${user.role}">${MOCK_DATA.roles[user.role].name}</span>
                    <p class="text-secondary mt-2">${user.dept} Department</p>
                </div>
                <div class="mt-4">
                    <h5>Performance Score: ${user.evaluation}%</h5>
                    <div class="eval-bar">
                        <div class="eval-fill" style="width: ${user.evaluation}%"></div>
                    </div>
                    <div class="mt-4">
                        <h6>Evaluation Report:</h6>
                        <p class="text-secondary">${user.report}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function renderChat() {
    return `
        <div class="animate-up">
            <h2 class="page-title">Gemini AI Advisor</h2>
            <p class="page-subtitle">Personalized career and performance guidance</p>
            <div class="glass-card chat-container">
                <div id="chatMessages" class="chat-messages">
                    <div class="chat-bubble ai">Hello ${Auth.getUser().name}! I am your AI assistant. How can I help you improve your performance today?</div>
                </div>
                <div class="chat-input-area">
                    <input type="text" id="chatInput" placeholder="Ask about career advice, skills, or your report...">
                    <button onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    `;
}

function initChat() {
    const input = document.getElementById('chatInput');
    if (input) {
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    }
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const messages = document.getElementById('chatMessages');
    const text = input.value.trim();

    if (!text) return;

    // User Message
    messages.innerHTML += `<div class="chat-bubble user">${text}</div>`;
    input.value = '';
    messages.scrollTop = messages.scrollHeight;

    // Mock AI Response
    setTimeout(() => {
        const responses = [
            "Based on your latest evaluation, I recommend focusing on technical documentation to reach the next level.",
            "Your performance in the IT department is currently in the top 10%. Keep up the great work!",
            "Have you considered taking a leadership workshop? It would complement your strategic vision.",
            "I've analyzed your trends; your efficiency has increased by 15% this month."
        ];
        const randomResponse = responses[Math.floor(Math.random() * responses.length)];
        messages.innerHTML += `<div class="chat-bubble ai">${randomResponse}</div>`;
        messages.scrollTop = messages.scrollHeight;
    }, 1000);
}

function renderDepartmentView(dept) {
    const list = MOCK_DATA.employees.filter(e => e.dept === dept);
    return `
        <div class="animate-up">
            <h2 class="page-title">${dept} Department Employees</h2>
            <p class="page-subtitle">Manage your team evaluations</p>
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="qt-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Score</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${list.map(e => `
                                <tr>
                                    <td>${e.name}</td>
                                    <td><span class="badge-role badge-${e.role}">${e.role}</span></td>
                                    <td>${e.evaluation}%</td>
                                    <td><button class="btn btn-sm btn-outline-primary">Evaluate</button></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function renderHRView() {
    return `
        <div class="animate-up">
            <h2 class="page-title">Company-Wide Overview</h2>
            <p class="page-subtitle">View and filter all company employees</p>
            <div class="filter-bar">
                <select onchange="filterEmployees(this.value)">
                    <option value="all">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                </select>
                <input type="text" placeholder="Search employee...">
            </div>
            <div class="glass-card">
                <div class="table-responsive">
                    <table class="qt-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="hrTableBody">
                            ${MOCK_DATA.employees.map(e => `
                                <tr>
                                    <td>${e.name}</td>
                                    <td>${e.dept}</td>
                                    <td><span class="badge-role badge-${e.role}">${e.role}</span></td>
                                    <td><button class="btn btn-sm btn-outline-primary">Edit / Evaluate</button></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;
}

function renderGMView() {
    return `
        <div class="animate-up">
            <h2 class="page-title">General Strategic Monitor</h2>
            <p class="page-subtitle">Global performance and HR overview</p>
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon bg-primary-light">👥</div>
                        <div class="stat-value">5</div>
                        <div class="stat-label">Total Staff</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon bg-success">📈</div>
                        <div class="stat-value">84%</div>
                        <div class="stat-label">Avg Evaluation</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon bg-warning">🏢</div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Departments</div>
                    </div>
                </div>
            </div>
            <div class="glass-card">
                <h5>HR Performance Tracking</h5>
                <p class="text-secondary">Evaluate your HR team and department managers.</p>
                <!-- Add more GM specific lists here -->
            </div>
        </div>
    `;
}
