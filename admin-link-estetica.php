<?php
session_start();
$adminToken = getenv('ADMIN_TOKEN') ?: 'sem-token-configurado';

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pwd'])) {
    if ($_POST['pwd'] === $adminToken) {
        $_SESSION['admin'] = true;
        header('Location: admin-link-estetica.php');
        exit;
    } else {
        $loginError = true;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-link-estetica.php');
    exit;
}

$isLoggedIn = !empty($_SESSION['admin']);
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin · Link Estética Paliativa</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
  body{font-family:'Inter',sans-serif;background:#f4f4f5;color:#18181b;font-size:14px;}
  .topbar{background:#0a0a0a;padding:16px 32px;display:flex;align-items:center;gap:16px;}
  .topbar img{height:36px;filter:brightness(0) invert(1);opacity:.9;}
  .topbar span{color:#e8b84b;font-size:13px;font-weight:600;letter-spacing:.5px;}
  .container{max-width:820px;margin:32px auto;padding:0 24px 80px;}
  .card{background:#fff;border-radius:12px;border:1px solid #e4e4e7;margin-bottom:20px;overflow:hidden;}
  .card-header{padding:18px 24px;background:#fafafa;border-bottom:1px solid #e4e4e7;display:flex;align-items:center;gap:10px;}
  .card-header h2{font-size:15px;font-weight:600;}
  .badge{font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;}
  .badge-form{background:#fef3c7;color:#b45309;}
  .badge-compra{background:#dcfce7;color:#15803d;}
  .card-body{padding:20px 24px;display:flex;flex-direction:column;gap:14px;}
  label{display:block;font-size:11.5px;font-weight:600;color:#52525b;margin-bottom:5px;letter-spacing:.3px;text-transform:uppercase;}
  input,textarea,select{width:100%;padding:9px 12px;border:1px solid #d4d4d8;border-radius:8px;font-size:14px;font-family:'Inter',sans-serif;color:#18181b;transition:border .15s;}
  input:focus,textarea:focus,select:focus{outline:none;border-color:#c8922a;box-shadow:0 0 0 3px rgba(200,146,42,.1);}
  textarea{min-height:72px;resize:vertical;}
  .row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
  .toggle-group{display:flex;}
  .toggle-btn{flex:1;padding:10px;border:1px solid #d4d4d8;background:#fff;cursor:pointer;font-size:13px;font-weight:500;color:#71717a;transition:.15s;font-family:'Inter',sans-serif;}
  .toggle-btn:first-child{border-radius:8px 0 0 8px;}
  .toggle-btn:last-child{border-radius:0 8px 8px 0;border-left:none;}
  .toggle-btn.active{background:#c8922a;border-color:#c8922a;color:#fff;}
  .curso-card{border:1px solid #e4e4e7;border-radius:10px;padding:14px 16px;display:flex;flex-direction:column;gap:10px;position:relative;margin-bottom:10px;}
  .remove-btn{position:absolute;top:10px;right:12px;background:none;border:none;color:#ef4444;cursor:pointer;font-size:15px;padding:2px 6px;}
  .add-btn{display:inline-flex;align-items:center;gap:8px;background:none;border:2px dashed #d4d4d8;border-radius:8px;padding:10px 18px;color:#71717a;font-size:13px;cursor:pointer;font-family:'Inter',sans-serif;font-weight:500;width:100%;justify-content:center;transition:.15s;}
  .add-btn:hover{border-color:#c8922a;color:#c8922a;}
  .save-bar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e4e4e7;padding:14px 24px;z-index:100;}
  .save-bar-inner{max-width:820px;margin:0 auto;display:flex;align-items:center;gap:12px;}
  .btn-save{background:#c8922a;color:#fff;border:none;border-radius:8px;padding:12px 32px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;}
  .btn-save:disabled{opacity:.5;cursor:not-allowed;}
  .btn-preview{background:#18181b;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;text-decoration:none;}
  .toast{padding:10px 16px;border-radius:8px;font-size:13px;font-weight:500;display:none;}
  .toast-ok{background:#dcfce7;color:#15803d;}
  .toast-err{background:#fee2e2;color:#dc2626;}
  .hint{font-size:11.5px;color:#a1a1aa;margin-top:4px;}
  /* LOGIN */
  .login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0a0a0a;}
  .login-box{background:#1a1a1a;border:1px solid #333;border-radius:16px;padding:40px;width:340px;text-align:center;}
  .login-box img{height:40px;filter:brightness(0) invert(1);margin-bottom:24px;}
  .login-box h2{color:#fff;font-size:18px;margin-bottom:8px;}
  .login-box p{color:#888;font-size:13px;margin-bottom:24px;}
  .login-box input{background:#111;border:1px solid #333;color:#fff;margin-bottom:12px;}
  .login-box input:focus{border-color:#c8922a;}
  .login-box button{width:100%;background:#c8922a;color:#000;border:none;border-radius:8px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;}
  .login-err{color:#ef4444;font-size:12px;margin-top:8px;}
  .logout-link{margin-left:auto;font-size:12px;color:#888;text-decoration:none;cursor:pointer;}
  .logout-link:hover{color:#e8b84b;}
  .status-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;}
  .status-txt{font-size:12px;color:#4ade80;}
</style>
</head>
<body>

<?php if (!$isLoggedIn): ?>
<div class="login-wrap">
  <div class="login-box">
    <img src="logo-grupo-venda.png" alt="Grupo Venda">
    <h2>Admin Panel</h2>
    <p>Estética Paliativa · Link da Bio</p>
    <form method="POST">
      <input type="password" name="pwd" placeholder="Senha de acesso" autofocus>
      <button type="submit">Entrar</button>
    </form>
    <?php if (!empty($loginError)): ?>
      <div class="login-err">Senha incorreta</div>
    <?php endif; ?>
  </div>
</div>
<?php else: ?>

<div class="topbar">
  <img src="logo-grupo-venda.png" alt="Grupo Venda">
  <span>Admin · Link Estética Paliativa</span>
  <span class="status-dot" id="status-dot" style="margin-left:auto;"></span>
  <span class="status-txt" id="status-txt">Conectado</span>
  <a href="?logout" class="logout-link">Sair</a>
</div>

<div class="container">

  <div class="card">
    <div class="card-header"><h2>WhatsApp</h2></div>
    <div class="card-body">
      <div>
        <label>Número (DDI+DDD, sem espaço)</label>
        <input type="text" id="wa" placeholder="5514999999999">
        <div class="hint">Exemplo: 5514996908256</div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h2>Evento Presencial</h2>
      <span class="badge badge-form" id="modo-badge">Pré-inscrição</span>
    </div>
    <div class="card-body">
      <div>
        <label>Modo do botão</label>
        <div class="toggle-group">
          <button class="toggle-btn active" id="btn-form" onclick="setModo('form')">Pré-inscrição (WhatsApp)</button>
          <button class="toggle-btn" id="btn-compra" onclick="setModo('compra')">Vagas abertas (link direto)</button>
        </div>
        <div class="hint">Alterne quando as vagas abrirem</div>
      </div>
      <div class="row">
        <div><label>Cidade</label><input type="text" id="p-cidade"></div>
        <div><label>Data</label><input type="text" id="p-data"></div>
      </div>
      <div><label>Título do botão</label><input type="text" id="p-titulo"></div>
      <div><label>Descrição</label><textarea id="p-desc"></textarea></div>
      <div id="campo-wa-msg">
        <label>Mensagem WhatsApp</label>
        <textarea id="p-wa-msg"></textarea>
      </div>
      <div id="campo-link-compra" style="display:none;">
        <label>Link de compra</label>
        <input type="url" id="p-link-compra" placeholder="https://...">
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h2>Formações Online</h2></div>
    <div class="card-body">
      <div id="cursos-list"></div>
      <button class="add-btn" onclick="addCurso()">+ Adicionar formação</button>
    </div>
  </div>
</div>

<div class="save-bar">
  <div class="save-bar-inner">
    <button class="btn-save" id="btn-salvar" onclick="salvar()">Salvar e publicar</button>
    <a class="btn-preview" href="link-estetica-paliativa.html" target="_blank">Ver página</a>
    <span class="toast toast-ok" id="toast-ok">Salvo com sucesso!</span>
    <span class="toast toast-err" id="toast-err">Erro ao salvar</span>
  </div>
</div>

<script>
let modo = 'form';
let cursosData = [];

async function loadConfig() {
  try {
    const res = await fetch('/data/config.json?v=' + Date.now());
    const cfg = await res.json();
    populateForm(cfg);
    setStatus(true);
  } catch(e) { setStatus(false, 'Erro ao carregar'); }
}

function setStatus(ok, msg) {
  document.getElementById('status-dot').style.background = ok ? '#22c55e' : '#ef4444';
  const t = document.getElementById('status-txt');
  t.style.color = ok ? '#4ade80' : '#f87171';
  t.textContent = msg || (ok ? 'Conectado' : 'Erro');
}

function populateForm(cfg) {
  document.getElementById('wa').value = cfg.whatsapp || '';
  const p = cfg.presencial || {};
  document.getElementById('p-cidade').value = p.cidade || '';
  document.getElementById('p-data').value = p.data || '';
  document.getElementById('p-titulo').value = p.titulo || '';
  document.getElementById('p-desc').value = p.desc || '';
  document.getElementById('p-wa-msg').value = p.mensagemWhatsapp || '';
  document.getElementById('p-link-compra').value = p.linkCompra || '';
  setModo(p.modo || 'form');
  cursosData = cfg.cursos || [];
  renderCursos();
}

function setModo(m) {
  modo = m;
  document.getElementById('btn-form').classList.toggle('active', m === 'form');
  document.getElementById('btn-compra').classList.toggle('active', m === 'compra');
  document.getElementById('campo-wa-msg').style.display = m === 'form' ? '' : 'none';
  document.getElementById('campo-link-compra').style.display = m === 'compra' ? '' : 'none';
  const b = document.getElementById('modo-badge');
  b.textContent = m === 'form' ? 'Pré-inscrição' : 'Vagas abertas';
  b.className = 'badge ' + (m === 'form' ? 'badge-form' : 'badge-compra');
}

function escHtml(s){ return (s||'').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

function renderCursos() {
  document.getElementById('cursos-list').innerHTML = cursosData.map((c,i) => `
    <div class="curso-card">
      <button class="remove-btn" onclick="removeCurso(${i})">✕</button>
      <div class="row">
        <div><label>Nome</label><input id="c${i}-nome" value="${escHtml(c.nome)}"></div>
        <div><label>Ícone</label>
          <select id="c${i}-icon">
            <option value="fa-spa" ${c.icon==='fa-spa'?'selected':''}>Spa</option>
            <option value="fa-hand-holding-medical" ${c.icon==='fa-hand-holding-medical'?'selected':''}>Mão + Cruz</option>
            <option value="fa-graduation-cap" ${c.icon==='fa-graduation-cap'?'selected':''}>Formatura</option>
            <option value="fa-star" ${c.icon==='fa-star'?'selected':''}>Estrela</option>
            <option value="fa-leaf" ${c.icon==='fa-leaf'?'selected':''}>Folha</option>
            <option value="fa-heart" ${c.icon==='fa-heart'?'selected':''}>Coração</option>
          </select>
        </div>
      </div>
      <div><label>Descrição</label><input id="c${i}-desc" value="${escHtml(c.desc)}"></div>
      <div><label>Link</label><input id="c${i}-link" value="${escHtml(c.link)}"></div>
    </div>`).join('');
}

function addCurso(){ cursosData.push({nome:'',desc:'',link:'',icon:'fa-graduation-cap'}); renderCursos(); }
function removeCurso(i){ cursosData.splice(i,1); renderCursos(); }
function getCursos(){
  return cursosData.map((_,i) => ({
    nome: document.getElementById(`c${i}-nome`).value,
    desc: document.getElementById(`c${i}-desc`).value,
    link: document.getElementById(`c${i}-link`).value,
    icon: document.getElementById(`c${i}-icon`).value,
  }));
}

async function salvar() {
  const btn = document.getElementById('btn-salvar');
  btn.disabled = true; btn.textContent = 'Salvando...';
  const config = {
    presencial: {
      modo,
      cidade: document.getElementById('p-cidade').value,
      data: document.getElementById('p-data').value,
      titulo: document.getElementById('p-titulo').value,
      desc: document.getElementById('p-desc').value,
      mensagemWhatsapp: document.getElementById('p-wa-msg').value,
      linkCompra: document.getElementById('p-link-compra').value,
    },
    cursos: getCursos(),
    whatsapp: document.getElementById('wa').value,
  };
  try {
    const res = await fetch('/api/save-config.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Authorization':'Bearer <?php echo htmlspecialchars(getenv("ADMIN_TOKEN"), ENT_QUOTES); ?>'},
      body: JSON.stringify(config),
    });
    const data = await res.json();
    if (data.ok) {
      showToast('ok');
      setStatus(true, 'Salvo ' + new Date().toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'}));
    } else { showToast('err'); }
  } catch(e) { showToast('err'); setStatus(false,'Erro'); }
  btn.disabled=false; btn.textContent='Salvar e publicar';
}

function showToast(type){
  const t = document.getElementById('toast-'+type);
  t.style.display='inline';
  setTimeout(()=>t.style.display='none',3000);
}

loadConfig();
</script>

<?php endif; ?>
</body>
</html>
