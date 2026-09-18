window.getSyncedPlaybackTime = function (state, serverTime) {
    if (!state) return 0;
    const position = Math.max(0, Number(state.position) || 0);
    const age = Math.max(0, Number(serverTime) - (Number(state.updated_at) || Number(serverTime)));
    return position + (state.playing ? age : 0);
};

window.shouldCorrectPlayback = function (currentTime, targetTime, tolerance = 1.25) {
    return Math.abs((Number(currentTime) || 0) - (Number(targetTime) || 0)) > tolerance;
};

window.isYouTubeLivePlayback = function (videoData, firstDuration, secondDuration, currentTime = 0) {
    if (videoData && (videoData.isLive === true || videoData.isLiveContent === true)) return true;
    const before = Number(firstDuration) || 0;
    const after = Number(secondDuration) || 0;
    if (before > 0 && after - before >= 0.5) return true;
    const current = Number(currentTime) || 0;
    return after >= 300 && current >= after - 60;
};

window.detectYouTubeLivePlayback = function (player, callback, delay = 750, maxSamples = 5) {
    let baselineDuration = 0;
    let videoData = {};
    try {
        baselineDuration = Number(player.getDuration()) || 0;
        if (typeof player.getVideoData === 'function') videoData = player.getVideoData() || {};
    } catch (error) {}
    if (window.isYouTubeLivePlayback(videoData, baselineDuration, baselineDuration)) {
        callback(true);
        return;
    }
    let samples = 0;
    const sample = () => {
        let duration = baselineDuration;
        let currentTime = 0;
        try { duration = Number(player.getDuration()) || baselineDuration; } catch (error) {}
        try { currentTime = Number(player.getCurrentTime()) || 0; } catch (error) {}
        if (baselineDuration <= 0 && duration > 0) {
            baselineDuration = duration;
        } else if (window.isYouTubeLivePlayback(videoData, baselineDuration, duration, currentTime)) {
            callback(true);
            return;
        }
        samples++;
        if (samples >= maxSamples) callback(false);
        else setTimeout(sample, delay);
    };
    setTimeout(sample, delay);
};

window.seekYouTubeLiveEdge = function (player) {
    try {
        const duration = Number(player.getDuration()) || 0;
        if (duration > 0) player.seekTo(Math.max(0, duration - 1), true);
        player.playVideo();
    } catch (error) {}
};
