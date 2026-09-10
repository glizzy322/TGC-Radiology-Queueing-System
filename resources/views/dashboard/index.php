<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception | Radiology QMS</title>
    <link rel="stylesheet" href="/css/receptionist.css?v=2">
    <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
</head>
<body>
    <div class="rqs-shell">
        <aside class="rqs-sidebar" aria-label="Reception navigation">
            <div class="rqs-brand">
                <img class="rqs-logo" src="/images/logo.png" alt="Tagum Global Medical Center Logo">
            </div>

            <div class="user-indicator" style="padding: 0 24px; margin-bottom: 16px;">
                <div style="font-size: 11px; color: var(--muted); text-transform: uppercase; font-weight: 700; margin-bottom: 2px;">Logged in as</div>
                <div style="color: var(--text); font-weight: 600; font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($userName ?? '') ?>"><?= htmlspecialchars($userName ?? '') ?></div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 2px; text-transform: capitalize;"><?= htmlspecialchars(str_replace('_', ' ', $userRole ?? '')) ?></div>
            </div>

            <nav class="rqs-nav" aria-label="Main">
                <?php if ($userRole !== 'radiology_staff'): ?>
                <button class="rqs-nav-item active nav-item" data-view="dashboard" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg> Dashboard
                </button>
                <?php endif; ?>
                <?php if ($userRole === 'receptionist' || $userRole === 'administrator'): ?>
                <button class="rqs-nav-item nav-item" data-view="reception" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg> Reception
                </button>
                <?php endif; ?>
                <?php if ($userRole === 'radiology_staff' || $userRole === 'administrator'): ?>
                <button class="rqs-nav-item <?= $userRole === 'radiology_staff' ? 'active' : '' ?> nav-item" data-view="manage" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><line x1="10" x2="21" y1="6" y2="6"/><line x1="10" x2="21" y1="12" y2="12"/><line x1="10" x2="21" y1="18" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg> Manage queue
                </button>
                <?php endif; ?>
                <?php if ($userRole !== 'radiology_staff'): ?>
                <button class="rqs-nav-item nav-item" data-view="ads" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="18" height="14" x="3" y="5" rx="2"/><circle cx="8" cy="10" r="1.4"/><path d="m21 15-5-5L5 21"/></svg> Ads
                </button>
                <?php endif; ?>
            </nav>
            <div style="flex-grow: 1;"></div>
            <nav class="rqs-nav" style="margin-bottom: 12px;" aria-label="Account">
                <button class="rqs-nav-item" id="logoutBtn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg> Sign Out
                </button>
            </nav>

            <?php if ($userRole !== 'radiology_staff'): ?>
            <div class="rqs-sidebar-foot">
                <a class="rqs-mode-btn display-link" href="/public-display" target="_blank" rel="noopener">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-icon"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg> Open public display
                </a>
            </div>
            <?php endif; ?>
        </aside>

        <main class="workspace">
            <header class="page-header">
                <p>RADIOLOGY QUEUE MANAGEMENT SYSTEM</p>
                <h1 id="pageTitle"><?= $userRole === 'radiology_staff' ? 'Manage queue' : 'Dashboard' ?></h1>
                <div class="header-clock" aria-hidden="false">
                    <div id="headerDate" class="header-date-text">--</div>
                    <div id="headerTime" class="header-time-text">--:--:--</div>
                </div>
            </header>

            <?php if ($userRole !== 'radiology_staff'): ?>
            <section class="view active" id="dashboardView" aria-labelledby="dashboardTitle">
                <h2 id="dashboardTitle" class="sr-only">Dashboard</h2>
                <div class="analytics-dashboard">
                    <section class="panel analytics-filters-panel">
                        <div class="analytics-filters-header">
                            <div>
                                <p class="filters-kicker">Report filters</p>
                                <h3>Refine the analytics view</h3>
                                <p class="filters-subtitle">Filter summary by date range.</p>
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
            <?php endif; ?>

            <?php if ($userRole === 'receptionist' || $userRole === 'administrator'): ?>
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

            <?php if ($userRole === 'radiology_staff' || $userRole === 'administrator'): ?>
            <section class="view <?= $userRole === 'radiology_staff' ? 'active' : '' ?>" id="manageView" aria-labelledby="manageTitle">
                <h2 id="manageTitle" class="sr-only">Manage queue</h2>
                <div class="manage-grid" id="manageGrid"></div>
            </section>
            <?php endif; ?>


            <?php if ($userRole !== 'radiology_staff'): ?>
            <section class="view" id="adsView" aria-labelledby="adsTitle">
                <h2 id="adsTitle" class="sr-only">Ads</h2>
                <div class="ads-dashboard">
                    <section class="panel ads-card">
                        <div class="ads-panel-head">
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <h3 style="margin-right: 10px;">Ad library</h3>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button class="ads-add-btn" id="nextAdCommandBtn" type="button" style="background: var(--border-color); color: var(--text-color); border-color: var(--border-color);" title="Force public display to play next ad">
                                    Skip Next
                                </button>
                                <button class="ads-add-btn" id="showAdFormBtn" type="button">
                                    <span>+</span> Add ad
                                </button>
                            </div>
                        </div>

                        <form class="ads-form" id="adForm" hidden>
                            <div class="ads-form-head">
                                <h3>Add a new ad</h3>
                                <button class="ads-icon-btn" id="cancelAdFormBtn" type="button" aria-label="Close ad form">x</button>
                            </div>
                            
                            <!-- New Drag & Drop UI -->
                            <div class="pattern-upload-container">
                                <div class="upload-dropzone" id="adDropzone">
                                    <input id="adFileInput" type="file" accept="image/*,video/mp4,video/webm,video/quicktime" multiple hidden>
                                    
                                    <div class="dropzone-content">
                                        <div class="dropzone-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                        </div>
                                        <div class="dropzone-text">
                                            <p>Drop files here or <button type="button" class="browse-btn" onclick="document.getElementById('adFileInput').click()">browse files</button></p>
                                            <p class="dropzone-subtext">Maximum file size: 50MB • Maximum files: 10</p>
                                        </div>
                                    </div>
                                </div>

                                <div style="margin: 20px 0; display: flex; align-items: center; justify-content: center; color: #a1a1aa; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px;">
                                    <hr style="flex: 1; border: none; border-top: 1px solid #e4e4e7; margin: 0 10px;">
                                    OR
                                    <hr style="flex: 1; border: none; border-top: 1px solid #e4e4e7; margin: 0 10px;">
                                </div>
                                
                                <div class="youtube-input-group" style="margin-bottom: 20px;">
                                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #3f3f46;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color: #ef4444;"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"/><polygon fill="#fff" points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                                        YouTube Link
                                    </label>
                                    <input type="text" id="youtubeUrlInput" placeholder="https://www.youtube.com/watch?v=..." style="width: 100%; padding: 10px 12px; border: 1px solid #d4d4d8; border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#087447';" onblur="this.style.borderColor='#d4d4d8';">
                                </div>

                                <div class="upload-files-section" id="uploadFilesSection" hidden>
                                    <div class="files-header">
                                        <h3 id="filesCountHeader">Files (0)</h3>
                                        <div class="files-actions">
                                            <button type="button" class="btn-outline-sm" onclick="document.getElementById('adFileInput').click()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>
                                                Add files
                                            </button>
                                            <button type="button" class="btn-outline-sm" id="clearFilesBtn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                Remove all
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="files-grid" id="uploadFilesGrid">
                                        <!-- Rendered dynamically in JS -->
                                    </div>
                                </div>
                            </div>
                            <!-- End New Drag & Drop UI -->

                            <div class="ads-settings-group">
                                <label class="ads-duration-control">
                                    <span>Seconds on screen</span>
                                    <input id="adDurationInput" type="number" min="3" max="60" value="8">
                                </label>
                                <label class="ads-check-control">
                                    <input id="adActiveInput" type="checkbox" checked>
                                    <span>Show this ad on the public display</span>
                                </label>
                            </div>

                            <div class="ads-form-actions">
                                <button class="ads-save-btn" id="saveAdBtn" type="button">Upload</button>
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
            <?php endif; ?>


        </main>
        <div class="custom-confirm-overlay" id="customConfirmOverlay">
            <div class="custom-confirm-box">
                <div class="custom-confirm-header">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <h4>Confirmation Required</h4>
                </div>
                <div class="custom-confirm-body" id="customConfirmMessage">
                    Are you sure you want to proceed?
                </div>
                <div class="custom-confirm-footer">
                    <button class="btn-cancel" id="customConfirmCancel">Cancel</button>
                    <button class="btn-confirm" id="customConfirmOk">Yes, Proceed</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Custom Modal Logic for Alerts & Confirms
        function showModal({ title, message, type = 'confirm', icon = 'warning', confirmText = 'Yes, Proceed', cancelText = 'Cancel', headerColor = 'var(--maroon)' }, callback) {
            const overlay = document.getElementById('customConfirmOverlay');
            const titleEl = overlay.querySelector('.custom-confirm-header h4');
            const iconSvg = overlay.querySelector('.custom-confirm-header svg');
            const msgEl = document.getElementById('customConfirmMessage');
            const btnCancel = document.getElementById('customConfirmCancel');
            const btnOk = document.getElementById('customConfirmOk');
            const headerEl = overlay.querySelector('.custom-confirm-header');

            titleEl.textContent = title;
            msgEl.textContent = message;
            btnOk.textContent = confirmText;
            btnCancel.textContent = cancelText;

            // Set icon and header background dynamically
            headerEl.style.backgroundColor = headerColor;
            
            if (icon === 'warning') {
                iconSvg.innerHTML = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>';
                iconSvg.style.color = 'var(--green-soft)';
            } else if (icon === 'info') {
                iconSvg.innerHTML = '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>';
                iconSvg.style.color = '#fff';
            } else if (icon === 'error') {
                iconSvg.innerHTML = '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>';
                iconSvg.style.color = '#fff';
            } else if (icon === 'logout') {
                iconSvg.innerHTML = '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>';
                iconSvg.style.color = '#fff';
            } else if (icon === 'trash') {
                iconSvg.innerHTML = '<polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line>';
                iconSvg.style.color = '#fff';
            } else {
                iconSvg.innerHTML = '';
            }

            if (type === 'alert') {
                btnCancel.style.display = 'none';
                btnOk.style.marginLeft = 'auto'; // optional, pushes button to right
            } else {
                btnCancel.style.display = 'inline-block';
                btnOk.style.marginLeft = '0';
            }

            overlay.classList.add('active');

            const cleanup = () => {
                overlay.classList.remove('active');
                btnCancel.removeEventListener('click', onCancel);
                btnOk.removeEventListener('click', onOk);
            };

            const onCancel = () => { cleanup(); if (callback) callback(false); };
            const onOk = () => { cleanup(); if (callback) callback(true); };

            btnCancel.addEventListener('click', onCancel);
            btnOk.addEventListener('click', onOk);
        }

        function showConfirm(options, callback) {
            if (typeof options === 'string') {
                options = { message: options };
            }
            showModal({ 
                title: options.title || 'Confirmation Required', 
                message: options.message, 
                type: 'confirm', 
                icon: options.icon || 'warning',
                headerColor: options.headerColor || 'var(--maroon)',
                confirmText: options.confirmText || 'Yes, Proceed'
            }, callback);
        }

        window.alert = function(message) {
            showModal({ title: 'Alert', message: message, type: 'alert', icon: 'info', confirmText: 'OK', headerColor: 'var(--maroon)' });
        };
        window.showAlert = window.alert;

        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                showConfirm({ title: 'Sign Out', message: 'Are you sure you want to sign out?', icon: 'logout', confirmText: 'Sign Out' }, (confirmed) => {
                    if (confirmed) {
                        localStorage.removeItem('rqs_filter_start');
                        localStorage.removeItem('rqs_filter_end');
                        localStorage.removeItem('rqs_filter_category');
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/logout';
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        }

        const currentUserName = <?= json_encode($userName ?? '') ?>;
        const currentRole = <?= json_encode($userRole ?? '') ?>;
        const procedures = {
            xray: { name: 'X-Ray', shortName: 'X-RAY', chartLabel: 'X-Ray', prefix: 'XR', maxServing: 2 },
            ultrasound: { name: 'Ultrasound', shortName: 'UTS', chartLabel: 'Ultrasound', prefix: 'UT', maxServing: 2 },
            ctscan: { name: 'CT Scan', shortName: 'CTS', chartLabel: 'CT Scan', prefix: 'CT', maxServing: 1 }
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

        // ----------------------------------------------------
        // ADVERTISEMENTS LOGIC (API Integration)
        // ----------------------------------------------------
        async function fetchAds() {
            try {
                const response = await fetch('/api/ads?_=' + new Date().getTime());
                return await response.json();
            } catch (err) {
                console.error('Error fetching ads', err);
                return { status: 'error', data: [] };
            }
        }

        async function loadAds() {
            const result = await fetchAds();
            adLibrary = result.data || [];
            
            if (result.settings && result.settings.display_mode) {
                window.currentAdMode = result.settings.display_mode;
                document.querySelectorAll('.mode-btn').forEach(btn => {
                    if (btn.dataset.mode === window.currentAdMode) {
                        btn.style.background = 'var(--primary-color)';
                        btn.style.color = '#fff';
                        btn.style.borderColor = 'var(--primary-color)';
                    } else {
                        btn.style.background = 'transparent';
                        btn.style.color = 'var(--text-color)';
                        btn.style.borderColor = 'var(--border-color)';
                    }
                });
            }

            renderAdsView();
            renderAdsPreview();
        }

        window.currentAdMode = 'all';

        window.setAdMode = async function(mode) {
            window.currentAdMode = mode;
            document.querySelectorAll('.mode-btn').forEach(btn => {
                if (btn.dataset.mode === mode) {
                    btn.style.background = 'var(--primary-color)';
                    btn.style.color = '#fff';
                    btn.style.borderColor = 'var(--primary-color)';
                } else {
                    btn.style.background = 'transparent';
                    btn.style.color = 'var(--text-color)';
                    btn.style.borderColor = 'var(--border-color)';
                }
            });

            try {
                const response = await fetch('/api/ads/settings', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ display_mode: mode })
                });
                renderAdsPreview(); // Update instantly
            } catch (err) {
                console.error('Error updating display mode', err);
            }
        };

        async function sendCommand(action, adId = null) {
            try {
                const response = await fetch('/api/ads/command', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: action, ad_id: adId })
                });
                const result = await response.json();
                if (result.status !== 'success') {
                    showAlert('Failed to send command: ' + (result.message || 'Unknown error'));
                }
            } catch (err) {
                console.error('Error sending command', err);
            }
        }

        function deleteAd(id) {
            showConfirm({ title: 'Delete Advertisement', message: 'Are you sure you want to delete this advertisement?', icon: 'trash', confirmText: 'Delete' }, async (confirmed) => {
                if (!confirmed) return;
            try {
                const response = await fetch('/api/ads/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    loadAds();
                } else {
                    alert('Error deleting ad: ' + (result.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to server.');
            }
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
                        fetchedServing.forEach((t) => {
                            const slot = t.servingSlot != null ? t.servingSlot : 0;
                            if (slot < maxSlots) {
                                state.serving[key][slot] = t;
                            }
                        });
                    });

                    state.completed = data.completed.map(parseTicketDates);
                    if (!state.analyticsLoaded) {
                        state.analyticsLoaded = true;
                        if (typeof applyDateShortcut === 'function' && document.getElementById('startDateFilter')) {
                            const savedStart = localStorage.getItem('rqs_filter_start');
                            const savedEnd = localStorage.getItem('rqs_filter_end');
                            const savedCat = localStorage.getItem('rqs_filter_category');
                            
                            if (savedStart !== null || savedEnd !== null || savedCat !== null) {
                                document.getElementById('startDateFilter').value = savedStart || '';
                                document.getElementById('endDateFilter').value = savedEnd || '';
                                if (savedCat) document.getElementById('categoryFilter').value = savedCat;
                                fetchHistoricalTickets(savedStart || '', savedEnd || '');
                                syncDateShortcutState();
                            } else {
                                applyDateShortcut('year');
                            }
                        } else {
                            fetchHistoricalTickets('', ''); 
                        }
                    }

                    render();
                }
            } catch (error) {
                console.warn('Unable to load live queue state.', error);
            }
        }

        async function fetchHistoricalTickets(start, end) {
            try {
                let url = '/api/reports/tickets';
                const params = [];
                if (start) params.push(`start=${start}`);
                if (end) params.push(`end=${end}`);
                if (params.length > 0) url += '?' + params.join('&');
                
                const response = await fetch(url);
                const result = await response.json();
                
                if (result.status === 'success') {
                    state.generatedTickets = result.data.generatedTickets.map(parseTicketDates);
                    state.completedTicketsHistory = result.data.completedTickets.map(parseTicketDates);
                    renderDashboard();
                }
            } catch (error) {
                console.warn('Unable to load historical reports.', error);
            }
        }

        function loadSavedState() {
            fetchQueueState();
            setInterval(fetchQueueState, 2000);
        }

        function saveQueueState() {
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

        if (generateButton) {
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
                    const t = {
                        id: ticketData.ticket_code,
                        procedureKey: ticketData.procedure_code.toLowerCase(),
                        procedure: ticketData.procedure_name,
                        patientType: ticketData.category_code,
                        createdAt: new Date(ticketData.created_at)
                    };
                    
                    
                    state.latestTicket = t;
                    
                    formNote.textContent = `${t.id} added to ${t.procedure}.`;
                    fetchQueueState();
                    printTicket(t);
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
        }

        function printTicket(ticket) {
            const now = ticket.createdAt || new Date();
            const dateStr = now.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = now.toLocaleTimeString('en-PH', { hour: '2-digit', minute: '2-digit' });

            const printWindow = window.open('', '_blank', 'width=350,height=500');
            if (!printWindow) return;

            printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
<title>Queue Ticket - ${ticket.id}</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Inter', Arial, sans-serif;
        width: 280px;
        margin: 0 auto;
        padding: 20px 10px;
        text-align: center;
        color: #1a1a1a;
    }
    .hospital-name {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #555;
        margin-bottom: 2px;
    }
    .dept-name {
        font-size: 10px;
        color: #888;
        margin-bottom: 12px;
    }
    .divider {
        border: none;
        border-top: 1px dashed #ccc;
        margin: 10px 0;
    }
    .label {
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 4px;
    }
    .ticket-number {
        font-size: 48px;
        font-weight: 800;
        letter-spacing: 2px;
        margin: 8px 0;
        line-height: 1;
    }
    .procedure-name {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .patient-type {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 10px;
        background: ${ticket.patientType === 'IPD' ? '#f3e0e4' : '#e0f0e8'};
        color: ${ticket.patientType === 'IPD' ? '#921d31' : '#2d6a4f'};
        margin-bottom: 12px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        padding: 3px 8px;
    }
    .footer {
        margin-top: 14px;
        font-size: 9px;
        color: #aaa;
        line-height: 1.4;
    }
    @media print {
        body { width: 100%; padding: 10px 5px; }
        @page { size: 80mm auto; margin: 0; }
    }
</style>
</head>
<body>
    <div class="hospital-name">The Good Clinic</div>
    <div class="dept-name">Radiology Department</div>
    <hr class="divider">
    <div class="label">Queue Number</div>
    <div class="ticket-number">${ticket.id}</div>
    <div class="procedure-name">${ticket.procedure}</div>
    <div class="patient-type">${ticket.patientType === 'IPD' ? 'In-Patient (IPD)' : 'Out-Patient (OPD)'}</div>
    <hr class="divider">
    <div class="info-row"><span>Date</span><span>${dateStr}</span></div>
    <div class="info-row"><span>Time</span><span>${timeStr}</span></div>
    <hr class="divider">
    <div class="footer">
        Please wait for your number to be called.<br>
        Thank you for your patience.
    </div>
</body>
</html>`);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 400);
        }

        function updateGenerateState() {
            const ready = state.selectedProcedure && state.selectedPatient;
            generateButton.disabled = !ready;
            formNote.textContent = ready
                ? 'Ready to generate queue number.'
                : 'Select a procedure and patient category to continue.';
        }

        async function callNext(key, slotIndex = 0) {
            try {
                const response = await fetch('/api/queue/call', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ procedure_key: key, slot_index: slotIndex })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    fetchQueueState();
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
                    fetchQueueState();
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
            renderLiveMonitor();
        }

        function renderLatestTicket() {
            const latestTicket = document.getElementById('latestTicket');
            if (!latestTicket) return; // Radiology staff doesn't have this UI element

            if (!state.latestTicket) {
                ['xray','ultrasound','ctscan'].forEach(k => latestTicket.classList.remove('proc-' + k));
                latestTicket.innerHTML = '<p>No ticket generated yet this session.</p>';
                return;
            }

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
            if (!manageGrid) return;

            // Map room-specific accounts to their allowed procedure key and slot
            const roomMap = {
                'X-Ray 1': { key: 'xray', slot: 0 },
                'X-Ray 2': { key: 'xray', slot: 1 },
                'Ultrasound 1': { key: 'ultrasound', slot: 0 },
                'Ultrasound 2': { key: 'ultrasound', slot: 1 },
                'CT Scan': { key: 'ctscan', slot: 0 }
            };
            const myRoom = roomMap[currentUserName] || null;

            if (myRoom) {
                manageGrid.style.display = 'flex';
                manageGrid.style.justifyContent = 'center';
                manageGrid.style.alignItems = 'center';
                manageGrid.style.minHeight = '70vh';
            } else {
                manageGrid.style.display = '';
                manageGrid.style.justifyContent = '';
                manageGrid.style.alignItems = '';
                manageGrid.style.minHeight = '';
            }

            manageGrid.innerHTML = Object.entries(procedures)
                .filter(([key]) => {
                    // Room-specific accounts only see their assigned procedure
                    if (myRoom) return key === myRoom.key;
                    return true;
                })
                .map(([key, procedure]) => {
                const servingList = state.serving[key];
                const waiting = state.queues[key];
                const maxSlots = procedure.maxServing || 1;
                const servingSlots = Array.from({ length: maxSlots }, (_, index) => servingList[index] || null);
                const servingCount = servingSlots.filter(Boolean).length;

                const servingHtml = servingSlots
                    .map((ticket, slotIndex) => {
                        // Room-specific accounts only see their assigned slot
                        if (myRoom && slotIndex !== myRoom.slot) return '';

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
                                    <button class="primary-action small" type="button" ${(!ticket && waiting.length > 0) ? '' : 'disabled'} onclick="callNext('${key}', ${slotIndex})">
                                        <span class="call-icon"></span> Call next
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');

                // Room-specific accounts: hide the waiting list, only show their slot
                const showWaitingList = !myRoom;

                const cardStyle = myRoom 
                    ? 'padding: 24px; width: 100%; max-width: 460px; transform: scale(1.3); transform-origin: center; box-shadow: 0 20px 40px rgba(0,0,0,0.1);' 
                    : 'padding: 18px;';

                return `
                    <section class="panel rqs-exam-col manage-card" style="${cardStyle}">
                        <div class="rqs-exam-header">
                            <h3>${procedure.name}</h3>
                            <span class="rqs-count-chip">${waiting.length} waiting${maxSlots > 1 && !myRoom ? ` · ${servingCount}/${maxSlots} slots` : ''}</span>
                        </div>

                        ${servingHtml}

                        ${showWaitingList ? `
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
                        ` : ''}
                    </section>
                `;
            }).join('');
        }

        function renderDashboard() {
            const startDateFilter = document.getElementById('startDateFilter');
            if (!startDateFilter) return;

            const startDateValue = startDateFilter.value;
            const endDateValue = document.getElementById('endDateFilter').value;
            const categoryValue = document.getElementById('categoryFilter').value;
            const filteredGeneratedTickets = filterAnalyticsTickets(state.generatedTickets, null, null, categoryValue, 'createdAt');
            const filteredCompletedTickets = filterAnalyticsTickets(state.completedTicketsHistory || [], null, null, categoryValue, 'completedAt');
            
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
            const startStr = startDate ? toDateInputValue(startDate) : '';
            const endStr = endDate ? toDateInputValue(endDate) : '';
            document.getElementById('startDateFilter').value = startStr;
            document.getElementById('endDateFilter').value = endStr;
            localStorage.setItem('rqs_filter_start', startStr);
            localStorage.setItem('rqs_filter_end', endStr);
            fetchHistoricalTickets(startStr, endStr);
            syncDateShortcutState();
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
            if (tickets.length === 0) {
                chart.innerHTML = `
                    <div style="grid-column: 1 / -1; grid-row: 1 / -1; display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; padding: 48px 0; border: 1px dashed #e4e4e7; border-radius: 8px; background: #fafafa;">
                        <div style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; border-radius:8px; background: #f4f4f5; margin-bottom:16px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #71717a;"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                        </div>
                        <h4 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #18181b;">No data yet</h4>
                        <p style="margin: 0; font-size: 14px; color: #71717a; text-align: center;">No data available for this date range.</p>
                    </div>`;
                return;
            }

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
            if (tickets.length === 0) {
                pie.style.background = 'conic-gradient(#eee8df 0 100%)';
                pie.innerHTML = '<span>0</span>';
                legend.innerHTML = '<div style="color:var(--muted)">No data available</div>';
                return;
            }
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

        function renderAdsView() {
            const list = document.getElementById('adsList');
            if (!list) return;

            if (!adLibrary.length) {
                list.innerHTML = '<div class="ads-empty-row">No ads added yet. Add an image or video to start the public display rotation.</div>';
                return;
            }

            list.innerHTML = adLibrary.map((ad) => {
                const isVideo = (ad.type || '').startsWith('video/');
                const isYoutube = ad.type === 'youtube';
                
                let thumbHtml = '';
                if (isYoutube) {
                    let videoId = '';
                    const match = ad.src.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?]+)/);
                    if (match) videoId = match[1];
                    thumbHtml = videoId ? `<img src="https://img.youtube.com/vi/${videoId}/default.jpg" alt="YouTube Thumbnail">` : `<div style="background:#000;width:100%;height:100%;"></div>`;
                } else if (isVideo) {
                    thumbHtml = `<video src="${ad.src}" muted playsinline></video>`;
                } else {
                    thumbHtml = `<img src="${ad.src}" alt="${escapeHtml(ad.name)}">`;
                }

                return `
                    <div class="ad-row">
                        <span class="ad-grip">::</span>
                        <div class="ad-thumb">
                            ${thumbHtml}
                            <span>${(isVideo || isYoutube) ? '▶' : '▧'}</span>
                        </div>
                        <div class="ad-main">
                            <strong>${escapeHtml(ad.name)}</strong>
                            <small>${isYoutube ? 'Auto - Plays until video ends (YouTube)' : ad.duration + 's on screen'}</small>
                        </div>
                        <button class="ads-icon-btn btn-play-ad" type="button" onclick="playSelectedAd(${ad.id})" aria-label="Play this ad" title="Play this ad">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        </button>
                        <button class="ads-icon-btn btn-delete-ad" type="button" onclick="deleteAd(${ad.id})" aria-label="Delete ad" title="Remove Advertisement">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
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
            const ytInput = document.getElementById('youtubeUrlInput');
            
            selectedAdFile = null;
            selectedAdDataUrl = '';
            
            if (fileInput) fileInput.value = '';
            if (fileNote) fileNote.textContent = 'No file selected.';
            if (durationInput) {
                durationInput.value = 8;
                durationInput.disabled = false;
                const durLabel = durationInput.closest('label').querySelector('span');
                if (durLabel) durLabel.textContent = 'Seconds on screen';
            }
            if (ytInput) ytInput.value = '';
            if (activeInput) activeInput.checked = true;
            if (form) form.hidden = true;
        }

        async function toggleAdStatus(id) {
            // Placeholder: Not implemented in Phase 6 backend yet
        }

        async function editAdDuration(id) {
            // Placeholder: Not implemented in Phase 6 backend yet
        }

        // The deleteAd function is defined above


        function getActivePreviewAds() {
            let activeAds = adLibrary.filter((ad) => ad.active);
            if (window.currentAdMode === 'images_only') {
                activeAds = activeAds.filter(ad => ad.type !== 'youtube');
            } else if (window.currentAdMode === 'youtube_only') {
                activeAds = activeAds.filter(ad => ad.type === 'youtube');
            }
            return activeAds;
        }

        async function playSelectedAd(adId) {
            const ad = adLibrary.find(ad => ad.id == adId);
            if (!ad) return;
            if (!ad.active) {
                showAlert('This ad must be active before it can play on the public display.');
                return;
            }
            if (!getActivePreviewAds().some(ad => ad.id == adId)) {
                await window.setAdMode('all');
            }
            adsPreviewIndex = getActivePreviewAds().findIndex(ad => ad.id == adId);
            renderAdsPreview();
            await sendCommand('play_ad', adId);
        }

        function playNextAd() {
            adsPreviewIndex += 1;
            const adId = renderAdsPreview();
            if (adId != null) sendCommand('play_ad', adId);
        }

        function renderAdsPreview() {
            const stage = document.getElementById('adsPreviewStage');
            if (!stage) return;
            clearTimeout(adsPreviewTimer);
            if (window.ytPreviewCheckInterval) clearInterval(window.ytPreviewCheckInterval);
            if (window.currentYtPreviewPlayer && typeof window.currentYtPreviewPlayer.destroy === 'function') {
                try { window.currentYtPreviewPlayer.destroy(); } catch (e) {}
            }
            window.currentYtPreviewPlayer = null;

            const activeAds = getActivePreviewAds();

            if (!activeAds.length) {
                stage.innerHTML = '<div class="ads-preview-empty">No active ads yet for this mode.</div>';
                return;
            }

            if (adsPreviewIndex >= activeAds.length) adsPreviewIndex = 0;
            const ad = activeAds[adsPreviewIndex];
            const isVideo = (ad.type || '').startsWith('video/');
            const isYoutube = ad.type === 'youtube';

            let mediaHtml = '';
            let videoId = '';
            if (isYoutube) {
                const match = ad.src.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?]+)/);
                if (match) videoId = match[1];
                if (videoId) {
                    mediaHtml = `<div id="yt-preview-display" class="ads-preview-media" style="width:100%;height:auto;aspect-ratio:16/9;max-height:100%;background:#000;"></div>`;
                } else {
                    mediaHtml = `<div style="background:#000;width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#fff;">Invalid YouTube Link</div>`;
                }
            } else if (isVideo) {
                mediaHtml = `<video class="ads-preview-media" src="${ad.src}" autoplay muted playsinline></video>`;
            } else {
                mediaHtml = `<img class="ads-preview-media" src="${ad.src}" alt="${escapeHtml(ad.name)}">`;
            }

            stage.innerHTML = `
                <div class="ads-preview-brand"><span>A+</span> TAGUM GLOBAL</div>
                ${mediaHtml}
                <div class="ads-preview-dot"></div>
            `;

            const next = () => {
                adsPreviewIndex = (adsPreviewIndex + 1) % activeAds.length;
                renderAdsPreview();
            };

            if (isYoutube) {
                if (videoId) {
                    if (!(window.YT && window.YT.Player) && !document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
                        const tag = document.createElement('script');
                        tag.src = "https://www.youtube.com/iframe_api";
                        const firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    }
                    window.ytPreviewCheckInterval = setInterval(() => {
                        if (window.YT && window.YT.Player) {
                            clearInterval(window.ytPreviewCheckInterval);
                            window.currentYtPreviewPlayer = new YT.Player('yt-preview-display', {
                                videoId,
                                playerVars: { autoplay: 1, playsinline: 1, controls: 1, rel: 0, origin: window.location.origin },
                                events: {
                                    'onAutoplayBlocked': (event) => {
                                        event.target.mute();
                                        event.target.playVideo();
                                    },
                                    'onStateChange': (event) => {
                                        if (event.data === 0) {
                                            next();
                                        }
                                    }
                                }
                            });
                        }
                    }, 100);
                }
            } else if (isVideo) {
                const video = stage.querySelector('video');
                if (video) video.onended = next;

            } else {
                adsPreviewTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            }
            return ad.id;
        }

        const startDateFilter = document.getElementById('startDateFilter');
        if (startDateFilter) {
            startDateFilter.addEventListener('change', function() {
                localStorage.setItem('rqs_filter_start', this.value);
                fetchHistoricalTickets(this.value, document.getElementById('endDateFilter').value);
                syncDateShortcutState();
            });
            document.getElementById('endDateFilter').addEventListener('change', function() {
                localStorage.setItem('rqs_filter_end', this.value);
                fetchHistoricalTickets(document.getElementById('startDateFilter').value, this.value);
                syncDateShortcutState();
            });
            document.getElementById('categoryFilter').addEventListener('change', function() {
                localStorage.setItem('rqs_filter_category', this.value);
                renderDashboard();
            });
            document.getElementById('downloadExcelBtn').addEventListener('click', downloadExcelReport);
            document.querySelectorAll('[data-range]').forEach((button) => {
                button.addEventListener('click', () => applyDateShortcut(button.dataset.range));
            });
        }
        const showAdFormBtn = document.getElementById('showAdFormBtn');
        if (showAdFormBtn) {
            const adForm = document.getElementById('adForm');
            const dropzone = document.getElementById('adDropzone');
            const fileInput = document.getElementById('adFileInput');
            const filesSection = document.getElementById('uploadFilesSection');
            const filesGrid = document.getElementById('uploadFilesGrid');
            const filesCountHeader = document.getElementById('filesCountHeader');

            const ytUrlInput = document.getElementById('youtubeUrlInput');
            const durInput = document.getElementById('adDurationInput');
            if (ytUrlInput && durInput) {
                ytUrlInput.addEventListener('input', () => {
                    updateUploadButtonLabel();
                    const durLabel = durInput.closest('label').querySelector('span');
                    if (ytUrlInput.value.trim().length > 0) {
                        durInput.disabled = true;
                        durLabel.textContent = 'Duration (Auto - Plays until video ends)';
                    } else {
                        durInput.disabled = false;
                        durLabel.textContent = 'Seconds on screen';
                    }
                });
            }
            
            let uploadFilesQueue = []; // Array of { file, id, preview, status, progress }

            function updateUploadButtonLabel() {
                const button = document.getElementById('saveAdBtn');
                if (button.disabled) return;
                const hasYoutube = ytUrlInput && ytUrlInput.value.trim() !== '';
                button.textContent = !hasYoutube && uploadFilesQueue.length > 1 ? 'Upload all' : 'Upload';
            }

            function renderUploadFiles() {
                updateUploadButtonLabel();
                if (uploadFilesQueue.length === 0) {
                    filesSection.hidden = true;
                    if (fileInput) fileInput.value = ''; // Allow re-selecting the same file
                    return;
                }
                filesSection.hidden = false;
                filesCountHeader.textContent = `Files (${uploadFilesQueue.length})`;
                
                filesGrid.innerHTML = uploadFilesQueue.map(item => {
                    const isImage = item.file.type.startsWith('image/');
                    return `
                    <div class="file-card">
                        <button type="button" class="file-card-remove" onclick="removeUploadFile('${item.id}')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                        <div class="file-card-preview">
                            ${isImage && item.preview ? `<img src="${item.preview}">` : `
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            `}
                            ${item.status === 'uploading' ? `
                                <div class="file-upload-overlay">
                                    <svg class="circular-progress" viewBox="0 0 48 48">
                                        <circle class="bg" cx="24" cy="24" r="20"></circle>
                                        <circle class="fg" cx="24" cy="24" r="20" style="stroke-dashoffset: ${125.6 * (1 - item.progress / 100)}"></circle>
                                    </svg>
                                </div>
                            ` : ''}
                        </div>
                        <div class="file-card-info">
                            <p class="file-card-name" title="${escapeHtml(item.file.name)}">${escapeHtml(item.file.name)}</p>
                            <span class="file-card-size">${(item.file.size / 1024 / 1024).toFixed(2)} MB</span>
                        </div>
                    </div>
                    `;
                }).join('');
            }

            window.removeUploadFile = function(id) {
                uploadFilesQueue = uploadFilesQueue.filter(f => f.id !== id);
                renderUploadFiles();
            };

            function handleNewFiles(files) {
                Array.from(files).forEach(file => {
                    if (uploadFilesQueue.length >= 10) return;
                    if (file.size > 50 * 1024 * 1024) return;
                    
                    const id = 'file_' + Math.random().toString(36).substr(2, 9);
                    const preview = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
                    
                    uploadFilesQueue.push({
                        file,
                        id,
                        preview,
                        status: 'pending',
                        progress: 0
                    });
                });
                renderUploadFiles();
            }

            fileInput.addEventListener('change', (e) => handleNewFiles(e.target.files));

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('dragging'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragging'), false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleNewFiles(files);
            }, false);

            document.getElementById('clearFilesBtn')?.addEventListener('click', () => {
                uploadFilesQueue = [];
                renderUploadFiles();
                fileInput.value = '';
            });

            showAdFormBtn.addEventListener('click', () => {
                adForm.hidden = false;
            });
            
            const nextAdCommandBtn = document.getElementById('nextAdCommandBtn');
            if (nextAdCommandBtn) {
                nextAdCommandBtn.addEventListener('click', playNextAd);
            }


            
            function resetAdForm() {
                adForm.hidden = true;
                uploadFilesQueue = [];
                renderUploadFiles();
                fileInput.value = '';
            }

            document.getElementById('cancelAdFormBtn').addEventListener('click', resetAdForm);
            document.getElementById('cancelAdFormBtn2').addEventListener('click', resetAdForm);

            document.getElementById('saveAdBtn').addEventListener('click', async (event) => {
                event.preventDefault();
                
                const ytUrlInput = document.getElementById('youtubeUrlInput');
                const hasYoutube = ytUrlInput && ytUrlInput.value.trim() !== '';

                if (!hasYoutube && uploadFilesQueue.length === 0) {
                    showAlert('Please choose at least one image or video, or enter a YouTube link.');
                    return;
                }

                const submitBtn = document.getElementById('saveAdBtn');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Uploading...';
                submitBtn.disabled = true;

                const duration = Math.max(3, Math.min(60, Number(document.getElementById('adDurationInput').value) || 8));
                const active = document.getElementById('adActiveInput').checked;
                let uploadFailed = false;

                if (hasYoutube) {
                    const formData = new FormData();
                    formData.append('youtube_url', ytUrlInput.value.trim());
                    formData.append('duration', duration);
                    formData.append('active', active);

                    try {
                        const response = await fetch('/api/ads', { method: 'POST', body: formData });
                        const result = await response.json();
                        if (!response.ok || result.status !== 'success') {
                            uploadFailed = true;
                            showAlert('Failed to save YouTube link: ' + (result.error || result.message || 'Please try again.'));
                        }
                    } catch (err) {
                        uploadFailed = true;
                        showAlert('Unable to save the YouTube link. Please try again.');
                        console.error('Error saving YouTube link', err);
                    }
                } else {
                    for (let i = 0; i < uploadFilesQueue.length; i++) {
                        const item = uploadFilesQueue[i];
                        item.status = 'uploading';
                        item.progress = 30; // simulated start progress
                        renderUploadFiles();

                        const formData = new FormData();
                        formData.append('media', item.file);
                        formData.append('duration', duration);
                        formData.append('active', active);

                        try {
                            const response = await fetch('/api/ads', { method: 'POST', body: formData });
                            const result = await response.json();
                            item.progress = 100;
                            renderUploadFiles();
                            if (!response.ok || result.status !== 'success') {
                                uploadFailed = true;
                                showAlert('Failed to upload ' + item.file.name + ': ' + (result.error || result.message || 'Please try again.'));
                            } else {
                                item.status = 'complete';
                            }
                        } catch (err) {
                            uploadFailed = true;
                            showAlert('Unable to upload ' + item.file.name + '. Please try again.');
                            console.error('Upload error for', item.file.name, err);
                        }
                    }
                }

                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                if (uploadFailed) {
                    uploadFilesQueue = uploadFilesQueue.filter(item => item.status !== 'complete');
                    renderUploadFiles();
                } else {
                    if (ytUrlInput) ytUrlInput.value = '';
                    resetAdForm();
                }
                await loadAds();
            });
        }

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
            const filteredCompleted = filterAnalyticsTickets(state.completedTicketsHistory || [], startDateValue, endDateValue, categoryValue, 'completedAt');

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
            const filterSuffix = (startDateValue || endDateValue || categoryValue !== 'all')
                ? `_Filtered_${categoryLabel}`.replace(/\s+/g, '-')
                : '';
            const fileName = `Radiology_QMS_Report_${dateStamp}${filterSuffix}.xlsx`;

            XLSX.writeFile(wb, fileName);
        }
    </script>
</body>
</html>
