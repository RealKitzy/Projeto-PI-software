const API = { register: "/backend/auth/register.php" };

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

const REMEMBER_EMAIL_KEY = "site_login_email";

function setMessage(text, type = "") {
  el.msg.textContent = text;
  el.msg.className = type ? `msg ${type}` : "msg";
}

function setLoading(on) {
  el.btnCadastrar.disabled = on;
  el.btnCadastrar.textContent = on ? "Criando..." : "Criar conta";
}

function togglePassword(input, btn) {
  const isPassword = input.type === "password";
  input.type = isPassword ? "text" : "password";
  btn.textContent = isPassword ? "Ocultar" : "Mostrar";
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

async function handleRegister(e) {
  e.preventDefault();

  const nome = el.nome.value.trim();
  const email = el.email.value.trim().toLowerCase();
  const perfil = el.perfil.value;
  const senha = el.senha.value;
  const confirmarSenha = el.confirmarSenha.value;

  if (!nome || !email || !perfil || !senha || !confirmarSenha) {
    setMessage("Preencha todos os campos.", "error");
    return;
  }

  setLoading(true);
  setMessage("Enviando...");

  try {
    const { res, data } = await postJson(API.register, { nome, email, perfil, senha, confirmarSenha });

    if (!res.ok || !data.success) {
      setMessage(data.message || "Não foi possível cadastrar.", "error");
      return;
    }

    localStorage.setItem(REMEMBER_EMAIL_KEY, email);
    setMessage("Conta criada. Redirecionando para o login...", "success");

    setTimeout(() => {
      window.location.href = "../login/login.html";
    }, 900);
  } catch {
    setMessage("Falha de conexão. Tente novamente.", "error");
  } finally {
    setLoading(false);
  }
}

function init() {
  el.toggleSenha.addEventListener("click", () => togglePassword(el.senha, el.toggleSenha));
  el.toggleConfirmarSenha.addEventListener("click", () => togglePassword(el.confirmarSenha, el.toggleConfirmarSenha));
  el.form.addEventListener("submit", handleRegister);
}
init();
