<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}
require_once 'config.php';
$db = getDB();
$stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$balance = $user['balance'] ?? 0.00;
$userName = $_SESSION['user_name'];
$userEmail = $_SESSION['user_email'] ?? '';
// Get initials
$nameParts = explode(' ', $userName);
$initials = '';
if (count($nameParts) >= 2) {
    $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
} else {
    $initials = strtoupper(substr($userName, 0, 2));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Ex-Envios</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/painel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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

        /* Modal Premium Styles - Robust Version */
        .modal-premium {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 43, 73, 0.8) !important;
            display: none; /* Controlled by JS flex */
            align-items: flex-start; /* Start for better scroll handling */
            justify-content: center;
            z-index: 10000;
            overflow-y: auto;
            padding: 2rem 0; /* Padding top/bottom for scrolling space */
        }
        .modal-content-premium {
            background: #ffffff !important;
            width: 95%;
            max-width: 600px;
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3) !important;
            animation: slideUp 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
            position: relative;
            display: flex;
            flex-direction: column;
            opacity: 1 !important;
            visibility: visible !important;
            margin: auto; /* Centering trick with flex-start parent */
        }
        .modal-header-premium {
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-bottom: 1px solid #edf2f7;
        }
        .modal-header-premium h2 {
            margin: 0;
            font-size: 1.4rem;
            color: #002b49;
            text-align: center;
            flex: 1;
        }
        .btn-modal-back {
            background: none;
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            color: #FF6600;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: absolute;
            left: 2rem;
            transition: all 0.2s;
        }
        .btn-modal-back:hover {
            background: #fff5f0;
            border-color: #ffccaa;
        }
        .btn-close-robust {
            position: absolute;
            right: 2rem;
            background: #f1f4f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            color: #718096;
            transition: all 0.2s;
        }
        .btn-close-robust:hover {
            background: #e2e8f0;
            color: #002b49;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            margin-top: 1rem;
        }

        /* Standardized Premium Card (Image 2 Style) */
        .premium-card {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 1.3rem 0.8rem;
            border: 2px solid #f1f4f9;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.02);
            height: 100%;
        }
        .premium-card:hover {
            border-color: #FF6600;
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(255,102,0,0.08);
        }
        .premium-card .postage-icon {
            width: 52px;
            height: 52px;
            background: #fff9f0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #FF6600;
            border: 1px solid #fff0e0;
        }
        .premium-card strong {
            display: block;
            color: var(--navy-dark);
            font-size: 1rem;
            margin-bottom: 8px;
        }
        .premium-card p {
            margin: 0;
            color: #718096;
            font-size: 0.8rem;
            line-height: 1.3;
        }
        .premium-card .price-tag {
            margin-top: auto;
            color: var(--primary-color);
            font-weight: 800;
            font-size: 1.05rem;
            padding-top: 1rem;
        }
        .btn-close {
            background: #f1f4f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            color: #718096;
            transition: all 0.2s;
        }
        .btn-close:hover {
            background: #e2e8f0;
            color: #002b49;
        }

        /* Checkout Multi-tab styles */
        .checkout-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 0.5rem;
            justify-content: center;
        }
        .checkout-tab {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            color: #718096;
            transition: all 0.2s;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .checkout-tab.active {
            background: #fff5f0;
            color: #FF6600;
            border-color: #ffccaa;
        }
        .checkout-pane {
            display: none;
        }
        .checkout-pane.active {
            display: block;
        }
        .cc-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .cc-form .full {
            grid-column: span 2;
        }
        .checkout-summary-mini {
            background: #f8fafc;
            border-radius: 12px;
            padding: 0.8rem;
            margin-top: 1rem;
            border: 1px solid #edf2f7;
        }
        .checkout-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }
        .checkout-summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 800;
            color: #002b49;
            font-size: 1.1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 0.5rem;
        }
    </style>

