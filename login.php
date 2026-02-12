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
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="img/logo.svg" alt="VDL Groep Logo">
            <h1>VDL Groep</h1>
            <p>Meld je aan met je Google account</p>
            
            <script src="https://accounts.google.com/gsi/client" async defer></script>
            <div id="g_id_onload"
                 data-client_id="208183931140-rafgpe00evlhagalk0adasd7ffelt5is.apps.googleusercontent.com"
                 data-callback="handleCredentialResponse">
            </div>
            <div class="g_id_signin" data-type="standard"></div>
        </div>
    </div>

    <script>
        function handleCredentialResponse(response) {
            // Decode JWT token (without verification - voor client-side)
            const base64Url = response.credential.split('.')[1];
            const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            const userData = JSON.parse(jsonPayload);
            
            // Sla user data op
            localStorage.setItem('user', JSON.stringify(userData));
            localStorage.setItem('token', response.credential);
            
            // Autorisatie: zet rollen (FOR DEMO - in production op server doen!)
            const email = userData.email;
            let userRole = 'user'; // Default
            
            // Voorbeeld: admin emails
            if (email === 'chrisjanvanh@gmail.com' || email.includes('admin')) {
                userRole = 'admin';
            } else if (email.includes('manager')) {
                userRole = 'manager';
            }
            
            localStorage.setItem('userRole', userRole);
            
            // Redirect naar homepagina
            window.location.href = 'index.php';
        }

        // Als gebruiker al ingelogd is, ga naar homepagina
        window.addEventListener('load', function() {
            if (localStorage.getItem('user')) {
                window.location.href = 'index.php';
            }
        });
    </script>
</body>
</html>
