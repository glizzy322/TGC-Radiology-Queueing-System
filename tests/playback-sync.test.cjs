const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

const window = {};
vm.runInNewContext(
    fs.readFileSync(path.join(__dirname, '../public/js/playback-sync.js'), 'utf8'),
    { window, URLSearchParams }
);

test('builds an identified, muted YouTube embed URL for reliable autoplay', () => {
    const url = new URL(window.getYouTubeEmbedUrl(
        'q1hjSEvN9-I',
        'http://127.0.0.1:8000',
        'http://127.0.0.1:8000/public-display'
    ));
    assert.equal(url.pathname, '/embed/q1hjSEvN9-I');
    assert.equal(url.searchParams.get('autoplay'), '1');
    assert.equal(url.searchParams.get('mute'), '1');
    assert.equal(url.searchParams.get('enablejsapi'), '1');
    assert.equal(url.searchParams.get('origin'), 'http://127.0.0.1:8000');
    assert.equal(url.searchParams.get('widget_referrer'), 'http://127.0.0.1:8000/public-display');
});

test('advances a playing public-display position by server-observed age', () => {
    const state = { position: 18.25, playing: true, updated_at: 100 };
    assert.equal(window.getSyncedPlaybackTime(state, 100.75), 19);
});

test('does not advance a paused public-display position', () => {
    const state = { position: 18.25, playing: false, updated_at: 100 };
    assert.equal(window.getSyncedPlaybackTime(state, 110), 18.25);
});

test('never rewinds because of a negative clock age', () => {
    const state = { position: 12, playing: true, updated_at: 101 };
    assert.equal(window.getSyncedPlaybackTime(state, 100), 12);
});

test('corrects material drift but ignores normal polling jitter', () => {
    assert.equal(window.shouldCorrectPlayback(20, 20.8), false);
    assert.equal(window.shouldCorrectPlayback(20, 21.3), true);
});

test('detects explicit and duration-growing YouTube live playback', () => {
    assert.equal(window.isYouTubeLivePlayback({ isLive: true }, 500, 500), true);
    assert.equal(window.isYouTubeLivePlayback({}, 500, 501.2), true);
    assert.equal(window.isYouTubeLivePlayback({}, 500, 500), false);
    assert.equal(window.isYouTubeLivePlayback({}, 22000, 22000, 21998), true);
    assert.equal(window.isYouTubeLivePlayback({}, 22000, 22000, 2), false);
    assert.equal(window.isYouTubeLivePlayback({}, 15, 15, 14), false);
});

test('seeks a YouTube live stream to its current live edge', () => {
    let sought = null;
    let played = false;
    window.seekYouTubeLiveEdge({
        getDuration: () => 7200,
        seekTo: (position, ahead) => { sought = [position, ahead]; },
        playVideo: () => { played = true; }
    });
    assert.deepEqual(sought, [7199, true]);
    assert.equal(played, true);
});
