const API = { login: "/backend/auth/login.php" };

const el = {
  form: document.getElementById("loginForm"),
  email: document.getElementById("email"),
  senha: document.getElementById("senha"),
  toggleSenha: document.getElementById("toggleSenha"),
  rememberMe: document.getElementById("rememberMe"),
  btnEntrar: document.getElementById("btnEntrar"),
  msg: document.getElementById("msg"),
};

const REMEMBER_EMAIL_KEY = "site_login_email";
const CURRENT_USER_KEY = "site_current_user";

function setMessage(text, type = "") {
  el.msg.textContent = text;
  el.msg.className = type ? `msg ${type}` : "msg";
}

function setLoading(on) {
  el.btnEntrar.disabled = on;
  el.btnEntrar.textContent = on ? "Entrando..." : "Entrar";
}

function loadRememberedEmail() {
  const saved = localStorage.getItem(REMEMBER_EMAIL_KEY);
  if (saved) {
    el.email.value = saved;
    el.rememberMe.checked = true;
  }
}

function saveRememberedEmail() {
  if (el.rememberMe.checked) {
    localStorage.setItem(REMEMBER_EMAIL_KEY, el.email.value.trim().toLowerCase());
  } else {
    localStorage.removeItem(REMEMBER_EMAIL_KEY);
  }
}

function togglePassword() {
  const isPassword = el.senha.type === "password";
  el.senha.type = isPassword ? "text" : "password";
  el.toggleSenha.textContent = isPassword ? "Ocultar" : "Mostrar";
}

async function postJson(url, body) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({ success: false, message: "Resposta inválida do servidor." }));
  return { res, data };
}

async function handleLogin(e) {
  e.preventDefault();

  const email = el.email.value.trim().toLowerCase();
  const senha = el.senha.value;

  if (!email || !senha) {
    setMessage("Preencha e-mail e senha.", "error");
    return;
  }

  saveRememberedEmail();
  setLoading(true);
  setMessage("Validando...");

  try {
    const { res, data } = await postJson(API.login, { email, senha });

    if (!res.ok || !data.success) {
      setMessage(data.message || "Não foi possível entrar.", "error");
      return;
    }

    localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(data.user || {}));
    setMessage("Login realizado. Redirecionando...", "success");

    setTimeout(() => {
      window.location.href = "../home/index.html";
    }, 800);
  } catch {
    setMessage("Falha de conexão. Tente novamente.", "error");
  } finally {
    setLoading(false);
  }
}

function init() {
  loadRememberedEmail();
  el.toggleSenha.addEventListener("click", togglePassword);
  el.form.addEventListener("submit", handleLogin);
}
init();
