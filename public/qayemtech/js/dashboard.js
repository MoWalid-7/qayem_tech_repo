/**
 * Qayem Dashboard JavaScript
 * Refactored to separate file for better maintainability.
 */

// Universal Form Handler
async function handleFormSubmit(formId, route, successReload = true) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        const formData = new FormData(this);
        try {
            const response = await fetch(route, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.QayemConfig.csrfToken
                }
            });
            const result = await response.json();
            if (result.success) {
                showToast(result.message);
                if (successReload) location.reload();
            } else {
                showToast(result.message || 'Error occurred', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (err) {
            showToast('Network error', 'danger');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}

// Export Handlers
function printReport() {
    const printContents = document.getElementById('printableReportArea').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>AI Performance Report</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
            <style>
                body { background: #1e1e2d; color: #fff; padding: 40px; font-family: system-ui, -apple-system, sans-serif; }
                .glass-card { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255,255,255,0.1); }
                .border-glass { border-color: rgba(255,255,255,0.1) !important; }
                .text-secondary { color: #8b8fb1 !important; }
                @media print {
                    body { background: white; color: black; padding: 0; }
                    .text-white, .text-light { color: black !important; }
                    .text-white-50, .text-secondary { color: #555 !important; }
                    .bg-dark { background: #f8f9fa !important; border: 1px solid #ddd !important; }
                    .badge { border: 1px solid #ddd !important; color: black !important; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2 class="mb-4 pb-2 border-bottom">Performance Evaluation Report</h2>
                ${printContents}
            </div>
            <scr` + `ipt>
                window.onload = function() { window.print(); window.close(); }
            </scr` + `ipt>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function downloadReport() {
    if (typeof html2pdf === 'undefined') {
        showToast('PDF library is loading, please try again in a moment.', 'warning');
        return;
    }

    const element = document.getElementById('printableReportArea');
    const name = document.getElementById('profName').innerText || 'Employee';

    // Clone element to apply specific PDF styles without affecting UI
    const clone = element.cloneNode(true);
    const wrapper = document.createElement('div');
    wrapper.style.padding = '30px';
    wrapper.style.background = '#1e1e2d';
    wrapper.style.color = 'white';

    // Add header for PDF
    const header = document.createElement('h2');
    header.innerHTML = `QayemTech - Performance Report<br><small style="font-size: 0.5em; opacity: 0.7;">Generated: ${new Date().toLocaleDateString()}</small>`;
    header.style.borderBottom = '1px solid rgba(255,255,255,0.1)';
    header.style.paddingBottom = '15px';
    header.style.marginBottom = '25px';

    wrapper.appendChild(header);
    wrapper.appendChild(clone);

    const opt = {
        margin: 10,
        filename: `Performance_Report_${name.replace(/\s+/g, '_')}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, backgroundColor: '#1e1e2d' },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(wrapper).save().then(() => {
        showToast('PDF downloaded successfully!', 'success');
    }).catch(err => {
        console.error('PDF generation error:', err);
        showToast('Failed to generate PDF.', 'danger');
    });
}

function openEditDeptModal(id, name, managerId) {
    document.getElementById('editDeptId').value = id;
    document.getElementById('editDeptName').value = name;
    document.getElementById('editDeptManagerId').value = managerId || '';
    new bootstrap.Modal('#editDeptModal').show();
}

function openEditHrModal(id, name, email) {
    document.getElementById('editHrId').value = id;
    document.getElementById('editHrName').value = name;
    document.getElementById('editHrEmail').value = email;
    new bootstrap.Modal('#editHrModal').show();
}

async function deleteHrAccount() {
    const id = document.getElementById('editHrId').value;
    if (!id) return;

    if (!confirm('Are you sure you want to delete this HR account? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(window.QayemConfig.routes.hrDelete, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.QayemConfig.csrfToken
            },
            body: JSON.stringify({
                id: id
            })
        });
        const result = await response.json();
        if (result.success) {
            showToast(result.message);
            location.reload();
        } else {
            showToast(result.message, 'danger');
        }
    } catch (err) {
        showToast('Error deleting account', 'danger');
    }
}

function openMetricsModal(element) {
    const id = element.getAttribute('data-id');
    const name = element.getAttribute('data-name');
    const type = element.getAttribute('data-type');
    const hireDate = element.getAttribute('data-hire-date');
    const tasksReq = element.getAttribute('data-tasks-req');
    const tasksDone = element.getAttribute('data-tasks-done');

    document.getElementById('metricsId').value = id;
    document.getElementById('metricsType').value = type;
    document.getElementById('metricsEmployeeName').innerText = name;
    document.getElementById('metricsHireDate').value = hireDate || '';
    document.getElementById('metricsRequested').value = tasksReq || 0;
    document.getElementById('metricsCompleted').value = tasksDone || 0;

    if (type === 'manager') {
        document.getElementById('attendanceFieldContainer').classList.add('d-none');
        document.getElementById('metricsAttendance').required = false;
        document.getElementById('metricsAttendance').value = '';
    } else {
        const attendance = element.getAttribute('data-attendance');
        document.getElementById('attendanceFieldContainer').classList.remove('d-none');
        document.getElementById('metricsAttendance').required = true;
        document.getElementById('metricsAttendance').value = attendance || 0;
    }

    new bootstrap.Modal('#metricsModal').show();
}

async function runAI(route) {
    showToast('AI thinking... please wait', 'info');
    try {
        const response = await fetch(route, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.QayemConfig.csrfToken
            }
        });
        const result = await response.json();
        if (result.success) {
            showToast(result.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(result.message, 'danger');
        }
    } catch (err) {
        showToast('AI Evaluation failed', 'danger');
    }
}

async function openProfileModal(type, id) {
    const modal = new bootstrap.Modal(document.getElementById('profileModal'));
    // Reset fields
    document.getElementById('profName').innerText = 'Loading...';
    document.getElementById('profStrengths').innerText = '...';
    document.getElementById('profWeaknesses').innerText = '...';
    document.getElementById('profRecommendations').innerText = '...';

    try {
        const response = await fetch(`${window.QayemConfig.routes.profileGet}?type=${type}&id=${id}`);
        const result = await response.json();
        if (result.success) {
            const data = result.data;
            const profileEval = result.latest_evaluation;

            document.getElementById('profName').innerText = data.name;
            document.getElementById('profAvatar').innerText = data.name.charAt(0);
            document.getElementById('profTitle').innerText = (type === 'employee' ? data.job_title : data.role).toUpperCase();
            document.getElementById('profDept').innerText = data.department ? data.department.name : 'N/A';
            document.getElementById('profHireDate').innerText = data.hire_date || 'N/A';

            const taskRate = data.tasks_requested > 0 ? Math.round((data.tasks_completed / data.tasks_requested) * 100) : 0;
            document.getElementById('profTaskRate').innerText = taskRate + '%';
            document.getElementById('profProgressBar').style.width = taskRate + '%';

            if (profileEval) {
                document.getElementById('profScore').innerText = profileEval.score || '-';
                document.getElementById('profStrengths').innerText = profileEval.strengths || 'N/A';
                document.getElementById('profWeaknesses').innerText = profileEval.weaknesses || 'N/A';
                document.getElementById('profRecommendations').innerText = profileEval.recommendations || 'N/A';
            } else {
                document.getElementById('profScore').innerText = '-';
                document.getElementById('profStrengths').innerText = 'No evaluation generated yet. Click the "AI" button to start.';
            }
            modal.show();
        }
    } catch (err) {
        showToast('Error loading profile', 'danger');
    }
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `qt-toast bg-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Initializations
document.addEventListener('DOMContentLoaded', () => {
    // Form Registrations
    if (document.getElementById('addHrForm')) handleFormSubmit('addHrForm', window.QayemConfig.routes.hrStore);
    if (document.getElementById('editHrForm')) handleFormSubmit('editHrForm', window.QayemConfig.routes.hrUpdate);
    if (document.getElementById('addDeptForm')) handleFormSubmit('addDeptForm', window.QayemConfig.routes.deptStore);
    if (document.getElementById('addManagerForm')) handleFormSubmit('addManagerForm', window.QayemConfig.routes.managerStore);
    if (document.getElementById('addEmployeeForm')) handleFormSubmit('addEmployeeForm', window.QayemConfig.routes.employeeStore);
    if (document.getElementById('editDeptForm')) handleFormSubmit('editDeptForm', window.QayemConfig.routes.deptUpdate);

    // Metrics Form Handler
    const updateMetricsForm = document.getElementById('updateMetricsForm');
    if (updateMetricsForm) {
        updateMetricsForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const type = document.getElementById('metricsType').value;
            const route = type === 'employee' ? window.QayemConfig.routes.employeeMetrics : window.QayemConfig.routes.managerMetrics;

            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;

            const formData = new FormData(this);
            if (type === 'employee') formData.append('employee_id', formData.get('id'));
            else formData.append('manager_id', formData.get('id'));

            try {
                const response = await fetch(route, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': window.QayemConfig.csrfToken
                    }
                });
                const result = await response.json();
                if (result.success) {
                    showToast(result.message);
                    location.reload();
                } else {
                    showToast(result.message, 'danger');
                    btn.disabled = false;
                }
            } catch (err) {
                showToast('Error saving data', 'danger');
                btn.disabled = false;
            }
        });
    }

    // AI Chat Handler
    const aiChatForm = document.getElementById('aiChatForm');
    if (aiChatForm) {
        aiChatForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const input = document.getElementById('aiMessageInput');
            const container = document.getElementById('chatMessages');
            const message = input.value.trim();
            if (!message) return;

            // User message
            const userBubble = document.createElement('div');
            userBubble.className = 'chat-bubble user animate-fade-in shadow-sm';
            userBubble.innerText = message;
            container.appendChild(userBubble);
            input.value = '';
            container.scrollTop = container.scrollHeight;

            // Loading bubble
            const loadingBubble = document.createElement('div');
            loadingBubble.className = 'chat-bubble ai loading animate-fade-in';
            loadingBubble.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> ${window.QayemConfig._i18n.thinking}`;
            container.appendChild(loadingBubble);
            container.scrollTop = container.scrollHeight;

            try {
                const response = await fetch(window.QayemConfig.routes.aiChat, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.QayemConfig.csrfToken
                    },
                    body: JSON.stringify({ message: message })
                });
                const result = await response.json();

                if (loadingBubble.parentNode) container.removeChild(loadingBubble);

                const aiBubble = document.createElement('div');
                aiBubble.className = 'chat-bubble ai animate-fade-in shadow-sm';
                aiBubble.innerText = result.response || window.QayemConfig._i18n.errorMsg;
                container.appendChild(aiBubble);
                container.scrollTop = container.scrollHeight;
            } catch (err) {
                if (loadingBubble.parentNode) container.removeChild(loadingBubble);
                showToast('Failed to get AI response', 'danger');
            }
        });
    }

    // Chart.js initialization
    const ctx = document.getElementById('performanceChart');
    if (ctx) {
        const chartCtx = ctx.getContext('2d');
        const gradient = chartCtx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.QayemConfig.chartData.labels,
                datasets: [{
                    label: window.QayemConfig.chartData.label,
                    data: [65, 72, 68, 85, 82, 90],
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: 'rgba(255,255,255,0.05)', drawBorder: false },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Scroll Reveal Animation Logic
    const revealCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    };

    const revealObserver = new IntersectionObserver(revealCallback, { threshold: 0.1 });
    document.querySelectorAll('.reveal-fade-up, .reveal-scale-in').forEach(el => revealObserver.observe(el));

    // Floating Chat logic
    initFloatingChat();
});

// Floating Chat logic
function initFloatingChat() {
    const floatingChatBtn = document.getElementById('floatingChatBtn');
    const floatingChatWindow = document.getElementById('floatingChatWindow');
    const container = document.querySelector('.floating-chat-container');
    const aiMessageInput = document.getElementById('aiMessageInput');

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

    container.addEventListener('mousedown', startDrag);
    container.addEventListener('touchstart', startDrag, { passive: false });

    function startDrag(e) {
        if (!e.target.closest('#floatingChatBtn') && !e.target.closest('.chat-header')) return;
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
}
