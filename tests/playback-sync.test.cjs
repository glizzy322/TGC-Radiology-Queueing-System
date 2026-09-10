const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');

const window = {};
vm.runInNewContext(
    fs.readFileSync(path.join(__dirname, '../public/js/playback-sync.js'), 'utf8'),
    { window }
);

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
