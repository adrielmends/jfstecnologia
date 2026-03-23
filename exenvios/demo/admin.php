<?php
session_start();
// MOCK DATA FOR ADMIN DEMO
$_SESSION['user_id'] = 999;
$_SESSION['user_name'] = 'Admin Demo';
$_SESSION['user_role'] = 'admin';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Ex-Envios (DEMO)</title>
    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Overlay for Mobile Sidebar -->
        <div class="sidebar-overlay" id="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <img src="../assets/img/login/logo.png" alt="Ex-Envios Logo">
            </div>

            <nav class="sidebar-menu">
                <div class="menu-label">Principal</div>
                <a href="#" class="nav-item active" onclick="switchSection('dashboard', this)">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="#" class="nav-item" onclick="switchSection('encomendas', this)">
                    <i class="fas fa-box"></i> Encomendas
                </a>
                <a href="#" class="nav-item" onclick="switchSection('locais', this)">
                    <i class="fas fa-map-marker-alt"></i> Locais / Unidade
                </a>
                <a href="#" class="nav-item" onclick="switchSection('totens', this)">
                    <i class="fas fa-microchip"></i> Totens (Hardware)
                </a>

                <div class="menu-label">Gestão</div>
                <a href="#" class="nav-item" onclick="switchSection('clientes', this)">
                    <i class="fas fa-users"></i> Clientes
                </a>
                <a href="#" class="nav-item" onclick="switchSection('servicos', this)">
                    <i class="fas fa-concierge-bell"></i> Serviços & Preços
                </a>

                <div class="menu-label">Sistema</div>
                <a href="#" class="nav-item" onclick="switchSection('config', this)">
                    <i class="fas fa-cog"></i> Configurações
                </a>
                <!-- Link to real production if needed, or just exit demo -->
                <a href="../login.html" class="nav-item" style="margin-top: auto; color: #ef4444;">
                    <i class="fas fa-sign-out-alt"></i> Sair do Demo
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Notifications (Burger for mobile) -->
                    <div class="notification-mobile" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notif-badge">2</span>
                        <div class="notification-dropdown" id="notif-dropdown">
                            <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9;">
                                <h4 style="margin:0; font-size: 0.9rem; font-weight: 800; color: var(--navy-dark);">
                                    NOTIFICAÇÕES</h4>
                            </div>
                            <div class="notif-list">
                                <div class="notification-item">
                                    <div class="notif-icon-sm" style="background:#fff7ed; color:#ea580c;"><i
                                            class="fas fa-exclamation-triangle"></i></div>
                                    <div class="notification-item-content">
                                        <h5>Alerta de Ocupação</h5>
                                        <p>Unidade <strong>Shopping Campo Grande</strong> está com 95% de ocupação.</p>
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 0.8rem; text-align: center; border-top: 1px solid #f1f5f9;">
                                <a href="#"
                                    style="font-size: 0.75rem; color: var(--orange-vibrant); font-weight: 700; text-decoration: none;">VER
                                    TODAS</a>
                            </div>
                        </div>
                    </div>
                    <h2 style="font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Visão Geral do Sistema
                    </h2>
                </div>
                <div class="header-right">
                    <div class="header-user-info">
                        <span class="user-greeting">Olá, Administrador</span>
                        <span class="wallet-badge-compact">Nível: Super Admin (Demo)</span>
                    </div>
                    <div class="user-avatar">AD</div>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- Section: Dashboard -->
                <div id="section-dashboard" class="content-section active">
                    <!-- Stats -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #fff7ed; color: #ea580c;">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Pacotes Hoje</h4>
                                <span>158</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Pontos Ativos</h4>
                                <span>42</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Status Totens</h4>
                                <span>98% OK</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #faf5ff; color: #9333ea;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Receita Diária</h4>
                                <span>R$ 4.850</span>
                            </div>
                        </div>
                    </div>

                    <!-- Locais Section -->
                    <div class="section-title">Monitoramento de Locais</div>
                    <div class="admin-table-container" style="margin-bottom: 2rem;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Local / Unidade</th>
                                    <th>Tipo</th>
                                    <th>Totens</th>
                                    <th>Lockers Ocupados</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td data-label="Unidade">Shopping Campo Grande</td>
                                    <td data-label="Tipo">Shopping</td>
                                    <td data-label="Totens">02</td>
                                    <td data-label="Ocupação">15/20</td>
                                    <td data-label="Status"><span class="location-dot"
                                            style="background: #16a34a;"></span> Operando</td>
                                </tr>
                                <tr>
                                    <td data-label="Unidade">Condomínio Royal Park</td>
                                    <td data-label="Tipo">Residencial</td>
                                    <td data-label="Totens">01</td>
                                    <td data-label="Ocupação">04/08</td>
                                    <td data-label="Status"><span class="location-dot"
                                            style="background: #16a34a;"></span> Operando</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Encomendas -->
                <div id="section-encomendas" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Gestão Global de Encomendas</div>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Rastreio</th>
                                    <th>Data/Hora</th>
                                    <th>Modal</th>
                                    <th>Peso</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                <tr>
                                    <td data-label="Rastreio">EX-12345-BR</td>
                                    <td data-label="Data">08/03/2026 14:30</td>
                                    <td data-label="Modal">SEDEX</td>
                                    <td data-label="Peso">1.5kg</td>
                                    <td data-label="Valor">R$ 45,90</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Pago</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Clientes -->
                <div id="section-clientes" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Gestão de Usuários / Clientes</div>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Saldo Atual</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                <tr>
                                    <td data-label="Nome"><strong>Cliente Demo</strong></td>
                                    <td data-label="Email">cliente@demo.com</td>
                                    <td data-label="Saldo"><strong>R$ 1.250,75</strong></td>
                                    <td data-label="Ações">
                                        <button class="close-modal" style="width:30px; height:30px;"><i class="fas fa-wallet"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Servicos -->
                <div id="section-servicos" class="content-section">
                    <div class="section-title">Serviços & Preços</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Caixa Pequena</td>
                                    <td>Empacotamento</td>
                                    <td>R$ 6,90</td>
                                    <td><span class="status-badge status-deposited">Ativo</span></td>
                                </tr>
                                <tr>
                                    <td>Frete SEDEX</td>
                                    <td>Fretes</td>
                                    <td>Markup 30%</td>
                                    <td><span class="status-badge status-deposited">Ativo</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('sidebar-overlay').classList.toggle('active');
        }

        function toggleNotifications() {
            const d = document.getElementById('notif-dropdown');
            d.style.display = d.style.display === 'block' ? 'none' : 'block';
        }

        function switchSection(sectionId, element) {
            document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
            if (element) element.classList.add('active');
            
            document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
            const target = document.getElementById('section-' + sectionId);
            if (target) target.classList.add('active');

            const titles = {
                'dashboard': 'Visão Geral do Sistema',
                'encomendas': 'Encomendas',
                'locais': 'Monitoramento de Locais',
                'totens': 'Hardware',
                'clientes': 'Gestão de Clientes',
                'servicos': 'Serviços & Preços',
                'config': 'Configurações'
            };
            document.querySelector('.top-header h2').innerText = titles[sectionId] || 'Admin';

            if (window.innerWidth < 992) toggleSidebar();
        }
    </script>
</body>
</html>
