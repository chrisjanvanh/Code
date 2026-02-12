<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - VDL Groep</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">

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
        .login-box h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .login-box p {
            color: #666;
            margin-bottom: 30px;
        }
        .login-box img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        #g_id_onload {
            display: flex;
            justify-content: center;
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

    <!-- Microsoft SSO -->
    <script src="https://alcdn.msauth.net/browser/2.37.0/js/msal-browser.min.js"></script>

    <!-- Google SSO -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <img src="img/logo.svg" alt="VDL Groep Logo">
            <h1>VDL Groep</h1>
            <p>Meld je aan met je account</p>

            <!-- Microsoft Login -->
            <button class="ms-btn" onclick="loginMicrosoft()">Login met Microsoft</button>

            <!-- Google Login -->
            <div id="g_id_onload"
                data-client_id="208183931140-rafgpe00evlhagalk0adasd7ffelt5is.apps.googleusercontent.com"
                data-callback="handleCredentialResponse">
            </div>
            <div class="g_id_signin" data-type="standard"></div>
        </div>
    </div>

    <script>
        /* ---------------- GOOGLE LOGIN ---------------- */
        function handleCredentialResponse(response) {
            const base64Url = response.credential.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(c =>
                '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2)
            ).join(''));

            const userData = JSON.parse(jsonPayload);

            localStorage.setItem('user', JSON.stringify(userData));
            localStorage.setItem('token', response.credential);

            assignRole(userData.email);
            window.location.href = 'index.php';
        }

        /* ---------------- MICROSOFT LOGIN ---------------- */
        const msalConfig = {
            auth: {
                clientId: "00fc974d-f877-4135-8bb4-fb6345b5bac4",
                authority: "https://login.microsoftonline.com/87c50b58-2ef2-423d-a4db-1fa7c84efcfa",
                redirectUri: window.location.href
            }
        };

        const msalInstance = new msal.PublicClientApplication(msalConfig);

        function loginMicrosoft() {
            msalInstance.loginPopup({
                scopes: ["User.Read"]
            }).then(response => {
                localStorage.setItem("token", response.accessToken);

                fetch("https://graph.microsoft.com/v1.0/me", {
                    headers: { "Authorization": `Bearer ${response.accessToken}` }
                })
                .then(res => res.json())
                .then(user => {
                    localStorage.setItem("user", JSON.stringify(user));
                    assignRole(user.mail);
                    window.location.href = "index.php";
                });
            })
            .catch(err => console.error("Microsoft login error:", err));
        }

        /* ---------------- ROLLEN ---------------- */
        function assignRole(email) {
            let role = "user";

            if (email.includes("admin")) role = "admin";
            if (email.includes("manager")) role = "manager";

            localStorage.setItem("userRole", role);
        }

        /* ---------------- AUTO REDIRECT ---------------- */
        window.addEventListener('load', () => {
            if (localStorage.getItem('user')) {
                window.location.href = 'index.php';
            }
        });
    </script>
</body>
</html>
