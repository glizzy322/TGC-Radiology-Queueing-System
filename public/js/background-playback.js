// Keep the existing player and its position when browser visibility changes.
window.createBackgroundPlayback = function () {
    let player = null;
    let playing = false;
    let resumeWhenVisible = false;
    let retriedWhileHidden = false;

    function resume() {
        try {
            if (!player || !player.isPaused()) return;
            const result = player.play();
            if (result && typeof result.catch === 'function') result.catch(() => {});
        } catch (error) {
            // Autoplay or background restrictions may require returning to the tab.
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            resumeWhenVisible = playing;
            retriedWhileHidden = false;
            if (resumeWhenVisible) resume();
        } else {
            if (resumeWhenVisible) resume();
            resumeWhenVisible = false;
            retriedWhileHidden = false;
        }
    });

    function stateChanged(currentPlayer, state) {
        if (player !== currentPlayer) return;
        if (state === 'playing') {
            playing = true;
            if (document.hidden) resumeWhenVisible = true;
        } else if (state === 'paused') {
            const shouldResume = playing || resumeWhenVisible;
            playing = false;
            if (document.hidden && shouldResume) {
                resumeWhenVisible = true;
                // Retry once per visibility cycle, avoiding a rejected-play loop.
                if (!retriedWhileHidden) {
                    retriedWhileHidden = true;
                    resume();
                }
            }
        } else if (state === 'ended') {
            playing = false;
            resumeWhenVisible = false;
        }
    }

    return {
        reset() {
            player = null;
            playing = false;
            resumeWhenVisible = false;
            retriedWhileHidden = false;
        },
        attachVideo(video) {
            const currentPlayer = {
                isPaused: () => video.paused && !video.ended && video.isConnected,
                play: () => video.play()
            };
            player = currentPlayer;
            playing = !video.paused && !video.ended;
            video.addEventListener('playing', () => stateChanged(currentPlayer, 'playing'));
            video.addEventListener('pause', () => stateChanged(currentPlayer, 'paused'));
            video.addEventListener('ended', () => stateChanged(currentPlayer, 'ended'));
        },
        attachYouTube(video) {
            const currentPlayer = {
                isPaused: () => video.getPlayerState() === 2,
                play: () => video.playVideo()
            };
            player = currentPlayer;
            playing = video.getPlayerState() === 1;
            return (state) => {
                if (state === 1) stateChanged(currentPlayer, 'playing');
                else if (state === 2) stateChanged(currentPlayer, 'paused');
                else if (state === 0) stateChanged(currentPlayer, 'ended');
            };
        }
    };
};
