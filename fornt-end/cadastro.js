// Cadastro frontend-only (sem banco) usando localStorage.
// Após "Criar conta", volta para o Login para o usuário entrar normalmente.

const el = {
  form: document.getElementById("cadastroForm"),
  nome: document.getElementById("nome"),
  email: document.getElementById("email"),
  perfil: document.getElementById("perfil"),
  senha: document.getElementById("senha"),
  confirmarSenha: document.getElementById("confirmarSenha"),
  toggleSenha: document.getElementById("toggleSenha"),
  toggleConfirmarSenha: document.getElementById("toggleConfirmarSenha"),
  btnCadastrar: document.getElementById("btnCadastrar"),
  msg: document.getElementById("msg"),
};

const USERS_LOCAL_KEY = "site_users_local";
const REMEMBER_EMAIL_KEY = "site_login_email";

function setMessage(text, type = "info") {
  el.msg.textContent = text;
  el.msg.className = `msg ${type}`;
}

function setLoading(isLoading) {
  el.btnCadastrar.disabled = isLoading;
  el.btnCadastrar.textContent = isLoading ? "Criando..." : "Criar conta";
}

function togglePassword(inputEl, btnEl) {
  const isPassword = inputEl.type === "password";
  inputEl.type = isPassword ? "text" : "password";
  btnEl.textContent = isPassword ? "Ocultar" : "Mostrar";
  btnEl.setAttribute("aria-label", isPassword ? "Ocultar senha" : "Mostrar senha");
}

function getLocalUsers() {
  try {
    return JSON.parse(localStorage.getItem(USERS_LOCAL_KEY) || "[]");
  } catch {
    return [];
  }
}

function saveLocalUsers(users) {
  localStorage.setItem(USERS_LOCAL_KEY, JSON.stringify(users));
}

function validate(payload) {
  const { nome, email, perfil, senha, confirmarSenha } = payload;

  if (!nome || !email || !perfil || !senha || !confirmarSenha) {
    return "Preencha todos os campos.";
  }

  const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  if (!emailOk) return "Digite um e-mail válido.";

  const allowed = ["gerador", "catador_cooperativa"];
  if (!allowed.includes(perfil)) return "Selecione um perfil válido.";

  if (senha.length < 4) return "A senha deve ter pelo menos 4 caracteres.";

  if (senha !== confirmarSenha) return "As senhas não coincidem.";

  return null;
}

async function handleCadastro(e) {
  e.preventDefault();

  const payload = {
    nome: el.nome.value.trim(),
    email: el.email.value.trim().toLowerCase(),
    perfil: el.perfil.value,
    senha: el.senha.value,
    confirmarSenha: el.confirmarSenha.value,
  };

  const error = validate(payload);
  if (error) {
    setMessage(error, "error");
    return;
  }

  setLoading(true);
  setMessage("Validando dados...", "info");

  try {
    const users = getLocalUsers();
    const exists = users.some(u => u.email === payload.email);

    if (exists) {
      setMessage("Já existe uma conta com esse e-mail.", "error");
      return;
    }

    // OBS: senha em texto puro é só para protótipo sem banco.
    // Em produção, isso vai para o backend com hash.
    const newUser = {
      id: Date.now(),
      nome: payload.nome,
      email: payload.email,
      perfil: payload.perfil,
      senha: payload.senha,
      criadoEm: new Date().toISOString(),
    };

    users.push(newUser);
    saveLocalUsers(users);

    // Preenche o e-mail no login automaticamente (UX)
    localStorage.setItem(REMEMBER_EMAIL_KEY, payload.email);

    setMessage("Cadastro realizado! Redirecionando para o login...", "success");

    setTimeout(() => {
      window.location.href = "./login.html";
    }, 1100);

  } catch {
    setMessage("Erro ao cadastrar. Tente novamente.", "error");
  } finally {
    setLoading(false);
  }
}

function init() {
  el.form.addEventListener("submit", handleCadastro);
  el.toggleSenha.addEventListener("click", () => togglePassword(el.senha, el.toggleSenha));
  el.toggleConfirmarSenha.addEventListener("click", () => togglePassword(el.confirmarSenha, el.toggleConfirmarSenha));
  setMessage("", "info");
}

init();
