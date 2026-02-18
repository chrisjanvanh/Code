// Check of gebruiker ingelogd is
function checkAuth() {
    if (!localStorage.getItem('user')) {
        window.location.href = 'login.php';
        return false;
    }
    return true;
}

// Haal user info op (Google of Microsoft)
function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

// Haal naam op (werkt voor Google + Microsoft)
function getUserName() {
    const user = getUser();
    if (!user) return "Onbekend";

    return user.name || user.displayName || "Onbekend";
}

// Haal email op (werkt voor Google + Microsoft)
function getUserEmail() {
    const user = getUser();
    if (!user) return "onbekend";

    return user.email || user.mail || user.userPrincipalName || "onbekend";
}

// Haal rol op
function getUserRole() {
    return localStorage.getItem('userRole') || 'user';
}

// Check rol
function checkRole(requiredRole) {
    const userRole = getUserRole();
    const allowedRoles = {
        'admin': ['admin', 'manager', 'user'],
        'manager': ['manager', 'user'],
        'user': ['user']
    };

    if (!allowedRoles[requiredRole] || !allowedRoles[requiredRole].includes(userRole)) {
        window.location.href = 'forbidden.php';
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

    window.location.href = 'login.php';
}

// Voeg logout knop toe aan header
function addLogoutButton() {
    const header = document.querySelector('header');
    if (!header) return;

    const user = getUser();
    if (!user) return;

    const name = getUserName();
    const role = getUserRole();

    const logoutDiv = document.createElement('div');
    logoutDiv.style.cssText = `
        position: absolute;
        right: 20px;
        top: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        color: white;
    `;

    logoutDiv.innerHTML = `
        <span class="username">${name}</span>
        <span class="userrole" style="font-size: 12px; background: #667eea; padding: 5px 10px; border-radius: 5px;">
            ${role}
        </span>
        <button onclick="logout()" style="padding: 8px 15px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Uitloggen
        </button>
    `;

    header.appendChild(logoutDiv);
}

// Bij pagina‑load
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    addLogoutButton();
});
