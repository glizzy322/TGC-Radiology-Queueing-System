window.getSyncedPlaybackTime = function (state, serverTime) {
    if (!state) return 0;
    const position = Math.max(0, Number(state.position) || 0);
    const age = Math.max(0, Number(serverTime) - (Number(state.updated_at) || Number(serverTime)));
    return position + (state.playing ? age : 0);
};

window.shouldCorrectPlayback = function (currentTime, targetTime, tolerance = 1.25) {
    return Math.abs((Number(currentTime) || 0) - (Number(targetTime) || 0)) > tolerance;
};
