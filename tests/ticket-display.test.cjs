const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../resources/views/dashboard/index.php'), 'utf8');
const formatter = source.match(/function displayTicketId\(ticket\) \{[\s\S]*?\n        \}/)[0];
test('printed ticket retains short number and separate date', () => {
  const printer = source.slice(source.indexOf('function printTicket(ticket)'), source.indexOf('function updateGenerateState()'));
  let html = '';
  const context = {window: {open: () => ({document: {write: value => { html = value; }, close() {}}, focus() {}})}, setTimeout() {}, Date};
  vm.createContext(context);
  vm.runInContext(formatter + '\n' + printer + '\nprintTicket({id:"XRAY-20260918-001", procedure:"X-Ray", patientType:"IPD",createdAt:new Date("2026-09-18T01:00:00Z")});', context);
  assert.match(html, /class="ticket-number">XRAY-001</);
  assert.doesNotMatch(html, /XRAY-20260918-001/);
  assert.match(html, /2026/);
});
test('incoming queue renders short identifiers', () => {
  const display = fs.readFileSync(path.join(__dirname, '../resources/views/partials/public-display-content.php'), 'utf8');
  const start = display.indexOf('function renderIncoming(');
  assert.notEqual(start, -1);
  const fn = display.slice(start, display.indexOf('function getTicketNumberValue', start));
  const wrapper = {innerHTML: ''};
  const context = {document:{querySelector:()=>wrapper}, procedures:{xray:{name:'X-Ray',title:'X-Ray'}}};
  vm.createContext(context);
  vm.runInContext(formatter + '\n' + fn + '\nrenderIncoming({xray:[{id:"XRAY-20260918-001"}]});', context);
  assert.match(wrapper.innerHTML, /XRAY-001/);
  assert.doesNotMatch(wrapper.innerHTML, /XRAY-20260918-001/);
});
