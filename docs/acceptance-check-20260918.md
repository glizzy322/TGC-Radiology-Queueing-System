# Local acceptance check — September 18, 2026

Tested at http://127.0.0.1:8000 using browser interactions, real HTTP requests, and isolated database regression tests.

| Workflow | Result and evidence |
| --- | --- |
| Administrator login and Reception | Passed in browser with the supplied account. Generated XRAY-001 (internal XRAY-20260918-001). |
| Short ticket on staff and public screens | Passed in browser for staff waiting/serving and public incoming/serving. Fixed a missed formatter in the public incoming list. |
| Printed ticket | Actual print-template execution produced XRAY-001 with a separate date. In-app browser did not expose a print preview; physical printing is unverified. |
| Call and complete | X-Ray 1 called the test ticket, public display showed XRAY-001, and completion released the slot. The completed test ticket remains in audit/history. |
| Five room accounts | All five logins and server-rendered room assignments passed. X-Ray 1 was also checked visually; its interface exposed only its own slot. |
| Server restrictions | All five accounts rejected another room's call request. X-Ray 1 could not call X-Ray 2's slot; X-Ray 2 could not complete X-Ray 1's serving ticket. Invalid slots returned 400; unauthorized rooms returned 403; occupied slot returned 409. Fixed controllers incorrectly mapping these expected rejections to 500. |
| JPG, PNG, GIF, WebP | Generated valid image files uploaded through the actual HTTP endpoint. |
| MP4, WebM | Browser-generated real videos uploaded successfully (HTTP 200), marked inactive. |
| Invalid media | Unsupported PHP and source text disguised as JPG, MP4, or WebM rejected with HTTP 400. |
| YouTube URL rules | Official HTTPS URL accepted. HTTP, lookalike host, and unrelated host rejected. Ads form visibly showed the rejection for an unrelated URL. This verifies URL acceptance, not playback availability of every YouTube video. |

All temporary test advertisements and uploaded files were removed. The temporary browser video-check page was removed. No pre-existing tickets or advertisements were deleted.

Verification: `php tests/live-workflows.php`, `php tests/integrity.php`, and `node --test tests/*.test.cjs` (12 passing JavaScript tests). The optional ticket-code argument to live-workflows checks occupied-slot and cross-room completion rejection against a known serving test ticket; omit it after completing that ticket.
