const API = {
  logout: "/backend/auth/logout.php",
  session: "/backend/auth/session_check.php",
};

const CURRENT_USER_KEY = "site_current_user";

async function postJson(url, body) {
  const res = await fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    credentials: "include",
    body: JSON.stringify(body || {}),
  });
  const data = await res.json().catch(() => ({ success: false }));
  return { res, data };
}

async function getJson(url) {
  const res = await fetch(url, { method: "GET", credentials: "include" });
  const data = await res.json().catch(() => ({ success: false }));
  return { res, data };
}

async function logout() {
  localStorage.removeItem(CURRENT_USER_KEY);
  try { await postJson(API.logout, {}); } catch {}
  window.location.href = "../login/login.html";
}

async function init() {
  const btn = document.getElementById("btnLogout");
  const line = document.getElementById("userLine");
  btn.addEventListener("click", logout);

  try {
    const { res, data } = await getJson(API.session);
    if (res.ok && data.success && data.user) {
      const u = data.user;
      const perfil = u.perfil === "catador_cooperativa" ? "Catador/Cooperativa" : "Gerador";
      line.textContent = `Conectado como: ${u.nome} (${u.email}) • ${perfil}`;
      localStorage.setItem(CURRENT_USER_KEY, JSON.stringify(u));
      return;
    }
  } catch {}

  line.textContent = "Você não está autenticado.";
}
init();
