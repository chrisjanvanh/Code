<?php
session_start();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - VDL Groep</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">

    <!-- Microsoft SSO -->
    <script src="https://alcdn.msauth.net/browser/2.37.0/js/msal-browser.min.js"></script>

    <!-- Google SSO -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
        }
        .ms-btn {
            display:block;
            margin-top:20px;
            padding:12px;
            background:#2F2F2F;
            color:white;
            border-radius:6px;
            text-decoration:none;
            cursor:pointer;
        }
    </style>
</head>

<body>
<div class="login-container">
    <div class="login-box">
        <img src="img/logo.svg" alt="VDL Groep Logo" style="max-width:150px;">
        <h1>VDL Groep</h1>
        <p>Meld je aan met je account</p>

        <!-- Microsoft Login -->
        <button class="ms-btn" onclick="loginMicrosoft()">Login met Microsoft</button>

        <!-- Google Login -->
        <div id="g_id_onload"
             data-client_id="208183931140-rafgpe00evlhagalk0adasd7ffelt5is.apps.googleusercontent.com"
             data-callback="handleGoogleLogin">
        </div>
        <div class="g_id_signin" data-type="standard"></div>
    </div>
</div>

<script>
/* ---------------- GOOGLE LOGIN ---------------- */
function handleGoogleLogin(response) {
    const base64Url = response.credential.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(atob(base64).split('').map(c =>
        '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
    ).join(''));

    const user = JSON.parse(jsonPayload);
    const email = user.email;

    setSession(email);
}

/* ---------------- MICROSOFT LOGIN ---------------- */
const msalConfig = {
    auth: {
        clientId: "00fc974d-f877-4135-8bb4-fb6345b5bac4",
        authority: "https://login.microsoftonline.com/common",
        redirectUri: "https://vdl-backend.azurewebsites.net/login.php"
    }
};

const msalInstance = new msal.PublicClientApplication(msalConfig);

function loginMicrosoft() {
    msalInstance.loginPopup({
        scopes: ["User.Read"]
    }).then(response => {
        fetch("https://graph.microsoft.com/v1.0/me", {
            headers: { "Authorization": `Bearer ${response.accessToken}` }
        })
        .then(res => res.json())
        .then(user => {
            const email = user.mail || user.userPrincipalName;
            setSession(email);
        });
    });
}

/* ---------------- SESSIE ZETTEN ---------------- */
function setSession(email) {
    fetch("backend/setSession.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "email=" + encodeURIComponent(email)
    })
    .then(() => {
        window.location.href = "index.php";
    });
}
</script>

</body>
</html>
