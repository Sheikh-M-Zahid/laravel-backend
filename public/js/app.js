/**
 * app.js — small helpers shared across Blade views.
 */
window.AgriApp = {
    getCropRecommendation(farmProfileId) {
        return axios.post('/farmer/recommend/crop', { farm_profile_id: farmProfileId })
            .then(res => res.data)
            .catch(err => { console.error('Crop recommendation failed', err); throw err; });
    },
    getFertilizerRecommendation(recommendationId) {
        return axios.post('/farmer/recommend/fertilizer', { recommendation_id: recommendationId })
            .then(res => res.data);
    },
    getPriceForecast(crop) {
        return axios.post('/farmer/recommend/price', { crop })
            .then(res => res.data);
    },
};

// Password show/hide toggle: any <button class="toggle-password" data-target="fieldId">
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.toggle-password');
    if (!btn) return;
    const input = document.getElementById(btn.dataset.target);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    btn.textContent = input.type === 'password' ? '👁' : '🙈';
});

// OTP countdown: any element with id="otp-timer" and data-expires-in-seconds="300"
document.addEventListener('DOMContentLoaded', function () {
    const timer = document.getElementById('otp-timer');
    if (!timer) return;
    let remaining = parseInt(timer.dataset.expiresInSeconds || '300', 10);
    const tick = () => {
        if (remaining <= 0) {
            timer.textContent = 'Code expired — request a new one below.';
            timer.classList.add('expired');
            return;
        }
        const m = Math.floor(remaining / 60);
        const s = String(remaining % 60).padStart(2, '0');
        timer.textContent = `Code expires in ${m}:${s}`;
        remaining -= 1;
        setTimeout(tick, 1000);
    };
    tick();
});

// Generic modal system: used for both "feature detail" popups and now
// "click a tile, get a form" action modals across every dashboard.
window.openModal = function (id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
};
window.closeModal = function (id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
};
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(el => el.classList.remove('active'));
    }
});

// Notification bell dropdown: close it on outside click or Escape.
document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('notif-dropdown');
    if (!dropdown) return;
    const wrap = e.target.closest('.notif-bell-wrap');
    if (!wrap) dropdown.classList.remove('open');
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const dropdown = document.getElementById('notif-dropdown');
        if (dropdown) dropdown.classList.remove('open');
    }
});
