<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrativo - Ex-Envios</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet">

    <!-- Preload Background Images to prevent "flashing" -->
    <link rel="preload" as="image" href="assets/img/login/background.webp">
    <link rel="preload" as="image" href="assets/img/login/van.png">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/login.css">
    
    <!-- Custom Toast Notification System -->
    <style>
        .toast-container {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 99999;
            background: #2d3748;
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            transition: opacity 0.3s ease, transform 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            align-items: center;
        }
        .toast-container.show { opacity: 1; transform: translateY(0); }
        .toast-content { display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 1.5rem; }
        #toast-message { font-size: 0.95rem; font-weight: 500; }
        .toast-close { background: none; border: none; color: #cbd5e0; font-size: 1.2rem; cursor: pointer; padding: 0; margin: 0; transition: color 0.2s; }
        .toast-close:hover { color: #fff; }

        .admin-badge {
            background: var(--orange-vibrant);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            display: inline-block;
            margin-bottom: 1rem;
            letter-spacing: 1px;
        }
    </style>
    <script>
        // Override standard alert early
        window.alert = function showToast(message) {
            let toast = document.getElementById('custom-toast');
            if(!toast) {
                if(document.body) {
                    injectToastUI();
                    toast = document.getElementById('custom-toast');
                } else {
                    console.log("Alert (pre-DOM): ", message);
                    return; 
                }
            }
            document.getElementById('toast-message').innerText = message;
            toast.style.display = 'flex';
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 4000);
        };
        
        function closeToast() {
            const toast = document.getElementById('custom-toast');
            if(toast) {
                toast.classList.remove('show');
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }
        }

        function injectToastUI() {
            if(!document.getElementById('custom-toast')) {
                const div = document.createElement('div');
                div.id = 'custom-toast';
                div.className = 'toast-container';
                div.style.display = 'none';
                div.innerHTML = `
                    <div class="toast-content">
                        <span id="toast-message">Mensagem</span>
                        <button class="toast-close" onclick="closeToast()">&times;</button>
                    </div>
                `;
                document.body.appendChild(div);
            }
        }
        document.addEventListener('DOMContentLoaded', injectToastUI);
    </script>
</head>

<body>
    <div class="login-container">
        <!-- Left Side: Image -->
        <div class="login-image">
            <img src="assets/img/login/background.webp" alt="Background Texture" class="bg-texture">
            <div class="van-wrapper">
                <img src="assets/img/login/van.png" alt="Entregas Ex-Envios" class="van-image">
            </div>
            <div class="overlay"></div>
        </div>

        <!-- Right Side: Form -->
        <div class="login-form-container">
            <div class="login-content">
                <div class="logo-container" style="margin-top: 2rem; margin-bottom: 0rem;">
                    <img src="assets/img/logo_exenvios.png" alt="Ex-Envios Logo" class="logo">
                </div>

                <div class="text-center mb-4">
                    <div class="admin-badge">PORTAL ADMINISTRATIVO</div>
                    <h1 class="welcome-title">Gestão do Sistema</h1>
                    <p class="welcome-subtitle">Acesso restrito para administradores Ex-Envios.</p>
                </div>

                <form class="login-form" id="form-auth-login" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label for="email">E-MAIL</label>
                        <input type="email" id="login-email" name="email" placeholder="seu@email.com" required="">
                    </div>

                    <div class="form-group">
                        <label for="password">SENHA</label>
                        <input type="password" id="login-password" name="password" placeholder="Sua senha" required="">
                    </div>

                    <div class="form-actions">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Lembrar-me</span>
                        </label>
                        <a href="#" class="forgot-password">Esqueceu a senha?</a>
                    </div>

                    <button type="submit" class="btn-submit" id="btn-submit-login">ACESSAR PAINEL</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        async function handleLogin(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-login');
            const originalText = btn.innerText;
            btn.innerText = "AUTENTICANDO...";
            btn.disabled = true;

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const res = await fetch('endpoints/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const result = await res.json();

                if (result.ok) {
                    // Independente do papel, se logou pelo admin-login, tentamos mandar para /admin
                    // O admin.php fará a verificação final de segurança.
                    window.location.href = '/admin';
                } else {
                    alert(result.error || "Erro ao fazer login");
                    btn.innerText = originalText;
                    btn.disabled = false;
                }
            } catch (err) {
                alert("Erro de conexão com o servidor");
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }
    </script>
</body>

</html>