</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/login/logo.png" alt="Ex-Envios" class="sidebar-logo">
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" onclick="switchView('calculator', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 15.75l-2.489-2.489m0 0a3.375 3.375 0 10-4.773-4.773 3.375 3.375 0 004.774 4.774zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Calcular
                </a>
                <a href="#" class="nav-item" onclick="switchView('etiquetas', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.593l6.002-4.288a1.5 1.5 0 00.518-2.091l-9.581-9.581A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                    </svg>
                    Etiquetas
                </a>
                <a href="#" class="nav-item" onclick="switchView('rastreio', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    Rastreio
                </a>
                <a href="#" class="nav-item" onclick="switchView('ajuda', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                    Ajuda
                </a>
                <a href="#" class="nav-item" onclick="switchView('integracoes', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                    </svg>
                    Integrações
                </a>
                <a href="#" class="nav-item" onclick="switchView('convide', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Convide e Ganhe
                </a>
                <a href="#" class="nav-item" onclick="switchView('perfil', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Perfil
                </a>
                <a href="logout" class="nav-item" style="color: #ff5f5f !important;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9m9 0l-4.5-4.5M18 12l-4.5 4.5" />
                    </svg>
                    Sair
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="totem.html?direct=true" class="btn-sidebar-emitir">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    Emitir Frete
                </a>
            </div>
        </aside>

        <!-- Bottom Navigation for Mobile -->
        <nav class="mobile-bottom-nav">
            <a href="#" class="mobile-nav-item active" onclick="switchView('calculator', this)">
                <i class="fas fa-calculator"></i>
                <span>Calcular</span>
            </a>
            <a href="#" class="mobile-nav-item" onclick="switchView('etiquetas', this)">
                <i class="fas fa-tags"></i>
                <span>Etiquetas</span>
            </a>
            <a href="#" class="mobile-nav-item center" onclick="switchView('calculator', this)">
                <div class="center-icon">
                    <i class="fas fa-plus"></i>
                </div>
            </a>
            <a href="#" class="mobile-nav-item" onclick="switchView('rastreio', this)">
                <i class="fas fa-truck"></i>
                <span>Rastreio</span>
            </a>
            <a href="logout" class="mobile-nav-item" style="color: #ff5f5f;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sair</span>
            </a>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <!-- Title/Breadcrumbs -->
                </div>
                <div class="header-right">
                    <button class="btn-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </button>

                    <div class="header-user-info">
                        <span class="user-greeting">Olá, <?php echo htmlspecialchars($userName); ?></span>
                        <div class="wallet-badge-compact">
                            <span>Saldo: <strong>R$ <?php echo number_format($balance, 2, ',', '.'); ?></strong></span>
                            <button class="btn-add-funds-sm" onclick="openDepositModal()">+</button>
                        </div>
                    </div>

                    <div class="user-avatar">
                        <?= $initials ?>
                    </div>
                </div>
            </header>

            <div class="content-wrapper">
                <!-- VIEW 1: Calculator -->
                <div id="view-calculator">
                    <!-- Promo Banner -->
                    <div class="hero-banner">
                        <div class="banner-content">
                            <h1>Frete que cabe <br>no bolso do <br>seu cliente!</h1>
                            <p>Seu primeiro envio a partir de <strong>R$1,99</strong> <br>pelas principais
                                transportadoras
                            </p>
                        </div>
                        <div class="banner-image">
                            <!-- Placeholder for banner image if needed, or CSS background -->
                            <img src="assets/img/login/icon.png" alt="Promo" style="height: 150px; opacity: 0.2;">
                        </div>
                    </div>

                    <div class="calculator-section">
                        <h3 class="section-title">INFORME A ORIGEM</h3>
                        <div class="calculator-card">
                            <div class="form-row">
                                <div class="input-group">
                                    <label>CEP de origem</label>
                                    <input type="text" id="calc-cep-origem" placeholder="00000-000" value="79115-800">
                                </div>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-primary" onclick="saveOriginCEP()">SALVAR</button>
                                    <button class="btn-sm btn-outline" onclick="clearCalculatorForm()">LIMPAR</button>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="input-group">
                                    <label>Formato</label>
                                    <select id="calc-formato">
                                        <option value="1">Caixa / Pacote</option>
                                        <option value="2">Envelope</option>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label>Peso</label>
                                    <select id="calc-peso">
                                        <option value="">Selecione</option>
                                        <option value="0.300">Até 300g</option>
                                        <option value="1.000">Até 1kg</option>
                                        <option value="2.000">Até 2kg</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row three-cols">
                                <div class="input-group">
                                    <label>Altura</label>
                                    <div class="input-suffix">
                                        <input type="number" id="calc-altura" placeholder="00" value="10">
                                        <span>cm</span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label>Largura</label>
                                    <div class="input-suffix">
                                        <input type="number" id="calc-largura" placeholder="00" value="15">
                                        <span>cm</span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <label>Comprimento</label>
                                    <div class="input-suffix">
                                        <input type="number" id="calc-comprimento" placeholder="00" value="20">
                                        <span>cm</span>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-option">
                                <span>Seguro, aviso e mão própria</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>

                        <h3 class="section-title" style="margin-top: 2rem;">INFORME O DESTINO</h3>
                        <div class="calculator-card">
                            <div class="form-row">
                                <div class="input-group">
                                    <label>CEP de destino</label>
                                    <div class="input-link-row">
                                        <input type="text" id="calc-cep-destino" placeholder="00000-000">
                                        <a href="#" class="link-small">Pesquisar CEP</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button class="btn-calculate" onclick="calculateShipping()">CALCULAR FRETE COM DESCONTO</button>

                        <!-- Shipping Results (Hidden by default) -->
                        <div id="shipping-results" style="display: none; margin-top: 2rem;">
                            <div class="shipping-options" id="shipping-options-container">
                                <!-- Dynamic results will be injected here -->
                            </div>
                        </div>
                    </div>
                </div> <!-- End view-calculator -->

                <!-- VIEW 2: Etiquetas (Labels) -->
                <div id="view-etiquetas" style="display: none;">
                    <div class="section-header">
                        <h2>Etiquetas</h2>
                        <button class="btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.505-.71-.93-.78l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>

                    <div class="limit-section">
                        <div class="limit-info">
                            <span>Limite restante 5 de 5</span>
                            <a href="#"
                                style="color: var(--primary-color); font-size: 0.85rem; font-weight: 600; text-decoration: none;">Pedir
                                aumento de limite</a>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 5%;"></div>
                        </div>
                    </div>

                    <div class="search-bar-row">
                        <div class="search-input">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                            <input type="text" placeholder="Buscar por destinatário">
                        </div>
                    </div>

                    <div class="tabs">
                        <button class="tab active" onclick="switchEtiquetasTab('todas', this)">Todas <span class="badge" id="badge-etiq-todas">0</span></button>
                        <button class="tab" onclick="switchEtiquetasTab('emitir', this)">A Emitir <span class="badge" id="badge-etiq-emitir">0</span></button>
                        <button class="tab" onclick="switchEtiquetasTab('postar', this)">A Postar <span class="badge" id="badge-etiq-postar">0</span></button>
                    </div>

                    <div class="etiquetas-panel" style="background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 2rem; border: 1px solid #edf2f7; min-height: 300px; display: flex; flex-direction: column; justify-content: center;">
                        <div id="etiquetas-empty-state" class="empty-state" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                            </div>
                            <h3>Emita o seu primeiro frete com a gente <br>para ter acesso aos nossos benefícios</h3>
                            <p>Caso queira saber mais sobre os nossos <br>descontos <a href="#"
                                    style="color: var(--primary-color);">veja
                                    aqui</a></p>
                            <button class="btn-primary" onclick="selectShipping('empty-state')">Emitir frete</button>
                        </div>
                        <div id="etiquetas-list" style="display: none; flex-direction: column; gap: 1rem;">
                        </div>
                    </div>
                </div>


                <!-- VIEW 3: Rastreio -->
                <div id="view-rastreio" style="display: none; padding-top: 1rem;">
                    <div class="section-header" style="margin-bottom: 1rem;">
                        <h2>Rastreio</h2>
                        <!-- Changed from confusing icon to a refresh or filter icon, or removed if not needed -->
                    </div>

                    <!-- Banner -->
                    <div class="hero-banner"
                        style="margin-bottom: 2rem; background: linear-gradient(135deg, #002b49 0%, #004e8a 100%);">
                        <div class="banner-content">
                            <h1 style="color: #fff9f0;">Frete que cabe <br>no bolso do <br>seu cliente!</h1>
                            <p style="color: #cbd5e0;">Seu primeiro envio a partir de <strong
                                    style="color: var(--primary-color);">R$1,99</strong> <br>pelas principais
                                transportadoras
                            </p>
                        </div>
                        <div class="banner-image">
                            <!-- Placeholder for banner image -->
                            <div
                                style="width: 120px; height: 120px; opacity: 0.1; background: url('assets/img/login/icon.png') no-repeat center/contain;">
                            </div>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="tabs">
                        <button class="tab active" onclick="switchRastreioTab('todos', this)">
                            TODOS <span class="badge" id="badge-rast-todos">0</span>
                        </button>
                        <button class="tab" onclick="switchRastreioTab('pendentes', this)">
                            PENDENTES <span class="badge" id="badge-rast-pendentes">0</span>
                        </button>
                        <button class="tab" onclick="switchRastreioTab('entregues', this)">
                            ENTREGUES <span class="badge" id="badge-rast-entregues">0</span>
                        </button>
                    </div>

                    <!-- Tab Content Containers -->
                    <div id="tab-content-todos" class="tab-content active">
                        <div class="empty-state" style="text-align: center; padding: 4rem 0; color: var(--text-muted);">
                            <p>Nenhuma encomenda encontrada.</p>
                        </div>
                    </div>
                    <div id="tab-content-pendentes" class="tab-content" style="display: none;">
                        <div class="empty-state" style="text-align: center; padding: 4rem 0; color: var(--text-muted);">
                            <p>Nenhuma encomenda pendente.</p>
                        </div>
                    </div>
                    <div id="tab-content-entregues" class="tab-content" style="display: none;">
                        <div class="empty-state" style="text-align: center; padding: 4rem 0; color: var(--text-muted);">
                            <p>Nenhuma encomenda entregue.</p>
                        </div>
                    </div>

                    <!-- Floating Action Button -->
                    <button class="fab"
                        style="position: fixed; bottom: 2rem; right: 2rem; width: 56px; height: 56px; border-radius: 50%; background-color: var(--primary-color); color: white; border: none; font-size: 24px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 100;">
                        +
                    </button>
                </div>

                <!-- VIEW 4: Ajuda -->
                <div id="view-ajuda" style="display: none; padding-top: 1rem;">
                    <!-- Header -->
                    <div class="section-header"
                        style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                        <button class="btn-icon"
                            onclick="switchView('calculator', document.querySelector('.nav-item'))">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" style="width: 24px; height: 24px;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                        </button>
                        <h2 style="margin: 0; font-size: 1.5rem; color: #2d3748;">Central de Ajuda</h2>
                    </div>

                    <!-- Intro Hero with Search -->
                    <div class="help-hero"
                        style="background-color: var(--primary-color); border-radius: var(--border-radius); padding: 3rem 2rem; color: white; margin-bottom: 2rem; text-align: center; box-shadow: var(--shadow-md);">
                        <div style="margin-bottom: 1.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.9;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            </svg>
                            <h1 style="color: white; margin-bottom: 0.5rem; font-size: 1.8rem;">Conheça a Ex-Envios</h1>
                            <p style="opacity: 0.9; font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Conheça o
                                aplicativo Ex-Envios e garanta descontos de até 80% no envio de fretes dos Correios.</p>
                        </div>

                        <!-- Search Bar -->
                        <div class="help-search" style="position: relative; max-width: 600px; margin: 0 auto;">
                            <input type="text" placeholder="Pesquise sua dúvida aqui"
                                style="width: 100%; padding: 15px 25px; padding-left: 50px; border-radius: 30px; border: none; font-size: 1rem; color: #4a5568; outline: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="#cbd5e0"
                                style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Articles List -->
                    <div class="help-articles"
                        style="background: white; border-radius: var(--border-radius); border: 1px solid #e2e8f0; margin-bottom: 2rem; overflow: hidden;">

                        <!-- Question 1 -->
                        <div class="accordion-item">
                            <div class="accordion-header" style="padding: 1.5rem;" onclick="toggleAccordion(this)">
                                <span style="font-weight: 500; color: #2d3748; font-size: 1.05rem;">Como rastrear
                                    encomendas?</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#a0aec0" class="arrow-icon" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Para rastrear sua encomenda, basta acessar a aba "Rastreio" no menu lateral, inserir
                                    o código do objeto e clicar em pesquisar. Você verá o status em tempo real
                                    diretamente da nossa base integrada com os Correios.
                                </div>
                            </div>
                        </div>

                        <!-- Question 2 -->
                        <div class="accordion-item">
                            <div class="accordion-header" style="padding: 1.5rem;" onclick="toggleAccordion(this)">
                                <span style="font-weight: 500; color: #2d3748; font-size: 1.05rem;">Como cadastrar e
                                    configurar minha conta?</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#a0aec0" class="arrow-icon" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    O cadastro é feito na página inicial. Após o primeiro acesso, você pode completar
                                    seu perfil na aba "Perfil", adicionando endereços padrão de remetente e métodos de
                                    pagamento para agilizar suas emissões.
                                </div>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="accordion-item">
                            <div class="accordion-header" style="padding: 1.5rem;" onclick="toggleAccordion(this)">
                                <span style="font-weight: 500; color: #2d3748; font-size: 1.05rem;">Como integrar minha
                                    loja com a Ex-Envios?</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#a0aec0" class="arrow-icon" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Vá até a aba "Integrações", selecione sua plataforma (Nuvemshop, Shopify,
                                    WooCommerce, etc.) e siga as instruções na tela para conectar sua chave de API. Seus
                                    pedidos aparecerão automaticamente para cálculo.
                                </div>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="accordion-item">
                            <div class="accordion-header" style="padding: 1.5rem;" onclick="toggleAccordion(this)">
                                <span style="font-weight: 500; color: #2d3748; font-size: 1.05rem;">Posso excluir minha
                                    conta?</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="#a0aec0" class="arrow-icon" style="width: 20px; height: 20px;">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Sim, a exclusão pode ser solicitada através do nosso chat de suporte ou e-mail.
                                    Lembre-se que ao excluir a conta, seu histórico de etiquetas e saldo na carteira
                                    serão permanentemente removidos.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="help-footer" style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button class="btn-outline"
                            style="flex: 1; justify-content: center; padding: 1rem; border-color: #cbd5e0; color: var(--text-muted); font-weight: 600; background: white;">
                            E-MAIL
                        </button>
                        <button class="btn-outline"
                            style="flex: 1; justify-content: center; padding: 1rem; color: var(--primary-color); border-color: var(--primary-color); font-weight: 700; background: white;">
                            CHAT
                        </button>
                        <!-- Floating Chat Button (Bottom Right Fixed) -->
                        <button
                            style="position: fixed; bottom: 2rem; right: 2rem; width: 60px; height: 60px; border-radius: 50%; background-color: #25D366; color: white; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.15); cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 100;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" style="width: 32px; height: 32px;">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- VIEW 5: Integrações -->
                <div id="view-integracoes" style="display: none; padding-top: 1rem;">
                    <!-- Header -->
                    <div class="section-header" style="margin-bottom: 0.5rem;">
                        <h2 style="font-size: 1.5rem; color: #2d3748;">Qual a integração?</h2>
                        <p style="color: var(--text-muted); font-size: 0.95rem;">Selecione abaixo a plataforma que
                            deseja
                            realizar a integração:</p>
                    </div>

                    <!-- Grid of Integrations -->
                    <div class="integrations-grid">
                        <!-- Nuvemshop -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_nuvemshop.svg?alt=media&token=1c62fae0-da1d-47a8-aa6d-5c7ed2326cd2"
                                alt="nuvemshop" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Wix -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2FWix.svg?alt=media&token=5d3c708f-bc9f-40fc-84a6-2feb648be4a1"
                                alt="wix" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Yampi -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Fyampi.svg?alt=media&token=e2a5c08b-a6ff-4e92-bd4c-23b562490681"
                                alt="yampi" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- WooCommerce -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_woocommerce.svg?alt=media&token=ff9f3a64-e06e-4e92-9b40-a6e5f09233f9"
                                alt="woocommerce" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Shopify -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_shopify.svg?alt=media&token=ff19d3cf-7db3-4f45-8410-53f1fe4ef8c1"
                                alt="shopify" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- KaBuM! -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_kabum.svg?alt=media&token=fc44a47f-6e73-4ab5-aa04-fe8fbeef00c1"
                                alt="kabum" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Tray -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Ftray.svg?alt=media&token=7ef1979a-a2aa-4e79-9f2d-d65ceaf6466a"
                                alt="tray" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Olist Tiny -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Folist.svg?alt=media&token=ee4fd9b5-8311-4427-afe8-53ef6f9dd307"
                                alt="olist-tiny" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Bling -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2FBling.svg?alt=media&token=11e221c8-fa01-4124-ada6-4967646066b4"
                                alt="bling" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Unicopag -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_unicopag.svg?alt=media&token=abe668f1-7d09-4668-82a7-e0a750084d3f"
                                alt="unicopag" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Notazz -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_notazz.svg?alt=media&token=83d35f9c-b29d-4452-8774-f9487a230b7e"
                                alt="notazz" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Magazord -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_magazord_211101.svg?alt=media&token=8adcdc84-5998-4526-8745-d7b94cf5be88"
                                alt="magazord" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- BW Commerce -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_bw_commerce_211101.svg?alt=media&token=ae392804-71f7-40d5-8868-ad696a382f90"
                                alt="bw" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- wBuy -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_wbuy.png?alt=media"
                                alt="wbuy" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Facilzap -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_facilzap.svg?alt=media&token=c056bdef-c52d-40cb-bc1b-8179d53fc0f9"
                                alt="facilzap" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Vendizap -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_vendizap.svg?alt=media&token=f6587578-771f-411b-969f-c007bb3a5fd8"
                                alt="vendizap" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- LPQV -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_LPQV.svg?alt=media&token=52396098-aae2-4005-979d-6849526258e0"
                                alt="lpqv" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Andcommerce -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2FAndcommerce.svg?alt=media&token=bbdbd87e-dc04-49bd-9206-42edd089db1b"
                                alt="Andcommerce" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Base -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_base_191104.svg?alt=media&token=52f70d42-64cd-4dbc-8b97-9332d310759b"
                                alt="base" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Irroba -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_irooba.svg?alt=media&token=1b4d688b-165c-47e4-aa1c-95fee3d87c07"
                                alt="irroba" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- MeLoja -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Fmeloja.svg?alt=media&token=21947fec-836f-48d8-9cde-5d128c413784"
                                alt="meloja" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Tribox -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Ftribox.svg?alt=media&token=c0de6ae9-813a-4bf9-b0a8-fa03abb6c20a"
                                alt="tribox" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Lojas Virtuais BR -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo_lojas_virtuais.svg?alt=media&token=621507eb-68d9-44d0-9626-cbc387677311"
                                alt="Lojas virtuais" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Trocame -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2FFrame%20419.svg?alt=media&token=c5e962db-3fcd-4618-8eb5-8daa2c11cf5b"
                                alt="Trocame" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Stoqui -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Fstoqui.svg?alt=media&token=a1f5746c-4864-443f-b313-7f5682506ba9"
                                alt="stoqui" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Loja Integrada -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2Flogo-li.png?alt=media&token=45695854-dea7-46a6-9f09-f6743ba6de33"
                                alt="lojaintegrada" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>

                        <!-- Desenvolvedores (API) -->
                        <div class="integration-card">
                            <img src="https://firebasestorage.googleapis.com/v0/b/freight-calculator-8d6c1.appspot.com/o/images%2Fintegrations%2FDesenvolvedores.svg?alt=media&token=45c8bf38-d4ab-4779-8edc-aed7fc2191d4"
                                alt="api" class="integration-logo">
                            <button class="btn-integrate">Integrar</button>
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="integrations-footer">
                        <p>Não encontrou a plataforma do seu site?</p>
                        <p><a href="#">Clique aqui</a> para nos falar qual plataforma você gostaria que a gente tivesse!
                        </p>
                    </div>
                </div>

                <div id="view-perfil" style="display: none; padding-top: 1.5rem;">
                    <div class="promo-banner-premium">
                        <div class="promo-inner">
                            <div class="promo-text-side">
                                <span class="promo-chip">EXCLUSIVO</span>
                                <h3>Convide e Ganhe</h3>
                                <p>Ganhe <strong>R$ 10,00</strong> em créditos por cada amigo indicado.</p>
                            </div>
                            <button class="promo-btn-action"
                                onclick="switchView('convide', document.querySelector('.nav-item[onclick*=\'convide\']'))">
                                INDICAR AGORA
                            </button>
                        </div>
                    </div>

                    <div class="profile-card-premium" style="margin-top: 1.5rem;">
                        <div class="profile-main-info">
                            <span class="profile-label-top">Olá, Bem-vindo</span>
                            <h2 class="profile-user-name"><?php echo htmlspecialchars($userName); ?></h2>
                        </div>

                        <div class="profile-stats-grid">
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-wallet"></i></div>
                                <span>Pagamento</span>
                            </div>
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-question-circle"></i></div>
                                <span>Ajuda</span>
                            </div>
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-id-card"></i></div>
                                <span>Dados Pessoais</span>
                            </div>
                        </div>
                    </div>

                    <ul class="profile-list">
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M18 17H6v-2h12zm0-4H6v-2h12zm0-4H6V7h12zM3 22l1.5-1.5L6 22l1.5-1.5L9 22l1.5-1.5L12 22l1.5-1.5L15 22l1.5-1.5L18 22l1.5-1.5L21 22V2l-1.5 1.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2l-1.5 1.5L9 2 7.5 3.5 6 2 4.5 3.5 3 2z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Minhas Etiquetas</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                stroke-width="2">
                                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M3 5v14a2 2 0 0 0 2 2h16v-5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M18 12a2 2 0 0 0 0 4h4v-4Z" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Carteira</span>
                            </div>
                            <div class="profile-item-right">
                                <span class="profile-item-value">R$ <?php echo number_format($balance, 2, ',', '.'); ?></span>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                    stroke-width="2">
                                    <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2m6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5z"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Notificações</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e"
                                stroke-width="2">
                                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1M8 13h8v-2H8zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Inserir cupom amigo Ex-Envios</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e"
                                stroke-width="2">
                                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M19.43 12.98c.04-.32.07-.64.07-.98 0-.34-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46c-.09-.16-.26-.25-.44-.25-.06 0-.12.01-.17.03l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.06-.02-.12-.03-.18-.03-.17 0-.34.09-.43.25l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65c-.04.32-.07.65-.07.98 0 .33.03.66.07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46c.09.16.26.25.44.25.06 0 .12-.01.17-.03l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.06.02.12.03.18.03.17 0 .34-.09.43-.25l2-3.46c.12-.22.07-.49-.12-.64zm-1.98-1.71c.04.31.05.52.05.73 0 .21-.02.43-.05.73l-.14 1.13.89.7 1.08.84-.7 1.21-1.27-.51-1.04-.42-.9.68c-.43.32-.84.56-1.25.73l-1.06.43-.16 1.13-.2 1.35h-1.4l-.19-1.35-.16-1.13-1.06-.43c-.43-.18-.83-.41-1.23-.71l-.91-.7-1.06.43-1.27.51-.7-1.21 1.08-.84.89-.7-.14-1.13c-.03-.31-.05-.54-.05-.74s.02-.43.05-.73l.14-1.13-.89-.7-1.08-.84.7-1.21 1.27.51 1.04.42.9-.68c.43-.32.84-.56 1.25-.73l1.06-.43.16-1.13.2-1.35h1.39l.19 1.35.16 1.13 1.06.43c.43.18.83.41 1.23.71l.91.7 1.06-.43 1.27-.51.7 1.21-1.07.85-.89.7zM12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4m0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Integrações</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                stroke-width="2">
                                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <circle cx="12" cy="10" r="3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="profile-item-text">Meus endereços</span>
                            </div>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                stroke-width="2">
                                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                            </svg>
                        </li>
                        <li class="profile-list-item accordion-item" style="display: block; padding: 0;">
                            <div class="accordion-header" style="padding: 1.2rem 1.5rem;"
                                onclick="toggleAccordion(this)">
                                <div class="profile-item-left">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <polyline points="14 2 14 8 20 8" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <line x1="16" y1="13" x2="8" y2="13" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <line x1="16" y1="17" x2="8" y2="17" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                        <polyline points="10 9 9 9 8 9" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                    <span class="profile-item-text">Termos e condições</span>
                                </div>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                    stroke-width="2" class="arrow-icon">
                                    <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Ao utilizar a Ex-Envios, você concorda com nossos termos de proteção de dados e
                                    políticas de envio. Garantimos a segurança de suas informações e a transparência
                                    em todo o processo de cotação e emissão de fretes.
                                </div>
                            </div>
                        </li>
                        <li class="profile-list-item accordion-item" style="display: block; padding: 0;">
                            <div class="accordion-header" style="padding: 1.2rem 1.5rem;"
                                onclick="toggleAccordion(this)">
                                <div class="profile-item-left">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <circle cx="12" cy="8" r="7" stroke-linecap="round" stroke-linejoin="round" />
                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="profile-item-text">Regras da promoção</span>
                                </div>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#cbd5e0"
                                    stroke-width="2" class="arrow-icon">
                                    <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    A cada amigo indicado que realizar o primeiro envio, você ganha R$ 10 em
                                    créditos na sua carteira Ex-Envios. Os créditos podem ser utilizados para abater
                                    o valor de qualquer frete futuro sem data de expiração.
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="app-version">
                        App ID: mlsax4o7 - 5.6.0 - browser
                    </div>
                </div>
            </div>

            <!-- VIEW 7: Convide e Ganhe -->
            <div id="view-convide" style="display: none;">
                <!-- Hero Banner -->
                <div class="referral-hero">
                    <h2>Oi, <?php echo htmlspecialchars($userName); ?>!</h2>
                    <p>Pronto(a) para indicar?</p>
                </div>

                <!-- Sharing Section -->
                <div class="referral-section-title">
                    <h3>Convide e ganhe</h3>
                    <a href="#" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">Termos
                        da
                        campanha</a>
                </div>

                <div class="referral-card">
                    <div style="margin-bottom: 1rem;">
                        <span class="referral-link-label">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                            Campanha ativo
                        </span>
                    </div>

                    <p style="margin-bottom: 0.8rem; font-weight: 600; color: #1a202c;">Compartilhe seu link de
                        indicação:</p>
                    <div class="referral-share-box">
                        <span class="referral-url">https://ex-envios.bvr.li/c/MDWQF</span>
                        <button class="btn-copy" title="Copiar Link">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <h3 class="referral-section-title">Meu desempenho</h3>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <span class="metric-value">0</span>
                        <span class="metric-label">Clique</span>
                    </div>
                    <div class="metric-card">
                        <span class="metric-value">0</span>
                        <span class="metric-label">Postagem</span>
                    </div>
                </div>

                <!-- How to win -->
                <h3 class="referral-section-title">Veja como fazer para ganhar</h3>
                <div class="referral-steps">
                    <div class="referral-step">
                        <div class="step-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                <polyline points="16 6 12 2 8 6"></polyline>
                                <line x1="12" y1="2" x2="12" y2="15"></line>
                            </svg>
                        </div>
                        <div class="step-content">
                            <h4>Compartilhe seu link</h4>
                            <p>Envie seu link de indicação para amigos lojistas que ainda não conhecem a Ex-Envios.
                            </p>
                        </div>
                    </div>

                    <div class="referral-step">
                        <div class="step-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </svg>
                        </div>
                        <div class="step-content">
                            <h4>Eles se cadastram</h4>
                            <p>Seus amigos se cadastram na plataforma através do seu link exclusivo.</p>
                        </div>
                    </div>

                    <div class="referral-step">
                        <div class="step-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </svg>
                        </div>
                        <div class="step-content">
                            <h4>Você ganha prêmios</h4>
                            <p>A cada nova postagem realizada pelos seus indicados, você acumula créditos e prêmios.
                            </p>
                        </div>
                    </div>
