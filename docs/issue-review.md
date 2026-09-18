# Issue review

The screenshot was treated as a list of claims to verify against the application.

| Finding | Assessment | Resolution |
| --- | --- | --- |
| Missing serving slot | High: clean installs cannot call tickets. The local database already had the column. | Fresh schema corrected; conditional upgrade migration added and tested. |
| Daily code collision | High: next-day issuance can fail with duplicate keys. | Date-qualified, server-derived internal codes; patient-facing screens and printed tickets retain the short form such as `XRAY-001`. Existing identifiers are preserved. First-counter creation is also serialized. |
| Issuer attribution | Significant integrity issue: actions attributed to administrator 1. | Required authenticated issuer; repository validates issuer role. |
| Room authorization | High: callers can bypass browser restrictions. | Database assignments enforced during call and completion; dashboard reads the same assignments. |
| Slot occupancy | High: simultaneous calls can place multiple tickets in one slot. | Slot bounds validated; procedure-row transaction lock serializes selection and occupancy checks for the current day. |
| Playback writes | Moderate: anonymous clients can falsify dashboard playback position. | Writes require a staff session and session-bound token. Log in on the display browser to enable reporting; anonymous display viewing and playback still work. |
| Upload validation | High: arbitrary file extensions can expose executable files under the web root. | MIME/extension allowlist, image decoding check, random filenames, HTTPS YouTube host allowlist. |
| Audit coverage | Moderate operational traceability gap, not an immediate queue failure. | Queue issue/call/complete events remain transactional, with corrected issuer identity. Broad login/staff/media audit logging remains a follow-up; this change does not claim complete audit coverage. |

Verification includes both clean SQL installation paths, repeatable upgrades, issuer and room rejection, invalid slots, occupied slots, completion, spoofed URLs and file content, concurrent first-ticket issuance, and concurrent calls to one slot. Existing JavaScript playback tests also pass. These tests do not replace full browser acceptance testing or a complete security review.
