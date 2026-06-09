<?php
session_start();
$adminToken = getenv('ADMIN_TOKEN') ?: 'sem-token-configurado';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pwd'])) {
    if ($_POST['pwd'] === $adminToken) {
        $_SESSION['admin'] = true;
        header('Location: admin-link-estetica.php');
        exit;
    } else {
        $loginError = true;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-link-estetica.php');
    exit;
}

$isLoggedIn = !empty($_SESSION['admin']);
$token = $isLoggedIn ? htmlspecialchars(getenv('ADMIN_TOKEN'), ENT_QUOTES) : '';
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
  .container{max-width:860px;margin:0 auto;padding:0 24px 80px;}
  .tabs-nav{display:flex;gap:4px;padding:20px 0 0;border-bottom:2px solid #e4e4e7;margin-bottom:24px;}
  .tab-btn{padding:10px 20px;background:none;border:none;border-bottom:2px solid transparent;margin-bottom:-2px;font-size:14px;font-weight:600;color:#71717a;cursor:pointer;font-family:'Inter',sans-serif;transition:.15s;}
  .tab-btn.active{color:#18181b;border-bottom-color:#c8922a;}
  .tab-panel{display:none;}.tab-panel.active{display:block;}
  .card{background:#fff;border-radius:12px;border:1px solid #e4e4e7;margin-bottom:20px;overflow:hidden;}
  .card-header{padding:18px 24px;background:#fafafa;border-bottom:1px solid #e4e4e7;display:flex;align-items:center;gap:10px;}
  .card-header h2{font-size:15px;font-weight:600;}
  .badge{font-size:11px;padding:3px 10px;border-radius:20px;font-weight:600;}
  .badge-form{background:#fef3c7;color:#b45309;}
  .badge-compra{background:#dcfce7;color:#15803d;}
  .badge-inscricao{background:#ede9fe;color:#7c3aed;}
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
  .toggle-btn.active-purple{background:#7c3aed;border-color:#7c3aed;color:#fff;}
  .curso-card{border:1px solid #e4e4e7;border-radius:10px;padding:14px 16px;display:flex;flex-direction:column;gap:10px;position:relative;margin-bottom:10px;}
  .remove-btn{position:absolute;top:10px;right:12px;background:none;border:none;color:#ef4444;cursor:pointer;font-size:15px;padding:2px 6px;}
  .add-btn{display:inline-flex;align-items:center;gap:8px;background:none;border:2px dashed #d4d4d8;border-radius:8px;padding:10px 18px;color:#71717a;font-size:13px;cursor:pointer;font-family:'Inter',sans-serif;font-weight:500;width:100%;justify-content:center;transition:.15s;}
  .add-btn:hover{border-color:#c8922a;color:#c8922a;}
  .save-bar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid #e4e4e7;padding:14px 24px;z-index:100;}
  .save-bar-inner{max-width:860px;margin:0 auto;display:flex;align-items:center;gap:12px;}
  .btn-save{background:#c8922a;color:#fff;border:none;border-radius:8px;padding:12px 32px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;}
  .btn-save:disabled{opacity:.5;cursor:not-allowed;}
  .btn-preview{background:#18181b;color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;text-decoration:none;}
  .toast{padding:10px 16px;border-radius:8px;font-size:13px;font-weight:500;display:none;}
  .toast-ok{background:#dcfce7;color:#15803d;}
  .toast-err{background:#fee2e2;color:#dc2626;}
  .hint{font-size:11.5px;color:#a1a1aa;margin-top:4px;}
  /* Oportunidades */
  .curso-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px;}
  .curso-tab{padding:7px 16px;border-radius:20px;border:1px solid #d4d4d8;background:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:'Inter',sans-serif;color:#52525b;transition:.15s;}
  .curso-tab.active{background:#7c3aed;border-color:#7c3aed;color:#fff;}
  .leads-table{width:100%;border-collapse:collapse;font-size:13px;}
  .leads-table th{text-align:left;padding:8px 12px;font-size:11px;font-weight:700;color:#71717a;text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid #e4e4e7;}
  .leads-table td{padding:10px 12px;border-bottom:1px solid #f4f4f5;vertical-align:middle;}
  .leads-table tr:last-child td{border-bottom:none;}
  .leads-table tr:hover td{background:#fafafa;}
  .btn-sm{padding:5px 10px;border-radius:6px;border:none;font-size:12px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;}
  .btn-edit{background:#ede9fe;color:#7c3aed;}
  .btn-edit:hover{background:#ddd6fe;}
  .btn-del{background:#fee2e2;color:#dc2626;}
  .btn-del:hover{background:#fecaca;}
  .btn-csv{background:#18181b;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;display:inline-flex;align-items:center;gap:6px;}
  .empty-state{text-align:center;padding:40px;color:#a1a1aa;font-size:14px;}
  .lead-count{background:#f4f4f5;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:600;color:#52525b;margin-left:6px;}
  /* Modal edição */
  .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:200;align-items:center;justify-content:center;}
  .modal-overlay.open{display:flex;}
  .modal{background:#fff;border-radius:16px;padding:28px;width:100%;max-width:420px;display:flex;flex-direction:column;gap:14px;}
  .modal h3{font-size:16px;font-weight:700;}
  .modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:4px;}
  .btn-cancel{background:#f4f4f5;color:#52525b;border:none;border-radius:8px;padding:10px 20px;font-size:14px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;}
  .btn-confirm{background:#c8922a;color:#fff;border:none;border-radius:8px;padding:10px 20px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif;}
  /* Login */
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

  /* Funil */
  .funil-wrap { display: flex; flex-direction: column; gap: 8px; }
  .funil-step { display: flex; align-items: center; gap: 14px; padding: 14px 18px; background: #fafafa; border-radius: 10px; border: 1px solid #ebebeb; }
  .funil-step.top { background: #fff; border-color: #c8922a; }
  .funil-step .fs-label { flex: 1; font-size: 14px; font-weight: 600; color: #111; }
  .funil-step .fs-sub { font-size: 12px; color: #888; font-weight: 400; margin-top: 2px; }
  .funil-step .fs-count { font-size: 22px; font-weight: 800; color: #111; min-width: 60px; text-align: right; }
  .funil-step .fs-pct { font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 20px; min-width: 52px; text-align: center; }
  .pct-high { background: #dcfce7; color: #15803d; }
  .pct-mid  { background: #fef3c7; color: #b45309; }
  .pct-low  { background: #fee2e2; color: #dc2626; }
  .pct-neu  { background: #f4f4f5; color: #52525b; }
  .funil-divider { display: flex; align-items: center; gap: 10px; padding: 0 18px; }
  .funil-divider::before { content: ''; flex: 1; height: 1px; background: #e4e4e7; }
  .funil-divider span { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #a1a1aa; white-space: nowrap; }
  .funil-divider::after { content: ''; flex: 1; height: 1px; background: #e4e4e7; }
  .funil-sub { padding-left: 28px; display: flex; flex-direction: column; gap: 6px; }
  .funil-sub .funil-step { background: #fff; }
  .eventos-raw { margin-top: 8px; }
  .eventos-raw summary { font-size: 13px; font-weight: 600; color: #71717a; cursor: pointer; padding: 8px 0; }
  .eventos-raw table { width: 100%; font-size: 13px; margin-top: 8px; border-collapse: collapse; }
  .eventos-raw td { padding: 6px 8px; border-bottom: 1px solid #f4f4f5; }
  .eventos-raw td:last-child { text-align: right; font-weight: 700; }
  .funil-updated { font-size: 11px; color: #a1a1aa; text-align: right; margin-top: 8px; }
  @media(max-width:600px){.row{grid-template-columns:1fr;} .leads-table td:nth-child(2){display:none;}}
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

  <div class="tabs-nav">
    <button class="tab-btn active" onclick="switchTab('config')">Configuracoes</button>
    <button class="tab-btn" onclick="switchTab('opor')" id="tab-opor-btn">Oportunidades <span id="total-leads-badge" class="lead-count" style="display:none">0</span></button>
  </div>

  <!-- ==================== ABA CONFIGURACOES ==================== -->
  <div class="tab-panel active" id="tab-config">

    <div class="card">
      <div class="card-header"><h2>WhatsApp</h2></div>
      <div class="card-body">
        <div>
          <label>Numero (DDI+DDD, sem espaco)</label>
          <input type="text" id="wa" placeholder="5514999999999">
          <div class="hint">Exemplo: 5514996908256</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Evento Presencial</h2>
        <span class="badge badge-form" id="modo-badge">Pre-inscricao</span>
      </div>
      <div class="card-body">
        <div>
          <label>Modo do botao</label>
          <div class="toggle-group">
            <button class="toggle-btn active" id="btn-form" onclick="setModo('form')">Pre-inscricao (WhatsApp)</button>
            <button class="toggle-btn" id="btn-captura" onclick="setModo('form-captura')">Formulario (captura leads)</button>
            <button class="toggle-btn" id="btn-compra" onclick="setModo('compra')">Vagas abertas (link direto)</button>
          </div>
          <div class="hint">Alterne quando as vagas abrirem</div>
        </div>
        <div class="row">
          <div><label>Cidade</label><input type="text" id="p-cidade"></div>
          <div><label>Data</label><input type="text" id="p-data"></div>
        </div>
        <div><label>Titulo do botao</label><input type="text" id="p-titulo"></div>
        <div><label>Descricao</label><textarea id="p-desc"></textarea></div>
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
      <div class="card-header"><h2>Formacoes Online</h2></div>
      <div class="card-body">
        <div id="cursos-list"></div>
        <button class="add-btn" onclick="addCurso()">+ Adicionar formacao</button>
      </div>
    </div>

  </div>

  <!-- ==================== ABA OPORTUNIDADES ==================== -->
  <div class="tab-panel" id="tab-opor">

    <!-- FUNIL -->
    <div class="card" id="card-funil">
      <div class="card-header" style="justify-content:space-between;">
        <h2>Funil de cliques</h2>
        <button class="btn-csv" onclick="carregarFunil()" id="btn-refresh-funil">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.29"/></svg>
          Atualizar
        </button>
      </div>
      <div class="card-body">
        <div id="funil-content"><div class="empty-state">Carregando...</div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header" style="justify-content:space-between;">
        <h2>Leads por curso</h2>
        <button class="btn-csv" id="btn-export-csv" onclick="exportarCSV()">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Exportar CSV
        </button>
      </div>
      <div class="card-body">
        <div class="curso-tabs" id="leads-tabs"></div>
        <div id="leads-content"><div class="empty-state">Carregando...</div></div>
      </div>
    </div>

  </div>

</div>

<div class="save-bar" id="save-bar">
  <div class="save-bar-inner">
    <button class="btn-save" id="btn-salvar" onclick="salvar()">Salvar e publicar</button>
    <a class="btn-preview" href="/" target="_blank">Ver pagina</a>
    <span class="toast toast-ok" id="toast-ok">Salvo com sucesso!</span>
    <span class="toast toast-err" id="toast-err">Erro ao salvar</span>
  </div>
</div>

<!-- MODAL EDICAO DE LEAD -->
<div class="modal-overlay" id="modal-edit">
  <div class="modal">
    <h3>Editar lead</h3>
    <div>
      <label>Nome completo</label>
      <input type="text" id="edit-nome">
    </div>
    <div>
      <label>E-mail</label>
      <input type="email" id="edit-email">
    </div>
    <div>
      <label>WhatsApp</label>
      <input type="tel" id="edit-whatsapp">
    </div>
    <div class="modal-actions">
      <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
      <button class="btn-confirm" onclick="salvarEdicao()">Salvar</button>
    </div>
  </div>
</div>

<script>
const ADMIN_TOKEN = '<?php echo $token; ?>';
let modo = 'form';
let cursosData = [];
let leadsData  = {};
let activeLeadSlug = null;
let editingLead = null;

/* ===== TABS ===== */
function switchTab(name) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  const btn = name === 'config'
    ? document.querySelector('.tab-btn:first-child')
    : document.querySelector('.tab-btn:nth-child(2)');
  btn.classList.add('active');
  document.getElementById('save-bar').style.display = name === 'config' ? '' : 'none';
  if (name === 'opor') { carregarFunil(); carregarLeads(); }
}

/* ===== CONFIG ===== */
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
  document.getElementById('p-data').value   = p.data   || '';
  document.getElementById('p-titulo').value = p.titulo || '';
  document.getElementById('p-desc').value   = p.desc   || '';
  document.getElementById('p-wa-msg').value = p.mensagemWhatsapp || '';
  document.getElementById('p-link-compra').value = p.linkCompra || '';
  setModo(p.modo || 'form');
  cursosData = cfg.cursos || [];
  renderCursos();
}

function setModo(m) {
  modo = m;
  document.getElementById('btn-form').classList.toggle('active', m === 'form');
  document.getElementById('btn-captura').classList.toggle('active', m === 'form-captura');
  document.getElementById('btn-compra').classList.toggle('active', m === 'compra');
  document.getElementById('campo-wa-msg').style.display      = m === 'form'         ? '' : 'none';
  document.getElementById('campo-link-compra').style.display = m === 'compra'       ? '' : 'none';
  const b = document.getElementById('modo-badge');
  if (m === 'form')         { b.textContent = 'Pre-inscricao WhatsApp'; b.className = 'badge badge-form'; }
  else if (m === 'form-captura') { b.textContent = 'Formulario'; b.className = 'badge badge-inscricao'; }
  else                      { b.textContent = 'Vagas abertas';  b.className = 'badge badge-compra'; }
}

function escHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

function setModoCurso(i, m) {
  cursosData[i] = cursosData[i] || {};
  cursosData[i].modo = m;
  const btnLink = document.getElementById(`c${i}-modo-link`);
  const btnForm = document.getElementById(`c${i}-modo-form`);
  if (btnLink) { btnLink.classList.toggle('active', m === 'link'); btnLink.classList.remove('active-purple'); }
  if (btnForm) {
    btnForm.classList.toggle('active-purple', m === 'form');
    btnForm.classList.remove('active');
    if (m !== 'form') btnForm.classList.remove('active-purple');
  }
}

function renderCursos() {
  document.getElementById('cursos-list').innerHTML = cursosData.map((c,i) => `
    <div class="curso-card">
      <button class="remove-btn" onclick="removeCurso(${i})">x</button>
      <div class="row">
        <div><label>Nome</label><input id="c${i}-nome" value="${escHtml(c.nome)}"></div>
        <div><label>Icone</label>
          <select id="c${i}-icon">
            <option value="fa-spa" ${c.icon==='fa-spa'?'selected':''}>Spa</option>
            <option value="fa-hand-holding-medical" ${c.icon==='fa-hand-holding-medical'?'selected':''}>Mao + Cruz</option>
            <option value="fa-graduation-cap" ${c.icon==='fa-graduation-cap'?'selected':''}>Formatura</option>
            <option value="fa-star" ${c.icon==='fa-star'?'selected':''}>Estrela</option>
            <option value="fa-leaf" ${c.icon==='fa-leaf'?'selected':''}>Folha</option>
            <option value="fa-heart" ${c.icon==='fa-heart'?'selected':''}>Coracao</option>
          </select>
        </div>
      </div>
      <div><label>Descricao</label><input id="c${i}-desc" value="${escHtml(c.desc)}"></div>
      <div>
        <label>Modo do botao</label>
        <div class="toggle-group">
          <button class="toggle-btn ${(c.modo||'link')==='link'?'active':''}" id="c${i}-modo-link" onclick="setModoCurso(${i},'link')">Link direto</button>
          <button class="toggle-btn ${(c.modo||'link')==='form'?'active-purple':''}" id="c${i}-modo-form" onclick="setModoCurso(${i},'form')">Pre-inscricao</button>
        </div>
        <div class="hint">Link direto: abre a landing page do curso · Pre-inscricao: coleta nome, e-mail e WhatsApp</div>
      </div>
      <div id="c${i}-link-wrap" style="${(c.modo||'link')==='form'?'display:none':''}">
        <label>Link da landing page</label>
        <input id="c${i}-link" value="${escHtml(c.link)}">
      </div>
    </div>`).join('');

  // esconde campo link quando modo form
  cursosData.forEach((c,i) => {
    const wrap = document.getElementById(`c${i}-link-wrap`);
    const btnForm = document.getElementById(`c${i}-modo-form`);
    if (!wrap || !btnForm) return;
    btnForm.addEventListener('click', () => { wrap.style.display = 'none'; });
    document.getElementById(`c${i}-modo-link`).addEventListener('click', () => { wrap.style.display = ''; });
  });
}

function addCurso(){ cursosData.push({nome:'',desc:'',link:'',icon:'fa-graduation-cap',modo:'link'}); renderCursos(); }
function removeCurso(i){ cursosData.splice(i,1); renderCursos(); }

function getCursos() {
  return cursosData.map((_,i) => ({
    nome: document.getElementById(`c${i}-nome`).value,
    desc: document.getElementById(`c${i}-desc`).value,
    link: document.getElementById(`c${i}-link`) ? document.getElementById(`c${i}-link`).value : '',
    icon: document.getElementById(`c${i}-icon`).value,
    modo: cursosData[i].modo || 'link',
  }));
}

async function salvar() {
  const btn = document.getElementById('btn-salvar');
  btn.disabled = true; btn.textContent = 'Salvando...';
  const config = {
    presencial: {
      modo,
      cidade: document.getElementById('p-cidade').value,
      data:   document.getElementById('p-data').value,
      titulo: document.getElementById('p-titulo').value,
      desc:   document.getElementById('p-desc').value,
      mensagemWhatsapp: document.getElementById('p-wa-msg').value,
      linkCompra: document.getElementById('p-link-compra').value,
    },
    cursos: getCursos(),
    whatsapp: document.getElementById('wa').value,
  };
  try {
    const res = await fetch('/api/save-config.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Authorization':'Bearer ' + ADMIN_TOKEN},
      body: JSON.stringify(config),
    });
    const data = await res.json();
    if (data.ok) {
      showToast('ok');
      setStatus(true, 'Salvo ' + new Date().toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'}));
    } else { showToast('err'); }
  } catch(e) { showToast('err'); setStatus(false,'Erro'); }
  btn.disabled = false; btn.textContent = 'Salvar e publicar';
}

function showToast(type) {
  const t = document.getElementById('toast-' + type);
  t.style.display = 'inline';
  setTimeout(() => t.style.display = 'none', 3000);
}


/* ===== FUNIL ===== */
async function carregarFunil() {
  document.getElementById('funil-content').innerHTML = '<div class="empty-state">Carregando...</div>';
  try {
    const res = await fetch('/api/get-analytics.php', {
      headers: { 'Authorization': 'Bearer ' + ADMIN_TOKEN }
    });
    const data = await res.json();
    renderFunil(data.data || { events: {} });
  } catch(e) {
    document.getElementById('funil-content').innerHTML = '<div class="empty-state">Erro ao carregar dados</div>';
  }
}

function pctClass(v) {
  if (v === null) return 'pct-neu';
  if (v >= 50) return 'pct-high';
  if (v >= 20) return 'pct-mid';
  return 'pct-low';
}

function fmtPct(v) {
  if (v === null) return '<span class="fs-pct pct-neu">-</span>';
  const cls = pctClass(v);
  return `<span class="fs-pct ${cls}">${v}%</span>`;
}

function stepHtml(label, sub, count, pct) {
  return `<div class="funil-step">
    <div style="flex:1"><div class="fs-label">${label}</div>${sub ? `<div class="fs-sub">${sub}</div>` : ''}</div>
    <div class="fs-count">${count.toLocaleString('pt-BR')}</div>
    ${fmtPct(pct)}
  </div>`;
}

function renderFunil(data) {
  const e = data.events || {};
  const n = k => e[k] || 0;
  const pct = (a, b) => b === 0 ? null : Math.round((a / b) * 100);

  const visitantes   = n('view_home');
  const presencial   = n('view_presencial');
  const online       = n('view_online');
  const waPresencial = n('presencial_whatsapp_preinscricao');
  const formPresencial = n('presencial_form_captura');

  // Soma todos os cliques em cursos (link e form)
  const cursosLink = Object.entries(e).filter(([k]) => k.startsWith('curso_') && !k.startsWith('curso_form_')).reduce((s,[,v]) => s+v, 0);
  const cursosForm = Object.entries(e).filter(([k]) => k.startsWith('curso_form_')).reduce((s,[,v]) => s+v, 0);
  const formSuccess = n('form_submit_success');
  const totalForms  = formPresencial + cursosForm;

  // Cursos individuais
  const cursosDetalhes = Object.entries(e)
    .filter(([k]) => k.startsWith('curso_'))
    .sort(([,a],[,b]) => b - a);

  let html = '<div class="funil-wrap">';

  // Visitantes
  html += `<div class="funil-step top">
    <div style="flex:1"><div class="fs-label">Visitantes na pagina</div><div class="fs-sub">view_home</div></div>
    <div class="fs-count">${visitantes.toLocaleString('pt-BR')}</div>
    <span class="fs-pct pct-neu">100%</span>
  </div>`;

  // Presencial
  html += '<div class="funil-divider"><span>Evento Presencial</span></div>';
  html += '<div class="funil-sub">';
  html += stepHtml('Clicou em Evento Presencial', 'view_presencial', presencial, pct(presencial, visitantes));
  if (waPresencial > 0 || formPresencial > 0) {
    html += '<div class="funil-sub">';
    if (waPresencial > 0)   html += stepHtml('Clicou no WhatsApp', 'presencial_whatsapp_preinscricao', waPresencial, pct(waPresencial, presencial));
    if (formPresencial > 0) html += stepHtml('Abriu formulario', 'presencial_form_captura', formPresencial, pct(formPresencial, presencial));
    html += '</div>';
  }
  html += '</div>';

  // Online
  html += '<div class="funil-divider"><span>Formacoes Online</span></div>';
  html += '<div class="funil-sub">';
  html += stepHtml('Clicou em Formacoes Online', 'view_online', online, pct(online, visitantes));
  if (cursosDetalhes.length > 0) {
    html += '<div class="funil-sub">';
    cursosDetalhes.forEach(([key, val]) => {
      const isForm = key.startsWith('curso_form_');
      const nome = isForm ? key.replace('curso_form_', '') : key.replace('curso_', '');
      const label = isForm ? `Abriu formulario: ${nome}` : `Clicou no link: ${nome}`;
      html += stepHtml(label, key, val, pct(val, online));
    });
    html += '</div>';
  }
  html += '</div>';

  // Conversao formularios
  if (totalForms > 0 || formSuccess > 0) {
    html += '<div class="funil-divider"><span>Formularios</span></div>';
    html += '<div class="funil-sub">';
    html += stepHtml('Formularios abertos', 'presencial + cursos', totalForms, pct(totalForms, visitantes));
    html += stepHtml('Formularios enviados com sucesso', 'form_submit_success', formSuccess, pct(formSuccess, totalForms));
    html += '</div>';
  }

  html += '</div>';

  // Todos os eventos (expansivel)
  if (Object.keys(e).length > 0) {
    const rows = Object.entries(e).sort(([,a],[,b]) => b-a)
      .map(([k,v]) => `<tr><td>${escHtml(k)}</td><td>${v.toLocaleString('pt-BR')}</td></tr>`).join('');
    html += `<details class="eventos-raw">
      <summary>Ver todos os eventos (${Object.keys(e).length})</summary>
      <table><tbody>${rows}</tbody></table>
    </details>`;
  }

  if (data.updatedAt) {
    html += `<div class="funil-updated">Ultima atualizacao: ${formatarData(data.updatedAt)}</div>`;
  }

  document.getElementById('funil-content').innerHTML = html;
}

/* ===== OPORTUNIDADES ===== */
async function carregarLeads() {
  try {
    const res = await fetch('/api/get-leads.php', {
      headers: {'Authorization': 'Bearer ' + ADMIN_TOKEN}
    });
    const data = await res.json();
    leadsData = data.leads || {};
    renderLeadsTabs();
  } catch(e) {
    document.getElementById('leads-content').innerHTML = '<div class="empty-state">Erro ao carregar leads</div>';
  }
}

function toSlugJS(str) {
  const map = {'a':['a','á','â','ã','à'],'e':['e','é','ê'],'i':['i','í'],'o':['o','ó','ô','õ'],'u':['u','ú'],'c':['c','ç']};
  let s = str.toLowerCase();
  for (const [r, chars] of Object.entries(map)) chars.forEach(c => { s = s.split(c).join(r); });
  return s.replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
}

function renderLeadsTabs() {
  const tabsEl = document.getElementById('leads-tabs');
  const config = Array.from(document.querySelectorAll('[id^="c"][id$="-nome"]'))
    .map(el => ({ nome: el.value, slug: toSlugJS(el.value) }))
    .filter(c => c.nome);

  // tambem inclui slugs que ja tem leads mas podem nao estar no config
  const allSlugs = new Set([...config.map(c => c.slug), ...Object.keys(leadsData)]);
  const allCursos = [...allSlugs].map(slug => ({
    slug,
    nome: config.find(c => c.slug === slug)?.nome || slug,
    count: (leadsData[slug] || []).length
  }));

  let totalLeads = Object.values(leadsData).reduce((a, arr) => a + arr.length, 0);
  const badge = document.getElementById('total-leads-badge');
  badge.textContent = totalLeads;
  badge.style.display = totalLeads > 0 ? '' : 'none';

  if (allCursos.length === 0) {
    tabsEl.innerHTML = '';
    document.getElementById('leads-content').innerHTML = '<div class="empty-state">Nenhum curso cadastrado ainda</div>';
    return;
  }

  if (!activeLeadSlug || !allSlugs.has(activeLeadSlug)) {
    activeLeadSlug = allCursos[0].slug;
  }

  tabsEl.innerHTML = allCursos.map(c => `
    <button class="curso-tab ${c.slug === activeLeadSlug ? 'active' : ''}"
            onclick="selectLeadTab('${c.slug}')">
      ${escHtml(c.nome)} <span class="lead-count">${c.count}</span>
    </button>`).join('');

  renderLeadsTable(activeLeadSlug);
}

function selectLeadTab(slug) {
  activeLeadSlug = slug;
  document.querySelectorAll('.curso-tab').forEach(t => t.classList.remove('active'));
  event.target.closest('.curso-tab').classList.add('active');
  renderLeadsTable(slug);
}

function renderLeadsTable(slug) {
  const leads = (leadsData[slug] || []).slice().reverse();
  const el = document.getElementById('leads-content');

  if (leads.length === 0) {
    el.innerHTML = '<div class="empty-state">Nenhum lead para este curso ainda</div>';
    return;
  }

  el.innerHTML = `
    <table class="leads-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>E-mail</th>
          <th>WhatsApp</th>
          <th>Data</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        ${leads.map(l => `
          <tr>
            <td><strong>${escHtml(l.nome)}</strong></td>
            <td>${escHtml(l.email)}</td>
            <td><a href="https://wa.me/${l.whatsapp}" target="_blank" style="color:#22c55e;text-decoration:none;">${l.whatsapp}</a></td>
            <td style="color:#71717a;white-space:nowrap;">${formatarData(l.criadoEm)}</td>
            <td style="white-space:nowrap;display:flex;gap:6px;">
              <button class="btn-sm btn-edit" onclick="abrirEdicao('${escHtml(slug)}','${escHtml(l.id)}','${escHtml(l.nome)}','${escHtml(l.email)}','${escHtml(l.whatsapp)}')">Editar</button>
              <button class="btn-sm btn-del" onclick="deletarLead('${escHtml(slug)}','${escHtml(l.id)}','${escHtml(l.nome)}')">Excluir</button>
            </td>
          </tr>`).join('')}
      </tbody>
    </table>`;
}

function formatarData(iso) {
  if (!iso) return '-';
  const d = new Date(iso);
  return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
}

/* ===== EDICAO ===== */
function abrirEdicao(slug, id, nome, email, whatsapp) {
  editingLead = { slug, id };
  document.getElementById('edit-nome').value = nome;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-whatsapp').value = whatsapp;
  document.getElementById('modal-edit').classList.add('open');
}

function fecharModal() {
  document.getElementById('modal-edit').classList.remove('open');
  editingLead = null;
}

async function salvarEdicao() {
  if (!editingLead) return;
  const body = {
    action: 'edit',
    slug: editingLead.slug,
    id: editingLead.id,
    data: {
      nome: document.getElementById('edit-nome').value,
      email: document.getElementById('edit-email').value,
      whatsapp: document.getElementById('edit-whatsapp').value,
    }
  };
  try {
    const res = await fetch('/api/update-lead.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Authorization':'Bearer ' + ADMIN_TOKEN},
      body: JSON.stringify(body),
    });
    const data = await res.json();
    if (data.ok) {
      fecharModal();
      await carregarLeads();
    }
  } catch(e) { alert('Erro ao salvar'); }
}

/* ===== EXCLUSAO ===== */
async function deletarLead(slug, id, nome) {
  if (!confirm(`Excluir o lead de ${nome}?`)) return;
  try {
    const res = await fetch('/api/update-lead.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Authorization':'Bearer ' + ADMIN_TOKEN},
      body: JSON.stringify({ action: 'delete', slug, id }),
    });
    const data = await res.json();
    if (data.ok) await carregarLeads();
  } catch(e) { alert('Erro ao excluir'); }
}

/* ===== EXPORT CSV ===== */
function exportarCSV() {
  const leads = leadsData[activeLeadSlug] || [];
  if (leads.length === 0) { alert('Nenhum lead para exportar'); return; }
  const header = ['Nome','E-mail','WhatsApp','Data'];
  const rows = leads.map(l => [l.nome, l.email, l.whatsapp, l.criadoEm].map(v => `"${(v||'').replace(/"/g,'""')}"`));
  const csv = [header, ...rows].map(r => r.join(',')).join('\n');
  const blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = `leads-${activeLeadSlug}-${new Date().toISOString().slice(0,10)}.csv`;
  a.click(); URL.revokeObjectURL(url);
}

/* Fecha modal ao clicar fora */
document.getElementById('modal-edit').addEventListener('click', function(e) {
  if (e.target === this) fecharModal();
});

loadConfig();
</script>

<?php endif; ?>
</body>
</html>
