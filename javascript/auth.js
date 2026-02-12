// Check of gebruiker ingelogd is
function checkAuth() {
    if (!localStorage.getItem('user')) {
        window.location.href = 'login.php';
        return false;
    }
    return true;
}

// Check rol
function checkRole(requiredRole) {
    const userRole = localStorage.getItem('userRole');
    const allowedRoles = {
        'admin': ['admin', 'manager', 'user'],
        'manager': ['manager', 'user'],
        'user': ['user']
    };

    if (!allowedRoles[requiredRole] || !allowedRoles[requiredRole].includes(userRole)) {
        window.location.href = 'forbidden';
        return false;
    }
    return true;
}

// Logout
function logout() {
    if (window.google && window.google.accounts) {
        window.google.accounts.id.disableAutoSelect();
    }
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    localStorage.removeItem('userRole');
    window.location.href = 'login';
}

// Haal user info op
function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// Haal rol op
function getUserRole() {
    return localStorage.getItem('userRole') || 'user';
}

// Als pagina laadt: check auth
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    addLogoutButton();
});

// Voeg logout knop toe aan header
function addLogoutButton() {
    const header = document.querySelector('header');
    if (header) {
        const user = getUser();
        if (user) {
            const logoutDiv = document.createElement('div');
            logoutDiv.style.cssText = 'position: absolute; right: 20px; top: 15px; display: flex; align-items: center; gap: 15px; color: white;';
            logoutDiv.innerHTML = `
                <span>${user.name}</span>
                <span style="font-size: 12px; background: #667eea; padding: 5px 10px; border-radius: 5px;">${getUserRole()}</span>
                <button onclick="logout()" style="padding: 8px 15px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">Uitloggen</button>
            `;
            header.appendChild(logoutDiv);
        }
    }
}
