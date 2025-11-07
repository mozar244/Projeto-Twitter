// script.js

// === FUNÇÕES DE NAVEGAÇÃO ===
function showRegister() {
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('registerSection').style.display = 'block';
    document.getElementById('supportSection').style.display = 'none';
}

function showLogin() {
    document.getElementById('loginSection').style.display = 'block';
    document.getElementById('registerSection').style.display = 'none';
    document.getElementById('supportSection').style.display = 'none';
}

function showSupport() {
    document.getElementById('loginSection').style.display = 'none';
    document.getElementById('registerSection').style.display = 'none';
    document.getElementById('supportSection').style.display = 'block';
}

// === LOGIN ===
async function handleLogin() {
    const username = document.getElementById('login-username').value.trim();
    const password = document.getElementById('login-password').value;

    if (!username || !password) {
        alert('Preencha usuário e senha!');
        return;
    }

    try {
        const res = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        });

        const data = await res.json();
        if (data.success) {
            window.location.href = 'dashboard.php';
        } else {
            alert(data.message || 'Erro no login');
        }
    } catch (err) {
        console.error('Erro de conexão:', err);
        alert('Erro de conexão com o servidor.');
    }
}

// === CADASTRO ===
async function register() {
    const username = document.getElementById('regUsername').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const password = document.getElementById('regPassword').value;
    const is_admin = document.getElementById('regIsAdmin')?.checked ? 1 : 0;

    if (!username || !password) {
        document.getElementById('regError').textContent = 'Preencha usuário e senha!';
        return;
    }

    try {
        const res = await fetch('api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `username=${encodeURIComponent(username)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&is_admin=${is_admin}`
        });

        const data = await res.json();
        if (data.success) {
            alert('Registrado com sucesso! Faça login.');
            showLogin();
            document.getElementById('regUsername').value = '';
            document.getElementById('regEmail').value = '';
            document.getElementById('regPassword').value = '';
            if (document.getElementById('regIsAdmin')) document.getElementById('regIsAdmin').checked = false;
            document.getElementById('regError').textContent = '';
        } else {
            document.getElementById('regError').textContent = data.error || 'Erro ao cadastrar';
        }
    } catch (err) {
        console.error('Erro no cadastro:', err);
        document.getElementById('regError').textContent = 'Erro de conexão.';
    }
}

// === EVENTOS ===
document.addEventListener('DOMContentLoaded', () => {
    // Botões
    document.getElementById('login-btn').addEventListener('click', handleLogin);
    document.getElementById('register-btn').addEventListener('click', register);
    document.getElementById('show-register').addEventListener('click', (e) => { e.preventDefault(); showRegister(); });
    document.getElementById('show-login').addEventListener('click', (e) => { e.preventDefault(); showLogin(); });
    document.getElementById('show-support').addEventListener('click', (e) => { e.preventDefault(); showSupport(); });
    document.getElementById('back-to-login').addEventListener('click', showLogin);

    // Enter no login
    document.getElementById('login-password').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleLogin();
    });
});