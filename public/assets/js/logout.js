// Handles logout functionality for the header dropdown
// Requires window.baseUrl to be set

document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const baseUrl = window.baseUrl || '/rioanime/';
            const url = baseUrl.endsWith('/') ? baseUrl + 'account/logout' : baseUrl + '/account/logout';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect || baseUrl;
                } else {
                    alert('Logout failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('Logout error: ' + error);
            });
        });
    }
});
