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
            xray: { title: 'X-RAY', spokenName: 'X-Ray', codeClass: 'XR', maxServing: 2 },
            ultrasound: { title: 'Ultrasound', spokenName: 'Ultrasound', codeClass: 'UT', maxServing: 2 },
            ctscan: { title: 'CT-Scan', spokenName: 'CT Scan', codeClass: 'CT', maxServing: 1 }
        };
        let audioEnabled = true;
        let lastServingIds = {};
        let isInitialLoad = true;

        async function fetchQueueState() {
            try {
                const response = await fetch('/api/queue');
                const result = await response.json();
                if (result.status === 'success') {
                    const data = result.data;
                    Object.keys(procedures).forEach((key) => {
                        const maxSlots = procedures[key].maxServing || 1;
                        const slotArray = Array(maxSlots).fill(null);
                        (data.serving[key] || []).forEach((t) => {
                            const slot = t.servingSlot != null ? t.servingSlot : 0;
                            if (slot < maxSlots) {
                                slotArray[slot] = t;
                            }
                        });
                        data.serving[key] = slotArray;
                    });
                    return data;
                }
                return {};
            } catch (error) {
                console.warn('Unable to fetch live queue state.', error);
                return {};
            }
        }

        // ----------------------------------------------------
        // ADVERTISEMENTS (API Integration)
        // ----------------------------------------------------
        let lastCommandTimestamp = 0;

        async function fetchAds() {
            try {
                const response = await fetch('/api/ads?_=' + new Date().getTime(), { cache: 'no-store' });
                const result = await response.json();
                return result;
            } catch (err) {
                console.warn('Error fetching ads', err);
                return { status: 'error', data: [] };
            }
        }

        function renderDisplayAd(ads) {
            const container = document.querySelector('.slideshow-container');
            if (!container || !ads || !ads.length) return;

            clearTimeout(publicAdTimer);
            if (window.ytCheckInterval) clearInterval(window.ytCheckInterval);
            
            if (window.currentYtPlayer && typeof window.currentYtPlayer.destroy === 'function') {
                try { window.currentYtPlayer.destroy(); } catch(e) {}
            }
            window.currentYtPlayer = null;
            
            if (publicAdIndex >= ads.length) publicAdIndex = 0;
            const ad = ads[publicAdIndex];
            
            const isVideo = (ad.type || '').startsWith('video/');
            const isYoutube = ad.type === 'youtube';
            let videoId = '';

            if (isYoutube) {
                const match = ad.src.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([^&?]+)/);
                if (match) videoId = match[1];

                if (videoId) {
                    container.innerHTML = `<div id="yt-player-display" class="display-ad-media active" style="width:100%;height:auto;aspect-ratio:16/9;max-height:100%;background:#000;"></div>`;
                } else {
                    container.innerHTML = `<div class="empty-ad-placeholder" style="display:flex;align-items:center;justify-content:center;height:100%;background:#000;color:#fff;">Invalid YouTube Link</div>`;
                }
            } else if (isVideo) {
                container.innerHTML = `<video class="display-ad-media active" src="${ad.src}" autoplay muted playsinline></video>`;
            } else {
                container.innerHTML = `<img class="display-ad-media active" src="${ad.src}" alt="${ad.name}">`;
            }

            const next = () => {
                publicAdIndex = (publicAdIndex + 1) % ads.length;
                renderDisplayAd(ads);
            };

            // Global next function for command jumping
            window.forceNextAd = next;

            if (isYoutube) {
                if (videoId) {
                    if (!(window.YT && window.YT.Player) && !document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) {
                        const tag = document.createElement('script');
                        tag.src = "https://www.youtube.com/iframe_api";
                        const firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    }
                    window.ytCheckInterval = setInterval(() => {
                        if (window.YT && window.YT.Player) {
                            clearInterval(window.ytCheckInterval);
                            window.currentYtPlayer = new YT.Player('yt-player-display', {
                                videoId,
                                playerVars: { autoplay: 1, playsinline: 1, controls: 1, rel: 0, origin: window.location.origin },
                                events: {
                                    'onReady': (event) => {
                                        event.target.playVideo();
                                        if (window.speechSynthesis && (window.speechSynthesis.pending || window.speechSynthesis.speaking)) {
                                            event.target.mute();
                                        }
                                    },
                                    'onAutoplayBlocked': (event) => {
                                        event.target.mute();
                                        event.target.playVideo();
                                    },
                                    'onStateChange': (event) => {
                                        if (event.data === 0) { // YT.PlayerState.ENDED
                                            next();
                                        }
                                    }
                                }
                            });
                        }
                    }, 100);
                }
            } else if (isVideo) {
                const video = container.querySelector('video');
                if (video) video.onended = next;

            } else {
                publicAdTimer = setTimeout(next, Math.max(3, ad.duration || 8) * 1000);
            }
        }

        async function refreshDisplayAds() {
            try {
                const result = await fetchAds();
                const ads = result.data || [];
                let activeAds = ads.filter(ad => ad.active);
                
                // Apply display mode filter
                if (result.settings && result.settings.display_mode) {
                    const mode = result.settings.display_mode;
                    if (mode === 'images_only') {
                        activeAds = activeAds.filter(ad => ad.type !== 'youtube');
                    } else if (mode === 'youtube_only') {
                        activeAds = activeAds.filter(ad => ad.type === 'youtube');
                    }
                }

                const container = document.querySelector('.slideshow-container');
                if (!activeAds.length) {
                    if (container) {
                        container.innerHTML = '<div class="empty-ad-placeholder" style="display:flex;align-items:center;justify-content:center;height:100%;color:#888;font-size:1.5rem;background:#f5f5f5;">No announcements</div>';
                    }
                    publicAdsSignature = '';
                    clearTimeout(publicAdTimer);
                    return;
                }
                const signature = activeAds.map((ad) => `${ad.id}:${ad.active}:${ad.duration}`).join('|');
                if (signature !== publicAdsSignature) {
                    publicAdsSignature = signature;
                    publicAdIndex = 0;
                    renderDisplayAd(activeAds);
                }

                // Handle operator commands
                if (result.command && result.command.timestamp > lastCommandTimestamp) {
                    lastCommandTimestamp = result.command.timestamp;
                    if (result.command.action === 'next_ad') {
                        if (window.forceNextAd) window.forceNextAd();
                    } else if (result.command.action === 'play_ad' && result.command.ad_id) {
                        const targetIndex = activeAds.findIndex(a => a.id == result.command.ad_id);
                        if (targetIndex !== -1) {
                            publicAdIndex = targetIndex;
                            renderDisplayAd(activeAds);
                        }
                    }
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

        window.speechUtterances = []; // Prevent Chrome garbage collection bug

        function speakAnnouncement(ticket, procedure, spokenLabel = procedure.spokenName) {
            if (!audioEnabled || !('speechSynthesis' in window)) return;

            // Force mute YouTube via API and postMessage fallback
            if (window.currentYtPlayer && typeof window.currentYtPlayer.mute === 'function') {
                try { window.currentYtPlayer.mute(); } catch (e) {}
            }
            const ytIframe = document.getElementById('yt-player-display');
            if (ytIframe && ytIframe.contentWindow) {
                try { ytIframe.contentWindow.postMessage('{"event":"command","func":"mute","args":""}', '*'); } catch (e) {}
            }
            // Mute local videos if any were unmuted manually
            document.querySelectorAll('.display-ad-media').forEach(media => {
                if (media.tagName === 'VIDEO') media.muted = true;
            });

            const message = `Queue number ${getSpokenTicketId(ticket.id)}, please proceed to ${spokenLabel}.`;
            window.speechSynthesis.cancel();
            
            const utterance1 = new SpeechSynthesisUtterance(message);
            utterance1.rate = 0.9;
            utterance1.pitch = 1;
            utterance1.volume = 1;
            
            const utterance2 = new SpeechSynthesisUtterance(message);
            utterance2.rate = 0.9;
            utterance2.pitch = 1;
            utterance2.volume = 1;

            const unmuteAds = () => {
                setTimeout(() => {
                    if (!window.speechSynthesis.pending && !window.speechSynthesis.speaking) {
                        if (window.currentYtPlayer && typeof window.currentYtPlayer.unMute === 'function') {
                            try { window.currentYtPlayer.unMute(); } catch (e) {}
                        }
                        const yt = document.getElementById('yt-player-display');
                        if (yt && yt.contentWindow) {
                            try { yt.contentWindow.postMessage('{"event":"command","func":"unMute","args":""}', '*'); } catch (e) {}
                        }
                    }
                }, 500);
            };

            utterance1.onend = () => {
                window.speechSynthesis.speak(utterance2);
            };

            utterance2.onend = () => {
                unmuteAds();
            };
            
            window.speechUtterances.push(utterance1, utterance2); // Keep references
            window.speechSynthesis.speak(utterance1);
        }

        function announceServingChanges(serving) {
            Object.entries(procedures).forEach(([key, procedure]) => {
                const tickets = Array.isArray(serving?.[key]) ? serving[key].filter(Boolean) : [];
                const currentIds = tickets.map((ticket) => ticket.id).join('|');
                const previousIds = lastServingIds[key] || '';

                if (!isInitialLoad && currentIds && currentIds !== previousIds) {
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

        async function refreshDisplayFromReception() {
            const state = await fetchQueueState();
            if (state.queues && state.serving) {
                renderIncoming(state.queues);
                renderServing(state.serving, state.completed || []);
                isInitialLoad = false;
            }
        }

        refreshDisplayFromReception();
        refreshDisplayAds();
        setInterval(refreshDisplayFromReception, 2000);
        setInterval(refreshDisplayAds, 500);
    });
</script>
