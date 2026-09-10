const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

function setup() {
    const document = new EventTarget();
    document.hidden = false;
    const window = {};
    vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../public/js/background-playback.js'), 'utf8'), { document, window });
    return {
        playback: window.createBackgroundPlayback(),
        visibility(hidden) {
            document.hidden = hidden;
            document.dispatchEvent(new Event('visibilitychange'));
        }
    };
}

function video() {
    const media = new EventTarget();
    Object.assign(media, { paused: false, ended: false, isConnected: true, currentTime: 42, attempts: 0 });
    media.play = () => {
        media.attempts++;
        media.paused = false;
        media.dispatchEvent(new Event('playing'));
        return Promise.resolve();
    };
    media.pause = () => {
        media.paused = true;
        media.dispatchEvent(new Event('pause'));
    };
    return media;
}

test('tab changes preserve an uninterrupted video and its position', () => {
    const { playback, visibility } = setup();
    const media = video();
    playback.attachVideo(media);
    visibility(true);
    visibility(false);
    assert.equal(media.attempts, 0);
    assert.equal(media.currentTime, 42);
});

test('a background pause resumes the same video without seeking', () => {
    const { playback, visibility } = setup();
    const media = video();
    playback.attachVideo(media);
    visibility(true);
    media.pause();
    assert.equal(media.attempts, 1);
    assert.equal(media.paused, false);
    assert.equal(media.currentTime, 42);
});

test('manual foreground pauses stay paused after switching tabs', () => {
    const { playback, visibility } = setup();
    const media = video();
    playback.attachVideo(media);
    media.pause();
    visibility(true);
    visibility(false);
    assert.equal(media.attempts, 0);
});

test('blocked background playback retries on return without looping', async () => {
    const { playback, visibility } = setup();
    const media = video();
    media.play = () => {
        media.attempts++;
        return Promise.reject(new Error('Background playback blocked'));
    };
    playback.attachVideo(media);
    visibility(true);
    media.pause();
    media.pause();
    assert.equal(media.attempts, 1);
    visibility(false);
    assert.equal(media.attempts, 2);
    await new Promise(resolve => setImmediate(resolve));
});

test('replaced or ended videos are not restarted', () => {
    const { playback, visibility } = setup();
    const old = video();
    playback.attachVideo(old);
    visibility(true);
    playback.reset();
    const current = video();
    playback.attachVideo(current);
    old.pause();
    current.ended = true;
    current.paused = true;
    current.dispatchEvent(new Event('ended'));
    visibility(false);
    assert.equal(old.attempts, 0);
    assert.equal(current.attempts, 0);
});

test('YouTube recovers background pauses and respects foreground pauses', () => {
    const { playback, visibility } = setup();
    let state = 1;
    let attempts = 0;
    const update = playback.attachYouTube({
        getPlayerState: () => state,
        playVideo: () => { attempts++; }
    });
    visibility(true);
    state = 2;
    update(state);
    assert.equal(attempts, 1);
    state = 1;
    update(state);
    visibility(false);
    state = 2;
    update(state);
    visibility(true);
    visibility(false);
    assert.equal(attempts, 1);
});
