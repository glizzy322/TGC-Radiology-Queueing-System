<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception | Radiology QMS</title>
    <link rel="stylesheet" href="/css/receptionist.css">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
</head>
<body>
    <div class="rqs-shell">
        <aside class="rqs-sidebar" aria-label="Reception navigation">
            <div class="rqs-brand">
                <img class="rqs-logo" src="/images/logo.png" alt="Tagum Global Medical Center Logo">
            </div>

            <nav class="rqs-nav" aria-label="Main">
                <button class="rqs-nav-item active nav-item" data-view="dashboard" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </button>
                <?php if ($userRole === 'receptionist'): ?>
                <button class="rqs-nav-item nav-item" data-view="reception" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg> Reception
                </button>
                <?php endif; ?>
                <?php if ($userRole === 'radiology_staff'): ?>
                <button class="rqs-nav-item nav-item" data-view="manage" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg> Manage queue
                </button>
                <?php endif; ?>
                <button class="rqs-nav-item nav-item" data-view="ads" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="18" height="14" x="3" y="5" rx="2"/><circle cx="8" cy="10" r="1.4"/><path d="m21 15-5-5L5 21"/></svg> Ads
                </button>
            </nav>

            <div class="rqs-sidebar-foot">
                <a class="rqs-mode-btn display-link" href="/public-display.php" target="_blank" rel="noopener">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg> Open public display
                </a>
            </div>
        </aside>

        <main class="workspace">
            <header class="page-header">
                <p>RADIOLOGY QUEUE MANAGEMENT SYSTEM</p>
                <h1 id="pageTitle">Dashboard</h1>
                <div class="header-clock" aria-hidden="false">
                    <div id="headerDate" class="header-date-text">--</div>
                    <div id="headerTime" class="header-time-text">--:--:--</div>
                </div>
            </header>

            <section class="view active" id="dashboardView" aria-labelledby="dashboardTitle">
                <h2 id="dashboardTitle" class="sr-only">Dashboard</h2>
                <div class="analytics-dashboard">
                    <section class="panel analytics-filters-panel">
                        <div class="analytics-filters-header">
                            <div>
                                <p class="filters-kicker">Report filters</p>
                                <h3>Refine the analytics view</h3>
                                <p class="filters-subtitle">Filter the summary by date range and procedure, or jump to a preset range.</p>
                            </div>
                        </div>

                        <div class="analytics-toolbar">
                            <label class="filter-control">
                                <span>From</span>
                                <input id="startDateFilter" type="date">
                            </label>
                            <label class="filter-control">
                                <span>To</span>
                                <input id="endDateFilter" type="date">
                            </label>
                            <label class="filter-control">
                                <span>Category</span>
                                <select id="categoryFilter">
                                    <option value="all">All categories</option>
                                    <option value="xray">X-Ray</option>
                                    <option value="ultrasound">Ultrasound</option>
                                    <option value="ctscan">CT Scan</option>
                                </select>
                            </label>
                            <button class="export-btn" id="downloadExcelBtn" type="button" title="Download Summary Report as Excel">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                Download Excel Report
                            </button>
                        </div>

                        <div class="date-shortcuts" aria-label="Quick date filters">
                            <button class="date-shortcut-btn" type="button" data-range="today">This day</button>
                            <button class="date-shortcut-btn" type="button" data-range="week">This week</button>
                            <button class="date-shortcut-btn" type="button" data-range="month">This month</button>
                            <button class="date-shortcut-btn" type="button" data-range="year">This year</button>
                        </div>
                    </section>

                    <div class="analytics-layout">
                        <div class="analytics-main-col">
                        <div class="metric-grid">
                        <section class="panel metric-card">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="metric-icon" style="color: var(--green)"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <p>Total tickets</p>
                            </div>
                            <strong id="totalTicketsMetric">0</strong>
                        </section>
                        <section class="panel metric-card">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="metric-icon" style="color: var(--green)"><path d="M3 3v18h18"/><path d="M7 13h3v5H7z"/><path d="M14 8h3v10h-3z"/></svg>
                                <p>Busiest category</p>
                            </div>
                            <strong id="busiestCategoryMetric">None</strong>
                        </section>
                        <section class="panel metric-card">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="metric-icon" style="color: var(--green)"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                                <p>Avg per category</p>
                            </div>
                            <strong id="averageCategoryMetric">0</strong>
                        </section>
                        </div>

                        <section class="panel chart-card queue-analytics-card">
                            <div class="section-heading simple">
                                <h3>Queue Analytics</h3>
                            </div>
                            <div class="bar-chart" id="procedureBarChart"></div>
                        </section>

                        <div class="analytics-secondary">
                        <section class="panel chart-card">
                            <div class="section-heading simple">
                                <h3>Served Patient Category</h3>
                            </div>
                            <div class="pie-wrap">
                                <div class="pie-chart" id="patientPieChart"></div>
                                <div class="pie-legend" id="patientPieLegend"></div>
                            </div>
                        </section>

                        </div>
                        </div>

                        <div class="live-monitor-side">
                            <section class="panel live-monitor" id="liveMonitorCompact">
                                <div class="live-monitor-header">
                                    <h3>Live Queue Monitor</h3>
                                    <span class="live-status"><i></i>Live</span>
                                </div>
                                <div class="live-monitor-body">
                                    <div class="monitor-simple">
                                        <div class="monitor-title">Now Serving</div>
                                        <ul class="monitor-simple-list" id="monitorSimpleNow"></ul>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </section>

            <?php if ($userRole === 'receptionist'): ?>
            <section class="view" id="receptionView" aria-labelledby="receptionTitle">
                <h2 id="receptionTitle" class="sr-only">Reception</h2>
                    <div class="reception-layout">
                        <div class="register-layout-group">
                            <section class="panel register-card" style="margin-bottom: 24px;">
                                <div class="section-heading simple">
                                    <h3 class="dark-title">Register Patient Queue</h3>
                                </div>

                                <div class="field-group">
                                    <p class="light-label">Procedure Category</p>
                                    <div class="choice-row">
                                        <?php foreach ($procedures as $proc): ?>
                                            <button class="choice-btn" data-procedure="<?= $proc['id'] ?>" data-prefix="<?= substr($proc['code'], 0, 2) ?>" data-name="<?= htmlspecialchars($proc['name']) ?>" type="button"><?= htmlspecialchars($proc['name']) ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="field-group">
                                    <p class="light-label">Patient Category</p>
                                    <div class="choice-row">
                                        <?php foreach ($categories as $cat): ?>
                                            <button class="choice-btn" data-patient="<?= $cat['id'] ?>" data-code="<?= $cat['code'] ?>" type="button"><?= $cat['code'] === 'IPD' ? 'In-patient <span>(IPD)</span>' : 'Out-patient <span>(OPD)</span>' ?></button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </section>

                            <div class="register-action-area">
                                <button class="primary-action" id="generateTicket" type="button" disabled>
                                    Generate queue number
                                </button>
                                <p class="form-note" id="formNote">Select a procedure and patient category to continue.</p>
                            </div>
                        </div>

                    <aside class="panel latest-card">
                        <div class="section-heading simple">
                            <h3>Latest Ticket</h3>
                        </div>
                        <div class="ticket-preview" id="latestTicket">
                            <p>No ticket generated yet this session.</p>
                        </div>
                    </aside>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($userRole === 'radiology_staff'): ?>
            <section class="view" id="manageView" aria-labelledby="manageTitle">
                <h2 id="manageTitle" class="sr-only">Manage queue</h2>
                <div class="manage-grid" id="manageGrid"></div>
            </section>
            <?php endif; ?>

            <section class="view" id="adsView" aria-labelledby="adsTitle">
                <h2 id="adsTitle" class="sr-only">Ads</h2>
                <div class="ads-dashboard">
                    <section class="panel ads-card">
                        <div class="ads-panel-head">
                            <h3>Ad library</h3>
                            <button class="ads-add-btn" id="showAdFormBtn" type="button">
                                <span>+</span> Add ad
                            </button>
                        </div>

                        <form class="ads-form" id="adForm" hidden>
                            <div class="ads-form-head">
                                <h3>Add a new ad</h3>
                                <button class="ads-icon-btn" id="cancelAdFormBtn" type="button" aria-label="Close ad form">x</button>
                            </div>
                            <label class="ads-upload">
                                <span class="ads-upload-icon">↑</span>
                                <strong>Click to choose a file from your device</strong>
                                <small>JPG, PNG, WEBP, MP4, WEBM, or MOV</small>
                                <input id="adFileInput" type="file" accept="image/*,video/mp4,video/webm,video/quicktime" hidden>
                            </label>
                            <div class="ads-file-note" id="adFileNote">No file selected.</div>
                            <label class="ads-duration-control">
                                <span>Seconds on screen</span>
                                <input id="adDurationInput" type="number" min="3" max="60" value="8">
                            </label>
                            <label class="ads-check-control">
                                <input id="adActiveInput" type="checkbox" checked>
                                <span>Show this ad on the public display</span>
                            </label>
                            <div class="ads-form-actions">
                                <button class="ads-save-btn" id="saveAdBtn" type="submit">Add ad</button>
                                <button class="ads-cancel-btn" id="cancelAdFormBtn2" type="button">Cancel</button>
                            </div>
                        </form>

                        <div class="ads-list" id="adsList"></div>
                    </section>

                    <section class="panel ads-preview-card">
                        <div class="ads-preview-head">
                            <h3>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
                                Public display preview
                            </h3>
                            <span>ON AIR</span>
                        </div>
                        <div class="ads-preview-stage" id="adsPreviewStage">
                            <div class="ads-preview-empty">No active ads yet.</div>
                        </div>
                        <p class="ads-preview-note">Rotates automatically through every ad marked Active. Images hold for their set duration; videos play through once, then advance.</p>
                    </section>
                </div>
            </section>

            <section class="view" id="accountView" aria-labelledby="accountTitle">
                <h2 id="accountTitle" class="sr-only">Account</h2>
                <section class="panel account-card">
                    <div>
                        <p class="account-label">Signed in as</p>
                        <h3><?= htmlspecialchars($userName) ?></h3>
                    </div>
                    <form method="POST" action="/logout" style="margin-top: 16px;">
                        <button type="submit" class="secondary-action">Sign Out</button>
                    </form>
                </section>
            </section>
        </main>
    </div>

    <script>
        const procedures = {
            xray: { name: 'X-Ray', shortName: 'X-RAY', chartLabel: 'Xray', prefix: 'XR', maxServing: 2 },
            ultrasound: { name: 'Ultrasound', shortName: 'UTZ', chartLabel: 'Utz', prefix: 'UT', maxServing: 2 },
            ctscan: { name: 'CT Scan', shortName: 'CTS', chartLabel: 'CTS', prefix: 'CT', maxServing: 1 }
        };

        const state = {
            selectedProcedure: '',
            selectedPatient: '',
            latestTicket: null,
            completed: [],
            generatedTickets: [],
            queues: {
                xray: [],
                ultrasound: [],
                ctscan: []
            },
            serving: {
                xray: [null, null],
                ultrasound: [null, null],
                ctscan: [null]
            },
            counters: {
                xray: 0,
                ultrasound: 0,
                ctscan: 0
            },
            callSequence: 0,
            calledTickets: []
        };
        const STORAGE_KEY = 'radiologyQueueState';
        const ADS_DB_NAME = 'radiologyAdsDb';
        const ADS_STORE_NAME = 'ads';
        let adLibrary = [];
        let selectedAdFile = null;
        let selectedAdDataUrl = '';
        let adsPreviewIndex = 0;
        let adsPreviewTimer = null;

        function openAdsDb() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(ADS_DB_NAME, 1);
                request.onupgradeneeded = () => {
                    const db = request.result;
                    if (!db.objectStoreNames.contains(ADS_STORE_NAME)) {
                        db.createObjectStore(ADS_STORE_NAME, { keyPath: 'id' });
                    }
                };
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async function getStoredAds() {
            const db = await openAdsDb();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(ADS_STORE_NAME, 'readonly');
                const request = tx.objectStore(ADS_STORE_NAME).getAll();
                request.onsuccess = () => resolve(request.result.sort((a, b) => (a.order || 0) - (b.order || 0)));
                request.onerror = () => reject(request.error);
            });
        }

        async function saveStoredAd(ad) {
            const db = await openAdsDb();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(ADS_STORE_NAME, 'readwrite');
                tx.objectStore(ADS_STORE_NAME).put(ad);
                tx.oncomplete = resolve;
                tx.onerror = () => reject(tx.error);
            });
        }

        async function deleteStoredAd(id) {
            const db = await openAdsDb();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(ADS_STORE_NAME, 'readwrite');
                tx.objectStore(ADS_STORE_NAME).delete(id);
                tx.oncomplete = resolve;
                tx.onerror = () => reject(tx.error);
            });
        }

        function readFileAsDataUrl(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = () => reject(reader.error);
                reader.readAsDataURL(file);
            });
        }

        function escapeHtml(value) {
            return String(value).replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char]));
        }

        function parseTicketDates(ticket) {
            if (!ticket) return null;
            return {
                ...ticket,
                createdAt: ticket.createdAt ? new Date(ticket.createdAt) : new Date(),
                calledAt: ticket.calledAt ? new Date(ticket.calledAt) : undefined,
                calledOrder: Number(ticket.calledOrder || 0),
                completedAt: ticket.completedAt ? new Date(ticket.completedAt) : undefined
            };
        }

        function normalizeServingSlots(key, tickets = []) {
            const maxSlots = procedures[key].maxServing || 1;
            const slots = Array(maxSlots).fill(null);
            tickets.slice(0, maxSlots).forEach((ticket, index) => {
                slots[index] = parseTicketDates(ticket);
            });
            return slots;
        }

        function getServingCount(key) {
            return state.serving[key].filter(Boolean).length;
        }

        function getTicketNumberValue(ticket) {
            const match = String(ticket?.id || '').match(/\d+/);
            return match ? Number(match[0]) : Number.MAX_SAFE_INTEGER;
        }

        function migrateServingCallOrder() {
            const allServingTickets = Object.values(state.serving).flat().filter(Boolean);
            state.callSequence = Math.max(
                Number(state.callSequence || 0),
                ...allServingTickets.map((ticket) => Number(ticket.calledOrder || 0)),
                ...state.calledTickets.map((ticket) => Number(ticket.calledOrder || 0))
            );

            // Collect all serving tickets that need calledOrder, with their keys
            const needsOrder = [];
            Object.entries(state.serving).forEach(([key, tickets]) => {
                tickets.forEach((ticket, idx) => {
                    if (ticket && !ticket.calledOrder) {
                        needsOrder.push({ key, idx, ticket });
                    }
                });
            });

            needsOrder
                .sort((a, b) => {
                    const aTime = a.ticket.calledAt ? a.ticket.calledAt.getTime() : 0;
                    const bTime = b.ticket.calledAt ? b.ticket.calledAt.getTime() : 0;
                    if (aTime && bTime && aTime !== bTime) return aTime - bTime;
                    return getTicketNumberValue(a.ticket) - getTicketNumberValue(b.ticket);
                })
                .forEach((item) => {
                    state.callSequence += 1;
                    item.ticket.calledOrder = state.callSequence;
                    if (!item.ticket.calledAt) {
                        item.ticket.calledAt = new Date();
                    }
                    state.calledTickets.push({
                        id: item.ticket.id,
                        procedureKey: item.key,
                        calledOrder: item.ticket.calledOrder
                    });
                });
        }

        async function fetchQueueState() {
            try {
                const response = await fetch('/api/queue');
                const result = await response.json();
                if (result.status === 'success') {
                    const data = result.data;
                    
                    Object.keys(procedures).forEach((key) => {
                        state.queues[key] = data.queues[key].map(parseTicketDates);
                        
                        const fetchedServing = data.serving[key].map(parseTicketDates);
                        const maxSlots = procedures[key].maxServing || 1;
                        state.serving[key] = Array(maxSlots).fill(null);
                        fetchedServing.slice(0, maxSlots).forEach((t, i) => {
                            state.serving[key][i] = t;
                        });
                    });

                    state.completed = data.completed.map(parseTicketDates);
                    // Build generated tickets array for analytics
                    state.generatedTickets = [];
                    ['xray', 'ultrasound', 'ctscan'].forEach(key => {
                        state.generatedTickets.push(...state.queues[key]);
                        state.generatedTickets.push(...state.serving[key].filter(Boolean));
                    });
                    state.generatedTickets.push(...state.completed);

                    render();
                }
            } catch (error) {
                console.warn('Unable to load live queue state.', error);
            }
        }

        function loadSavedState() {
            // Replaced by live fetchQueueState polling
            fetchQueueState();
            setInterval(fetchQueueState, 2000);
        }

        function saveQueueState() {
            // Deprecated: Queue state is now managed purely by the backend database
        }

        const navItems = document.querySelectorAll('.nav-item');
        const views = document.querySelectorAll('.view');
        const pageTitle = document.getElementById('pageTitle');
        const generateButton = document.getElementById('generateTicket');
        const formNote = document.getElementById('formNote');

        navItems.forEach((item) => {
            item.addEventListener('click', () => {
                navItems.forEach((nav) => nav.classList.remove('active'));
                views.forEach((view) => view.classList.remove('active'));
                item.classList.add('active');
                document.getElementById(`${item.dataset.view}View`).classList.add('active');
                pageTitle.textContent = item.textContent.trim();
            });
        });

        document.querySelectorAll('[data-procedure]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedProcedure = button.dataset.procedure;
                document.querySelectorAll('[data-procedure]').forEach((item) => item.classList.remove('selected'));
                button.classList.add('selected');
                updateGenerateState();
            });
        });

        document.querySelectorAll('[data-patient]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedPatient = button.dataset.patient;
                document.querySelectorAll('[data-patient]').forEach((item) => item.classList.remove('selected'));
                button.classList.add('selected');
                updateGenerateState();
            });
        });

        generateButton.addEventListener('click', async () => {
            if (!state.selectedProcedure || !state.selectedPatient) return;

            const procBtn = document.querySelector(`[data-procedure="${state.selectedProcedure}"]`);
            const catBtn = document.querySelector(`[data-patient="${state.selectedPatient}"]`);
            
            generateButton.disabled = true;
            generateButton.textContent = 'Generating...';

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        procedure_id: state.selectedProcedure,
                        procedure_prefix: procBtn.dataset.prefix,
                        category_id: state.selectedPatient
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    const ticketData = result.ticket;
                    // Temporarily update local state so the preview UI works immediately
                    const t = {
                        id: ticketData.ticket_code,
                        procedureKey: ticketData.procedure_code.toLowerCase(),
                        procedure: ticketData.procedure_name,
                        patientType: ticketData.category_code,
                        createdAt: new Date(ticketData.created_at)
                    };
                    
                    
                    state.latestTicket = t;
                    
                    formNote.textContent = `${t.id} added to ${t.procedure}.`;
                    fetchQueueState(); // Immediately pull new state
                } else {
                    alert('Error generating ticket: ' + (result.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to server.');
            } finally {
                generateButton.disabled = false;
                generateButton.textContent = 'Generate queue number';
            }
        });

        function updateGenerateState() {
            const ready = state.selectedProcedure && state.selectedPatient;
            generateButton.disabled = !ready;
            formNote.textContent = ready
                ? 'Ready to generate queue number.'
                : 'Select a procedure and patient category to continue.';
        }

        async function callNext(key, slotIndex = null) {
            try {
                const response = await fetch('/api/queue/call', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ procedure_key: key })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    fetchQueueState(); // Refresh UI instantly
                } else {
                    alert(result.message || 'Error calling ticket');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to server.');
            }
        }

        async function completePatient(key, slotIndex) {
            const targetSlot = Number(slotIndex);
            const ticket = state.serving[key][targetSlot];
            if (!ticket) return;

            try {
                const response = await fetch('/api/queue/complete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticket_code: ticket.id })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    fetchQueueState(); // Refresh UI instantly
                } else {
                    alert(result.message || 'Error completing ticket');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to server.');
            }
        }

        function renderTicket(ticket, compact = false) {
            return `
                <div class="${compact ? 'queue-pill' : 'ticket-row'}">
                    <strong>${ticket.id}</strong>
                    <span>${ticket.patientType}</span>
                </div>
            `;
        }

        function render() {
            renderLatestTicket();
            renderManageQueue();
            renderDashboard();
            renderLiveMonitor();
            saveQueueState();
        }

        function renderLatestTicket() {
            const latestTicket = document.getElementById('latestTicket');
            if (!state.latestTicket) {
                // remove any procedure class when empty
                ['xray','ultrasound','ctscan'].forEach(k => latestTicket.classList.remove('proc-' + k));
                latestTicket.innerHTML = '<p>No ticket generated yet this session.</p>';
                return;
            }

            // ensure latestTicket has a procedure-specific class so we can style it
            ['xray','ultrasound','ctscan'].forEach(k => latestTicket.classList.remove('proc-' + k));
            latestTicket.classList.add('proc-' + state.latestTicket.procedureKey);

            latestTicket.innerHTML = `
                <div class="ticket-fade">
                    <span class="ticket-procedure">${state.latestTicket.procedure.toUpperCase()}</span>
                    <strong>${state.latestTicket.id}</strong>
                    <small>${state.latestTicket.patientType}</small>
                    <p>Now waiting · hand this number<br>to the patient</p>
                </div>
            `;
        }

        function renderManageQueue() {
            const manageGrid = document.getElementById('manageGrid');
            manageGrid.innerHTML = Object.entries(procedures).map(([key, procedure]) => {
                const servingList = state.serving[key];
                const waiting = state.queues[key];
                const maxSlots = procedure.maxServing || 1;
                const servingSlots = Array.from({ length: maxSlots }, (_, index) => servingList[index] || null);
                const servingCount = servingSlots.filter(Boolean).length;

                const servingHtml = servingSlots.map((ticket, slotIndex) => {
                    const slotLabel = key === 'xray'
                        ? `Xray ${slotIndex + 1}`
                        : `${procedure.name}${maxSlots > 1 ? ` ${slotIndex + 1}` : ''}`;

                    return `
                        <div class="rqs-serving-slot" id="${key}-${slotIndex + 1}">
                            <div class="rqs-slot-label">${slotLabel}</div>
                            <div class="rqs-serving-hero proc-${key}">
                                ${ticket ? `
                                    <div class="label">Now serving</div>
                                    <div class="num rqs-num">${ticket.id}</div>
                                    <span class="category-badge">${ticket.patientType}</span>
                                ` : `
                                    <div class="none">No patient being served</div>
                                `}
                            </div>
                            <div class="rqs-action-row">
                                <button class="secondary-action" type="button" ${ticket ? '' : 'disabled'} onclick="completePatient('${key}', ${slotIndex})">
                                    <span class="check-icon"></span> Complete
                                </button>
                                <button class="primary-action small" type="button" ${ticket || waiting.length === 0 ? 'disabled' : ''} onclick="callNext('${key}', ${slotIndex})">
                                    <span class="call-icon"></span> Call next
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                return `
                    <section class="panel rqs-exam-col manage-card" style="padding: 18px;">
                        <div class="rqs-exam-header">
                            <h3>${procedure.name}</h3>
                            <span class="rqs-count-chip">${waiting.length} waiting${maxSlots > 1 ? ` · ${servingCount}/${maxSlots} slots` : ''}</span>
                        </div>

                        ${servingHtml}

                        <div>
                            <p class="rqs-group-label" style="margin-bottom: 8px; font-size: 12px; font-weight: 800; color: #59645e; text-transform: uppercase;">Waiting list</p>
                            ${waiting.length === 0 ? `
                                <div class="rqs-empty-mini">Queue is empty</div>
                            ` : `
                                <div class="rqs-waiting-list">
                                    ${waiting.map((ticket, index) => `
                                        <div class="rqs-waiting-row">
                                            <span class="pos">${index + 1}</span>
                                            <span class="num rqs-num">${ticket.id}</span>
                                            <span class="category-badge">${ticket.patientType}</span>
                                        </div>
                                    `).join('')}
                                </div>
                            `}
                        </div>
                    </section>
                `;
            }).join('');
        }

        function renderDashboard() {
            const startDateValue = document.getElementById('startDateFilter').value;
            const endDateValue = document.getElementById('endDateFilter').value;
            const categoryValue = document.getElementById('categoryFilter').value;
            const filteredGeneratedTickets = filterAnalyticsTickets(state.generatedTickets, startDateValue, endDateValue, categoryValue, 'createdAt');
            const filteredCompletedTickets = filterAnalyticsTickets(state.completed, startDateValue, endDateValue, categoryValue, 'completedAt');
            const generatedCounts = getProcedureCounts(filteredGeneratedTickets);
            const busiestCategory = Object.entries(generatedCounts)
                .sort((a, b) => b[1] - a[1])
                .find((item) => item[1] > 0);
            const activeCategoryCount = Object.values(generatedCounts).filter((count) => count > 0).length || 3;
            const averagePerCategory = filteredGeneratedTickets.length / activeCategoryCount;

            document.getElementById('totalTicketsMetric').textContent = filteredGeneratedTickets.length;
            document.getElementById('busiestCategoryMetric').textContent = busiestCategory
                ? procedures[busiestCategory[0]].chartLabel
                : 'None';
            document.getElementById('averageCategoryMetric').textContent = formatAverage(averagePerCategory);

            renderProcedureChart(filteredGeneratedTickets);
            renderPatientPie(filteredCompletedTickets);
            syncDateShortcutState();
        }

        function toDateInputValue(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function getWeekStart(date) {
            const result = new Date(date);
            const day = result.getDay();
            const offset = day === 0 ? -6 : 1 - day;
            result.setDate(result.getDate() + offset);
            result.setHours(0, 0, 0, 0);
            return result;
        }

        function getWeekEnd(date) {
            const result = getWeekStart(date);
            result.setDate(result.getDate() + 6);
            result.setHours(23, 59, 59, 999);
            return result;
        }

        function setDateRange(startDate, endDate) {
            document.getElementById('startDateFilter').value = startDate ? toDateInputValue(startDate) : '';
            document.getElementById('endDateFilter').value = endDate ? toDateInputValue(endDate) : '';
            renderDashboard();
        }

        function syncDateShortcutState() {
            const shortcutButtons = document.querySelectorAll('[data-range]');
            const startValue = document.getElementById('startDateFilter').value;
            const endValue = document.getElementById('endDateFilter').value;

            shortcutButtons.forEach((button) => {
                button.classList.remove('active');
                const range = button.dataset.range;
                const today = new Date();
                let expectedStart = '';
                let expectedEnd = '';

                if (range === 'today') {
                    expectedStart = toDateInputValue(today);
                    expectedEnd = expectedStart;
                } else if (range === 'week') {
                    expectedStart = toDateInputValue(getWeekStart(today));
                    expectedEnd = toDateInputValue(getWeekEnd(today));
                } else if (range === 'month') {
                    expectedStart = toDateInputValue(new Date(today.getFullYear(), today.getMonth(), 1));
                    expectedEnd = toDateInputValue(new Date(today.getFullYear(), today.getMonth() + 1, 0));
                } else if (range === 'year') {
                    expectedStart = toDateInputValue(new Date(today.getFullYear(), 0, 1));
                    expectedEnd = toDateInputValue(new Date(today.getFullYear(), 11, 31));
                }

                if (startValue === expectedStart && endValue === expectedEnd) {
                    button.classList.add('active');
                }
            });
        }

        function applyDateShortcut(range) {
            const today = new Date();

            if (range === 'today') {
                setDateRange(today, today);
                return;
            }

            if (range === 'week') {
                setDateRange(getWeekStart(today), getWeekEnd(today));
                return;
            }

            if (range === 'month') {
                setDateRange(new Date(today.getFullYear(), today.getMonth(), 1), new Date(today.getFullYear(), today.getMonth() + 1, 0));
                return;
            }

            if (range === 'year') {
                setDateRange(new Date(today.getFullYear(), 0, 1), new Date(today.getFullYear(), 11, 31));
            }
        }

        function filterAnalyticsTickets(tickets, startDateValue, endDateValue, categoryValue, dateKey) {
            const startDate = startDateValue ? new Date(`${startDateValue}T00:00:00`) : null;
            const endDate = endDateValue ? new Date(`${endDateValue}T23:59:59.999`) : null;

            return tickets.filter((ticket) => {
                const ticketDate = ticket[dateKey] || ticket.createdAt;
                const startMatches = !startDate || ticketDate >= startDate;
                const endMatches = !endDate || ticketDate <= endDate;
                const categoryMatches = categoryValue === 'all' || ticket.procedureKey === categoryValue;

                return startMatches && endMatches && categoryMatches;
            });
        }

        function getProcedureCounts(tickets) {
            return Object.keys(procedures).reduce((counts, key) => {
                counts[key] = tickets.filter((ticket) => ticket.procedureKey === key).length;
                return counts;
            }, {});
        }

        function getDailyAverage(tickets, dateKey) {
            return formatAverage(getDailyAverageNumber(tickets, dateKey));
        }

        function getDailyAverageNumber(tickets, dateKey) {
            if (!tickets.length) return 0;

            const dates = new Set(tickets.map((ticket) => (ticket[dateKey] || ticket.createdAt).toDateString()));
            return tickets.length / dates.size;
        }

        function formatAverage(value) {
            return Number(value).toFixed(1).replace('.0', '');
        }

        function renderProcedureChart(tickets) {
            const chart = document.getElementById('procedureBarChart');
            const counts = Object.entries(procedures).map(([key, procedure]) => ({
                key,
                label: procedure.chartLabel,
                count: tickets.filter((ticket) => ticket.procedureKey === key).length
            }));
            const maxCount = Math.max(4, Math.ceil(Math.max(...counts.map((item) => item.count)) / 10) * 10);

            chart.innerHTML = `
                <div class="chart-legend">
                    ${counts.map((item) => `<span><i class="${item.key}"></i>${item.label}</span>`).join('')}
                </div>
                <div class="chart-scale">
                    ${[4, 3, 2, 1, 0].map((value) => `<span>${Math.round((maxCount / 4) * value)}</span>`).join('')}
                </div>
                <div class="chart-plot">
                    ${[4, 3, 2, 1].map(() => '<span class="grid-line"></span>').join('')}
                    <div class="bar-row">
                        ${counts.map((item) => `
                            <div class="bar-item">
                                <div class="bar-track">
                                    <span class="${item.key}" style="--bar-height: ${maxCount ? (item.count / maxCount) * 100 : 0}%"></span>
                                </div>
                                <strong>${item.label}</strong>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        function renderPatientPie(tickets) {
            const pie = document.getElementById('patientPieChart');
            const legend = document.getElementById('patientPieLegend');
            const ipd = tickets.filter((ticket) => ticket.patientType === 'IPD').length;
            const opd = tickets.filter((ticket) => ticket.patientType === 'OPD').length;
            const total = ipd + opd;
            const ipdPercent = total ? Math.round((ipd / total) * 100) : 0;

            pie.style.background = total
                ? `conic-gradient(#921d31 0 ${ipdPercent}%, #9fc4b0 ${ipdPercent}% 100%)`
                : 'conic-gradient(#eee8df 0 100%)';
            pie.innerHTML = `<span>${total}</span>`;
            legend.innerHTML = `
                <div><span class="legend-dot maroon"></span>IPD <strong>${ipd}</strong></div>
                <div><span class="legend-dot green"></span>OPD <strong>${opd}</strong></div>
            `;
        }

        // Live monitor rendering: compact 'Now Serving' card used on dashboard
        function renderLiveMonitor() {
            const simple = document.getElementById('monitorSimpleNow');
            if (!simple) return;

            simple.innerHTML = Object.entries(procedures).map(([key, procedure]) => {
                const servingList = state.serving[key].filter(Boolean);
                const ids = servingList.length > 0
                    ? servingList.map(t => t.id).join(', ')
                    : '-';
                return `
                    <li class="monitor-simple-row">
                        <span class="proc-name proc-${key}">${procedure.shortName}</span>
                        <span class="proc-id proc-${key}">${ids}</span>
                    </li>
                `;
            }).join('');
        }

        async function loadAds() {
            try {
                adLibrary = await getStoredAds();
                renderAdsView();
                renderAdsPreview();
            } catch (error) {
                console.warn('Unable to load ads.', error);
            }
        }

        function renderAdsView() {
            const list = document.getElementById('adsList');
            if (!list) return;

            if (!adLibrary.length) {
                list.innerHTML = '<div class="ads-empty-row">No ads added yet. Add an image or video to start the public display rotation.</div>';
                return;
            }

            list.innerHTML = adLibrary.map((ad) => {
                const isVideo = (ad.type || '').startsWith('video/');
                return `
                    <div class="ad-row">
                        <span class="ad-grip">::</span>
                        <div class="ad-thumb">
                            ${isVideo
                                ? `<video src="${ad.src}" muted playsinline></video>`
                                : `<img src="${ad.src}" alt="${escapeHtml(ad.name)}">`
                            }
                            <span>${isVideo ? '▶' : '▧'}</span>
                        </div>
                        <div class="ad-main">
                            <strong>${escapeHtml(ad.name)}</strong>
                            <small>${ad.duration}s on screen</small>
                        </div>
                        <span class="ad-status ${ad.active ? 'active' : 'paused'}">${ad.active ? 'Active' : 'Paused'}</span>
                        <button class="ad-small-btn" type="button" onclick="toggleAdStatus('${ad.id}')">${ad.active ? 'Pause' : 'Resume'}</button>
                        <button class="ads-icon-btn" type="button" onclick="editAdDuration('${ad.id}')" aria-label="Edit ad">✎</button>
                        <button class="ads-icon-btn" type="button" onclick="removeAd('${ad.id}')" aria-label="Delete ad">⌫</button>
                    </div>
                `;
            }).join('');
        }

        function resetAdForm() {
            const form = document.getElementById('adForm');
            const fileInput = document.getElementById('adFileInput');
            const fileNote = document.getElementById('adFileNote');
            const durationInput = document.getElementById('adDurationInput');
            const activeInput = document.getElementById('adActiveInput');
            selectedAdFile = null;
            selectedAdDataUrl = '';
            if (fileInput) fileInput.value = '';
            if (fileNote) fileNote.textContent = 'No file selected.';
            if (durationInput) durationInput.value = 8;
            if (activeInput) activeInput.checked = true;
            if (form) form.hidden = true;
        }

        async function toggleAdStatus(id) {
            const ad = adLibrary.find((item) => item.id === id);
            if (!ad) return;
            ad.active = !ad.active;
            await saveStoredAd(ad);
            await loadAds();
        }

        async function editAdDuration(id) {
            const ad = adLibrary.find((item) => item.id === id);
            if (!ad) return;
            const value = prompt('Seconds on screen', ad.duration);
            if (value === null) return;
            const duration = Math.max(3, Math.min(60, Number(value) || ad.duration));
            ad.duration = duration;
            await saveStoredAd(ad);
            await loadAds();
        }

        async function removeAd(id) {
            if (!confirm('Remove this ad from the library?')) return;
            await deleteStoredAd(id);
            await loadAds();
        }

        function renderAdsPreview() {
            const stage = document.getElementById('adsPreviewStage');
            if (!stage) return;
            clearTimeout(adsPreviewTimer);

            const activeAds = adLibrary.filter((ad) => ad.active);
            if (!activeAds.length) {
                stage.innerHTML = '<div class="ads-preview-empty">No active ads yet.</div>';
                return;
            }

            if (adsPreviewIndex >= activeAds.length) adsPreviewIndex = 0;
            const ad = activeAds[adsPreviewIndex];
            const isVideo = (ad.type || '').startsWith('video/');
            stage.innerHTML = `
                <div class="ads-preview-brand"><span>A+</span> TAGUM GLOBAL</div>
                ${isVideo
                    ? `<video class="ads-preview-media" src="${ad.src}" autoplay muted playsinline></video>`
                    : `<img class="ads-preview-media" src="${ad.src}" alt="${escapeHtml(ad.name)}">`
                }
                <div class="ads-preview-dot"></div>
            `;

            const next = () => {
                adsPreviewIndex = (adsPreviewIndex + 1) % activeAds.length;
                renderAdsPreview();
            };

            if (isVideo) {
                const video = stage.querySelector('video');
                video.onended = next;
                adsPreviewTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            } else {
                adsPreviewTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            }
        }

        document.getElementById('startDateFilter').addEventListener('change', renderDashboard);
        document.getElementById('endDateFilter').addEventListener('change', renderDashboard);
        document.getElementById('categoryFilter').addEventListener('change', renderDashboard);
        document.getElementById('downloadExcelBtn').addEventListener('click', downloadExcelReport);
        document.querySelectorAll('[data-range]').forEach((button) => {
            button.addEventListener('click', () => applyDateShortcut(button.dataset.range));
        });
        document.getElementById('showAdFormBtn').addEventListener('click', () => {
            document.getElementById('adForm').hidden = false;
        });
        document.getElementById('cancelAdFormBtn').addEventListener('click', resetAdForm);
        document.getElementById('cancelAdFormBtn2').addEventListener('click', resetAdForm);
        document.getElementById('adFileInput').addEventListener('change', async (event) => {
            selectedAdFile = event.target.files[0] || null;
            selectedAdDataUrl = selectedAdFile ? await readFileAsDataUrl(selectedAdFile) : '';
            document.getElementById('adFileNote').textContent = selectedAdFile
                ? selectedAdFile.name
                : 'No file selected.';
        });
        document.getElementById('adForm').addEventListener('submit', async (event) => {
            event.preventDefault();
            if (!selectedAdFile || !selectedAdDataUrl) {
                alert('Please choose an image or video first.');
                return;
            }

            const ad = {
                id: `ad-${Date.now()}`,
                name: selectedAdFile.name,
                type: selectedAdFile.type || 'application/octet-stream',
                src: selectedAdDataUrl,
                duration: Math.max(3, Math.min(60, Number(document.getElementById('adDurationInput').value) || 8)),
                active: document.getElementById('adActiveInput').checked,
                order: adLibrary.length + 1,
                createdAt: new Date().toISOString()
            };

            await saveStoredAd(ad);
            resetAdForm();
            await loadAds();
        });

        // Header clock: update date and time in the top-right header
        function updateHeaderClock() {
            const dElem = document.getElementById('headerDate');
            const tElem = document.getElementById('headerTime');
            if (!dElem || !tElem) return;
            const now = new Date();
            const dateStr = now.toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' });
            const timeStr = now.toLocaleTimeString();
            dElem.textContent = dateStr;
            tElem.textContent = timeStr;
        }

        updateHeaderClock();
        setInterval(updateHeaderClock, 1000);

        loadSavedState();
        render();
        loadAds();

        // Download Excel Summary Report
        function downloadExcelReport() {
            if (typeof XLSX === 'undefined') {
                alert('Excel export library is still loading. Please try again in a moment.');
                return;
            }

            const startDateValue = document.getElementById('startDateFilter').value;
            const endDateValue = document.getElementById('endDateFilter').value;
            const categoryValue = document.getElementById('categoryFilter').value;

            const startLabel = startDateValue ? new Date(`${startDateValue}T00:00:00`).toLocaleDateString() : 'All dates';
            const endLabel = endDateValue ? new Date(`${endDateValue}T00:00:00`).toLocaleDateString() : 'All dates';
            const categoryLabel = categoryValue === 'all' ? 'All Categories' : procedures[categoryValue]?.name || categoryValue;

            const filteredGenerated = filterAnalyticsTickets(state.generatedTickets, startDateValue, endDateValue, categoryValue, 'createdAt');
            const filteredCompleted = filterAnalyticsTickets(state.completed, startDateValue, endDateValue, categoryValue, 'completedAt');

            const generatedCounts = getProcedureCounts(filteredGenerated);
            const busiestEntry = Object.entries(generatedCounts).sort((a, b) => b[1] - a[1]).find(e => e[1] > 0);
            const activeCats = Object.values(generatedCounts).filter(c => c > 0).length || 3;
            const avgPerCat = formatAverage(filteredGenerated.length / activeCats);

            const ipdCount = filteredCompleted.filter(t => t.patientType === 'IPD').length;
            const opdCount = filteredCompleted.filter(t => t.patientType === 'OPD').length;

            // --- Sheet 1: Summary ---
            const summaryData = [
                ['RADIOLOGY QUEUE MANAGEMENT SYSTEM'],
                ['Summary Report'],
                [],
                ['Generated On:', new Date().toLocaleString()],
                ['Filters Applied:'],
                ['  From:', startLabel],
                ['  To:', endLabel],
                ['  Category:', categoryLabel],
                [],
                ['KEY METRICS'],
                ['Total Tickets Generated', filteredGenerated.length],
                ['Total Tickets Completed', filteredCompleted.length],
                ['Busiest Category', busiestEntry ? procedures[busiestEntry[0]].name : 'None'],
                ['Average Per Category', avgPerCat],
                [],
                ['CATEGORY BREAKDOWN'],
                ['Category', 'Generated', 'Completed'],
            ];

            Object.entries(procedures).forEach(([key, proc]) => {
                const gen = filteredGenerated.filter(t => t.procedureKey === key).length;
                const comp = filteredCompleted.filter(t => t.procedureKey === key).length;
                summaryData.push([proc.name, gen, comp]);
            });

            summaryData.push([]);
            summaryData.push(['PATIENT TYPE BREAKDOWN (Completed)']);
            summaryData.push(['Patient Type', 'Count', 'Percentage']);
            const totalCompleted = ipdCount + opdCount;
            summaryData.push(['In-Patient (IPD)', ipdCount, totalCompleted ? Math.round((ipdCount / totalCompleted) * 100) + '%' : '0%']);
            summaryData.push(['Out-Patient (OPD)', opdCount, totalCompleted ? Math.round((opdCount / totalCompleted) * 100) + '%' : '0%']);

            const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);

            // Set column widths
            wsSummary['!cols'] = [
                { wch: 28 },
                { wch: 18 },
                { wch: 14 }
            ];

            // --- Sheet 2: Ticket Details ---
            const detailHeaders = ['Ticket ID', 'Procedure', 'Patient Type', 'Created At', 'Called At', 'Completed At', 'Status'];
            const detailData = [detailHeaders];

            // Combine generated and completed, deduplicating by ID
            const allMap = new Map();
            filteredGenerated.forEach(t => {
                allMap.set(t.id, { ...t, status: 'Generated' });
            });
            filteredCompleted.forEach(t => {
                const existing = allMap.get(t.id);
                allMap.set(t.id, { ...(existing || {}), ...t, status: 'Completed' });
            });

            // Mark currently serving
            Object.values(state.serving).flat().filter(Boolean).forEach(t => {
                if (allMap.has(t.id)) {
                    allMap.get(t.id).status = 'Serving';
                }
            });

            // Mark waiting
            Object.values(state.queues).flat().forEach(t => {
                if (allMap.has(t.id)) {
                    allMap.get(t.id).status = 'Waiting';
                }
            });

            Array.from(allMap.values())
                .sort((a, b) => (a.createdAt || 0) - (b.createdAt || 0))
                .forEach(t => {
                    detailData.push([
                        t.id,
                        procedures[t.procedureKey]?.name || t.procedureKey,
                        t.patientType,
                        t.createdAt ? t.createdAt.toLocaleString() : '',
                        t.calledAt ? t.calledAt.toLocaleString() : '',
                        t.completedAt ? t.completedAt.toLocaleString() : '',
                        t.status
                    ]);
                });

            const wsDetail = XLSX.utils.aoa_to_sheet(detailData);
            wsDetail['!cols'] = [
                { wch: 12 },
                { wch: 14 },
                { wch: 14 },
                { wch: 22 },
                { wch: 22 },
                { wch: 22 },
                { wch: 12 }
            ];

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, wsSummary, 'Summary');
            XLSX.utils.book_append_sheet(wb, wsDetail, 'Ticket Details');

            // Generate filename with date
            const now = new Date();
            const dateStamp = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
            const filterSuffix = yearValue !== 'all' || monthValue !== 'all' || categoryValue !== 'all'
                ? `_${yearLabel}_${monthLabel}_${categoryLabel}`.replace(/\s+/g, '-')
                : '';
            const fileName = `Radiology_QMS_Report_${dateStamp}${filterSuffix}.xlsx`;

            XLSX.writeFile(wb, fileName);
        }
    </script>
</body>
</html>
