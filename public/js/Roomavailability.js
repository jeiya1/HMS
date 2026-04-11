(function () {
    'use strict';

    // ── Config ────────────────────────────────────────────────────────────────
    const POLL_INTERVAL   = 12000;   // ms between polls (12 s is polite to the server)
    const ENDPOINT        = '/search-available';
    const FADE_DURATION   = 500;     // ms for the fade-out animation

    // ── State ─────────────────────────────────────────────────────────────────
    let pollTimer      = null;
    let isPolling      = false;
    let removedRooms   = new Set();   // rooms we already removed this session

    // ── DOM helpers ───────────────────────────────────────────────────────────

    /** Inject a small status badge into the search results header */
    function injectStatusBadge() {
        const sortRow = document.querySelector('.pt-5.px-5.pb-1');
        if (!sortRow || document.getElementById('availability-badge')) return;

        const badge = document.createElement('span');
        badge.id = 'availability-badge';
        badge.style.cssText = `
            display: inline-flex; align-items: center; gap: 6px;
            margin-left: auto; font-size: 11px; font-family: 'Roboto', sans-serif;
            color: #6b7280; user-select: none;
        `;
        badge.innerHTML = `
            <span id="badge-dot" style="
                width:8px; height:8px; border-radius:50%;
                background:#22c55e; display:inline-block;
                animation: pulse-green 2s infinite;
            "></span>
            <span id="badge-text">Live availability</span>
        `;

        // Pulse keyframe (injected once)
        if (!document.getElementById('av-keyframes')) {
            const style = document.createElement('style');
            style.id = 'av-keyframes';
            style.textContent = `
                @keyframes pulse-green {
                    0%,100% { opacity:1; transform:scale(1); }
                    50%      { opacity:.5; transform:scale(1.3); }
                }
                @keyframes pulse-orange {
                    0%,100% { opacity:1; }
                    50%      { opacity:.4; }
                }
                .room-item {
                    transition: opacity ${FADE_DURATION}ms ease, transform ${FADE_DURATION}ms ease;
                }
                .room-unavailable {
                    opacity: 0 !important;
                    transform: scale(0.97) !important;
                    pointer-events: none;
                }
                .room-unavailable-banner {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: #fef2f2;
                    border: 1px solid #fecaca;
                    border-radius: 6px;
                    padding: 10px 16px;
                    font-family: 'Roboto', sans-serif;
                    font-size: 13px;
                    color: #b91c1c;
                    margin-bottom: 12px;
                    animation: slideIn .3s ease;
                }
                @keyframes slideIn {
                    from { opacity:0; transform:translateY(-8px); }
                    to   { opacity:1; transform:translateY(0); }
                }
            `;
            document.head.appendChild(style);
        }

        sortRow.style.display = 'flex';
        sortRow.style.alignItems = 'center';
        sortRow.appendChild(badge);
    }

    function setBadgeState(state) {
        const dot  = document.getElementById('badge-dot');
        const text = document.getElementById('badge-text');
        if (!dot || !text) return;

        if (state === 'checking') {
            dot.style.background = '#f59e0b';
            dot.style.animation = 'pulse-orange 1s infinite';
            text.textContent = 'Checking availability…';
        } else if (state === 'live') {
            dot.style.background = '#22c55e';
            dot.style.animation = 'pulse-green 2s infinite';
            text.textContent = 'Live availability';
        } else if (state === 'error') {
            dot.style.background = '#ef4444';
            dot.style.animation = 'none';
            text.textContent = 'Could not update';
        }
    }

    // ── Collect rooms currently in DOM ────────────────────────────────────────

    function getVisibleRoomNumbers() {
        return Array.from(document.querySelectorAll('.room-item[data-room]'))
            .map(el => el.dataset.room)
            .filter(Boolean);
    }

    // ── Build query string from current URL params + rooms[] ─────────────────

    function buildQueryString(roomNumbers) {
        const params = new URLSearchParams(window.location.search);

        // Remove any stale rooms[] param
        params.delete('rooms[]');
        params.delete('rooms');

        // Add each room number
        roomNumbers.forEach(rn => params.append('rooms[]', rn));

        return params.toString();
    }

    // ── Animate a room out and remove it ─────────────────────────────────────

    function removeRoomCard(roomEl, roomNumber) {
        if (removedRooms.has(roomNumber)) return;
        removedRooms.add(roomNumber);

        roomEl.classList.add('room-unavailable');

        setTimeout(() => {
            // Insert a "just booked" notice where the card was
            const notice = document.createElement('div');
            notice.className = 'room-unavailable-banner';
            notice.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <circle cx="8" cy="8" r="7" stroke="#dc2626" stroke-width="1.5"/>
                    <path d="M8 4.5v4M8 10.5v1" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Room <strong>${roomNumber}</strong> was just booked — it's no longer available.</span>
            `;

            roomEl.parentNode.insertBefore(notice, roomEl);
            roomEl.remove();

            // Auto-remove the notice after 8 s
            setTimeout(() => notice.remove(), 8000);
        }, FADE_DURATION);
    }

    // ── Main poll ─────────────────────────────────────────────────────────────

    async function poll() {
        if (isPolling) return;

        const roomNumbers = getVisibleRoomNumbers();
        if (roomNumbers.length === 0) return;   // nothing left to check

        isPolling = true;
        setBadgeState('checking');

        try {
            const qs  = buildQueryString(roomNumbers);
            const res = await fetch(`${ENDPOINT}?${qs}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                cache: 'no-store',
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();

            if (data.error) throw new Error(data.error);

            // data.available = { "101": true, "102": false, … }
            const availability = data.available || {};

            Object.entries(availability).forEach(([roomNumber, isAvail]) => {
                if (!isAvail) {
                    const el = document.querySelector(`.room-item[data-room="${roomNumber}"]`);
                    if (el) removeRoomCard(el, roomNumber);
                }
            });

            setBadgeState('live');

        } catch (err) {
            console.warn('[Availability] Poll failed:', err.message);
            setBadgeState('error');
            // Recover badge after 5 s
            setTimeout(() => setBadgeState('live'), 5000);
        } finally {
            isPolling = false;
        }
    }

    // ── Init ──────────────────────────────────────────────────────────────────

    function init() {
        // Only run on the search results page and only if there are rooms shown
        const hasRooms = document.querySelector('.room-item[data-room]');
        if (!hasRooms) return;

        injectStatusBadge();

        // First poll shortly after page load (let the page settle)
        setTimeout(poll, 3000);

        // Then poll on a regular interval
        pollTimer = setInterval(poll, POLL_INTERVAL);

        // Pause polling when tab is hidden (save server resources)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(pollTimer);
                pollTimer = null;
            } else {
                poll();   // immediate check when tab becomes visible again
                pollTimer = setInterval(poll, POLL_INTERVAL);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();