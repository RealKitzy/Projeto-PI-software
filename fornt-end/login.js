// Frontend-only (sem banco) usando localStorage.
// Quando você ligar o backend, eu adapto para consumir /backend/login/login.php.

const el = {
  form: document.getElementById("loginForm"),
  email: document.getElementById("email"),
  senha: document.getElementById("senha"),
  toggleSenha: document.getElementById("toggleSenha"),
  rememberMe: document.getElementById("rememberMe"),
  btnEntrar: document.getElementById("btnEntrar"),
  msg: document.getElementById("msg"),
};

const USERS_LOCAL_KEY = "site_users_local";
const CURRENT_USER_KEY = "site_current_user";
const REMEMBER_EMAIL_KEY = "site_login_email";

function setMessage(text, type = "info") {
  el.msg.textContent = text;
  el.msg.className = `msg ${type}`;
}

function setLoading(isLoading) {
  el.btnEntrar.disabled = isLoading;
  el.btnEntrar.textContent = isLoading ? "Entrando..." : "Entrar";
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
    localStorage.setItem(REMEMBER_EMAIL_KEY, el.email.value.trim());
  } else {
    localStorage.removeItem(REMEMBER_EMAIL_KEY);
  }
}

function getLocalUsers() {
  try {
    return JSON.parse(localStorage.getItem(USERS_LOCAL_KEY) || "[]");
  } catch {
    return [];
  }
}

function validateForm(email, senha) {
  if (!email || !senha) return "E-mail e senha são obrigatórios.";

  const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  if (!emailOk) return "Digite um e-mail válido.";

  if (senha.length < 4) return "A senha deve ter pelo menos 4 caracteres.";

  return null;
}

function handleTogglePassword() {
  const isPassword = el.senha.type === "password";
  el.senha.type = isPassword ? "text" : "password";
  el.toggleSenha.textContent = isPassword ? "Ocultar" : "Mostrar";
  el.toggleSenha.setAttribute("aria-label", isPassword ? "Ocultar senha" : "Mostrar senha");
}

async function handleLogin(e) {
  e.preventDefault();

  const email = el.email.value.trim().toLowerCase();
  const senha = el.senha.value;

  const error = validateForm(email, senha);
  if (error) {
    setMessage(error, "error");
    return;
  }

  saveRememberedEmail();
  setLoading(true);
  setMessage("Validando acesso...", "info");

  try {
    // Login local (sem banco)
    const users = getLocalUsers();
    const user = users.find(u => u.email === email);

    if (!user) {
      setMessage("Conta não encontrada. Faça seu cadastro.", "error");
      return;
    }

    if (user.senha !== senha) {
      setMessage("Senha incorreta. Tente novamente.", "error");
      return;
    }

    // “Sessão” local
    const safeUser = { id: user.id, nome: user.nome, email: user.email, perfil: user.perfil };
    localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(safeUser));

    setMessage("Login realizado com sucesso.", "success");

    // Se você quiser redirecionar depois:
    // window.location.href = "./index.html";
  } catch {
    setMessage("Não foi possível entrar no momento. Tente novamente.", "error");
  } finally {
    setLoading(false);
  }
}

function init() {
  loadRememberedEmail();
  el.form.addEventListener("submit", handleLogin);
  el.toggleSenha.addEventListener("click", handleTogglePassword);
  setMessage("", "info");
}

init();
