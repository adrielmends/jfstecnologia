<?php
session_start();
// MOCK DATA FOR DEMO
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Cliente Demo';
$_SESSION['user_role'] = 'user';

// Mock Balance
$balance = 1250.75;
$userName = 'Cliente Demo';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Ex-Envios (DEMO)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/painel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mobile Layout Overrides - Premium UI */
        .mobile-bottom-nav {
            display: none;
        }

        @media (max-width: 991px) {
            .dashboard-container {
                display: block;
                height: auto;
                min-height: 100vh;
                padding-bottom: 80px;
                /* Space for bottom nav */
                background: #f0f4f8;
            }

            .sidebar {
                display: none;
            }

            .top-header {
                height: 70px;
                padding: 0 1.25rem;
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(15px);
                position: sticky;
                top: 0;
                z-index: 1000;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            .header-right {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .header-user-info {
                align-items: flex-end;
                margin-left: auto;
                margin-right: 15px;
            }

            .user-avatar {
                width: 38px;
                height: 38px;
            }

            .content-wrapper {
                padding: 1rem;
                max-width: 100%;
            }

            /* Bottom Nav Styles */
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                height: 70px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                display: flex;
                justify-content: space-around;
                align-items: center;
                border-top: 1px solid rgba(0, 0, 0, 0.08);
                z-index: 2000;
                padding-bottom: env(safe-area-inset-bottom);
            }

            .mobile-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none;
                color: var(--text-muted);
                font-size: 0.75rem;
                gap: 4px;
                flex: 1;
                transition: color 0.3s;
            }

            .mobile-nav-item i {
                font-size: 1.25rem;
            }

            .mobile-nav-item.active {
                color: var(--primary-color);
            }

            .mobile-nav-item.center {
                position: relative;
                top: -20px;
            }

            .center-icon {
                width: 54px;
                height: 54px;
                background: var(--primary-color);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 16px rgba(255, 107, 0, 0.3);
                border: 4px solid white;
            }

            .center-icon i {
                font-size: 1.5rem;
            }

            /* Card Visual Improvements */
            .calculator-card,
            .limit-section,
            .hero-banner {
                border-radius: 20px;
                border: none;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            }

            .hero-banner {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                background: linear-gradient(135deg, #001A2C 0%, #004E82 100%);
            }

            .banner-content h1 {
                font-size: 1.5rem;
            }

            /* Calculator UI - Standardized Top-aligned Labels */
            .calculator-card {
                padding: 1.75rem !important;
                border-radius: 28px !important;
                background: #ffffff !important;
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.04) !important;
                border: 1px solid rgba(0, 0, 0, 0.02) !important;
            }

            .input-group {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                width: 100% !important;
            }

            .input-group label {
                display: block !important;
                font-size: 0.7rem !important;
                font-weight: 800 !important;
                color: #8E9BAE !important;
                text-transform: uppercase !important;
                letter-spacing: 0.1em !important;
                margin-bottom: 0.6rem !important;
                text-align: left !important;
                width: 100% !important;
            }

            .input-group input,
            .input-group select {
                width: 100% !important;
                height: 52px !important;
                padding: 0 1.25rem !important;
                border-radius: 14px !important;
                border: 2px solid #F1F4F9 !important;
                font-weight: 700 !important;
                color: var(--navy-dark) !important;
                background: #FAFBFE !important;
                font-size: 1rem !important;
                transition: all 0.3s ease !important;
            }

            .input-group input:focus,
            .input-group select:focus {
                border-color: var(--primary-color) !important;
                background: #fff !important;
                box-shadow: 0 0 0 5px rgba(255, 107, 0, 0.1) !important;
                outline: none !important;
            }

            .form-row {
                flex-direction: column !important;
                gap: 1.5rem !important;
                margin-bottom: 1.5rem !important;
            }

            .form-row.three-cols {
                display: grid !important;
                grid-template-columns: 1fr 1fr 1fr !important;
                gap: 0.8rem !important;
                background: #F8FAFD;
                padding: 1.25rem;
                border-radius: 20px;
                border: 1px solid #EBF0F7;
            }

            .form-row.three-cols .input-group label {
                font-size: 0.65rem !important;
                opacity: 0.9 !important;
            }

            .action-buttons {
                display: grid !important;
                grid-template-columns: 1.5fr 1fr !important;
                gap: 1rem !important;
                margin-top: 2rem !important;
                width: 100% !important;
            }

            .btn-confirm,
            .btn-secondary {
                height: 56px !important;
                margin: 0 !important;
                border-radius: 18px !important;
                font-weight: 900 !important;
                font-size: 0.95rem !important;
                letter-spacing: 0.05em !important;
                text-transform: uppercase !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .btn-secondary {
                background: #F1F4F9 !important;
                color: #718096 !important;
                border: none !important;
            }

            /* Profile Section - Ultra Premium Redesign */
            .profile-card-premium {
                background: #ffffff !important;
                border-radius: 32px !important;
                padding: 2rem !important;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.04) !important;
                margin-bottom: 1.5rem !important;
                border: 1px solid rgba(0, 0, 0, 0.01) !important;
            }

            .profile-label-top {
                font-size: 0.75rem !important;
                font-weight: 800 !important;
                color: #8E9BAE !important;
                text-transform: uppercase !important;
                letter-spacing: 0.12em !important;
                display: block !important;
                margin-bottom: 0.5rem !important;
            }

            .profile-user-name {
                font-size: 2.2rem !important;
                font-weight: 900 !important;
                color: var(--navy-dark) !important;
                letter-spacing: -0.03em !important;
                margin: 0 !important;
            }

            .profile-stats-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr 1fr !important;
                gap: 0.75rem !important;
                margin-top: 2rem !important;
            }

            .p-stat-item {
                background: #FAFBFE !important;
                border-radius: 20px !important;
                padding: 1rem 0.5rem !important;
                text-align: center !important;
                border: 1.5px solid #F1F4F9 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .p-stat-icon {
                width: 44px !important;
                height: 44px !important;
                background: #ffffff !important;
                border-radius: 14px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 auto 0.6rem !important;
                color: var(--primary-color) !important;
                font-size: 1.1rem !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02) !important;
            }

            .p-stat-item span {
                font-size: 0.65rem !important;
                color: #718096 !important;
                font-weight: 800 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                line-height: 1.2 !important;
                display: block !important;
            }

            /* Promo Banner Premium */
            .promo-banner-premium {
                background: linear-gradient(135deg, #001A2C 0%, #004E82 100%) !important;
                border-radius: 28px !important;
                padding: 1.75rem !important;
                position: relative !important;
                overflow: hidden !important;
                margin-bottom: 1.5rem !important;
                box-shadow: 0 15px 40px rgba(0, 26, 44, 0.15) !important;
            }

            .promo-banner-premium::after {
                content: '';
                position: absolute;
                top: -20%;
                right: -10%;
                width: 150px;
                height: 150px;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 50%;
            }

            .promo-chip {
                background: rgba(255, 107, 0, 0.15) !important;
                color: var(--primary-color) !important;
                padding: 0.4rem 0.8rem !important;
                border-radius: 10px !important;
                font-size: 0.65rem !important;
                font-weight: 900 !important;
                letter-spacing: 0.1em !important;
                display: inline-block !important;
                margin-bottom: 0.75rem !important;
                border: 1px solid rgba(255, 107, 0, 0.2) !important;
            }

            .promo-inner h3 {
                color: #ffffff !important;
                font-size: 1.35rem !important;
                font-weight: 800 !important;
                margin-bottom: 0.4rem !important;
            }

            .promo-inner p {
                color: rgba(255, 255, 255, 0.7) !important;
                font-size: 0.9rem !important;
                margin-bottom: 1.5rem !important;
                line-height: 1.4 !important;
            }

            .promo-btn-action {
                width: 100% !important;
                height: 52px !important;
                background: #ffffff !important;
                color: var(--navy-dark) !important;
                border-radius: 16px !important;
                border: none !important;
                font-weight: 900 !important;
                font-size: 0.85rem !important;
                letter-spacing: 0.05em !important;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
            }

            /* Profile Lists Elegant */
            .profile-list {
                padding: 0 !important;
                margin: 0 !important;
                list-style: none !important;
            }

            .profile-list-item {
                background: #ffffff !important;
                border-radius: 20px !important;
                padding: 1.25rem 1.5rem !important;
                margin-bottom: 0.8rem !important;
                border: 1px solid #F1F4F9 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                transition: all 0.2s ease !important;
            }

            .profile-item-left {
                display: flex !important;
                align-items: center !important;
                gap: 1rem !important;
            }

            .profile-item-left svg {
                color: var(--primary-color) !important;
                opacity: 0.8 !important;
            }

            .profile-item-right {
                display: flex !important;
                align-items: center !important;
            }

            .profile-item-text {
                font-weight: 700 !important;
                color: var(--navy-dark) !important;
                font-size: 0.95rem !important;
            }

            .profile-item-value {
                font-weight: 800 !important;
                color: var(--primary-color) !important;
                margin-right: 0.5rem !important;
            }

            /* Global Profile Stats Grid Fix (Desktop & Mobile) */
            .profile-stats-grid {
                display: grid !important;
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)) !important;
                gap: 1rem !important;
                margin-top: 2rem !important;
                width: 100% !important;
            }

            .p-stat-item {
                background: #FAFBFE !important;
                border-radius: 20px !important;
                padding: 1.5rem 1rem !important;
                text-align: center !important;
                border: 1.5px solid #F1F4F9 !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.8rem !important;
            }

            .p-stat-icon {
                width: 50px !important;
                height: 50px !important;
                background: #ffffff !important;
                border-radius: 14px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                color: var(--primary-color) !important;
                font-size: 1.25rem !important;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
            }

            .p-stat-item span {
                font-size: 0.75rem !important;
                color: #4A5568 !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                line-height: 1.2 !important;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/img/login/logo.png" alt="Ex-Envios" class="sidebar-logo">
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
                            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Perfil
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="../totem.html?direct=true" class="btn-sidebar-emitir">
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
            <a href="#" class="mobile-nav-item" onclick="switchView('perfil', this)">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
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
                        C
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
                            <img src="../assets/img/login/icon.png" alt="Promo" style="height: 150px; opacity: 0.2;">
                        </div>
                    </div>

                    <div class="calculator-section">
                        <h3 class="section-title">INFORME A ORIGEM (DEMO)</h3>
                        <div class="calculator-card">
                            <div class="form-row">
                                <div class="input-group">
                                    <label>CEP de origem</label>
                                    <input type="text" id="calc-cep-origem" placeholder="00000-000" value="79115-800">
                                </div>
                                <div class="action-buttons">
                                    <button class="btn-sm btn-primary">SALVAR</button>
                                    <button class="btn-sm btn-outline">LIMPAR</button>
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
                            <input type="text" placeholder="Buscar por destinatário">
                        </div>
                    </div>

                    <div class="empty-state">
                        <h3>Emita o seu primeiro frete com a gente <br>para ter acesso aos nossos benefícios</h3>
                        <button class="btn-primary" onclick="selectShipping('empty-state')">Emitir frete</button>
                    </div>
                </div>

                <!-- VIEW 4: Perfil -->
                <div id="view-perfil" style="display: none;">
                    <div class="profile-card-premium">
                        <span class="profile-label-top">Informações da Conta</span>
                        <h2 class="profile-user-name"><?php echo htmlspecialchars($userName); ?></h2>
                        
                        <div class="profile-stats-grid">
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-wallet"></i></div>
                                <span>Saldo: R$ <?php echo number_format($balance, 2, ',', '.'); ?></span>
                            </div>
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-box"></i></div>
                                <span>Total Envios: 0</span>
                            </div>
                            <div class="p-stat-item">
                                <div class="p-stat-icon"><i class="fas fa-star"></i></div>
                                <span>Nível: Bronze</span>
                            </div>
                        </div>
                    </div>

                    <ul class="profile-list">
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <i class="fas fa-user-edit"></i>
                                <span class="profile-item-text">Editar Perfil</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </li>
                        <li class="profile-list-item">
                            <div class="profile-item-left">
                                <i class="fas fa-shield-alt"></i>
                                <span class="profile-item-text">Segurança</span>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </li>
                        <li class="profile-list-item" onclick="location.href='../login.html'">
                            <div class="profile-item-left">
                                <i class="fas fa-sign-out-alt" style="color: #ef4444 !important;"></i>
                                <span class="profile-item-text" style="color: #ef4444;">Sair da Conta</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- END VIEWS -->
            </div>
        </main>
    </div>

    <!-- Registration Modal (Redesigned) -->
    <div id="modal-registration" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 450px;">
            <div class="modal-header">
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark);">Cadastre-se</h2>
                <button class="btn-close" onclick="closeRegistration()">×</button>
            </div>
            <div class="modal-body">
                <p class="modal-subtitle">Para emitir sua etiqueta, precisamos de alguns dados (Simulado).</p>
                <div class="input-group" style="margin-bottom: 1.25rem;">
                    <label>Nome Completo</label>
                    <input type="text" placeholder="Seu nome" id="reg-name">
                </div>
                <button class="btn-calculate" onclick="completeRegistration()" style="width: 100%;">Concluir Cadastro</button>
            </div>
        </div>
    </div>

    <!-- Postage Method Modal -->
    <div id="modal-postage" class="modal-premium" style="display: none;">
        <div class="modal-content-premium" style="max-width: 500px;">
            <div class="modal-header">
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--navy-dark);">Escolha a Postagem</h2>
                <button class="btn-close" onclick="closePostageModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="postage-options">
                    <div class="postage-card" onclick="selectShipping('agencia')" style="margin-bottom: 1rem; padding: 1.5rem; border: 2px solid #f1f4f9; border-radius: 20px; cursor: pointer;">
                        <strong>🏪 Levar até a Agência</strong>
                        <p>Poste em qualquer agência dos Correios.</p>
                    </div>
                    <div class="postage-card" onclick="selectShipping('coleta')" style="padding: 1.5rem; border: 2px solid #f1f4f9; border-radius: 20px; cursor: pointer;">
                        <strong>🚚 Solicitar Coleta</strong>
                        <p>Retiramos no seu endereço.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function calculateShipping() {
            const cepDest = document.getElementById('calc-cep-destino').value;
            if (!cepDest) { alert('Informe o CEP de destino'); return; }

            const btn = document.querySelector('.btn-calculate');
            btn.innerHTML = "Calculando...";
            btn.disabled = true;

            const payload = {
                sCepOrigem: '79115-800',
                sCepDestino: cepDest,
                nCdFormato: '1',
                nVlPeso: '1',
                nVlAltura: '10',
                nVlLargura: '15',
                nVlComprimento: '20'
            };

            try {
                const response = await fetch('../endpoints/calc_frete_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    renderShippingResults(result.servicos);
                    document.getElementById('shipping-results').style.display = 'block';
                }
            } catch (e) {
                alert('Erro ao calcular. (Demo)');
            } finally {
                btn.innerHTML = "CALCULAR FRETE COM DESCONTO";
                btn.disabled = false;
            }
        }

        function renderShippingResults(servicos) {
            const container = document.getElementById('shipping-options-container');
            container.innerHTML = '';
            servicos.forEach(s => {
                const html = `
                    <div class="shipping-option" style="padding: 1rem; border-bottom: 1px solid #f1f4f9; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${s.servico_nome}</strong>
                            <p style="margin: 0; font-size: 0.8rem;">Até ${s.prazo_dias} dias</p>
                        </div>
                        <div style="text-align: right;">
                            <strong>R$ ${s.valor_float.toFixed(2)}</strong>
                            <button onclick="openPostageModal()" style="display: block; background: var(--primary-color); color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; margin-top: 5px;">Gerar</button>
                        </div>
                    </div>
                `;
                container.innerHTML += html;
            });
        }

        function openPostageModal() { document.getElementById('modal-postage').style.display = 'flex'; }
        function closePostageModal() { document.getElementById('modal-postage').style.display = 'none'; }
        function selectShipping(method) { closePostageModal(); document.getElementById('modal-registration').style.display = 'flex'; }
        function closeRegistration() { document.getElementById('modal-registration').style.display = 'none'; }
        function completeRegistration() { alert("Simulado: Cadastro concluído!"); location.reload(); }

        function switchView(viewName, element) {
            document.querySelectorAll('.nav-item, .mobile-nav-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');
            ['calculator', 'etiquetas', 'rastreio', 'ajuda', 'integracoes', 'perfil', 'convide'].forEach(v => {
                const el = document.getElementById('view-' + v);
                if (el) el.style.display = (v === viewName ? 'block' : 'none');
            });
        }
    </script>

    <!-- Adicionar Saldo (MOCK) -->
    <div id="modal-deposit" class="modal-premium" style="display: none;">
        <div class="modal-content-premium">
            <div class="modal-header">
                <h2>Adicionar Saldo</h2>
                <button class="btn-close" onclick="closeDepositModal()">×</button>
            </div>
            <div class="modal-body">
                <input type="number" id="deposit-amount" placeholder="Valor R$" style="width: 100%; height: 50px; padding: 1rem; border-radius: 12px; border: 2px solid #f1f4f9;">
                <button class="btn-calculate" onclick="alert('Demo: Pagamento simulado!')" style="width: 100%; margin-top: 1rem;">GERAR PAGAMENTO</button>
            </div>
        </div>
    </div>

    <script>
        function openDepositModal() { document.getElementById('modal-deposit').style.display = 'flex'; }
        function closeDepositModal() { document.getElementById('modal-deposit').style.display = 'none'; }
    </script>
</body>
</html>
