<?php
$pageData = $pageData ?? include __DIR__ . '/../data/public-display-data.php';
?>
<div class="main-container">
    <div class="left-column">
        <div class="ad-wrapper">
            <div class="slideshow-container">
                <?php foreach ($pageData['announcement']['slides'] as $index => $slide): ?>
                    <img src="<?= htmlspecialchars($slide, ENT_QUOTES, 'UTF-8') ?>" class="slide <?= $index === 0 ? 'active' : '' ?>">
                <?php endforeach; ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        let slides = document.querySelectorAll('.slideshow-container .slide');
                        let currentSlide = 0;
                        if(slides.length > 1) {
                            setInterval(() => {
                                slides[currentSlide].classList.remove('active');
                                currentSlide = (currentSlide + 1) % slides.length;
                                slides[currentSlide].classList.add('active');
                            }, 5000);
                        }
                    });
                </script>
            </div>
        </div>

        <div class="incoming-section">
            <div class="incoming-main-header">
                <span class="pulse-dot green"></span>
                INCOMING
            </div>
            <div class="incoming-boxes-wrapper">
                <?php foreach ($pageData['queues'] as $queue): ?>
                    <div class="incoming-box">
                        <div class="incoming-box-title queue-<?= str_replace('-', '', strtolower($queue['title'])) ?>">
                            <?= htmlspecialchars($queue['title'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <div class="incoming-box-body">
                            <?php for ($i = 0; $i < 4; $i++): ?>
                                <div class="incoming-box-row">
                                    <span class="incoming-code"><?= isset($queue['items'][$i]) ? htmlspecialchars($queue['items'][$i]['id'], ENT_QUOTES, 'UTF-8') : '' ?></span>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="right-column">
        <div class="header-row">
            <img src="/images/logo.png" alt="<?= htmlspecialchars($pageData['brand']['alt'], ENT_QUOTES, 'UTF-8') ?>" class="sidebar-logo">
            <div class="header-text">
                <div class="header-title">RADIOLOGY</div>
                <div class="header-date" id="live-date"></div>
                <div class="header-time" id="live-clock"></div>
            </div>
        </div>
        <script>
            function updateClock() {
                var now = new Date();
                var h = now.getHours();
                var m = now.getMinutes();
                var s = now.getSeconds();
                var ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12; h = h ? h : 12;
                m = m < 10 ? '0' + m : m;
                s = s < 10 ? '0' + s : s;
                document.getElementById('live-clock').textContent = h + ':' + m + ':' + s + ' ' + ampm;
                var days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                document.getElementById('live-date').textContent = days[now.getDay()] + ', ' + months[now.getMonth()] + ' ' + now.getDate() + ', ' + now.getFullYear();
            }
            updateClock();
            setInterval(updateClock, 1000);
        </script>

        <div class="serving-section">
            <div class="serving-main-header">
                <span class="pulse-dot red"></span>
                NOW SERVING
            </div>
            <div class="serving-columns">
                <div class="serving-col">
                    <div class="serving-col-header ipd-header">IPD</div>
                    <div class="serving-col-body">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php $item = $pageData['serving']['ipd'][$i] ?? null; ?>
                            <div class="serving-col-row <?= $item ? '' : 'empty-row' ?>">
                                <span class="serving-code <?= $item ? 'proc-' . htmlspecialchars($item['codeClass'] ?? '', ENT_QUOTES, 'UTF-8') : 'empty-code' ?>"><?= $item ? htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') : '&nbsp;' ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="serving-col">
                    <div class="serving-col-header opd-header">OPD</div>
                    <div class="serving-col-body">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <?php $item = $pageData['serving']['opd'][$i] ?? null; ?>
                            <div class="serving-col-row <?= $item ? '' : 'empty-row' ?>">
                                <span class="serving-code <?= $item ? 'proc-' . htmlspecialchars($item['codeClass'] ?? '', ENT_QUOTES, 'UTF-8') : 'empty-code' ?>"><?= $item ? htmlspecialchars($item['id'] ?? '', ENT_QUOTES, 'UTF-8') : '&nbsp;' ?></span>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storageKey = 'radiologyQueueState';
        const adsDbName = 'radiologyAdsDb';
        const adsStoreName = 'ads';
        let publicAdIndex = 0;
        let publicAdTimer = null;
        let publicAdsSignature = '';
        const procedures = {
            xray: { title: 'X-RAY', spokenName: 'X-Ray', codeClass: 'XR' },
            ultrasound: { title: 'Ultrasound', spokenName: 'Ultrasound', codeClass: 'UT' },
            ctscan: { title: 'CT-Scan', spokenName: 'CT Scan', codeClass: 'CT' }
        };
        let audioEnabled = true;
        let lastServingIds = {};

        function getQueueState() {
            try {
                return JSON.parse(localStorage.getItem(storageKey) || '{}');
            } catch (error) {
                return {};
            }
        }

        function openAdsDb() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open(adsDbName, 1);
                request.onupgradeneeded = () => {
                    const db = request.result;
                    if (!db.objectStoreNames.contains(adsStoreName)) {
                        db.createObjectStore(adsStoreName, { keyPath: 'id' });
                    }
                };
                request.onsuccess = () => resolve(request.result);
                request.onerror = () => reject(request.error);
            });
        }

        async function getDisplayAds() {
            const db = await openAdsDb();
            return new Promise((resolve, reject) => {
                const tx = db.transaction(adsStoreName, 'readonly');
                const request = tx.objectStore(adsStoreName).getAll();
                request.onsuccess = () => resolve(request.result
                    .filter((ad) => ad.active)
                    .sort((a, b) => (a.order || 0) - (b.order || 0)));
                request.onerror = () => reject(request.error);
            });
        }

        function renderDisplayAd(ads) {
            const container = document.querySelector('.slideshow-container');
            if (!container || !ads.length) return;
            clearTimeout(publicAdTimer);
            if (publicAdIndex >= ads.length) publicAdIndex = 0;

            const ad = ads[publicAdIndex];
            const isVideo = (ad.type || '').startsWith('video/');
            container.innerHTML = isVideo
                ? `<video class="display-ad-media active" src="${ad.src}" autoplay muted playsinline></video>`
                : `<img class="display-ad-media active" src="${ad.src}" alt="${ad.name}">`;

            const next = () => {
                publicAdIndex = (publicAdIndex + 1) % ads.length;
                renderDisplayAd(ads);
            };

            if (isVideo) {
                const video = container.querySelector('video');
                video.onended = next;
                publicAdTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            } else {
                publicAdTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            }
        }

        async function refreshDisplayAds() {
            try {
                const ads = await getDisplayAds();
                if (!ads.length) return;
                const signature = ads.map((ad) => `${ad.id}:${ad.active}:${ad.duration}`).join('|');
                if (signature !== publicAdsSignature) {
                    publicAdsSignature = signature;
                    publicAdIndex = 0;
                    renderDisplayAd(ads);
                }
            } catch (error) {
                console.warn('Unable to load display ads.', error);
            }
        }

        function renderIncoming(queues) {
            const wrapper = document.querySelector('.incoming-boxes-wrapper');
            if (!wrapper || !queues) return;

            wrapper.innerHTML = Object.entries(procedures).map(([key, procedure]) => {
                const items = Array.isArray(queues[key]) ? queues[key].slice(0, 4) : [];
                const rows = Array.from({ length: 4 }, (_, index) => `
                    <div class="incoming-box-row">
                        <span class="incoming-code">${items[index]?.id || ''}</span>
                    </div>
                `).join('');

                return `
                    <div class="incoming-box">
                        <div class="incoming-box-title queue-${key === 'ctscan' ? 'cts' : key === 'ultrasound' ? 'utz' : 'xray'}">
                            ${procedure.title}
                        </div>
                        <div class="incoming-box-body">${rows}</div>
                    </div>
                `;
            }).join('');
        }

        function getTicketNumberValue(ticket) {
            const match = String(ticket?.id || '').match(/\d+/);
            return match ? Number(match[0]) : Number.MAX_SAFE_INTEGER;
        }

        function getServingOrder(item, history) {
            const historyItem = history.find((ticket) => ticket.id === item.id);
            if (historyItem?.calledOrder) return Number(historyItem.calledOrder);
            if (item.calledOrder) return Number(item.calledOrder);
            if (item.calledAt) return new Date(item.calledAt).getTime();
            return getTicketNumberValue(item);
        }

        function renderServing(serving, history = []) {
            const ipdBody = document.querySelector('.ipd-header')?.nextElementSibling;
            const opdBody = document.querySelector('.opd-header')?.nextElementSibling;
            if (!ipdBody || !opdBody || !serving) return;

            const items = Object.entries(procedures)
                .flatMap(([key, procedure]) => {
                    const tickets = Array.isArray(serving[key]) ? serving[key] : [];
                    return tickets.filter(Boolean).map((ticket) => ({ ticket, procedure }));
                })
                .sort((a, b) => getServingOrder(a.ticket, history) - getServingOrder(b.ticket, history));

            const buildRows = (patientType) => {
                const patientItems = items
                    .filter((item) => item.ticket.patientType === patientType)
                    .slice(0, 5);

                const rows = patientItems
                    .map((item) => `
                        <div class="serving-col-row">
                            <span class="serving-code proc-${item.procedure.codeClass}">${item.ticket.id}</span>
                        </div>
                    `).join('');

                return rows + Array.from({ length: 5 - patientItems.length }, () => '<div class="serving-col-row empty-row"><span class="serving-code empty-code">&nbsp;</span></div>').join('');
            };

            ipdBody.innerHTML = buildRows('IPD');
            opdBody.innerHTML = buildRows('OPD');
            announceServingChanges(serving);
        }

        function getSpokenTicketId(ticketId) {
            return String(ticketId).replace(/([A-Z])/g, '$1 ').replace(/(\d)/g, '$1 ').replace(/\s+/g, ' ').trim();
        }

        function speakAnnouncement(ticket, procedure, spokenLabel = procedure.spokenName) {
            if (!audioEnabled || !('speechSynthesis' in window)) return;

            const message = `Queue number ${getSpokenTicketId(ticket.id)}, please proceed to ${spokenLabel}.`;
            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(message);
            utterance.rate = 0.9;
            utterance.pitch = 1;
            utterance.volume = 1;
            window.speechSynthesis.speak(utterance);
        }

        function announceServingChanges(serving) {
            Object.entries(procedures).forEach(([key, procedure]) => {
                const tickets = Array.isArray(serving?.[key]) ? serving[key].filter(Boolean) : [];
                const currentIds = tickets.map((ticket) => ticket.id).join('|');
                const previousIds = lastServingIds[key] || '';

                if (currentIds && currentIds !== previousIds) {
                    const previousSet = new Set(previousIds ? previousIds.split('|') : []);
                    tickets.forEach((ticket) => {
                        if (!previousSet.has(ticket.id)) {
                            const slotIndex = Array.isArray(serving?.[key])
                                ? serving[key].findIndex((servedTicket) => servedTicket?.id === ticket.id)
                                : -1;

                            const spokenLabel = key === 'xray' || key === 'ultrasound'
                                ? `${procedure.spokenName} ${slotIndex >= 0 ? slotIndex + 1 : 1}`
                                : procedure.spokenName;

                            speakAnnouncement(ticket, procedure, spokenLabel);
                        }
                    });
                }

                lastServingIds[key] = currentIds;
            });
        }

        function refreshDisplayFromReception() {
            const state = getQueueState();
            renderIncoming(state.queues);
            renderServing(state.serving, Array.isArray(state.calledTickets) ? state.calledTickets : []);
        }

        refreshDisplayFromReception();
        refreshDisplayAds();
        setInterval(refreshDisplayFromReception, 1000);
        setInterval(refreshDisplayAds, 5000);
        window.addEventListener('storage', refreshDisplayFromReception);
    });
</script>