8px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        // --- System Initialization & UI Helpers ---
        window.alert = function showToast(message) {
            let toast = document.getElementById('custom-toast');
            if(!toast) {
                injectToastUI();
                toast = document.getElementById('custom-toast');
            }
            const msgEl = document.getElementById('toast-message');
            if (msgEl) msgEl.innerText = message;
            toast.style.display = 'flex';
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 4000);
        };
        
        window.showAlert = window.alert;

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

        // --- Shipping Calculator ---
        async function calculateShipping() {
            const cepDest = document.getElementById('calc-cep-destino').value;
            if (!cepDest) {
                alert('Informe o CEP de destino');
                return;
            }

            const btn = document.querySelector('.btn-calculate');
            const originalText = btn.innerHTML;
            btn.innerHTML = "Calculando...";
            btn.disabled = true;

            const payload = {
                sCepOrigem: document.getElementById('calc-cep-origem').value,
                sCepDestino: cepDest,
                nCdFormato: document.getElementById('calc-formato').value,
                nVlPeso: document.getElementById('calc-peso').value || '1',
                nVlAltura: document.getElementById('calc-altura').value,
                nVlLargura: document.getElementById('calc-largura').value,
                nVlComprimento: document.getElementById('calc-comprimento').value,
                nVlValorDeclarado: '0'
            };

            try {
                const response = await fetch('endpoints/calc_frete_api', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();

                if (result.ok) {
                    if (result.servicos && result.servicos.length > 0) {
                        renderShippingResults(result.servicos);
                        document.getElementById('shipping-results').style.display = 'block';
                        document.getElementById('shipping-results').scrollIntoView({ behavior: 'smooth' });
                    } else {
                        alert('Nenhum resultado de frete retornado.');
                    }
                } else {
                    alert('Erro: ' + (result.error || 'Falha no cálculo'));
                }
            } catch (e) {
                alert('Erro na conexão com o servidor.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function renderShippingResults(servicos) {
            const container = document.getElementById('shipping-options-container');
            container.innerHTML = '';
            container.className = 'shipping-options-list'; // Apply standard list styles

            servicos.forEach((s, index) => {
                let logoPath = 'Correios.png';
                let alt = 'Correios';
                if (s.codigo === 'TEX' || s.servico_nome.includes('Total')) {
                    logoPath = 'Total Express.png';
                    alt = 'Total Express';
                }

                const valorBalcao = (s.valor_float * 1.3).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const valorCalculado = s.valor_float.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const prazo = s.prazo_dias ? `Até ${s.prazo_dias} dias úteis` : 'Consulte o prazo';
                
                const isLast = (index === servicos.length - 1);
                const borderBottom = isLast ? 'none' : '1px solid #f1f4f9';

                const html = `
                    <div class="shipping-option" style="padding: 1.5rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: ${borderBottom}; background: white;">
                        <div class="ship-info" style="display: flex; align-items: center; gap: 2.5rem; flex: 1;">
                            <div class="ship-logo" style="width: 80px; display: flex; justify: flex-start;">
                                <img src="${logoPath}" alt="${alt}" style="max-width: 100%; max-height: 25px; object-fit: contain;">
                            </div>
                            <div style="flex: 1;">
                                <strong style="color: #002b49; font-size: 1.15rem; display: block; margin-bottom: 4px;">${s.servico_nome}</strong>
                                <p style="margin: 0; color: #718096; font-size: 0.95rem;">${prazo}</p>
                            </div>
                        </div>
                        <div class="ship-price-action" style="display: flex; align-items: center; gap: 3rem;">
                            <div class="ship-price" style="text-align: right; min-width: 100px;">
                                <strong style="color: #002b49; font-size: 1.25rem; display: block;">${valorCalculado}</strong>
                                <span style="text-decoration: line-through; color: #cbd5e0; font-size: 0.85rem;">${valorBalcao}</span>
                            </div>
                            <button class="btn-select" onclick="openAddressForm('${s.servico_nome}', '${s.valor_float}')" style="background: #FF6600; color: white; border: none; padding: 0.8rem 1.6rem; border-radius: 8px; font-weight: 700; cursor: pointer;">Emitir Frete</button>
                        </div>
                    </div>
                `;
                container.innerHTML += html;
            });
        }

        // --- Multi-step Shipping Flow Logic ---
        window.selectedService = '';
        window.selectedBasePrice = 0;
        window.addressCurrentStep = 1;

        window.openAddressForm = function(type, price) {
            console.log("Abrindo formulário:", type, price);
            window.selectedService = type;
            window.selectedBasePrice = parseFloat(price);
            window.addressCurrentStep = 1;

            // Close other modals safely
            const closeFns = [
                'closeDepositModal', 'closeRegistration', 'closeColetaOptionsModal', 
                'closeLockersModal', 'closePostageModal'
            ];
            closeFns.forEach(fn => {
                if (typeof window[fn] === 'function') {
                    try { window[fn](); } catch(e) { console.error("Error closing modal:", fn, e); }
                }
            });

            // Pre-fill CEPs from calculator
            const cepOrig = document.getElementById('calc-cep-origem')?.value;
            const cepDest = document.getElementById('calc-cep-destino')?.value;
            if (cepOrig) {
                const sCep = document.getElementById('sender-cep');
                if (sCep) {
                    sCep.value = cepOrig;
                    if (typeof lookupCEP === 'function') lookupCEP('sender');
                }
            }
            if (cepDest) {
                const rCep = document.getElementById('receiver-cep');
                if (rCep) rCep.value = cepDest;
            }

            hideAllModals();
            updateAddressUI();
            const modal = document.getElementById('modal-address-form');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            } else {
                alert("Erro: Formulário de endereço não encontrado.");
            }
        };

        function closeAddressForm() {
            const modal = document.getElementById('modal-address-form');
            if (modal) modal.style.display = 'none';
        }

        function navigateAddressForm(dir) {
            if (dir === 'next') {
                if (window.addressCurrentStep === 1) {
                    window.addressCurrentStep = 2;
                    updateAddressUI();
                } else {
                    closeAddressForm();
                    openPostageModal(window.selectedService, window.selectedBasePrice);
                }
            } else {
                if (window.addressCurrentStep === 2) {
                    window.addressCurrentStep = 1;
                    updateAddressUI();
                } else {
                    closeAddressForm();
                }
            }
        }

        function updateAddressUI() {
            const s1 = document.getElementById('address-step-1');
            const s2 = document.getElementById('address-step-2');
            const title = document.getElementById('address-modal-title');
            const backBtn = document.getElementById('btn-address-back');
            const dot1 = document.getElementById('step-1-dot');
            const dot2 = document.getElementById('step-2-dot');

            if (window.addressCurrentStep === 1) {
                if (s1) s1.style.display = 'block';
                if (s2) s2.style.display = 'none';
                if (title) title.innerText = 'Dados do Remetente';
                if (backBtn) backBtn.style.display = 'none';
                if (dot1) dot1.style.background = '#FF6600';
                if (dot2) dot2.style.background = '#e2e8f0';
            } else {
                if (s1) s1.style.display = 'none';
                if (s2) s2.style.display = 'block';
                if (title) title.innerText = 'Dados do Destinatário';
                if (backBtn) backBtn.style.display = 'block';
                if (dot1) dot1.style.background = '#e2e8f0';
                if (dot2) dot2.style.background = '#FF6600';
            }
        }

        async function lookupCEP(type) {
            const input = document.getElementById(`${type}-cep`);
            if (!input) return;
            const cep = input.value.replace(/\D/g, '');
            if (cep.length !== 8) return;

            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (!data.erro) {
                    const street = document.getElementById(`${type}-street`);
                    const neighbor = document.getElementById(`${type}-neighborhood`);
                    const cityUf = document.getElementById(`${type}-city-uf`);
                    if (street) street.value = data.logradouro;
                    if (neighbor) neighbor.value = data.bairro;
                    if (cityUf) cityUf.value = `${data.localidade} - ${data.uf}`;
                }
            } catch (e) { console.error("CEP error:", e); }
        }

        // --- Postage & Coleta Options ---
        function hideAllModals() {
            const modals = document.querySelectorAll('.modal-premium');
            modals.forEach(m => m.style.display = 'none');
        }

        function openPostageModal(type, price) {
            hideAllModals();
            window.selectedService = type;
            window.selectedBasePrice = parseFloat(price);
            
            // Update display
            const display = document.getElementById('coleta-base-price-display');
            if (display) display.innerText = `+ R$ ${window.selectedBasePrice.toFixed(2).replace('.', ',')}`;
            
            const modal = document.getElementById('modal-postage');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            }
        }

        function closePostageModal() {
            document.getElementById('modal-postage').style.display = 'none';
        }

        function selectShipping(method) {
            window.selectedMethod = method;
            if (method === 'coleta') {
                openColetaOptionsModal();
            } else {
                openLockersModal(); // Reusing points modal for Agencias
            }
        }

        function openColetaOptionsModal() {
            hideAllModals();
            const grid = document.querySelector('#modal-coleta-tipo .postage-options-grid');
            if (grid && appSyncData.services.length > 0) {
                grid.innerHTML = '';
                appSyncData.services.filter(s => s.category === 'coleta' && (s.status === 'active' || s.status === 'ativo')).forEach(s => {
                    const card = document.createElement('div');
                    card.className = 'premium-card';
                    card.onclick = () => selectColetaType(s);
                    
                    const nameLower = s.name.toLowerCase();
                    const idLower = s.id.toLowerCase();
                    
                    let iconClass = 'fas fa-calendar-alt';
                    let extraLabel = '';
                    let desc = s.description || '';

                    if (nameLower.includes('express') || idLower.includes('express') || nameLower.includes('bolt')) {
                        iconClass = 'fas fa-bolt';
                        extraLabel = 'Em até 60 min';
                        desc = 'Retiramos no seu endereço em até 60 minutos.';
                    } else if (nameLower.includes('programada') || idLower.includes('programada')) {
                        iconClass = 'fas fa-calendar-alt';
                        extraLabel = 'Seg a Sáb às 20h';
                        desc = 'Agendar uma coleta programada no seu endereço.';
                    } else if (nameLower.includes('locker') || idLower.includes('locker') || nameLower.includes('box')) {
                        iconClass = 'fas fa-archive';
                    }
                    
                    card.innerHTML = `
                        <div class="postage-icon"><i class="${iconClass}"></i></div>
                        <strong>${s.name.toUpperCase()}</strong>
                        ${extraLabel ? `<p style="font-size: 0.75rem; color: #718096; margin-bottom: 5px; font-weight: 600;">${extraLabel}</p>` : ''}
                        <p>${desc}</p>
                        <span class="price-tag">${parseFloat(s.price) > 0 ? '+ R$ ' + parseFloat(s.price).toFixed(2).replace('.', ',') : 'Grátis'}</span>
                    `;
                    grid.appendChild(card);
                });
            }
            const modal = document.getElementById('modal-coleta-tipo');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            }
        }

        function closeColetaOptionsModal() {
            document.getElementById('modal-coleta-tipo').style.display = 'none';
        }

        function selectColetaType(service) {
            window.selectedExtraService = service.id;
            window.extraPrice = parseFloat(service.price) || 0;
            
            const nameLower = service.name.toLowerCase();
            const idLower = service.id.toLowerCase();
            
            if (nameLower.includes('locker') || idLower.includes('locker') || nameLower.includes('box')) {
                openLockersModal();
            } else if (nameLower.includes('programada')) {
                openSchedulingModal();
            } else {
                openRegistrationModal();
            }
        }

        function openLockersModal() {
            hideAllModals();
            const modal = document.getElementById('modal-locker-select');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            }
            if (window.selectedMethod === 'agencia') {
                const title = document.querySelector('#modal-locker-select h2');
                if (title) title.innerText = 'Agências Próximas';
                loadAgencies();
            } else {
                const title = document.querySelector('#modal-locker-select h2');
                if (title) title.innerText = 'Escolha o Ponto';
                loadLockers();
            }
        }

        function closeLockersModal() {
            const modal = document.getElementById('modal-locker-select');
            if (modal) modal.style.display = 'none';
        }

        function goBackFromLockers() {
            if (window.selectedMethod === 'agencia') {
                openPostageModal(window.selectedService, window.selectedBasePrice);
            } else {
                openColetaOptionsModal();
            }
        }

        function openSchedulingModal() {
            hideAllModals();
            const modal = document.getElementById('modal-coleta-agendamento');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            }
            
            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('sched-date-picker').setAttribute('min', today);
            document.getElementById('sched-date-picker').value = '';
            document.getElementById('sched-time-picker').innerHTML = '<option value="">Selecione a data primeiro...</option>';
            document.getElementById('sched-error-msg').style.display = 'none';
        }

        function closeSchedulingModal() {
            document.getElementById('modal-coleta-agendamento').style.display = 'none';
        }

        function validateScheduling() {
            const dateVal = document.getElementById('sched-date-picker').value;
            const errorEl = document.getElementById('sched-error-msg');
            const errorText = document.getElementById('sched-error-text');
            const timePicker = document.getElementById('sched-time-picker');
            
            errorEl.style.display = 'none';
            timePicker.innerHTML = '';

            if (!dateVal) return;

            const settings = appSyncData.settings || {};
            const [y, m, d] = dateVal.split('-');
            const dateObj = new Date(y, m - 1, d);
            const dayOfWeek = dateObj.getDay(); // 0=dom, 1=seg...
            const dayMap = ['dom', 'seg', 'ter', 'qua', 'qui', 'sex', 'sab'];
            const dayKey = dayMap[dayOfWeek];

            // Check Operating Days
            const workDays = settings.work_days ? settings.work_days.split(',') : ['seg', 'ter', 'qua', 'qui', 'sex'];
            if (!workDays.includes(dayKey)) {
                errorText.innerText = 'Não atendemos neste dia da semana.';
                errorEl.style.display = 'block';
                return;
            }

            // Check Holidays
            const holidays = settings.holidays ? settings.holidays.split(',').map(h => h.trim()) : [];
            const checkDate = `${d}/${m}`; // Format DD/MM
            if (holidays.includes(checkDate)) {
                errorText.innerText = 'Esta data é um feriado sem expediente.';
                errorEl.style.display = 'block';
                return;
            }

            // Generate Time Slots
            const openTime = settings.open_time || '08:00';
            const closeTime = settings.close_time || '18:00';
            
            const [openH, openM] = openTime.split(':').map(Number);
            const [closeH, closeM] = closeTime.split(':').map(Number);
            
            timePicker.innerHTML = '<option value="">Escolha um horário...</option>';
            
            let current = new Date();
            current.setHours(openH, openM, 0, 0);
            
            const end = new Date();
            end.setHours(closeH, closeM, 0, 0);

            // If today, filter past times
            const now = new Date();
            const isToday = (dateVal === now.toISOString().split('T')[0]);

            while (current < end) {
                const timeStr = current.toTimeString().substring(0, 5);
                
                if (!isToday || current > now) {
                    const option = document.createElement('option');
                    option.value = timeStr;
                    option.innerText = timeStr;
                    timePicker.appendChild(option);
                }
                
                current.setMinutes(current.getMinutes() + 30); // 30 min intervals
            }

            if (timePicker.options.length <= 1) {
                errorText.innerText = 'Não há horários disponíveis para esta data.';
                errorEl.style.display = 'block';
            }
        }

        function confirmScheduling() {
            const date = document.getElementById('sched-date-picker').value;
            const time = document.getElementById('sched-time-picker').value;
            
            if (!date || !time) {
                alert('Por favor, selecione data e horário.');
                return;
            }

            window.selectedDate = date;
            window.selectedTime = time;
            
            openRegistrationModal();
        }

        function loadLockers() {
            const container = document.getElementById('lockers-container');
            container.innerHTML = '<div style="text-align:center; padding:2rem; color:#718096;">Buscando lockers...</div>';
            
            setTimeout(() => {
                container.innerHTML = '';
                if (!appSyncData.lockers || appSyncData.lockers.length === 0) {
                    container.innerHTML = '<div style="text-align:center; padding:2rem; color:#718096;">Nenhum locker disponível no momento.</div>';
                    return;
                }
                
                appSyncData.lockers.forEach(l => {
                    const div = document.createElement('div');
                    div.style.cssText = 'cursor:pointer; display:flex; align-items:center; gap:1.2rem; padding:1.2rem; border:2px solid #f1f4f9; border-radius:24px; margin-bottom:0.8rem; background:#fff; transition:all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.01);';
                    div.innerHTML = `
                        <div style="width:52px; height:52px; background:#fff9f0; display:flex; align-items:center; justify-content:center; border-radius:14px; color:#FF6600; font-size:1.5rem; border:1px solid #fff0e0;"><i class="fas fa-box"></i></div>
                        <div style="flex:1;">
                            <strong style="display:block; color:#002b49; font-size:1.15rem; margin-bottom:4px;">${l.label}</strong>
                            <span style="color:#22c55e; font-size:0.95rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; background:#22c55e; border-radius:50%;"></span> ${l.status === 'online' ? 'Disponível' : l.status}
                            </span>
                            <small style="color:#718096; font-size:0.85rem;">${l.location_name || ''}</small>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#cbd5e0; font-size:1.2rem;"></i>
                    `;
                    div.onmouseover = () => { div.style.borderColor = '#FF6600'; div.style.transform = 'translateY(-2px)'; div.style.boxShadow = '0 10px 25px rgba(255,102,0,0.05)'; };
                    div.onmouseout = () => { div.style.borderColor = '#f1f4f9'; div.style.transform = 'translateY(0)'; div.style.boxShadow = '0 4px 15px rgba(0,0,0,0.01)'; };
                    div.onclick = () => { window.selectedLockerId = l.id; openRegistrationModal(); };
                    container.appendChild(div);
                });
            }, 400);
        }

        function loadAgencies() {
            const container = document.getElementById('lockers-container');
            container.innerHTML = '<div style="text-align:center; padding:2rem; color:#718096;">Buscando agências próximas...</div>';

            setTimeout(() => {
                container.innerHTML = '';
                if (!appSyncData.locations || appSyncData.locations.length === 0) {
                    container.innerHTML = '<div style="text-align:center; padding:2rem; color:#718096;">Nenhuma agência disponível no momento.</div>';
                    return;
                }

                appSyncData.locations.forEach(l => {
                    const div = document.createElement('div');
                    div.style.cssText = 'cursor:pointer; display:flex; align-items:center; gap:1.2rem; padding:1.2rem; border:2px solid #f1f4f9; border-radius:24px; margin-bottom:0.8rem; background:#fff; transition:all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.01);';
                    div.innerHTML = `
                        <div style="width:52px; height:52px; background:#fff9f0; display:flex; align-items:center; justify-content:center; border-radius:14px; color:#FF6600; font-size:1.5rem; border:1px solid #fff0e0;"><i class="fas fa-store"></i></div>
                        <div style="flex:1;">
                            <strong style="display:block; color:#002b49; font-size:1.15rem; margin-bottom:4px;">${l.name}</strong>
                            <span style="color:#FF6600; font-size:0.95rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; background:#FF6600; border-radius:50%;"></span> Aberta
                            </span>
                            <small style="color:#718096; font-size:0.85rem;">${l.address || ''}</small>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#cbd5e0; font-size:1.2rem;"></i>
                    `;
                    div.onmouseover = () => { div.style.borderColor = '#FF6600'; div.style.transform = 'translateY(-2px)'; div.style.boxShadow = '0 10px 25px rgba(255,102,0,0.05)'; };
                    div.onmouseout = () => { div.style.borderColor = '#f1f4f9'; div.style.transform = 'translateY(0)'; div.style.boxShadow = '0 4px 15px rgba(0,0,0,0.01)'; };
                    div.onclick = () => { window.selectedAgencyId = l.id; openRegistrationModal(); };
                    container.appendChild(div);
                });
            }, 400);
        }

        function goBackFromRegistration() {
            closeRegistration();
            const svc = appSyncData.services.find(s => s.id === window.selectedExtraService);
            const isProgramada = svc && (svc.name.toLowerCase().includes('programada') || svc.id.toLowerCase().includes('programada'));
            const isLocker = svc && (svc.name.toLowerCase().includes('locker') || svc.id.toLowerCase().includes('locker'));

            if (isProgramada) {
                openSchedulingModal();
            } else if (isLocker) {
                openLockersModal();
            } else if (window.selectedMethod === 'agencia') {
                openLockersModal();
            } else {
                openColetaOptionsModal();
            }
        }

        let currentCheckoutMethod = 'CREDIT_CARD';

        function switchCheckoutTab(method) {
            currentCheckoutMethod = method;
            document.querySelectorAll('.checkout-tab').forEach(t => t.classList.toggle('active', t.dataset.method === method));
            document.querySelectorAll('.checkout-pane').forEach(p => p.classList.toggle('active', p.id === 'pane-' + method));
        }

        function fillTestData() {
            // Payer data
            const nameEl = document.getElementById('pay-name');
            const emailEl = document.getElementById('pay-email');
            const cpfEl = document.getElementById('pay-cpf');
            
            if (nameEl) nameEl.value = 'ASAAS TESTE';
            if (emailEl) emailEl.value = '<?php echo $userEmail; ?>' || 'vendas@exenvios.com.br';
            if (cpfEl) cpfEl.value = '32345678917'; // Verified valid CPF
            
            // Card data
            const ccNum = document.getElementById('cc-number');
            const ccHolder = document.getElementById('cc-holder');
            const ccExpiry = document.getElementById('cc-expiry');
            const ccCvv = document.getElementById('cc-cvv');
            
            if (ccNum) ccNum.value = '4444 4444 4444 4444';
            if (ccHolder) ccHolder.value = 'ASAAS TESTE';
            if (ccExpiry) ccExpiry.value = '12/28';
            if (ccCvv) ccCvv.value = '123';
            
            alert('Dados de teste preenchidos (Valid CPF)!');
        }

        function openRegistrationModal() {
            hideAllModals();
            updateOrderSummary();
            
            const modal = document.getElementById('modal-registration');
            if (modal) {
                modal.style.display = 'flex';
                modal.style.zIndex = '10000';
            }
        }

        function closeRegistration() {
            document.getElementById('modal-registration').style.display = 'none';
        }

        function updateOrderSummary() {
            const freight = window.selectedBasePrice || 0;
            const extra = window.extraPrice || 0;
            const total = freight + extra;

            const fVal = document.getElementById('summ-freight');
            const eRow = document.getElementById('summ-extra-row');
            const eVal = document.getElementById('summ-extra-value');
            const tVal = document.getElementById('summ-total');

            if (fVal) fVal.innerText = freight.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            if (eRow) eRow.style.display = (extra > 0) ? 'flex' : 'none';
            if (eVal) eVal.innerText = extra.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            if (tVal) tVal.innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        async function completeOrderAsaas() {
            const btn = document.querySelector('#modal-registration .btn-calculate');
            const originalText = btn.innerHTML;
            
            try {
                const name = document.getElementById('pay-name').value;
                const email = document.getElementById('pay-email').value;
                const cpfInput = document.getElementById('pay-cpf');
                let cpf = cpfInput ? cpfInput.value : '';
                cpf = cpf.replace(/\D/g, ''); // Strip masks before sending
                
                if (!name || !email || !cpf) {
                    alert('Por favor, preencha Nome, Email e CPF.');
                    return;
                }

                const payload = {
                    channel: 'WEB',
                    name: name,
                    email: email,
                    cpf: cpf,
                    billingType: currentCheckoutMethod,
                    service: window.selectedExtraService,
                    total: (window.selectedBasePrice || 0) + (window.extraPrice || 0),
                    scheduled_date: window.selectedDate || null,
                    scheduled_time: window.selectedTime || null,
                    locker_id: window.selectedLockerId || null,
                    agency_id: window.selectedAgencyId || null
                };

                if (currentCheckoutMethod === 'CREDIT_CARD') {
                    const ccNumber = document.getElementById('cc-number').value;
                    const ccHolder = document.getElementById('cc-holder').value;
                    const ccExpiry = document.getElementById('cc-expiry').value;
                    const ccCvv = document.getElementById('cc-cvv').value;

                    if (!ccNumber || !ccHolder || !ccExpiry || !ccCvv) {
                        alert('Preencha todos os dados do cartão.');
                        return;
                    }

                    payload.creditCard = {
                        number: ccNumber.replace(/\D/g, ''),
                        holderName: ccHolder,
                        expiryMonth: ccExpiry.split('/')[0],
                        expiryYear: '20' + ccExpiry.split('/')[1],
                        cvv: ccCvv
                    };
                }

                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PROCESSANDO...';
                btn.disabled = true;

                const response = await fetch('endpoints/process_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                if (result.ok) {
                    if (currentCheckoutMethod === 'PIX') {
                        const pixCode = result.data.payment.pix.payload;
                        alert('Pagamento PIX gerado! Copie o código abaixo:\n\n' + pixCode);
                    } else if (currentCheckoutMethod === 'BOLETO') {
                        const bankSlipUrl = result.data.payment.bankSlipUrl;
                        alert('Boleto gerado com sucesso! Clique em OK para visualizar.');
                        window.open(bankSlipUrl, '_blank');
                    } else {
                        alert('Pagamento processado com sucesso!');
                    }
                    location.reload();
                } else {
                    alert('Erro ao processar: ' + (result.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                console.error("Order error:", err);
                alert('Erro na conexão com o servidor.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        // --- Deposit (Asaas) ---
        let selectedDepositMethod = 'PIX';
        let currentPixCode = '';

        function openDepositModal() {
            hideAllModals();
            const m = document.getElementById('modal-deposit');
            if (m) {
                m.style.display = 'flex';
                m.style.zIndex = '10000';
            }
        }
        function closeDepositModal() {
            const m = document.getElementById('modal-deposit');
            if (m) m.style.display = 'none';
        }
        function setMethod(method) {
            selectedDepositMethod = method;
            document.querySelectorAll('.method-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');
        }

        async function generatePayment() {
            const amount = document.getElementById('deposit-amount').value;
            if (amount < 20) { alert('Valor mínimo: R$ 20,00'); return; }
            alert("Processando pagamento de R$ " + amount);
            // Simulação
            setTimeout(() => {
                alert("Pagamento simulado com sucesso!");
                closeDepositModal();
            }, 1000);
        }

        function copyPixCode() {
            if (currentPixCode) {
                navigator.clipboard.writeText(currentPixCode);
                alert("Código PIX copiado!");
            }
        }

        // --- Navigation ---
        function switchView(viewName, element) {
            document.querySelectorAll('.nav-item, .mobile-nav-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');
            
            const views = ['calculator', 'etiquetas', 'rastreio', 'ajuda', 'integracoes', 'perfil', 'convide'];
            views.forEach(v => {
                const el = document.getElementById('view-' + v);
                if (el) el.style.display = (v === viewName) ? 'block' : 'none';
            });
        }

        function switchRastreioTab(tab, el) {
            const container = el.closest('.tabs');
            container.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            document.querySelectorAll('#view-rastreio .tab-content').forEach(c => c.style.display = 'none');
            const target = document.getElementById('tab-content-' + tab);
            if (target) target.style.display = 'block';
        }

        let userOrders = [];
        let currentEtiquetasTab = 'todas';

        async function loadUserOrders() {
            try {
                const response = await fetch('endpoints/get_user_orders.php');
                const result = await response.json();
                if (result.ok) {
                    userOrders = result.data || [];
                    renderEtiquetas();
                    renderRastreio();
                }
            } catch (err) {
                console.error("Erro ao carregar encomendas do usuário:", err);
            }
        }

        function switchEtiquetasTab(tab, el) {
            const container = el.closest('.tabs');
            container.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            currentEtiquetasTab = tab;
            renderEtiquetas();
        }

        function renderEtiquetas() {
            let filtered = userOrders;
            if (currentEtiquetasTab === 'emitir') {
                filtered = userOrders.filter(o => o.status === 'pending');
            } else if (currentEtiquetasTab === 'postar') {
                filtered = userOrders.filter(o => o.status === 'paid' || o.status === 'label_generated');
            }

            // Update Badges
            const emitirCount = userOrders.filter(o => o.status === 'pending').length;
            const postarCount = userOrders.filter(o => o.status === 'paid' || o.status === 'label_generated').length;
            
            const bTodas = document.getElementById('badge-etiq-todas');
            const bEmitir = document.getElementById('badge-etiq-emitir');
            const bPostar = document.getElementById('badge-etiq-postar');
            
            if(bTodas) bTodas.innerText = userOrders.length;
            if(bEmitir) bEmitir.innerText = emitirCount;
            if(bPostar) bPostar.innerText = postarCount;

            const emptyState = document.getElementById('etiquetas-empty-state');
            const listContainer = document.getElementById('etiquetas-list');
            
            if (!emptyState || !listContainer) return;

            if (filtered.length === 0) {
                emptyState.style.display = 'block';
                listContainer.style.display = 'none';
            } else {
                emptyState.style.display = 'none';
                listContainer.style.display = 'flex';
                let html = '';
                filtered.forEach(order => {
                    const statusText = order.status === 'pending' ? 'Aguardando Pagamento' : 
                                      (order.status === 'paid' ? 'A Emitir (Pago)' : 
                                      (order.status === 'label_generated' ? 'A Postar' : 'Desconhecido'));
                    const color = order.status === 'pending' ? '#e53e3e' : '#22c55e';
                    
                    html += `
                    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                        <div>
                            <div style="font-weight: 700; color: #002b49; margin-bottom: 4px;">Pedido #${order.id} | ${order.external_ref}</div>
                            <div style="font-size: 0.85rem; color: #718096; margin-bottom: 4px;">Destinatário: ${order.customer_name || 'N/A'}</div>
                            <div style="font-size: 0.8rem; color: ${color}; font-weight: 600;">STATUS: ${statusText}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: var(--primary-color); margin-bottom: 8px;">R$ ${parseFloat(order.total_value).toFixed(2).replace('.', ',')}</div>
                            ${order.status === 'pending' ? `<button onclick="window.location.href='#'" style="background:#FF6600; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:0.8rem; font-weight:bold;">Pagar Agora</button>` : `<button style="background:#f1f4f9; color:#4a5568; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:0.8rem; font-weight:bold;">Ver Detalhes</button>`}
                        </div>
                    </div>`;
                });
                listContainer.innerHTML = html;
            }
        }

        function renderRastreio() {
            const todos = userOrders;
            const pendentes = userOrders.filter(o => o.status !== 'delivered');
            const entregues = userOrders.filter(o => o.status === 'delivered');

            document.getElementById('badge-rast-todos').innerText = todos.length;
            document.getElementById('badge-rast-pendentes').innerText = pendentes.length;
            document.getElementById('badge-rast-entregues').innerText = entregues.length;

            const renderList = (containerId, list) => {
                const container = document.getElementById(containerId);
                if(!container) return;
                
                if (list.length === 0) {
                    container.innerHTML = `<div class="empty-state" style="text-align: center; padding: 4rem 0; color: var(--text-muted);"><p>Nenhuma encomenda ${containerId === 'tab-content-entregues' ? 'entregue' : (containerId === 'tab-content-pendentes' ? 'pendente' : 'encontrada')}.</p></div>`;
                } else {
                    let html = '<div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">';
                    list.forEach(order => {
                        const statusColor = order.status === 'delivered' ? '#22c55e' : '#FF6600';
                        const statusText = order.status === 'delivered' ? 'Entregue' : 'Em Trânsito';
                        html += `
                        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-weight: 700; color: #002b49; margin-bottom: 4px;">Rastreio #${order.id}</div>
                                <div style="font-size: 0.85rem; color: #718096; margin-bottom: 4px;">Destinatário: ${order.customer_name || 'N/A'}</div>
                                <div style="font-size: 0.8rem; color: ${statusColor}; font-weight: 600;"><i class="fas fa-truck" style="margin-right:4px;"></i>${statusText}</div>
                            </div>
                            <div style="text-align: right;">
                                <button style="background:#fff5f0; color:#FF6600; border:1px solid #ffccaa; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:0.8rem; font-weight:bold;">Acompanhar</button>
                            </div>
                        </div>`;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                }
            };

            renderList('tab-content-todos', todos);
            renderList('tab-content-pendentes', pendentes);
            renderList('tab-content-entregues', entregues);
        }

        function toggleAccordion(el) {
            const item = el.parentElement;
            const wasActive = item.classList.contains('active');
            document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
            if (!wasActive) item.classList.add('active');
        }

        // --- Calculator Actions ---
        function saveOriginCEP() {
            const cep = document.getElementById('calc-cep-origem').value;
            if (cep) {
                localStorage.setItem('originCEP', cep);
                alert('CEP salvo!');
            }
        }

        function clearCalculatorForm() {
            document.getElementById('calc-cep-origem').value = '';
            document.getElementById('calc-cep-destino').value = '';
            document.getElementById('shipping-results').style.display = 'none';
            alert('Calculadora limpa.');
        }

        // --- Initialization ---
        document.addEventListener('DOMContentLoaded', () => {
            initAppSyncData();
            injectToastUI();
            const saved = localStorage.getItem('originCEP');
            if (saved) {
                const el = document.getElementById('calc-cep-origem');
                if (el) el.value = saved;
            }
            
            // Hook Add Funds button
            const addBtn = document.querySelector('.btn-add-funds-sm');
            if (addBtn) addBtn.onclick = openDepositModal;
            
            // Sync periodically
            setInterval(initAppSyncData, 60000);
            
            // Load user orders
            loadUserOrders();

            // Initialize Flatpickr for scheduling
            const dateInput = document.getElementById('sched-date-picker');
            if (dateInput) {
                flatpickr(dateInput, {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    locale: "pt",
                    onChange: function(selectedDates, dateStr) {
                        window.selectedDate = dateStr;
                        validateScheduling();
                    }
                });
            }
        });
    </script>

    <!-- MODALS SECTION (Moved to bottom for z-index safety) -->
    
    <div id="modal-registration" class="modal-premium" style="display: none; z-index: 10000;">
        <div class="modal-content-premium" style="max-width: 572px;">
            <div class="modal-header-premium">
                <button class="btn-modal-back" onclick="goBackFromRegistration()">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2 id="registration-modal-title" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                    Fazer Pagamento
                    <button onclick="fillTestData()" style="background: #edf2f7; border: none; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; color: #718096; cursor: pointer; font-weight: 600; transition: all 0.2s;">
                        <i class="fas fa-vial"></i> Preencher Teste
                    </button>
                </h2>
                <button class="btn-close-robust" onclick="closeRegistration()">×</button>
            </div>
            <div class="modal-body" style="padding: 0.5rem 1.5rem 0.8rem;">
                <!-- Checkout Tabs -->
                <div class="checkout-tabs">
                    <div class="checkout-tab active" data-method="CREDIT_CARD" onclick="switchCheckoutTab('CREDIT_CARD')">
                        <i class="fas fa-credit-card"></i> Cartão
                    </div>
                    <div class="checkout-tab" data-method="PIX" onclick="switchCheckoutTab('PIX')">
                        <span style="display:inline-flex; background:#FF6600; color:#fff; width:20px; height:20px; border-radius:4px; align-items:center; justify-content:center; font-size:10px; font-weight:900; margin-right:5px;">X</span> Pix
                    </div>
                    <div class="checkout-tab" data-method="BOLETO" onclick="switchCheckoutTab('BOLETO')">
                        <i class="fas fa-barcode"></i> Boleto
                    </div>
                </div>

                <div id="registration-form-inputs">
                    <div class="input-group" style="margin-bottom: 1rem;">
                        <label>Nome Completo (Pagador) *</label>
                        <input type="text" id="pay-name" value="<?php echo htmlspecialchars($userName); ?>" placeholder="Seu nome">
                    </div>
                    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="input-group">
                            <label>CPF/CNPJ *</label>
                            <input type="text" id="pay-cpf" placeholder="000.000.000-00">
                        </div>
                        <div class="input-group">
                            <label>Email *</label>
                            <input type="email" id="pay-email" value="<?php echo htmlspecialchars($userEmail); ?>" placeholder="seu@email.com">
                        </div>
                    </div>
                </div>

                <!-- Payment Panes -->
                <div id="pane-CREDIT_CARD" class="checkout-pane active">
                    <div class="cc-form">
                        <div class="input-group full">
                            <label>Número do Cartão</label>
                            <input type="text" id="cc-number" placeholder="0000 0000 0000 0000">
                        </div>
                        <div class="input-group full">
                            <label>Nome Impresso no Cartão</label>
                            <input type="text" id="cc-holder" placeholder="NOME COMO NO CARTÃO">
                        </div>
                        <div class="input-group">
                            <label>Validade</label>
                            <input type="text" id="cc-expiry" placeholder="MM/AA">
                        </div>
                        <div class="input-group">
                            <label>CVV</label>
                            <input type="text" id="cc-cvv" placeholder="123">
                        </div>
                    </div>
                </div>

                <div id="pane-PIX" class="checkout-pane">
                    <div style="text-align: center; padding: 1rem; background: #fff9f0; border-radius: 12px; border: 1px dashed #ffccaa;">
                        <i class="fab fa-pix" style="font-size: 2rem; color: #32bcad; margin-bottom: 10px;"></i>
                        <p style="font-size: 0.9rem; color: #718096;">O código PIX será gerado após clicar em pagar.</p>
                    </div>
                </div>

                <div id="pane-BOLETO" class="checkout-pane">
                    <div style="text-align: center; padding: 1rem; background: #f0f4ff; border-radius: 12px; border: 1px dashed #adc6ff;">
                        <i class="fas fa-barcode" style="font-size: 2rem; color: #2b6cb0; margin-bottom: 10px;"></i>
                        <p style="font-size: 0.9rem; color: #718096;">O boleto será gerado após clicar em pagar.</p>
                    </div>
                </div>

                <!-- Summary -->
                <div class="checkout-summary-mini">
                    <div class="checkout-summary-row">
                        <span>Frete Base</span>
                        <span id="summ-freight">R$ 0,00</span>
                    </div>
                    <div class="checkout-summary-row" id="summ-extra-row" style="display: none;">
                        <span id="summ-extra-label">Adicional</span>
                        <span id="summ-extra-value">R$ 0,00</span>
                    </div>
                    <div class="checkout-summary-total">
                        <span>Total a Pagar</span>
                        <span id="summ-total">R$ 0,00</span>
                    </div>
                </div>

                <button class="btn-calculate" onclick="completeOrderAsaas()" style="width: 100%;height: 55px;font-size: 1.1rem;border-radius: 12px;margin-top: 12px;">Finalizar e Pagar</button>
            </div>
        </div>
    </div>

    <!-- NEW: Postage Method Modal (Redesigned) -->
    <div id="modal-postage" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 650px;">
            <div class="modal-header-premium">
                <button class="btn-modal-back" onclick="openAddressForm(window.selectedService, window.selectedBasePrice)">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2>Escolha a Postagem</h2>
                <button class="btn-close-robust" onclick="closePostageModal()">×</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem 3rem 3rem;">
                <p class="modal-subtitle" style="text-align: center;margin-bottom: 2rem;font-size: 18px;font-weight: 500;">Como você deseja enviar seu pacote?</p>
                <div class="postage-options-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="premium-card" onclick="selectShipping('agencia')">
                        <div class="postage-icon"><i class="fas fa-building"></i></div>
                        <strong>Levar até a Agência</strong>
                        <p>Poste em qualquer transportadora ou ponto conveniado.</p>
                        <span class="price-tag">+ R$ 0,00</span>
                    </div>
                    <div class="premium-card" onclick="selectShipping('coleta')">
                        <div class="postage-icon"><i class="fas fa-truck"></i></div>
                        <strong>Solicitar Coleta</strong>
                        <p>Retiramos no seu endereço</p>
                        <p> em até 60 minutos.</p>
                        <span class="price-tag" id="coleta-base-price-display">+ R$ 17,91</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: Coleta Options Modal (Redesigned to 3-Column) -->
    <div id="modal-coleta-tipo" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 850px;">
            <div class="modal-header-premium">
                <button class="btn-modal-back" onclick="openPostageModal(window.selectedService, window.selectedBasePrice)">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2>Tipo de Coleta</h2>
                <button class="btn-close-robust" onclick="closeColetaOptionsModal()">×</button>
            </div>
            <div class="modal-body" style="padding: 2rem 3rem 3.5rem;">
                <div class="postage-options-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                    <div class="premium-card" onclick="selectColetaType('programada')">
                        <div class="postage-icon"><i class="fas fa-calendar-alt"></i></div>
                        <strong>COLETA PROGRAMADA</strong>
                        <p style="font-size: 0.75rem; color: #718096; margin-bottom: 5px; font-weight: 600;">Seg a Sáb às 20h</p>
                        <p>Agendar uma coleta programada no seu endereço.</p>
                        <span class="price-tag">(Valor do painel admin)</span>
                    </div>
                    <div class="premium-card" onclick="selectColetaType('express')">
                        <div class="postage-icon"><i class="fas fa-bolt"></i></div>
                        <strong>COLETA EXPRESS</strong>
                        <p style="font-size: 0.75rem; color: #718096; margin-bottom: 5px; font-weight: 600;">Em até 60 min</p>
                        <p>Retiramos no seu endereço em até 60 minutos.</p>
                        <span class="price-tag">(Valor do painel do admin)</span>
                    </div>
                    <div class="premium-card" onclick="selectColetaType('locker')">
                        <div class="postage-icon"><i class="fas fa-archive"></i></div>
                        <strong>Locker Inteligente</strong>
                        <p>Deixe no box seguro.</p>
                        <span class="price-tag">+ R$ 0,00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-coleta-agendamento" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 550px;">
            <div class="modal-header-premium">
                <button class="btn-modal-back" onclick="openColetaOptionsModal()">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2>Agende sua Coleta</h2>
                <button class="btn-close-robust" onclick="closeSchedulingModal()">×</button>
            </div>
            <div class="modal-body" style="padding: 2rem 2.5rem 3rem;">
                <p class="modal-subtitle" style="text-align: center; margin-bottom: 1.5rem;">Escolha o melhor momento para retirarmos seu pacote:</p>
                
                <div class="input-group" style="margin-bottom: 1.5rem;">
                    <label><i class="fas fa-calendar-day" style="color: var(--orange-vibrant); margin-right: 8px;"></i> Data da Coleta</label>
                    <input type="date" id="sched-date-picker" style="background: #f8fafc; border: 2px solid #f1f4f9; border-radius: 12px; height: 50px; padding: 0 15px; width: 100%;" onchange="validateScheduling()">
                </div>

                <div class="input-group" style="margin-bottom: 2rem;">
                    <label><i class="fas fa-clock" style="color: var(--orange-vibrant); margin-right: 8px;"></i> Horário Disponível</label>
                    <select id="sched-time-picker" style="background: #f8fafc; border: 2px solid #f1f4f9; border-radius: 12px; height: 50px; padding: 0 15px; width: 100%;">
                        <option value="">Selecione a data primeiro...</option>
                    </select>
                </div>

                <div id="sched-error-msg" style="display: none; background: #fff5f5; color: #e53e3e; padding: 1rem; border-radius: 12px; font-size: 0.9rem; margin-bottom: 1.5rem; border: 1px solid #fed7d7;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> <span id="sched-error-text"></span>
                </div>

                <button class="btn-calculate" id="btn-confirm-scheduling" onclick="confirmScheduling()" style="margin-top: 0; width: 100%; height: 55px; font-size: 1rem;">Prosseguir para Checkout</button>
            </div>
        </div>
    </div>
    <div id="modal-locker-select" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 550px;">
            <div class="modal-header-premium" style="padding-bottom: 1rem;">
                <button class="btn-modal-back" onclick="goBackFromLockers()">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2>Escolha o Ponto</h2>
                <button class="btn-close-robust" onclick="closeLockersModal()">×</button>
            </div>
            <div class="modal-body" style="padding: 1.5rem 2rem 2.5rem;">
                <p class="modal-subtitle" style="text-align: center; margin-bottom: 1rem; font-size: 1.1rem;">Selecione o ponto para depósito da sua encomenda:</p>
                <div id="lockers-container" style="display: flex; flex-direction: column; gap: 0.8rem; max-height: 350px; overflow-y: auto; padding-right: 5px; padding-top: 1rem;">
                    <div style="text-align: center; padding: 2rem; color: #718096;">Carregando lockers disponíveis...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-address-form" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 600px; border-radius: 16px;">
            <div class="modal-header-premium" style="border-bottom:0;">
                <button class="btn-modal-back" onclick="navigateAddressForm('prev')" id="btn-address-back" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Voltar
                </button>
                <h2 id="address-modal-title">Dados do Remetente</h2>
                <button class="btn-close-robust" onclick="closeAddressForm()">×</button>
            </div>
            <div class="step-indicator">
                <div id="step-1-dot" style="width: 12px; height: 12px; border-radius: 50%; background: #FF6600; transition: background 0.3s;"></div>
                <div id="step-2-dot" style="width: 12px; height: 12px; border-radius: 50%; background: #e2e8f0; transition: background 0.3s;"></div>
            </div>
            <div class="modal-body" style="padding: 0 2rem 2rem;">
                <div id="address-step-1" style="display: block;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.2rem;">
                        <div class="input-group"><label>CEP de Origem *</label><input type="text" id="sender-cep" placeholder="00000-000" onkeyup="if(this.value.length === 9) lookupCEP('sender')"></div>
                        <div class="input-group"><label>Documento (CPF/CNPJ) *</label><input type="text" id="sender-doc" placeholder="000.000.000-00"></div>
                    </div>
                    <div class="input-group" style="margin-bottom: 1.2rem;"><label>Nome Completo / Razão Social *</label><input type="text" id="sender-name" placeholder="Ex: João da Silva"></div>
                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1.5rem; margin-bottom: 1.2rem;">
                        <div class="input-group"><label>Logradouro/Rua *</label><input type="text" id="sender-street" placeholder="Sua Rua"></div>
                        <div class="input-group"><label>Número *</label><input type="text" id="sender-number" placeholder="123"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                        <div class="input-group"><label>Complemento</label><input type="text" id="sender-comp" placeholder="Apto 10"></div>
                        <div class="input-group"><label>Bairro *</label><input type="text" id="sender-neighborhood" placeholder="Centro"></div>
                        <div class="input-group"><label>Cidade/UF *</label><input type="text" id="sender-city-uf" placeholder="São Paulo - SP" readonly></div>
                    </div>
                    <button onclick="navigateAddressForm('next')" style="width: 100%; background: #FF6600; color: white; border: none; padding: 1rem; border-radius: 8px; font-size: 1.1rem; font-weight: 700;">Próximo: Destinatário <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></button>
                </div>
                <div id="address-step-2" style="display: none;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.2rem;">
                        <div class="input-group"><label>CEP de Destino *</label><input type="text" id="receiver-cep" placeholder="00000-000" onkeyup="if(this.value.length === 9) lookupCEP('receiver')"></div>
                        <div class="input-group"><label>Documento (CPF/CNPJ) *</label><input type="text" id="receiver-doc" placeholder="000.000.000-00"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 1.5rem; margin-bottom: 1.2rem;">
                        <div class="input-group"><label>Nome/Razão Social *</label><input type="text" id="receiver-name" placeholder="Ex: Maria Souza"></div>
                        <div class="input-group"><label>Email ou Celular</label><input type="text" id="receiver-contact" placeholder="(11) 90000-0000"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1.5rem; margin-bottom: 1.2rem;">
                        <div class="input-group"><label>Logradouro/Rua *</label><input type="text" id="receiver-street" placeholder="Sua Rua"></div>
                        <div class="input-group"><label>Número *</label><input type="text" id="receiver-number" placeholder="123"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                        <div class="input-group"><label>Complemento</label><input type="text" id="receiver-comp" placeholder="Apto 10"></div>
                        <div class="input-group"><label>Bairro *</label><input type="text" id="receiver-neighborhood" placeholder="Centro"></div>
                        <div class="input-group"><label>Cidade/UF *</label><input type="text" id="receiver-city-uf" placeholder="São Paulo - SP" readonly></div>
                    </div>
                    <button onclick="navigateAddressForm('next')" style="width: 100%; background: #FF6600; color: white; border: none; padding: 1rem; border-radius: 8px; font-size: 1.1rem; font-weight: 700;">Avançar para Opções de Agência <i class="fas fa-check" style="margin-left: 8px;"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- System Synchronization ---
        let appSyncData = { services: [], locations: [], lockers: [] };

        async function initAppSyncData() {
            try {
                const response = await fetch('endpoints/get_sync_data');
                const result = await response.json();
                if (result.ok) {
                    appSyncData = result.data;
                    console.log("Sistema sincronizado:", appSyncData);
                }
            } catch (err) {
                console.error("Erro ao sincronizar sistema:", err);
            }
        }

        // --- System Initialization & UI Helpers ---
        // Ensure all modals are present in the DOM and at the end of body for z-index safety
        function ensureModals() {
            const modals = document.querySelectorAll('.modal-premium');
            modals.forEach(m => document.body.appendChild(m));
        }
        document.addEventListener('DOMContentLoaded', ensureModals);
    </script>
</body>

</html>