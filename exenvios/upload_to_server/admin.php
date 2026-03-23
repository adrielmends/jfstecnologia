<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: /admin-login');
    exit;
}

$userName = $_SESSION['user_name'] ?? 'Administrador';
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
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Ex-Envios</title>
    <link rel="stylesheet" href="assets/css/painel.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
    </style>
    <script>
        // Override standard alert early
        window.alert = function showToast(message) {
            let toast = document.getElementById('custom-toast');
            if(!toast) {
                // Determine if DOM is ready to inject
                if(document.body) {
                    injectToastUI();
                    toast = document.getElementById('custom-toast');
                } else {
                    // Fallback if alert called before body exists
                    console.log("Alert (pre-DOM): ", message);
                    return; 
                }
            }
            document.getElementById('toast-message').innerText = message;
            toast.style.display = 'flex';
            setTimeout(() => { toast.classList.add('show'); }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => { toast.style.display = 'none'; }, 300);
            }, 4000);
        };
        
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
        
        // Inject UI when DOM is ready
        document.addEventListener('DOMContentLoaded', injectToastUI);
    </script>
    <style>
        :root {
            --navy-dark: #001a2c;
            --navy-light: #002b49;
            --orange-vibrant: #FF6B00;
        }

        .stat-card.active {
            border: 2px solid var(--orange-vibrant) !important;
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.1) !important;
        }

        body {
            background-color: #f0f4f8;
        }

        .sidebar {
            background-color: var(--navy-dark);
            border-right: none;
            display: flex;
            flex-direction: column;
            height: 100vh;
            width: 36vh;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding: 2.5rem 2rem 2rem 2rem;
        }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            padding-bottom: 1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 1rem 0.8rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            border-radius: 10px;
        }

        .nav-item:hover,
        .nav-item.active {
            background-color: rgba(255, 107, 0, 0.1) !important;
            color: var(--orange-vibrant) !important;
        }

        .nav-item.active {
            font-weight: 700;
        }

        .nav-item i {
            margin-right: 0.6rem;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-logo {
            filter: invert(1) hue-rotate(180deg) brightness(1.2);
            max-width: 100%;
        }

        .top-header {
            background: #ffffff;
            border-bottom: 1px solid #eef2f6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Modern Settings UI Styles */
        .settings-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
            margin-bottom: 2rem;
        }

        .settings-group-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .day-toggle-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .day-chip {
            position: relative;
        }

        .day-chip input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .day-chip label {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: #f1f5f9;
            color: #64748b;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            user-select: none;
        }

        .day-chip input:checked + label {
            background: var(--orange-vibrant);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 0, 0.25);
        }

        .day-chip:hover label {
            background: #e2e8f0;
        }

        .time-input-wrapper {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .time-input-wrapper:focus-within {
            border-color: var(--orange-vibrant);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.1);
        }

        .time-input-wrapper i {
            color: #94a3b8;
            margin-right: 12px;
        }

        .time-input-wrapper input {
            border: none;
            background: transparent;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--navy-dark);
            width: 100%;
            outline: none;
        }

        .holiday-textarea {
            width: 100%;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.2rem;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            min-height: 120px;
            resize: vertical;
            transition: all 0.2s;
            line-height: 1.6;
        }

        .holiday-textarea:focus {
            border-color: var(--orange-vibrant);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 107, 0, 0.1);
            outline: none;
        }

        .btn-save-settings {
            background: var(--orange-vibrant);
            color: #fff;
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 2rem;
            box-shadow: 0 4px 15px rgba(255, 107, 0, 0.2);
        }

        .btn-save-settings:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 0, 0.3);
            filter: brightness(1.1);
        }

        .stat-info h4 {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.2rem;
        }

        .stat-info span {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--navy-dark);
        }

        .admin-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            border: 1px solid #eef2f6;
            border-collapse: collapse;
        }

        .admin-table-container {
            overflow: visible !important;
            width: 100%;
        }

        .admin-table th {
            text-align: center;
            padding: 1.2rem 1rem;
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 700;
        }

        .admin-table td {
            text-align: center;
            padding: 1.2rem 1rem;
            border-top: 1px solid #f1f5f9;
            color: var(--navy-dark);
            font-weight: 500;
            vertical-align: middle;
            font-size: 15px;
        }

        .nav-item.active {
            font-weight: 700;
        }
        
        /* Dropdown Z-Index */
        .status-menu {
            z-index: 999999 !important;
        }
        
        /* Table Alignment and Padding Overrides */
        .align-left-padding {
            text-align: left !important;
            padding-left: 2rem !important;
        }
        
        /* Status Badge Interactivity */
        .status-badge, .location-dot-status {
            cursor: pointer;
            user-select: none;
            transition: opacity 0.2s;
        }
        .status-badge:hover, .location-dot-status:hover {
            filter: brightness(0.95);
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 145px;
            border: 2px solid transparent;
            position: relative;
        }

        .status-badge:hover {
            filter: brightness(0.95);
            border-color: rgba(0,0,0,0.1);
        }

        .status-pending {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-deposited {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-transit {
            background: #eff6ff;
            color: #2563eb;
        }

        .status-delivered {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-failed {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Status Dropup Menu */
        .status-menu {
            position: absolute;
            top: 100% !important;
            bottom: auto !important;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            display: none;
            flex-direction: column;
            width: 160px;
            z-index: 999999 !important;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: none !important;
            animation: none !important;
        }
        
        /* Consistently display the menu when open */
        .status-badge:active .status-menu,
        .status-badge.open .status-menu {
            display: flex !important;
        }
        
        /* Ensure open badge is above header (stacking context fix) */
        .status-badge.open {
            z-index: 10001 !important;
        }

        .status-option {
            padding: 0.8rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--navy-dark);
            text-align: left;
            transition: background 0.2s;
            border-bottom: 1px solid #f1f5f9;
        }

        .status-option:last-child {
            border-bottom: none;
        }

        .status-option:hover {
            background: #f8fafc;
            color: var(--orange-vibrant);
        }

        .location-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .location-dot-status {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            gap: 2px;
            font-size: 0.8rem;
            font-weight: 700;
            min-width: 90px;
            cursor: pointer;
            user-select: none;
            transition: opacity 0.2s;
        }

        .location-dot-status:hover {
            opacity: 0.8;
        }

        /* Fixed widths for Signal and Status to prevent flick/shift */
        .td-sinal {
            min-width: 140px !important;
            text-align: center !important;
        }
        .td-status {
            min-width: 170px !important;
            text-align: center !important;
        }

        /* Mobile elements hidden by default on desktop */
        .mobile-toggle,
        .notification-mobile {
            display: none;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 60px;
            right: 10px;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #eef2f6;
            display: none;
            z-index: 6000;
            overflow: hidden;
            animation: fadeIn 0.2s ease-out;
        }

        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .notification-header h4 {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--navy-dark);
            text-transform: uppercase;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 1rem;
            border-bottom: 1px solid #f8fafc;
            display: flex;
            gap: 1rem;
            transition: background 0.2s;
            cursor: pointer;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item i {
            color: var(--orange-vibrant);
            font-size: 1.2rem;
            margin-top: 3px;
        }

        .notification-item-content h5 {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--navy-dark);
            margin-bottom: 2px;
        }

        .notification-item-content p {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.4;
        }

        #alert-overlay {
            display: none !important;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .realtime-alert {
            background: var(--navy-dark);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            border-left: 5px solid var(--orange-vibrant);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            max-width: 400px;
        }

        .alert-icon {
            font-size: 2.5rem;
            color: var(--orange-vibrant);
        }

        .alert-content h4 {
            margin-bottom: 0.3rem;
            color: var(--orange-vibrant);
            text-transform: uppercase;
            font-weight: 800;
        }

        .alert-content p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Section Visibility */
        .content-section {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .content-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal System */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 26, 44, 0.9);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            width: 100%;
            max-width: 550px;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalIn 0.2s ease-out;
        }

        @keyframes modalIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Bulk Actions Bar */
        .bulk-actions-bar {
            position: fixed;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--navy-dark);
            padding: 1rem 2rem;
            border-radius: 50px;
            display: none;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 4000;
            border: 2px solid var(--orange-vibrant);
            color: white;
            animation: slideUp 0.3s ease-out;
        }

        .bulk-actions-bar.active {
            display: flex;
        }

        .selection-count {
            font-weight: 800;
            color: var(--orange-vibrant);
        }

        .btn-bulk-delete {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-bulk-delete:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .order-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--orange-vibrant);
        }

        .modal-header h3 {
            font-weight: 800;
            text-transform: uppercase;
            color: var(--navy-dark);
            font-size: 1.25rem;
            letter-spacing: 1px;
        }

        .close-modal {
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            background: #e2e8f0;
            color: #ef4444;
        }

        .admin-form .form-group {
            margin-bottom: 1.5rem;
        }

        .admin-form label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--navy-light);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .admin-form input,
        .admin-form select,
        .admin-form textarea {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            font-family: inherit;
            transition: all 0.2s;
        }

        .admin-form input:focus {
            outline: none;
            border-color: var(--orange-vibrant);
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.1);
        }

        .btn-action-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-cancel {
            flex: 1;
            padding: 1rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: white;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-confirm {
            flex: 2;
            padding: 1rem;
            border-radius: 10px;
            border: none;
            background: var(--orange-vibrant);
            color: white;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(255, 107, 0, 0.2);
        }

        .section-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn-new-item {
            padding: 0.6rem 1.2rem;
            background: var(--orange-vibrant);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s;
        }

        .btn-new-item:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        @keyframes slideDown {
            from {
                transform: translateY(0);
                opacity: 1;
            }

            to {
                transform: translateY(100px);
                opacity: 0;
            }
        }

        /* Content Area Area Constraint - CRITICAL FOR DESKTOP */
        .content-wrapper {
            padding-top: 2rem;
            max-width: 1280px;
            width: 100%;
        }

        /* MOBILE IMPROVEMENTS - STRICTLY ISOLATED */
        @media (max-width: 991px) {

            .mobile-toggle,
            .notification-mobile {
                display: block !important;
                background: none;
                border: none;
                color: var(--navy-dark);
                font-size: 1.4rem;
                padding: 0.5rem;
                margin-right: 0.5rem;
                cursor: pointer;
            }

            .header-left {
                display: flex;
                align-items: center;
            }

            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                width: 280px;
                background-color: var(--navy-dark);
                z-index: 5000;
                transform: translateX(-100%);
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                display: block !important;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .sidebar-nav {
                display: block !important;
                padding-top: 10px;
            }

            .nav-item {
                display: flex !important;
                align-items: center;
                padding: 12px 20px !important;
                color: rgba(255, 255, 255, 0.7) !important;
                text-decoration: none !important;
                font-size: 0.95rem !important;
            }

            .nav-item i {
                margin-right: 15px !important;
                width: 25px !important;
                text-align: center !important;
            }

            .nav-item.active {
                background: rgba(255, 107, 0, 0.15) !important;
                color: var(--orange-vibrant) !important;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 4999;
                display: none;
            }

            .sidebar-overlay.active {
                display: block;
            }

            .top-header {
                position: sticky;
                top: 0;
                z-index: 1000;
                padding: 1rem;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }

            .top-header h2 {
                display: none;
            }

            .section-title {
                font-size: 1.2rem;
                color: #000000;
                font-weight: 700;
                text-transform: uppercase;
                margin-bottom: 0.75rem;
                letter-spacing: 0.05em;
            }

            .header-right .header-user-info,
            .header-right .user-greeting {
                display: none;
            }

            .admin-table-container {
                background: transparent;
                border: none;
                box-shadow: none;
                padding: 0;
                overflow: visible;
            }

            .admin-table thead {
                display: none;
            }

            .admin-table tbody {
                display: block;
            }

            .admin-table tr {
                background: white;
                border-radius: 16px;
                padding: 1.25rem;
                margin-bottom: 1.5rem;
                box-shadow: 0 10px 25px rgba(240, 244, 248, 0.8);
                border: 1px solid #E2E8F0;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
                align-items: start;
            }

            .admin-table td {
                border: none;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                font-size: 0.85rem;
                gap: 4px;
            }

            .admin-table td:first-child {
                grid-column: span 3;
                border-bottom: 1px solid #F1F5F9;
                padding-bottom: 0.75rem;
                margin-bottom: 0.5rem;
                font-weight: 800;
            }

            .admin-table td[data-label="Status"],
            .admin-table td[data-label="Ações"] {
                grid-column: span 3;
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px dashed #F1F5F9;
            }

            .admin-table td::before {
                content: attr(data-label);
                font-weight: 800;
                color: #8E9BAE;
                text-transform: uppercase;
                font-size: 0.65rem;
                margin-bottom: 2px;
            }


            .main-content {
                padding-bottom: 2rem;
                padding-left: 0 !important;
                width: 100% !important;
            }

            .btn-new-item {
                position: fixed;
                bottom: 2rem;
                left: 50%;
                transform: translateX(-50%);
                z-index: 2100;
                box-shadow: 0 8px 25px rgba(255, 107, 0, 0.4);
                padding: 0.8rem 2rem;
                border-radius: 50px;
                width: max-content;
                border: 2px solid white;
            }

            .section-header-actions {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                text-align: center;
            }

            .wallet-badge-compact {
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 0.65rem;
                font-weight: 900;
            }

            .modal-content {
                padding: 1.5rem !important;
                border-radius: 12px !important;
            }

            .modal-header {
                margin-bottom: 1.5rem !important;
            }

            .modal-header h3 {
                font-size: 1.1rem !important;
            }

            .btn-action-group {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }

            .btn-action-group button {
                width: 100% !important;
                flex: none !important;
            }
        }
    </style>
</head>

<body>
    <div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="assets/img/logo_exenvios.png" alt="Logo" class="sidebar-logo">
            </div>
            <nav class="sidebar-nav">
                <a href="#" class="nav-item active" onclick="switchSection('dashboard', this)">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="#" class="nav-item" onclick="switchSection('encomendas', this)">
                    <i class="fas fa-box"></i> Encomendas
                </a>
                <a href="#" class="nav-item" onclick="switchSection('locais', this)">
                    <i class="fas fa-map-marker-alt"></i> Locais
                </a>
                <a href="#" class="nav-item" onclick="switchSection('totens', this)">
                    <i class="fas fa-microchip"></i> Totens & Lockers
                </a>
                <a href="#" class="nav-item" onclick="switchSection('clientes', this)">
                    <i class="fas fa-users"></i> Clientes / Usuários
                </a>
                <a href="#" class="nav-item" onclick="switchSection('config', this)">
                    <i class="fas fa-cog"></i> Configurações
                </a>
                
                <div class="sidebar-bottom-links" style="margin-top: auto; padding: 1rem; border-top: 1px solid rgba(255,255,255,0.05);">
                    <a href="painel" class="nav-item" style="background: rgba(255,255,255,0.05); margin: 0.2rem 0;">
                        <i class="fas fa-external-link-alt"></i> Visualizar Painel
                    </a>
                    <a href="totem.html?direct=true" class="nav-item" style="background: rgba(255,255,255,0.05); margin: 0.2rem 0;">
                        <i class="fas fa-desktop"></i> Visualizar Totem
                    </a>
                    <a href="logout" class="nav-item" style="color: #ff5f5f !important; margin: 0.2rem 0;">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="header-left">
                    <button class="mobile-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <!-- Notification Button Mobile -->
                    <div style="position: relative; display: flex; align-items:center;">
                        <button class="notification-mobile" onclick="toggleNotifications()">
                            <i class="fas fa-bell"></i>
                        </button>

                        <!-- Dropdown -->
                        <div id="notif-dropdown" class="notification-dropdown">
                            <div class="notification-header">
                                <h4>Notificações</h4>
                                <span style="font-size: 0.7rem; color: var(--orange-vibrant); font-weight: 700;">3
                                    NOVAS</span>
                            </div>
                            <div class="notification-list">
                                <div class="notification-item">
                                    <i class="fas fa-wallet"></i>
                                    <div class="notification-item-content">
                                        <h5>Novo depósito detectado</h5>
                                        <p>O cliente <strong>Fernando Costa</strong> acabou de depositar no
                                            <strong>LOCKER 04</strong>.
                                        </p>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <i class="fas fa-box"></i>
                                    <div class="notification-item-content">
                                        <h5>Coleta solicitada</h5>
                                        <p><strong>Loja Central</strong> solicitou uma coleta para 14:00.</p>
                                    </div>
                                </div>
                                <div class="notification-item">
                                    <i class="fas fa-exclamation-triangle"></i>
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
                        <span class="user-greeting">Olá, <?= htmlspecialchars($userName) ?></span>
                        <span class="wallet-badge-compact">Nível: Super Admin</span>
                    </div>
                    <div class="user-avatar"><?= $initials ?></div>
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
                                <span id="stat-packages-today">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                                <i class="fas fa-map-pin"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Pontos Ativos</h4>
                                <span id="stat-active-points">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Status Totens</h4>
                                <span id="stat-totems-status">0% OK</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background: #faf5ff; color: #9333ea;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-info">
                                <h4>Receita Diária</h4>
                                <span id="stat-daily-revenue">R$ 0,00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Locais Section -->
                    <div class="section-title">Monitoramento de Locais</div>
                    <div class="admin-table-container" style="margin-bottom: 2rem;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="align-left-padding">Local / Unidade</th>
                                    <th class="align-left-padding">Tipo</th>
                                    <th class="align-left-padding">Totens</th>
                                    <th class="align-left-padding">Lockers Ocupados</th>
                                    <th class="td-status">Status</th>
                                </tr>
                            </thead>
                            <tbody id="dashboard-locations-table-body">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Recent Packages -->
                    <div class="section-title">Últimas Encomendas (Locker/Totem)</div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="align-left-padding">ID Encomenda</th>
                                    <th class="align-left-padding">Cliente</th>
                                    <th class="align-left-padding">Origem</th>
                                    <th class="align-left-padding">Destino</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dashboard-orders-table-body">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Encomendas -->
                <div id="section-encomendas" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Gestão Global de Encomendas</div>
                        <div style="display:flex; gap:1rem; align-items:center;">
                            <div class="search-input-wrapper" style="position:relative;">
                                <i class="fas fa-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.9rem;"></i>
                                <input type="text" id="order-search-input" placeholder="Buscar ID, Usuário ou Cidade..." 
                                    style="padding: 8px 12px 8px 35px; border: 1px solid #e2e8f0; border-radius: 8px; width: 280px; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
                                    oninput="filterOrders()">
                            </div>
                            <button class="btn-new-item" onclick="openModal('modal-encomenda')">
                                <i class="fas fa-plus"></i> NOVA ENCOMENDA
                            </button>
                        </div>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="select-all-orders" onchange="toggleAllOrders(this.checked)" class="order-checkbox"></th>
                                    <th>Data/Hora</th>
                                    <th>Usuário</th>
                                    <th>Agendamento</th>
                                    <th>Modal</th>
                                    <th>Peso</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="orders-table-body">
                                <!-- Dynamic orders loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Locais (Full View) -->
                <div id="section-locais" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Todos os Pontos de Coleta</div>
                        <button class="btn-new-item" onclick="openModal('modal-local')">
                            <i class="fas fa-plus"></i> NOVO LOCAL
                        </button>
                    </div>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(255,107,0,0.1);color:var(--orange-vibrant)"><i
                                    class="fas fa-shopping-cart"></i></div>
                            <div class="stat-info">
                                <h4>Shoppings</h4><span id="stat-shoppings">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(255,107,0,0.1);color:var(--orange-vibrant)"><i
                                    class="fas fa-home"></i></div>
                            <div class="stat-info">
                                <h4>Condomínios</h4><span id="stat-condominios">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon" style="background:rgba(255,107,0,0.1);color:var(--orange-vibrant)"><i
                                    class="fas fa-building"></i></div>
                            <div class="stat-info">
                                <h4>Empresas</h4><span id="stat-empresas">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="align-left-padding">Unidade</th>
                                    <th class="align-left-padding">Endereço</th>
                                    <th class="align-left-padding">Totens</th>
                                    <th class="align-left-padding">Ocupação</th>
                                    <th class="align-left-padding">Coleta Programada</th>
                                    <th>Coleta Express</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="locations-table-body">
                                <!-- Dynamic locations loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Totens -->
                <div id="section-totens" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Hardware & Conectividade</div>
                        <button class="btn-new-item" onclick="openNewTotemModal()">
                            <i class="fas fa-plus"></i> NOVO TOTEM / LOCKER
                        </button>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="align-left-padding">ID Totem</th>
                                    <th class="align-left-padding">Localização</th>
                                    <th class="align-left-padding">Tipo</th>
                                    <th class="align-left-padding">Lockers Ocupados</th>
                                    <th class="td-sinal">Sinal 5G</th>
                                    <th class="td-status">Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="totens-table-body">
                                <!-- Dynamic totens loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Clientes -->
                <div id="section-clientes" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Gestão de Usuários / Clientes</div>
                        <button class="btn-new-item" onclick="openModal('modal-cliente')">
                            <i class="fas fa-plus"></i> NOVO USUÁRIO
                        </button>
                    </div>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th class="align-left-padding">Nome</th>
                                    <th class="align-left-padding">Email</th>
                                    <th class="align-left-padding">Telefone</th>
                                    <th class="align-left-padding">Documento</th>
                                    <th class="align-left-padding">Saldo Atual</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                <!-- Dynamic users loaded here -->
                                <tr><td colspan="5" style="text-align:center; padding: 2rem;">Carregando usuários...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section: Config -->
                <div id="section-config" class="content-section">
                    <div class="section-title">Configurações do Sistema</div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4>API de Cálculo</h4>
                                <p>Status: Conectado (v2.4.1)</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-info">
                                <h4>Notificações</h4>
                                <p>E-mail: Ativo | WhatsApp: Ativo</p>
                            </div>
                        </div>
                    </div>

                    <!-- Horário de Funcionamento Refined -->
                    <div class="settings-card">
                        <div class="settings-group-title">
                            <i class="fas fa-clock-rotate-left" style="color: var(--orange-vibrant);"></i> 
                            Horário de Funcionamento & Feriados
                        </div>
                        
                        <form id="form-global-settings" onsubmit="saveGlobalSettings(event)">
                            <div class="settings-grid">
                                <div>
                                    <label style="display: block; font-weight: 700; margin-bottom: 1.2rem; color: #4a5568; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;">Dias de Operação</label>
                                    <div class="day-toggle-group">
                                        <?php 
                                        $days = ['seg' => 'S', 'ter' => 'T', 'qua' => 'Q', 'qui' => 'Q', 'sex' => 'S', 'sab' => 'S', 'dom' => 'D'];
                                        $fullDays = ['seg' => 'Segunda', 'ter' => 'Terça', 'qua' => 'Quarta', 'qui' => 'Quinta', 'sex' => 'Sexta', 'sab' => 'Sábado', 'dom' => 'Domingo'];
                                        foreach($days as $key => $label): ?>
                                            <div class="day-chip" title="<?= $fullDays[$key] ?>">
                                                <input type="checkbox" name="work_days[]" value="<?= $key ?>" id="day-<?= $key ?>">
                                                <label for="day-<?= $key ?>"><?= $label ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.7rem;">
                                        <div>
                                            <label style="display: block; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Horário de Abertura</label>
                                            <div class="time-input-wrapper">
                                                <i class="fas fa-sun"></i>
                                                <input type="time" id="open_time" name="open_time">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display: block; font-weight: 700; color: #4a5568; font-size: 0.9rem;">Horário de Fechamento</label>
                                            <div class="time-input-wrapper">
                                                <i class="fas fa-moon"></i>
                                                <input type="time" id="close_time" name="close_time">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label style="display: block;font-weight: 700;margin-bottom: 0.5rem;color: #4a5568;font-size: 0.9rem;text-transform: uppercase;letter-spacing: 0.5px;margin-top: 1.2rem;">Feriados & Bloqueios</label>
                                    <p style="font-size: 0.85rem; color: #718096; margin-bottom: 1.2rem;">As datas inseridas aqui ficarão indisponíveis para agendamento.</p>
                                    
                                    <textarea id="holidays" name="holidays" class="holiday-textarea" placeholder="Ex: 25/12, 01/01, 01/05, 07/09"></textarea>
                                    
                                    <div style="margin-top: 1rem; display: flex; align-items: center; gap: 8px; color: #a0aec0; font-size: 0.8rem;">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Separe as datas por vírgula no formato DD/MM.</span>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn-save-settings">
                                    <i class="fas fa-save"></i>
                                    SALVAR CONFIGURAÇÕES
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- AI & FAQ Settings -->
                    <div class="settings-card" style="margin-top: 2rem;">
                        <div class="settings-group-title">
                            <i class="fas fa-brain" style="color: var(--orange-vibrant);"></i> 
                            Inteligência Artificial & FAQ
                        </div>

                        <?php
                        $kbPath = __DIR__ . '/data/knowledge_base.txt';
                        $kbContent = file_exists($kbPath) ? file_get_contents($kbPath) : "";
                        
                        $faqPath = __DIR__ . '/data/faq.txt';
                        $faqContent = file_exists($faqPath) ? file_get_contents($faqPath) : "Como rastrear um pedido?|Basta acessar a aba Rastreio no seu painel.\nQual o prazo de entrega?|O prazo varia de acordo com a transportadora escolhida.";
                        ?>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <!-- Knowledge Base -->
                            <div>
                                <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; color: #4a5568; font-size: 0.9rem; text-transform: uppercase;">Cérebro da IA (Base de Conhecimento)</label>
                                <p style="font-size: 0.8rem; color: #718096; margin-bottom: 1rem;">O que a IA sabe sobre a empresa.</p>
                                <textarea id="ai-kb-content-config" class="holiday-textarea" style="min-height: 300px; font-family: monospace;"><?php echo htmlspecialchars($kbContent); ?></textarea>
                            </div>

                            <!-- FAQ List -->
                            <div>
                                <label style="display: block; font-weight: 700; margin-bottom: 0.5rem; color: #4a5568; font-size: 0.9rem; text-transform: uppercase;">Perguntas Frequentes (Painel do Usuário)</label>
                                <p style="font-size: 0.8rem; color: #718096; margin-bottom: 1rem;">Adicione perguntas e respostas curtas que aparecerão como sugestões no chat.</p>
                                
                                <div id="faq-list-container" style="display: flex; flex-direction: column; gap: 1rem; max-height: 400px; overflow-y: auto; padding-right: 5px; margin-bottom: 1rem;">
                                    <!-- Dynamic items will be injected here -->
                                </div>

                                <button type="button" class="btn-new-item" style="width: 100%; justify-content: center; background: #e2e8f0; color: #4a5568; border: 1px dashed #cbd5e0;" onclick="addFaqItem('', '')">
                                    <i class="fas fa-plus"></i> ADICIONAR NOVA PERGUNTA
                                </button>
                                
                                <!-- Hidden textarea for backward compatibility with the save function if needed, or we just update the function -->
                                <textarea id="ai-faq-content" style="display:none;"><?php echo htmlspecialchars($faqContent); ?></textarea>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                            <button type="button" class="btn-save-settings" id="btn-save-ai-config" onclick="saveAiConfig()">
                                <i class="fas fa-robot"></i>
                                ATUALIZAR IA & FAQ
                            </button>
                        </div>
                    </div>


                <!-- Section: Produtos & Serviços -->
                <div id="section-servicos" class="content-section">
                    <div class="section-header-actions">
                        <div class="section-title" style="margin-bottom:0">Gestão de Produtos & Serviços</div>
                        <div style="display:flex; gap:0.5rem">
                            <button class="btn-new-item" onclick="openModal('modal-servico')">
                                <i class="fas fa-plus"></i> NOVO ITEM
                            </button>
                        </div>
                    </div>

                    <!-- Category Tabs -->
                    <div class="stats-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 1rem;">
                        <button class="stat-card active" onclick="filterServices('todos')" style="cursor:pointer">
                            <div class="stat-info">
                                <h4>Todos</h4>
                            </div>
                        </button>
                        <button class="stat-card" onclick="filterServices('empacotamento')" style="cursor:pointer">
                            <div class="stat-info">
                                <h4>Empacotamento</h4>
                            </div>
                        </button>
                        <button class="stat-card" onclick="filterServices('coleta')" style="cursor:pointer">
                            <div class="stat-info">
                                <h4>Coleta</h4>
                            </div>
                        </button>
                        <button class="stat-card" onclick="filterServices('fretes')" style="cursor:pointer">
                            <div class="stat-info">
                                <h4>Fretes/Extras</h4>
                            </div>
                        </button>
                    </div>

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tamanho</th>
                                    <th>Categoria</th>
                                    <th>Método</th>
                                    <th>Preço Final</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody id="services-table-body">
                                <!-- Empacotamento -->
                                <tr class="service-row" data-id="emb-01">
                                    <td data-label="Nome">Caixa Pequena</td>
                                    <td data-label="Categoria">Empacotamento</td>
                                    <td data-label="Método">Preço Fixo</td>
                                    <td data-label="Preço">R$ 6,90</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('emb-01')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="service-row" data-id="emb-04">
                                    <td data-label="Nome">CAIXA 1</td>
                                    <td data-label="Categoria">Empacotamento</td>
                                    <td data-label="Método">Preço Fixo</td>
                                    <td data-label="Preço">R$ 0,00</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('emb-04')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="service-row" data-id="emb-05">
                                    <td data-label="Nome">CAIXA 2</td>
                                    <td data-label="Categoria">Empacotamento</td>
                                    <td data-label="Método">Preço Fixo</td>
                                    <td data-label="Preço">R$ 0,00</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('emb-05')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="service-row" data-id="emb-06">
                                    <td data-label="Nome">CAIXA 3</td>
                                    <td data-label="Categoria">Empacotamento</td>
                                    <td data-label="Método">Preço Fixo</td>
                                    <td data-label="Preço">R$ 0,00</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('emb-06')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr class="service-row" data-id="emb-02">
                                    <td data-label="Nome">Sacola com Bolha</td>
                                    <td data-label="Categoria">Empacotamento</td>
                                    <td data-label="Método">Preço Fixo</td>
                                    <td data-label="Preço">R$ 4,50</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações"><button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"><i
                                                class="fas fa-edit"></i></button></td>
                                </tr>
                                <!-- Coletas -->
                                <tr class="service-row" data-id="col-01">
                                    <td data-label="Nome">Coleta Programada</td>
                                    <td data-label="Categoria">Coleta</td>
                                    <td data-label="Método">Por Faixa (KM)</td>
                                    <td data-label="Preço">R$ 8,00 - 18,00</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('col-01')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Fretes -->
                                <tr class="service-row" data-id="fre-01">
                                    <td data-label="Nome">Frete Correios (SEDEX)</td>
                                    <td data-label="Categoria">Fretes</td>
                                    <td data-label="Método">Markup (30%)</td>
                                    <td data-label="Preço">R$ 26,00</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('fre-01')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Extras -->
                                <tr class="service-row" data-id="ext-01">
                                    <td data-label="Nome">Seguro Adicional</td>
                                    <td data-label="Categoria">Fretes</td>
                                    <td data-label="Método">Markup (15%)</td>
                                    <td data-label="Preço">Variável</td>
                                    <td data-label="Status"><span class="status-badge status-deposited">Ativo</span>
                                    </td>
                                    <td data-label="Ações">
                                        <button class="close-modal"
                                            style="display:inline-flex; width:30px; height:30px; margin:0"
                                            onclick="editService('ext-01')">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alert Overlay -->
    <div id="alert-overlay">
        <div class="realtime-alert">
            <div class="alert-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="alert-content">
                <h4>NOVO DEPÓSITO DETECTADO</h4>
                <p>O cliente <strong id="alert-client-name">Fernando Costa</strong> acabou de depositar no
                    <strong>LOCKER 04</strong> (Shopping Campo Grande).
                </p>
            </div>
        </div>
    </div>

    <div id="modal-edit-order" class="modal-overlay">
        <div class="modal-content" style="max-width: 950px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h3 id="modal-edit-order-title">Detalhes da Encomenda</h3>
                <button class="close-modal" onclick="closeModal('modal-edit-order')">&times;</button>
            </div>
            <form class="admin-form" id="form-edit-order" onsubmit="saveOrderEdit(event)">
                <input type="hidden" id="edit-order-id">
                
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label>Status</label>
                        <select id="edit-order-status" required>
                            <option value="pending">Pendente</option>
                            <option value="paid">Pago / Coletado</option>
                            <option value="transit">Em Trânsito</option>
                            <option value="delivered">Entregue</option>
                            <option value="failed">Falhou</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rastreio</label>
                        <input type="text" id="edit-order-ref" readonly style="background: #f1f5f9;">
                    </div>
                    <div class="form-group">
                        <label>Serviço / Modal</label>
                        <input type="text" id="edit-order-service" required>
                    </div>
                    <div class="form-group">
                        <label>Valor (R$)</label>
                        <input type="number" step="0.01" id="edit-order-value" required>
                    </div>
                </div>

                <div style="padding: 1rem; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                    <h5 style="margin-bottom: 1rem; font-size: 0.8rem; color: var(--navy-light); text-transform: uppercase;">Dados Completos de Entrega</h5>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px; font-weight: bold;">REMETENTE</p>
                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-sender-name" placeholder="Nome" style="font-size: 0.8rem; padding: 0.5rem;" title="Nome do Remetente">
                                <input type="text" id="edit-sender-doc" placeholder="CPF/CNPJ" style="font-size: 0.8rem; padding: 0.5rem;" title="Documento do Remetente">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-sender-cep" placeholder="CEP" style="font-size: 0.8rem; padding: 0.5rem;" title="CEP do Remetente">
                                <input type="text" id="edit-sender-street" placeholder="Rua / Logradouro" style="font-size: 0.8rem; padding: 0.5rem;" title="Rua do Remetente">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-sender-number" placeholder="Número" style="font-size: 0.8rem; padding: 0.5rem;" title="Número">
                                <input type="text" id="edit-sender-comp" placeholder="Complemento" style="font-size: 0.8rem; padding: 0.5rem;" title="Complemento">
                                <input type="text" id="edit-sender-neighborhood" placeholder="Bairro" style="font-size: 0.8rem; padding: 0.5rem;" title="Bairro">
                            </div>
                            <input type="text" id="edit-sender-city" placeholder="Cidade-UF" style="font-size: 0.8rem; padding: 0.5rem; margin-bottom: 5px;" title="Cidade e Estado">
                        </div>
                        <div>
                            <p style="font-size: 0.75rem; color: #64748b; margin-bottom: 4px; font-weight: bold;">DESTINATÁRIO</p>
                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-receiver-name" placeholder="Nome" style="font-size: 0.8rem; padding: 0.5rem;" title="Nome do Destinatário">
                                <input type="text" id="edit-receiver-doc" placeholder="CPF/CNPJ" style="font-size: 0.8rem; padding: 0.5rem;" title="Documento do Destinatário">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-receiver-cep" placeholder="CEP" style="font-size: 0.8rem; padding: 0.5rem;" title="CEP do Destinatário">
                                <input type="text" id="edit-receiver-street" placeholder="Rua / Logradouro" style="font-size: 0.8rem; padding: 0.5rem;" title="Rua do Destinatário">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-receiver-number" placeholder="Número" style="font-size: 0.8rem; padding: 0.5rem;" title="Número">
                                <input type="text" id="edit-receiver-comp" placeholder="Complemento" style="font-size: 0.8rem; padding: 0.5rem;" title="Complemento">
                                <input type="text" id="edit-receiver-neighborhood" placeholder="Bairro" style="font-size: 0.8rem; padding: 0.5rem;" title="Bairro">
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 5px; margin-bottom: 5px;">
                                <input type="text" id="edit-receiver-city" placeholder="Cidade-UF" style="font-size: 0.8rem; padding: 0.5rem;" title="Cidade e Estado">
                                <input type="text" id="edit-receiver-contact" placeholder="Contato (Email/Tel)" style="font-size: 0.8rem; padding: 0.5rem;" title="Contato do Destinatário">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-edit-order')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulk-bar" class="bulk-actions-bar">
        <div class="selection-info">
            <span class="selection-count" id="selected-count">0</span> itens selecionados
        </div>
        <button class="btn-bulk-delete" onclick="bulkDeleteOrders()">
            <i class="fas fa-trash"></i> EXCLUIR SELECIONADOS
        </button>
        <button class="close-modal" style="background:none; color:white; font-size: 1.5rem;" onclick="toggleAllOrders(false); document.getElementById('select-all-orders').checked = false;">
            &times;
        </button>
    </div>
    <div id="modal-encomenda" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nova Encomenda</h3><button class="close-modal"
                    onclick="closeModal('modal-encomenda')">&times;</button>
            </div>
            <form class="admin-form" id="form-new-order" onsubmit="addOrder(event)">
                <div class="form-group">
                    <label>Cliente</label>
                    <input type="text" id="order-client" placeholder="Nome do cliente" required>
                </div>
                <div class="form-group">
                    <label>Código de Rastreio (Referência)</label>
                    <input type="text" id="order-ref" placeholder="EX-0000-BR" required>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Serviço / Modal</label>
                        <input type="text" id="order-service" placeholder="Ex: SEDEX" required>
                    </div>
                    <div class="form-group">
                        <label>Valor Total (R$)</label>
                        <input type="number" step="0.01" id="order-value" placeholder="0.00" required>
                    </div>
                </div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-encomenda')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">CADASTRAR ENCOMENDA</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-cliente" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Novo Usuário Manual</h3><button class="close-modal" onclick="closeModal('modal-cliente')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="adminAddUser(event)">
                <div class="form-group"><label>Nome Completo</label><input type="text" id="admin-user-name" placeholder="Ex: João Silva" required></div>
                <div class="form-group"><label>Email</label><input type="email" id="admin-user-email" placeholder="email@exemplo.com" required></div>
                <div class="form-group"><label>Telefone / WhatsApp</label><input type="text" id="admin-user-phone" placeholder="(xx) x xxx-xxxx" oninput="maskPhone(this)" inputmode="numeric" maxlength="16"></div>
                <div class="form-group"><label>CPF/CNPJ (Opcional)</label><input type="text" id="admin-user-cpf" placeholder="xxx.xxx.xxx-xx" oninput="maskCPF(this)" inputmode="numeric" maxlength="14"></div>
                <div class="form-group">
                    <label>Senha</label>
                    <div style="position:relative; display:flex; align-items:center;">
                        <input type="password" id="admin-user-password" placeholder="Definir senha do usuário" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePasswordVisibility('admin-user-password', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 5px;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group"><label>Saldo Inicial (R$)</label><input type="number" step="0.01" id="admin-user-balance" value="0.00"></div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-cliente')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">CADASTRAR USUÁRIO</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Saldo -->
    <div id="modal-balance" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Gerenciar Saldo</h3><button class="close-modal" onclick="closeModal('modal-balance')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="updateBalance(event)">
                <input type="hidden" id="balance-user-id">
                <div class="form-group">
                    <label>Ação</label>
                    <select id="balance-type">
                        <option value="add">Adicionar ao Saldo</option>
                        <option value="set">Definir Saldo Total</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Valor (R$)</label>
                    <input type="number" step="0.01" id="balance-amount" placeholder="0.00" required>
                </div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-balance')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">ATUALIZAR SALDO</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal: Editar Usuário -->
    <div id="modal-edit-user" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Usuário</h3><button class="close-modal" onclick="closeModal('modal-edit-user')">&times;</button>
            </div>
            <form class="admin-form" id="edit-user-form" onsubmit="updateUser(event)">
                <input type="hidden" id="edit-user-id">
                <div class="form-group">
                    <label>Nome do Usuário</label>
                    <input type="text" id="edit-user-name" placeholder="Ex: João Silva" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="edit-user-email" required>
                </div>
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label>Telefone</label>
                        <input type="text" id="edit-user-phone" placeholder="(xx) x xxx-xxxx" oninput="maskPhone(this)" inputmode="numeric" maxlength="16">
                    </div>
                    <div>
                        <label>CPF/CNPJ</label>
                        <input type="text" id="edit-user-cpf" placeholder="xxx.xxx.xxx-xx" oninput="maskCPF(this)" inputmode="numeric" maxlength="14">
                    </div>
                </div>
                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label>Saldo (R$)</label>
                        <input type="number" step="0.01" id="edit-user-balance" required>
                    </div>
                    <div>
                        <label>Nível de Acesso (Permissão)</label>
                        <select id="edit-user-role" required>
                            <option value="user">Usuário Comum</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Nova Senha (deixe em branco para não alterar)</label>
                    <div style="position:relative; display:flex; align-items:center;">
                        <input type="password" id="edit-user-password" placeholder="Nova senha" style="padding-right: 40px;">
                        <button type="button" onclick="togglePasswordVisibility('edit-user-password', this)" style="position: absolute; right: 10px; background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 5px;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-edit-user')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-local" class="modal-overlay">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="modal-local-title">Novo Ponto de Coleta</h3>
                <button class="close-modal" onclick="closeModal('modal-local')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="saveLocation(event)">
                <input type="hidden" id="location-id">
                <div class="form-group">
                    <label>Nome da Unidade</label>
                    <input type="text" id="loc-name" placeholder="Ex: Shopping Campo Grande" required>
                </div>
                <div class="form-group">
                    <label>Endereço / Cidade</label>
                    <input type="text" id="loc-address" placeholder="Av. Afonso Pena, 123 - Campo Grande" required>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>Tipo de Unidade</label>
                        <select id="loc-type" required>
                            <option value="Shopping">Shopping</option>
                            <option value="Condominio">Condomínio</option>
                            <option value="Empresa">Empresa</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Coleta Programada</label>
                        <input type="text" id="loc-sched" placeholder="Seg a Sex às 17h" required>
                    </div>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-bolt"></i> Coleta Express</label>
                    <input type="text" id="loc-express" placeholder="Em até 45 min" required>
                </div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-local')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">SALVAR UNIDADE</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-toten" class="modal-overlay">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="modal-toten-title">Novo Totem / Locker</h3><button class="close-modal" onclick="closeModal('modal-toten')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="saveTotem(event)">
                <input type="hidden" id="totem-id">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Identificador (Ex: T-0428)</label>
                        <input type="text" id="totem-label" placeholder="T-0000" required>
                    </div>
                    <div class="form-group">
                        <label>Localização</label>
                        <select id="totem-location-id" required>
                            <option value="">Selecionar Unidade...</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>ID Hardware</label>
                        <input type="text" id="totem-hardware" placeholder="HW-ID" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Unidade</label>
                        <select id="totem-type">
                            <option value="Totem">Apenas Totem</option>
                            <option value="Locker">Locker Inteligente</option>
                            <option value="Hibrido">Totem + Locker</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Capacidade (Gavetas)</label>
                        <input type="number" id="totem-capacity" min="1" placeholder="10" value="10" required title="Quantidade total de slots/gavetas">
                    </div>
                </div>
                <div class="form-group">
                    <label>Status Operacional</label>
                    <select id="totem-status">
                        <option value="online">Online / Operacional</option>
                        <option value="manutencao">Em Manutenção</option>
                        <option value="offline">Sinal Offline</option>
                    </select>
                </div>
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-toten')">CANCELAR</button>
                    <button type="submit" class="btn-confirm" id="btn-save-totem">ADICIONAR</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modal-export" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Exportar</h3><button class="close-modal" onclick="closeModal('modal-export')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="handleExportSubmit(event)">
                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-export')">FECHAR</button>
                    <button type="submit" class="btn-confirm">GERAR E BAIXAR</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar/Novo Serviço -->
    <div id="modal-servico" class="modal-overlay">
        <div class="modal-content" style="max-width: 650px;">
            <div class="modal-header">
                <h3 id="modal-servico-title">Novo Serviço</h3>
                <button class="close-modal" onclick="closeModal('modal-servico')">&times;</button>
            </div>
            <form class="admin-form" onsubmit="saveService(event)">
                <input type="hidden" id="service-id">
                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Nome do Serviço</label>
                        <input type="text" id="service-name" placeholder="Ex: Caixa Média" required>
                    </div>
                    <div class="form-group" id="group-service-size">
                        <label>Tamanho (Ex: 20x20x10)</label>
                        <input type="text" id="service-size" placeholder="Medidas">
                    </div>
                    <div class="form-group">
                        <label>Categoria</label>
                        <select id="service-category" onchange="toggleSizeField()">
                            <option value="empacotamento">Empacotamento</option>
                            <option value="coleta">Coleta</option>
                            <option value="fretes">Fretes/Extras</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Método de Precificação</label>
                    <div
                        style="display:flex; gap: 1rem; margin-top: 0.5rem; background: #f8fafc; padding: 1rem; border-radius: 10px;">
                        <label
                            style="display:flex; align-items:center; gap: 5px; cursor:pointer; text-transform:none; white-space:nowrap;">
                            <input type="radio" name="price-method" value="fixo" checked onchange="togglePriceFields()">
                            Fixo
                        </label>
                        <label
                            style="display:flex; align-items:center; gap: 5px; cursor:pointer; text-transform:none; white-space:nowrap;">
                            <input type="radio" name="price-method" value="markup" onchange="togglePriceFields()">
                            Markup %
                        </label>
                        <label
                            style="display:flex; align-items:center; gap: 5px; cursor:pointer; text-transform:none; white-space:nowrap;">
                            <input type="radio" name="price-method" value="faixa" onchange="togglePriceFields()"> Por
                            Faixa
                        </label>
                    </div>
                </div>

                <!-- Method: Fixed -->
                <div id="method-fixo" class="price-method-fields">
                    <div class="form-group">
                        <label>Preço Final (R$)</label>
                        <input type="number" step="0.01" id="price-fixed" placeholder="0,00">
                    </div>
                </div>

                <!-- Method: Markup -->
                <div id="method-markup" class="price-method-fields" style="display:none">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Custo Base (R$)</label>
                            <input type="number" step="0.01" id="price-base" placeholder="0,00"
                                oninput="updatePricePreview()">
                        </div>
                        <div class="form-group">
                            <label>Markup (%)</label>
                            <input type="number" id="price-markup" placeholder="30" oninput="updatePricePreview()">
                        </div>
                    </div>
                    <div
                        style="background: #fff7ed; padding: 0.75rem; border-radius: 8px; border: 1px dashed #ea580c; display:flex; justify-content:space-between; align-items:center">
                        <span style="font-size: 0.8rem; font-weight:700; color:#ea580c">PRÉVIA DO PREÇO FINAL</span>
                        <span id="price-preview" style="font-size: 1.1rem; font-weight:800; color:#ea580c">R$
                            0,00</span>
                    </div>
                </div>

                <!-- Method: Tiers (Simplified for now) -->
                <div id="method-faixa" class="price-method-fields" style="display:none">
                    <div class="form-group">
                        <label>Configuração de Faixas (Ex: KM ou Peso)</label>
                        <textarea id="price-tiers" placeholder="0-3km: 8.00&#10;3-8km: 12.00" rows="3"></textarea>
                    </div>
                </div>

                <div class="btn-action-group">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-servico')">CANCELAR</button>
                    <button type="submit" class="btn-confirm">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Tracking Info -->
    <div id="modal-tracking" class="modal-overlay">
        <div class="modal-content" style="max-width: 450px; text-align: center; padding: 2.5rem;">
            <button class="close-modal" onclick="closeModal('modal-tracking')">&times;</button>
            <div style="width: 70px; height: 70px; background: #fff7ed; color: #ea580c; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;">
                <i class="fas fa-truck"></i>
            </div>
            <h3 style="margin-bottom: 0.5rem; color: var(--navy-dark); font-weight: 800;">RASTREAMENTO</h3>
            <p id="track-modal-service" style="color: #718096; font-size: 0.9rem; margin-bottom: 1.5rem; font-weight: 600; text-transform: uppercase;"></p>
            
            <div style="background: #f8fafc; padding: 1.2rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
                <span style="display: block; font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">Código / Ref Externa</span>
                <strong id="track-modal-ref" style="font-size: 1.4rem; color: var(--navy-dark); letter-spacing: 1px;">---</strong>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                <button id="btn-rastrear-external" class="btn-confirm" style="width: 100%; margin: 0;">
                    <i class="fas fa-external-link-alt" style="margin-right: 8px;"></i> RASTREAR NO SITE
                </button>
                <button class="btn-cancel" onclick="closeModal('modal-tracking')" style="width: 100%; margin: 0;">FECHAR</button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Removed - All items in Burger Menu -->

    <script>
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            alert("Erro de Script: " + msg + "\nLinha: " + lineNo + "\nArquivo: " + url);
            return false;
        };


        let pricingData = {};
        let locationsData = {};
        let totensData = {};
        let ordersData = []; // Global orders data
        let currentServiceCategory = 'todos';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar) sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.style.display = 'flex';
        }
        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.style.display = 'none';
        }

        function toggleSizeField() {
            const cat = document.getElementById('service-category').value;
            const group = document.getElementById('group-service-size');
            if (group) {
                group.style.display = (cat === 'empacotamento') ? 'block' : 'none';
            }
        }

        function togglePriceFields() {
            const method = document.querySelector('input[name="price-method"]:checked').value;
            document.getElementById('method-fixo').style.display = (method === 'fixo') ? 'block' : 'none';
            document.getElementById('method-markup').style.display = (method === 'markup') ? 'block' : 'none';
            document.getElementById('method-faixa').style.display = (method === 'faixa') ? 'block' : 'none';
        }

        function updatePricePreview() {
            const base = parseFloat(document.getElementById('price-base').value) || 0;
            const markup = parseFloat(document.getElementById('price-markup').value) || 0;
            const final = base + (base * markup / 100);
            document.getElementById('price-preview').innerText = `R$ ${final.toFixed(2).replace('.', ',')}`;
        }

        function filterServices(category) {
            currentServiceCategory = category;
            
            // Update active state on buttons
            document.querySelectorAll('#section-servicos .stat-card').forEach(card => {
                card.classList.remove('active');
                if (card.getAttribute('onclick') === `filterServices('${category}')`) {
                    card.classList.add('active');
                }
            });

            renderServicesTable();
        }

        function renderServicesTable() {
            const tbody = document.getElementById('services-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            let filteredServices = Object.keys(pricingData).map(id => ({ id, ...pricingData[id] }));
            if (currentServiceCategory !== 'todos') {
                filteredServices = filteredServices.filter(s => s.cat === currentServiceCategory);
            }

            if (filteredServices.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 2rem;">Nenhum serviço encontrado nesta categoria.</td></tr>`;
                return;
            }

            filteredServices.forEach(s => {
                const tr = document.createElement('tr');
                tr.className = 'service-row';
                tr.setAttribute('data-id', s.id);
                
                let priceDisplay = 'R$ 0,00';
                if (s.method === 'fixo') {
                    priceDisplay = `R$ ${parseFloat(s.price).toFixed(2).replace('.', ',')}`;
                } else if (s.method === 'markup') {
                    priceDisplay = `Markup (${s.markup}%)`;
                } else if (s.method === 'faixa') {
                    priceDisplay = `Por Faixa`;
                }

                let catDisplay = s.cat.charAt(0).toUpperCase() + s.cat.slice(1);
                
                tr.innerHTML = `
                    <td class="align-left-padding" data-label="Nome"><strong>${s.name}</strong></td>
                    <td data-label="Tamanho">${s.size || '-'}</td>
                    <td data-label="Categoria">${catDisplay}</td>
                    <td data-label="Método">${s.method.charAt(0).toUpperCase() + s.method.slice(1)}</td>
                    <td data-label="Preço">${priceDisplay}</td>
                    <td data-label="Status">
                        <span class="status-badge ${s.status === 'active' ? 'status-deposited' : 'status-failed'}" onclick="toggleItemStatus('service', '${s.id}')">
                            ${s.status === 'active' ? 'Ativo' : 'Inativo'}
                        </span>
                    </td>
                    <td data-label="Ações">
                        <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center" onclick="editService('${s.id}')">
                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function editService(id) {
            const svc = pricingData[id];
            if (!svc) return;

            document.getElementById('modal-servico-title').innerText = 'Editar Serviço';
            document.getElementById('service-id').value = id;
            document.getElementById('service-name').value = svc.name;
            document.getElementById('service-size').value = svc.size || '';
            document.getElementById('service-category').value = svc.cat || 'empacotamento';
            
            toggleSizeField();

            document.querySelector(`input[name="price-method"][value="${svc.method}"]`).checked = true;
            togglePriceFields();

            if (svc.method === 'fixo') {
                document.getElementById('price-fixed').value = svc.price;
            } else if (svc.method === 'markup') {
                document.getElementById('price-base').value = svc.base;
                document.getElementById('price-markup').value = svc.markup;
                updatePricePreview();
            }

            openModal('modal-servico');
        }

        async function saveService(e) {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'SALVANDO...';
            btn.disabled = true;

            let id = document.getElementById('service-id').value;
            if (!id) id = 'srv-' + Date.now();

            const method = document.querySelector('input[name="price-method"]:checked').value;
            
            const newData = {
                id: id,
                name: document.getElementById('service-name').value,
                size: document.getElementById('service-size').value,
                cat: document.getElementById('service-category').value,
                method: method,
                price: (method === 'fixo') ? parseFloat(document.getElementById('price-fixed').value || 0) : 0,
                base: (method === 'markup') ? parseFloat(document.getElementById('price-base').value || 0) : 0,
                markup: (method === 'markup') ? parseFloat(document.getElementById('price-markup').value || 0) : 0,
                description: '',
                status: 'active'
            };

            try {
                const response = await fetch('endpoints/save_sync_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: 'service', item: newData })
                });
                const result = await response.json();
                
                if (result.ok) {
                    pricingData[id] = newData;
                    renderServicesTable();
                    closeModal('modal-servico');
                    alert('Serviço salvo com sucesso!');
                    
                    // Clear form for next use
                    form.reset();
                    document.getElementById('service-id').value = '';
                    document.getElementById('modal-servico-title').innerText = 'Novo Serviço';
                } else {
                    alert('Erro ao salvar: ' + (result.error || 'Erro desconhecido'));
                }
            } catch (err) {
                console.error("Save error:", err);
                alert('Erro na conexão com o servidor.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        async function initSyncData() {
            try {
                const response = await fetch('endpoints/get_sync_data.php?admin=1');
                const result = await response.json();
                if (result.ok) {
                    console.log("Sync data received:", result.data);
                    pricingData = {};
                    result.data.services.forEach(s => {
                        pricingData[s.id] = {
                            name: s.name,
                            size: s.size,
                            cat: s.category,
                            method: s.method,
                            price: parseFloat(s.price),
                            base: parseFloat(s.base_price),
                            markup: parseFloat(s.markup),
                            description: s.description,
                            status: s.status
                        };
                    });
                    
                    locationsData = {};
                    result.data.locations.forEach(l => {
                        locationsData[l.id] = {
                            name: l.name,
                            address: l.address,
                            sched: l.sched_info,
                            express: l.express_info,
                            type: l.type,
                            totens: l.totens,
                            occupancy: l.occupancy,
                            status: l.status
                        };
                    });

                    totensData = {};
                    result.data.lockers.forEach(t => {
                        totensData[t.id] = {
                            label: t.label,
                            locationId: t.location_id,
                            hardware: t.hardware_ref,
                            type: t.type,
                            status: t.status,
                            capacity: t.capacity,
                            occupiedCount: parseInt(t.occupied_count) || 0
                        };
                    });

                    renderTotensTable();
                    renderLocationsTable();
                    renderServicesTable(); // Ensure services are rendered
                    populateTotemLocationSelect();
                    loadGlobalSettings(result.data.settings);
                    updateDashboardUI();
                } else {
                    console.error("Sync error:", result.error);
                    alert("Erro na sincronização: " + result.error);
                }
            } catch (err) {
                console.error("Erro ao sincronizar dados:", err);
                alert("Erro crítico na sincronização: " + err.message + ". Verifique conexão com endpoints/get_sync_data.php");
            }
        }

        function loadGlobalSettings(settings) {
            if (!settings) return;
            
            // Operating Hours
            if (settings.open_time) document.getElementById('open_time').value = settings.open_time;
            if (settings.close_time) document.getElementById('close_time').value = settings.close_time;
            
            // Work Days
            if (settings.work_days) {
                const days = settings.work_days.split(',');
                document.querySelectorAll('input[name="work_days[]"]').forEach(cb => {
                    cb.checked = days.includes(cb.value);
                });
            }
            
            // Holidays
            if (settings.holidays) document.getElementById('holidays').value = settings.holidays;
        }

        async function saveGlobalSettings(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-settings');
            const originalText = btn.innerHTML;
            
            try {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SALVANDO...';
                btn.disabled = true;
                
                const workDays = Array.from(document.querySelectorAll('input[name="work_days[]"]:checked')).map(cb => cb.value);
                const settings = {
                    work_days: workDays.join(','),
                    open_time: document.getElementById('open_time').value,
                    close_time: document.getElementById('close_time').value,
                    holidays: document.getElementById('holidays').value
                };
                
                const response = await fetch('endpoints/save_settings.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ settings })
                });
                
                const result = await response.json();
                if (result.ok) {
                    alert('Configurações salvas com sucesso!');
                } else {
                    alert('Erro ao salvar: ' + (result.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                console.error("Save settings error:", err);
                alert('Erro na conexão com o servidor.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function switchSection(sectionId, element) {
            document.querySelectorAll('.nav-item, .mobile-nav-item').forEach(item => item.classList.remove('active'));
            if (element) {
                element.classList.add('active');
                const companion = document.querySelector(`.nav-item[onclick*="'${sectionId}'"], .mobile-nav-item[onclick*="'${sectionId}'"]`);
                if (companion) companion.classList.add('active');
            }
            document.querySelectorAll('.content-section').forEach(section => section.classList.remove('active'));
            const targetSection = document.getElementById('section-' + sectionId);
            if (targetSection) targetSection.classList.add('active');

            const titles = {
                'dashboard': 'Visão Geral do Sistema',
                'encomendas': 'Gestão de Encomendas',
                'locais': 'Monitoramento de Unidades',
                'totens': 'Hardware & Conectividade',
                'clientes': 'Base de Clientes',
                'servicos': 'Gestão de Produtos & Serviços',
                'config': 'Configurações Globais',
                'ai-kb': 'Base de Conhecimento Inteligente'
            };

            const titleElement = document.querySelector('.top-header h2');
            if (titleElement) titleElement.innerText = titles[sectionId] || 'Admin';

            if (sectionId === 'servicos') renderServicesTable();

            if (window.innerWidth < 992) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar && sidebar.classList.contains('active')) toggleSidebar();
            }
        }



        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            if (!dropdown) return;
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';

            if (dropdown.style.display === 'block') {
                setTimeout(() => {
                    const closeHandler = function (e) {
                        if (!e.target.closest('.notification-dropdown') && !e.target.closest('.notification-mobile')) {
                            dropdown.style.display = 'none';
                            window.removeEventListener('click', closeHandler);
                        }
                    };
                    window.addEventListener('click', closeHandler);
                }, 10);
            }
        }

        function handleFormSubmit(e, modalId) {
            e.preventDefault();
            alert('Dados salvos com sucesso!');
            closeModal(modalId);
        }

        async function fetchOrders() {
            try {
                const response = await fetch('endpoints/get_orders.php');
                const result = await response.json();
                if (result.ok) {
                    ordersData = result.data; // Save to global
                    renderOrdersTable(result.data);
                    updateDashboardUI();
                }
            } catch (error) {
                console.error('Erro ao buscar encomendas:', error);
                alert('Erro ao buscar encomendas. Verifique endpoints/get_orders.php');
            }
        }

        async function addOrder(e) {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> CADASTRANDO...';

            const payload = {
                client_name: document.getElementById('order-client').value,
                external_ref: document.getElementById('order-ref').value,
                service: document.getElementById('order-service').value,
                total_value: document.getElementById('order-value').value
            };

            try {
                const response = await fetch('endpoints/admin_add_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    closeModal('modal-encomenda');
                    fetchOrders();
                    alert('Encomenda cadastrada com sucesso!');
                    e.target.reset();
                } else {
                    alert('Erro: ' + (result.error || 'Erro ao processar'));
                }
            } catch (error) {
                alert('Erro na conexão com o servidor.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'CADASTRAR ENCOMENDA';
            }
        }

        function showTracking(orderId) {
            const order = ordersData.find(o => o.id == orderId);
            if (!order) return;
            
            document.getElementById('track-modal-ref').innerText = order.external_ref || '---';
            document.getElementById('track-modal-service').innerText = order.service || 'ENCOMENDA';
            
            const btnRastrear = document.getElementById('btn-rastrear-external');
            if (order.external_ref && order.external_ref.length > 5) {
                let url = '#';
                const service = (order.service || '').toUpperCase();
                if (service.includes('CORREIOS') || service.includes('PAC') || service.includes('SEDEX')) {
                    url = `https://rastreamento.correios.com.br/app/index.php?objeto=${order.external_ref}`;
                } else if (service.includes('TOTAL') || service.includes('TEX')) {
                    url = `https://www.totalexpress.com.br/atendimento/rastreio-de-encomenda/`;
                }
                btnRastrear.onclick = () => window.open(url, '_blank');
                btnRastrear.style.display = 'block';
            } else {
                btnRastrear.style.display = 'none';
            }
            
            openModal('modal-tracking');
        }


        function editOrder(orderId) {
            fetch(`endpoints/get_orders.php`)
                .then(res => res.json())
                .then(result => {
                    const order = result.data.find(o => o.id == orderId);
                    if (!order) return;
                    
                    document.getElementById('edit-order-id').value = order.id;
                    document.getElementById('edit-order-status').value = order.status || 'pending';
                    document.getElementById('edit-order-ref').value = order.external_ref;
                    document.getElementById('edit-order-service').value = order.service;
                    document.getElementById('edit-order-value').value = order.total_value;
                    
                    document.getElementById('edit-sender-name').value = order.sender_name || '';
                    document.getElementById('edit-sender-doc').value = order.sender_doc || '';
                    document.getElementById('edit-sender-cep').value = order.sender_cep || '';
                    document.getElementById('edit-sender-street').value = order.sender_street || '';
                    document.getElementById('edit-sender-number').value = order.sender_number || '';
                    document.getElementById('edit-sender-comp').value = order.sender_complement || '';
                    document.getElementById('edit-sender-neighborhood').value = order.sender_neighborhood || '';
                    document.getElementById('edit-sender-city').value = order.sender_city_uf || '';

                    document.getElementById('edit-receiver-name').value = order.receiver_name || '';
                    document.getElementById('edit-receiver-doc').value = order.receiver_doc || '';
                    document.getElementById('edit-receiver-cep').value = order.receiver_cep || '';
                    document.getElementById('edit-receiver-street').value = order.receiver_street || '';
                    document.getElementById('edit-receiver-number').value = order.receiver_number || '';
                    document.getElementById('edit-receiver-comp').value = order.receiver_complement || '';
                    document.getElementById('edit-receiver-neighborhood').value = order.receiver_neighborhood || '';
                    document.getElementById('edit-receiver-city').value = order.receiver_city_uf || '';
                    document.getElementById('edit-receiver-contact').value = order.receiver_contact || '';
                    
                    openModal('modal-edit-order');
                });
        }

        async function saveOrderEdit(e) {
            e.preventDefault();
            const btn = e.target.querySelector('.btn-confirm');
            const originalText = btn.innerText;
            btn.innerText = 'SALVANDO...';
            btn.disabled = true;

            const payload = {
                order_id: document.getElementById('edit-order-id').value,
                status: document.getElementById('edit-order-status').value,
                service: document.getElementById('edit-order-service').value,
                total_value: document.getElementById('edit-order-value').value,
                sender_name: document.getElementById('edit-sender-name').value,
                sender_doc: document.getElementById('edit-sender-doc').value,
                sender_cep: document.getElementById('edit-sender-cep').value,
                sender_street: document.getElementById('edit-sender-street').value,
                sender_number: document.getElementById('edit-sender-number').value,
                sender_complement: document.getElementById('edit-sender-comp').value,
                sender_neighborhood: document.getElementById('edit-sender-neighborhood').value,
                sender_city_uf: document.getElementById('edit-sender-city').value,

                receiver_name: document.getElementById('edit-receiver-name').value,
                receiver_doc: document.getElementById('edit-receiver-doc').value,
                receiver_cep: document.getElementById('edit-receiver-cep').value,
                receiver_street: document.getElementById('edit-receiver-street').value,
                receiver_number: document.getElementById('edit-receiver-number').value,
                receiver_complement: document.getElementById('edit-receiver-comp').value,
                receiver_neighborhood: document.getElementById('edit-receiver-neighborhood').value,
                receiver_city_uf: document.getElementById('edit-receiver-city').value,
                receiver_contact: document.getElementById('edit-receiver-contact').value
            };

            try {
                const response = await fetch('endpoints/admin_update_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    closeModal('modal-edit-order');
                    fetchOrders();
                    alert('Encomenda atualizada com sucesso!');
                } else {
                    alert('Erro ao salvar: ' + result.error);
                }
            } catch (err) {
                alert('Erro na conexão.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        function toggleAllOrders(checked) {
            const checkboxes = document.querySelectorAll('.order-item-checkbox');
            checkboxes.forEach(cb => cb.checked = checked);
            updateBulkActionBar();
        }

        function updateBulkActionBar() {
            const checked = document.querySelectorAll('.order-item-checkbox:checked');
            const bar = document.getElementById('bulk-bar');
            const count = document.getElementById('selected-count');
            
            if (checked.length > 0) {
                bar.classList.add('active');
                count.innerText = checked.length;
            } else {
                bar.classList.remove('active');
            }
        }

        async function bulkDeleteOrders() {
            const checked = document.querySelectorAll('.order-item-checkbox:checked');
            if (checked.length === 0) return;

            if (!confirm(`Tem certeza que deseja excluir ${checked.length} encomendas selecionadas? Esta ação não pode ser desfeita.`)) return;

            const ids = Array.from(checked).map(cb => cb.value);

            try {
                const response = await fetch('endpoints/admin_bulk_action.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'delete', type: 'order', ids: ids })
                });
                const result = await response.json();
                if (result.ok) {
                    fetchOrders();
                    alert(result.message);
                    document.getElementById('select-all-orders').checked = false;
                } else {
                    alert('Erro: ' + result.error);
                }
            } catch (err) {
                alert('Erro na conexão.');
            }
        }

        async function quickUpdateStatus(orderId, newStatus) {
            try {
                const response = await fetch('endpoints/admin_update_order.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: newStatus })
                });
                const result = await response.json();
                if (result.ok) {
                    fetchOrders();
                } else {
                    alert('Erro ao atualizar status: ' + result.error);
                }
            } catch (err) {
                alert('Erro na conexão.');
            }
        }

        function toggleStatusMenu(e) {
            e.stopPropagation();
            const badge = e.currentTarget;
            const isOpen = badge.classList.contains('open');
            
            // Close all other menus
            document.querySelectorAll('.status-badge.open').forEach(b => b.classList.remove('open'));
            
            if (!isOpen) {
                // Always open down as requested
                badge.classList.add('open');

                const closeHandler = function() {
                    badge.classList.remove('open');
                    window.removeEventListener('click', closeHandler);
                };
                setTimeout(() => window.addEventListener('click', closeHandler), 10);
            }
        }

        function filterOrders() {
            const query = document.getElementById('order-search-input').value.toLowerCase();
            const filtered = ordersData.filter(o => {
                const searchString = [
                    `EX-${String(o.id).padStart(5, '0')}-BR`,
                    o.user_linked_name || '',
                    o.sender_name || '',
                    o.customer_name || '',
                    o.receiver_city_uf || '',
                    o.external_ref || ''
                ].join(' ').toLowerCase();
                return searchString.includes(query);
            });
            renderOrdersTable(filtered);
        }

        function renderOrdersTable(orders) {
            const tbody = document.getElementById('orders-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            orders.forEach(order => {
                const row = document.createElement('tr');
                const d = new Date(order.created_at);
                const datePart = d.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' });
                const timePart = d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

                let statusClass = 'status-pending';
                let statusText = 'Pendente';

                if (order.status === 'paid') {
                    statusClass = 'status-deposited';
                    statusText = 'Pago / Coletado';
                } else if (order.status === 'transit') {
                    statusClass = 'status-transit';
                    statusText = 'Em Trânsito';
                } else if (order.status === 'delivered') {
                    statusClass = 'status-delivered';
                    statusText = 'Entregue';
                } else if (order.status === 'failed') {
                    statusClass = 'status-failed';
                    statusText = 'Falhou';
                }

                row.innerHTML = `
                    <td><input type="checkbox" value="${order.id}" class="order-checkbox order-item-checkbox" onchange="updateBulkActionBar()"></td>
                    <td data-label="Data/Hora" style="padding: 12px 15px; white-space: nowrap;">
                        <div style="font-weight:600; color:#1a202c;">${datePart}</div>
                        <div style="font-size:0.75rem; color:#718096; margin-top: 2px;">${timePart}</div>
                    </td>
                    <td data-label="Usuário" style="padding: 12px 15px;">
                        <div style="font-weight:600; color:#2d3748;">${order.user_linked_name || '-'}</div>
                        <div style="font-size:0.75rem; color:#718096;">${order.user_id ? 'ID: ' + order.user_id : 'Visitante'}</div>
                    </td>
                    <td data-label="Agendamento" style="padding: 12px 15px; white-space: nowrap;">
                        <div style="font-weight:600; color:#2d3748;">${order.scheduled_date ? order.scheduled_date.split('-').reverse().join('/') : '-'}</div>
                        <div style="font-size:0.75rem; color:#718096; margin-top: 2px;">${order.scheduled_time ? order.scheduled_time.substring(0,5) : ''}</div>
                    </td>
                <td data-label="Modal" style="padding: 15px 15px; min-width: 180px;">
                    <div style="font-weight:700; color:#1a202c; line-height: 1.3; font-size: 0.95rem;">${order.service || '-'}</div>
                    <div style="font-size:0.8rem; color:#718096; margin-top: 6px; font-weight: 400;">${order.modality || (order.necessity ? 'Necessidade: ' + order.necessity : '-')}</div>
                </td>
                <td data-label="Peso" style="white-space: nowrap; font-weight: 600; color: #4a5568;">${order.weight ? order.weight + ' kg' : '---'}</td>
                <td data-label="Valor" style="white-space: nowrap; font-weight: 800; color: #1a202c; font-size: 1.05rem;">R$ ${parseFloat(order.total_value).toFixed(2)}</td>
                    <td data-label="Status">
                        <div class="status-badge ${statusClass}" onclick="toggleStatusMenu(event)">
                            ${statusText}
                            <div class="status-menu">
                                <div class="status-option" onclick="quickUpdateStatus('${order.id}', 'pending')">PENDENTE</div>
                                <div class="status-option" onclick="quickUpdateStatus('${order.id}', 'paid')">PAGO / COLETADO</div>
                                <div class="status-option" onclick="quickUpdateStatus('${order.id}', 'transit')">EM TRÂNSITO</div>
                                <div class="status-option" onclick="quickUpdateStatus('${order.id}', 'delivered')">ENTREGUE</div>
                                <div class="status-option" onclick="quickUpdateStatus('${order.id}', 'failed')">FALHOU</div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Ações">
                        <div style="display:flex; gap: 0.4rem; justify-content:center;">
                            <button class="close-modal" style="display:inline-flex; width:34px; height:34px; margin:0; align-items:center; justify-content:center; background: #fff7ed; color: #ea580c; border: 1px solid #ffedd5" onclick="showTracking('${order.id}')" title="Rastreamento">
                                <i class="fas fa-truck" style="font-size: 0.85rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:34px; height:34px; margin:0; align-items:center; justify-content:center; background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0" onclick="editOrder('${order.id}')" title="Ver / Editar Detalhes">
                                <i class="fas fa-eye" style="font-size: 0.85rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:34px; height:34px; margin:0; align-items:center; justify-content:center; background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2" onclick="deleteItem('order', '${order.id}')" title="Excluir Encomenda">
                                <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function renderLocationsTable() {
            const tbody = document.getElementById('locations-table-body');
            const dashboardTbody = document.getElementById('dashboard-locations-table-body');
            if (tbody) tbody.innerHTML = '';
            if (dashboardTbody) dashboardTbody.innerHTML = '';

            Object.keys(locationsData).forEach(id => {
                const loc = locationsData[id];
                // Count totens and occupancy for this location
                const locLockers = Object.values(totensData).filter(t => t.locationId == id);
                const totensCount = locLockers.length;
                const totalCapacity = locLockers.reduce((sum, t) => sum + (parseInt(t.capacity) || 10), 0);
                const occupiedCount = locLockers.reduce((sum, t) => sum + (parseInt(t.occupiedCount) || 0), 0);
                const occupancyStr = `${occupiedCount}/${totalCapacity}`;

                // Detailed table row
                if (tbody) {
                    const row = document.createElement('tr');
                    
                    const statusText = (loc.status || 'online') === 'online' ? 'Ativo' : 'Inativo';
                    const statusColor = (loc.status || 'online') === 'online' ? '#16a34a' : '#dc2626';

                    const addressParts = (loc.address || '').split(' - ');
                    const addressMain = addressParts[0];
                    const addressCity = addressParts.length > 1 ? addressParts[1] : '';
                    const addressHTML = `<strong>${addressMain}</strong>${addressCity ? `<div style="font-size:0.75rem; color:#718096; margin-top:2px;">${addressCity}</div>` : ''}`;

                    row.innerHTML = `
                        <td class="align-left-padding" data-label="Unidade"><strong>${loc.name}</strong></td>
                        <td class="align-left-padding" data-label="Endereço">${addressHTML}</td>
                        <td class="align-left-padding" data-label="Totens"><strong>${totensCount.toString().padStart(2, '0')}</strong></td>
                        <td class="align-left-padding" data-label="Ocupação"><strong>${occupancyStr}</strong></td>
                        <td class="align-left-padding" data-label="Coleta Programada">${loc.sched}</td>
                        <td data-label="Coleta Express" style="color: var(--text-muted); font-size: 0.9rem;">${loc.express}</td>
                        <td data-label="Status">
                            <div class="location-dot-status" onclick="toggleItemStatus('location', '${id}')">
                                <span class="location-dot" style="background:${statusColor}"></span> ${statusText}
                            </div>
                        </td>
                        <td data-label="Ações">
                            <div style="display:flex; gap:0.5rem; justify-content:center">
                                <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center" onclick="editLocation('${id}')">
                                    <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                </button>
                                <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center; background: #fff5f5; color: #e53e3e; border-color: #feb2b2" onclick="deleteItem('location', '${id}')">
                                    <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                }

                // Dashboard condensed row
                if (dashboardTbody) {
                    const dRow = document.createElement('tr');
                    
                    const statusText = (loc.status || 'online') === 'online' ? 'Online' : 'Offline';
                    const statusColor = (loc.status || 'online') === 'online' ? '#16a34a' : '#dc2626';

                    dRow.innerHTML = `
                        <td class="align-left-padding" data-label="Unidade"><strong>${loc.name}</strong></td>
                        <td class="align-left-padding" data-label="Tipo">${loc.type || 'Unidade'}</td>
                        <td class="align-left-padding" data-label="Totens">${totensCount.toString().padStart(2, '0')}</td>
                        <td class="align-left-padding" data-label="Ocupação">${occupancyStr}</td>
                        <td data-label="Status">
                            <div class="location-dot-status" onclick="toggleItemStatus('location', '${id}')">
                                <span class="location-dot" style="background:${statusColor}"></span> ${statusText}
                            </div>
                        </td>
                    `;
                    dashboardTbody.appendChild(dRow);
                }
            });
            updateDashboardUI();
        }

        function updateDashboardUI() {
            if (!ordersData) return;
            
            const now = new Date();
            const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
            
            // Stats calculation
            const todayOrders = ordersData.filter(o => o.created_at && o.created_at.startsWith(todayStr));
            const packagesToday = todayOrders.length;
            
            const locations = Object.values(locationsData);
            const activePoints = locations.filter(l => l.status === 'online' || l.status === 'ativo').length;
            
            const totens = Object.values(totensData);
            const onlineTotens = totens.filter(t => t.status === 'online').length;
            const totemsStatus = totens.length > 0 ? Math.round((onlineTotens / totens.length) * 100) : 100;
            
            const dailyRevenue = todayOrders.reduce((sum, o) => sum + parseFloat(o.total_value || 0), 0);

            // Update Stats UI
            const elPackages = document.getElementById('stat-packages-today');
            const elPoints = document.getElementById('stat-active-points');
            const elStatus = document.getElementById('stat-totems-status');
            const elRevenue = document.getElementById('stat-daily-revenue');

            if (elPackages) elPackages.innerText = packagesToday;
            if (elPoints) elPoints.innerText = activePoints;
            if (elStatus) elStatus.innerText = totemsStatus + '% OK';
            if (elRevenue) elRevenue.innerText = 'R$ ' + dailyRevenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

            // New stats for locations section
            const countByType = {
                shopping: 0,
                condominio: 0,
                empresa: 0
            };
            
            locations.forEach(l => {
                const type = (l.type || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                if (type.includes('shopping')) countByType.shopping++;
                else if (type.includes('condominio')) countByType.condominio++;
                else if (type.includes('empresa')) countByType.empresa++;
            });

            const elShoppings = document.getElementById('stat-shoppings');
            const elCondos = document.getElementById('stat-condominios');
            const elEmpresas = document.getElementById('stat-empresas');

            if (elShoppings) elShoppings.innerText = countByType.shopping;
            if (elCondos) elCondos.innerText = countByType.condominio;
            if (elEmpresas) elEmpresas.innerText = countByType.empresa;

            // Dashboard Recent Orders Table
            const dashboardOrdersTbody = document.getElementById('dashboard-orders-table-body');
            if (dashboardOrdersTbody) {
                dashboardOrdersTbody.innerHTML = '';
                const recentOrders = ordersData.slice(0, 5); // Show last 5
                
                if (recentOrders.length === 0) {
                    dashboardOrdersTbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 1.5rem; color: #718096;">Nenhuma encomenda recente.</td></tr>';
                }

                recentOrders.forEach(order => {
                    let statusClass = 'status-pending';
                    let statusText = 'Pendente';

                    if (order.status === 'paid') {
                        statusClass = 'status-deposited';
                        statusText = 'Pago / Coletado';
                    } else if (order.status === 'transit') {
                        statusClass = 'status-transit';
                        statusText = 'Em Trânsito';
                    } else if (order.status === 'delivered') {
                        statusClass = 'status-delivered';
                        statusText = 'Entregue';
                    } else if (order.status === 'failed') {
                        statusClass = 'status-failed';
                        statusText = 'Falhou';
                    }

                    const row = document.createElement('tr');
                    
                    // Format ID: EX-00067-BR
                    const displayId = `EX-${String(order.id).padStart(5, '0')}-BR`;
                    
                    // Map Origin: Locker ID -> Location Name or Label
                    let origin = 'Painel';
                    if (order.locker_id) {
                        const totem = totensData[order.locker_id];
                        if (totem) {
                            const loc = locationsData[totem.locationId];
                            origin = loc ? loc.name : totem.label;
                        } else {
                            origin = order.locker_id;
                        }
                    }

                    const destination = order.receiver_city_uf || 'Não inf.';
                    row.innerHTML = `
                        <td class="align-left-padding" data-label="ID"><strong>${displayId}</strong></td>
                        <td class="align-left-padding" data-label="Cliente">
                            <div style="font-weight:600;">${order.user_linked_name || order.sender_name || 'Usuário'}</div>
                            <div style="font-size:0.7rem; color:#718096;">${order.user_id ? 'Conta Vinculada' : 'Sem Conta'}</div>
                        </td>
                        <td class="align-left-padding" data-label="Origem">${origin}</td>
                        <td class="align-left-padding" data-label="Destino">${destination}</td>
                        <td data-label="Status"><span class="status-badge ${statusClass}">${statusText}</span></td>
                    `;
                    dashboardOrdersTbody.appendChild(row);
                });
            }
        }

        function renderTotensTable() {
            const tbody = document.getElementById('totens-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            Object.keys(totensData).forEach(id => {
                const t = totensData[id];
                const loc = locationsData[t.locationId] || { name: 'Não Vinculado' };
                
                let statusBadge = '<span class="status-badge status-deposited">ONLINE</span>';
                let signalIcon = '<i class="fas fa-signal" style="color:#16a34a"></i> Forte';
                
                if (t.status === 'manutencao') {
                    statusBadge = '<span class="status-badge status-transit">MANUTENÇÃO</span>';
                    signalIcon = '<i class="fas fa-signal" style="color:#ea580c"></i> Médio';
                } else if (t.status === 'offline') {
                    statusBadge = '<span class="status-badge" style="background:#dc2626; color:white">OFFLINE</span>';
                    signalIcon = '<i class="fas fa-signal" style="color:#94a3b8"></i> Sem Sinal';
                }

                const row = document.createElement('tr');
                
                // Use occupied_count from server data if available, or recalc from ordersData
                const serverCount = t.occupiedCount;
                const occupants = ordersData.filter(o => o.locker_id == id && o.status !== 'delivered' && o.status !== 'failed');
                const occupiedCount = serverCount !== undefined ? serverCount : occupants.length;
                const capacity = t.capacity || 10; 
                
                const statusClass = (t.status || 'online') === 'online' ? 'status-deposited' : (t.status === 'manutencao' ? 'status-transit' : 'status-failed');
                const statusText = (t.status || 'online') === 'online' ? 'Online' : (t.status === 'manutencao' ? 'Manutenção' : 'Offline');

                row.innerHTML = `
                    <td class="align-left-padding" data-label="ID">${t.label}</td>
                    <td class="align-left-padding" data-label="Local">${loc.name}</td>
                    <td class="align-left-padding" data-label="Tipo">${t.type}</td>
                    <td class="align-left-padding" data-label="Lockers Ocupados"><strong>${occupiedCount}/${capacity}</strong></td>
                    <td class="td-sinal" data-label="Sinal">${signalIcon}</td>
                    <td class="td-status" data-label="Status">
                        <span class="status-badge ${statusClass}" onclick="toggleItemStatus('locker', '${id}')">${statusText}</span>
                    </td>
                    <td data-label="Ações">
                        <div style="display:flex; gap:0.5rem; justify-content:center; align-items:center">
                            <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center" onclick="editTotem('${id}')" title="Editar">
                                <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center; background: #fff5f5; color: #e53e3e; border-color: #feb2b2" onclick="deleteItem('locker', '${id}')" title="Excluir">
                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:30px; height:30px; margin:0; align-items:center; justify-content:center; background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd" onclick="toggleLockerOccupants('${id}', this)" title="Ver Ocupantes">
                                <i class="fas fa-chevron-down" style="font-size: 0.8rem; transition: transform 0.3s ease;"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);

                // Create a hidden detail row for occupants
                const detailsRow = document.createElement('tr');
                detailsRow.className = 'occupants-detail-row';
                detailsRow.id = `occupants-row-${id}`;
                detailsRow.style.display = 'none';
                
                let occupantsHTML = '';
                if (occupants.length === 0) {
                    occupantsHTML = '<div style="padding: 1rem; text-align: center; color: #718096; font-size: 0.9rem;">O locker está vazio no momento.</div>';
                } else {
                    occupantsHTML = '<div style="padding: 1rem; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; margin: 0.5rem 0; width: 100%; box-sizing: border-box;">';
                    occupantsHTML += '<h4 style="font-size: 0.85rem; color: #4a5568; margin-bottom: 0.8rem; text-transform: uppercase; font-weight: 700;">Detalhes de Ocupação</h4>';
                    occupantsHTML += '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">';
                    occupants.forEach(o => {
                        let statusColor = '#3182ce'; // Default blue
                        let statusText = 'Pendente';
                        if (o.status === 'paid') {
                            statusColor = '#16a34a'; // Green
                            statusText = 'Pago / Coletado';
                        } else if (o.status === 'transit') {
                            statusColor = '#ea580c'; // Orange
                            statusText = 'Em Trânsito';
                        }
                        
                        occupantsHTML += `
                            <div style="background: white; padding: 0.8rem; border-radius: 6px; border-left: 3px solid ${statusColor}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <div style="font-weight: 700; color: #2d3748; font-size: 0.9rem; margin-bottom: 4px; text-transform: uppercase;">${o.sender_name || 'Remetente Desconhecido'}</div>
                                <div style="font-size: 0.85rem; color: #718096; margin-bottom: 4px;">
                                    Ref: ${o.external_ref || '---'}
                                </div>
                                <div style="color: ${statusColor}; font-weight: 600; font-size: 0.8rem;">
                                    ${statusText}
                                </div>
                            </div>
                        `;
                    });
                    occupantsHTML += '</div></div>';
                }
                
                detailsRow.innerHTML = `<td colspan="7" style="padding: 0; border: none; background-color: #f1f5f9;">${occupantsHTML}</td>`;
                tbody.appendChild(detailsRow);
            });
        }

        async function toggleItemStatus(type, id) {
            let item = null;
            let newStatus = '';
            
            if (type === 'service') {
                item = pricingData[id];
                newStatus = (item.status === 'active') ? 'inactive' : 'active';
            } else if (type === 'location') {
                item = locationsData[id];
                newStatus = (item.status === 'online') ? 'offline' : 'online';
            } else if (type === 'locker') {
                item = totensData[id];
                newStatus = (item.status === 'online') ? 'offline' : 'online';
            }
            
            if (!item) return;
            
            // Optimization: Update UI immediately
            const oldStatus = item.status;
            item.status = newStatus;
            if (type === 'service') renderServicesTable();
            else if (type === 'location') renderLocationsTable();
            else if (type === 'locker') renderTotensTable();
            
            try {
                const response = await fetch('endpoints/save_sync_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: type, item: { ...item, id: id } })
                });
                const result = await response.json();
                if (!result.ok) {
                    // Rollback on error
                    item.status = oldStatus;
                    if (type === 'service') renderServicesTable();
                    else if (type === 'location') renderLocationsTable();
                    else if (type === 'locker') renderTotensTable();
                    alert('Erro ao atualizar status: ' + result.error);
                }
            } catch (err) {
                item.status = oldStatus;
                if (type === 'service') renderServicesTable();
                else if (type === 'location') renderLocationsTable();
                else if (type === 'locker') renderTotensTable();
                alert('Erro na conexão.');
            }
        }

        function toggleLockerOccupants(totemId, btnElement) {
            const detailsRow = document.getElementById(`occupants-row-${totemId}`);
            if (!detailsRow) return;

            const icon = btnElement.querySelector('i');
            
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
                icon.style.transform = 'rotate(180deg)';
                btnElement.style.background = '#e0f2fe';
            } else {
                detailsRow.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
                btnElement.style.background = '#f0f9ff';
            }
        }


        function editLocation(id) {
            const loc = locationsData[id];
            if (loc) {
                document.getElementById('modal-local-title').innerText = 'Editar Unidade';
                document.getElementById('location-id').value = id;
                document.getElementById('loc-name').value = loc.name;
                document.getElementById('loc-address').value = loc.address;
                document.getElementById('loc-sched').value = loc.sched;
                document.getElementById('loc-express').value = loc.express;
                document.getElementById('loc-type').value = loc.type || 'Shopping';
                openModal('modal-local');
            }
        }

        async function saveLocation(e) {
            e.preventDefault();
            let id = document.getElementById('location-id').value;
            if (!id) id = 'loc-' + Date.now();

            const newData = {
                id: id,
                name: document.getElementById('loc-name').value,
                address: document.getElementById('loc-address').value,
                sched: document.getElementById('loc-sched').value,
                express: document.getElementById('loc-express').value,
                type: document.getElementById('loc-type').value,
                status: 'online'
            };

            try {
                const response = await fetch('endpoints/save_sync_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: 'location', item: newData })
                });
                const result = await response.json();
                if (result.ok) {
                    locationsData[id] = newData;
                    renderLocationsTable();
                    closeModal('modal-local');
                    alert('Unidade salva e sincronizada!');
                }
            } catch (err) {
                alert('Erro na conexão.');
            }
        }

        async function fetchUsers() {
            try {
                const response = await fetch('endpoints/get_users.php');
                const result = await response.json();
                if (result.ok) {
                    localUsersData = result.users;
                    renderUsersTable(result.users);
                }
            } catch (error) {
                console.error('Erro ao buscar usuários:', error);
                alert('Erro ao buscar usuários. Verifique se endpoints/get_users.php existe e se você tem permissão.');
            }
        }

        function renderUsersTable(users) {
            const tbody = document.getElementById('users-table-body');
            if (!tbody) return;
            tbody.innerHTML = '';

            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 2rem;">Nenhum usuário cadastrado.</td></tr>';
                return;
            }

            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="align-left-padding" data-label="Nome"><strong>${user.name}</strong> ${user.role === 'admin' ? '<span class="status-badge status-transit" style="font-size:0.6rem">ADMIN</span>' : ''}</td>
                    <td class="align-left-padding" data-label="Email">${user.email}</td>
                    <td class="align-left-padding" data-label="Telefone">${user.phone || '---'}</td>
                    <td class="align-left-padding" data-label="Documento">${user.cpf || '---'}</td>
                    <td class="align-left-padding" data-label="Saldo"><strong>R$ ${parseFloat(user.balance).toFixed(2).replace('.', ',')}</strong></td>
                    <td data-label="Ações">
                        <div style="display:flex; gap:0.4rem; justify-content:center">
                            <button class="close-modal" style="display:inline-flex; width:28px; height:28px; margin:0; background: #ebf8ff; color: #3182ce" onclick="openBalanceModal(${user.id})" title="Gerenciar Saldo">
                                <i class="fas fa-wallet" style="font-size: 0.75rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:28px; height:28px; margin:0;" onclick="openEditUserModal(${user.id})" title="Editar Usuário">
                                <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
                            </button>
                            <button class="close-modal" style="display:inline-flex; width:28px; height:28px; margin:0; background: #fff5f5; color: #e53e3e; border-color: #feb2b2" onclick="deleteItem('user', '${user.id}')" title="Excluir Usuário">
                                <i class="fas fa-trash-alt" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        let localUsersData = [];

        function openBalanceModal(userId) {
            document.getElementById('balance-user-id').value = userId;
            document.getElementById('balance-amount').value = '';
            openModal('modal-balance');
        }

        async function updateBalance(e) {
            e.preventDefault();
            const payload = {
                userId: document.getElementById('balance-user-id').value,
                amount: document.getElementById('balance-amount').value,
                type: document.getElementById('balance-type').value
            };

            try {
                const response = await fetch('endpoints/update_balance', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    closeModal('modal-balance');
                    fetchUsers();
                    alert('Saldo atualizado com sucesso!');
                } else {
                    alert('Erro: ' + result.error);
                }
            } catch (error) {
                alert('Erro na conexão.');
            }
        }

        function openEditUserModal(id) {
            const user = localUsersData.find(u => u.id == id);
            if (!user) return;
            
            document.getElementById('edit-user-id').value = user.id;
            document.getElementById('edit-user-name').value = user.name;
            document.getElementById('edit-user-email').value = user.email;
            document.getElementById('edit-user-phone').value = user.phone || '';
            document.getElementById('edit-user-cpf').value = user.cpf || '';
            document.getElementById('edit-user-balance').value = user.balance;
            
            // Set current role in select, default to user if not defined
            const roleSelect = document.getElementById('edit-user-role');
            roleSelect.value = (user.role === 'admin') ? 'admin' : 'user';
            
            document.getElementById('edit-user-password').value = '';
            openModal('modal-edit-user');
        }

        async function updateUser(e) {
            e.preventDefault();
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            const nameField = document.getElementById('edit-user-name').value.trim();
            if (nameField.split(' ').length < 2) {
                alert('O nome deve conter pelo menos um sobrenome.');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SALVANDO...';

            const payload = {
                user_id: document.getElementById('edit-user-id').value,
                name: nameField,
                email: document.getElementById('edit-user-email').value,
                phone: document.getElementById('edit-user-phone').value,
                cpf: document.getElementById('edit-user-cpf').value,
                balance: document.getElementById('edit-user-balance').value,
                role: document.getElementById('edit-user-role').value,
                password: document.getElementById('edit-user-password').value
            };

            try {
                const response = await fetch('endpoints/admin_update_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    closeModal('modal-edit-user');
                    fetchUsers(); // Refresh the list
                    alert(result.message || 'Usuário atualizado com sucesso!');
                } else {
                    alert('Erro: ' + (result.error || 'Erro desconhecido.'));
                }
            } catch (error) {
                alert('Erro de conexão com o servidor.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'SALVAR ALTERAÇÕES';
            }
        }

        async function adminAddUser(e) {
            e.preventDefault();
            
            const nameField = document.getElementById('admin-user-name').value.trim();
            if (nameField.split(' ').length < 2) {
                alert('O nome deve conter pelo menos um sobrenome.');
                return;
            }
            
            const payload = {
                name: nameField,
                email: document.getElementById('admin-user-email').value,
                phone: document.getElementById('admin-user-phone').value,
                cpf: document.getElementById('admin-user-cpf').value,
                balance: document.getElementById('admin-user-balance').value,
                password: document.getElementById('admin-user-password').value
            };

            try {
                const response = await fetch('endpoints/admin_add_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    closeModal('modal-cliente');
                    fetchUsers();
                    alert('Usuário cadastrado com sucesso!');
                    document.getElementById('admin-user-name').value = '';
                    document.getElementById('admin-user-email').value = '';
                    document.getElementById('admin-user-phone').value = '';
                    document.getElementById('admin-user-cpf').value = '';
                    document.getElementById('admin-user-password').value = '';
                } else {
                    alert('Erro: ' + result.error);
                }
            } catch (error) {
                alert('Erro na conexão.');
            }
        }

        function togglePasswordVisibility(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function maskPhone(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 11);
            let r = '';
            if (v.length > 0) r = '(' + v.substring(0, 2);
            if (v.length >= 3) r += ') ' + v.substring(2, 3);
            if (v.length >= 4) r += ' ' + v.substring(3, 6);
            if (v.length >= 7) r += '-' + v.substring(6, 10);
            if (v.length > 2 && v.length < 3) r += ')';
            input.value = r;
        }

        function maskCPF(input) {
            let v = input.value.replace(/\D/g, '').substring(0, 11);
            let r = '';
            if (v.length > 0) r = v.substring(0, 3);
            if (v.length >= 4) r += '.' + v.substring(3, 6);
            if (v.length >= 7) r += '.' + v.substring(6, 9);
            if (v.length >= 10) r += '-' + v.substring(9, 11);
            input.value = r;
        }

        function populateTotemLocationSelect() {
            const select = document.getElementById('totem-location-id');
            if (!select) return;
            const currentValue = select.value;
            select.innerHTML = '<option value="">Selecionar Unidade...</option>';
            Object.keys(locationsData).forEach(id => {
                const loc = locationsData[id];
                const opt = document.createElement('option');
                opt.value = id;
                opt.innerText = loc.name;
                select.appendChild(opt);
            });
            if (currentValue) select.value = currentValue;
        }

        function openNewTotemModal() {
            document.getElementById('modal-toten-title').innerText = 'Novo Totem / Locker';
            document.getElementById('totem-id').value = '';
            document.getElementById('totem-label').value = '';
            document.getElementById('totem-hardware').value = '';
            document.getElementById('totem-capacity').value = '10';
            populateTotemLocationSelect();
            document.getElementById('btn-save-totem').innerText = 'ADICIONAR';
            openModal('modal-toten');
        }

        function editTotem(id) {
            const t = totensData[id];
            if (t) {
                populateTotemLocationSelect();
                document.getElementById('modal-toten-title').innerText = 'Editar Totem / Locker';
                document.getElementById('totem-id').value = id;
                document.getElementById('totem-label').value = t.label;
                document.getElementById('totem-location-id').value = t.locationId;
                document.getElementById('totem-hardware').value = t.hardware;
                document.getElementById('totem-type').value = t.type;
                document.getElementById('totem-status').value = t.status;
                document.getElementById('totem-capacity').value = t.capacity || 10;
                document.getElementById('btn-save-totem').innerText = 'SALVAR ALTERAÇÕES';
                openModal('modal-toten');
            }
        }

        async function saveTotem(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-save-totem');
            const originalText = btn.innerText;
            btn.innerText = 'PROCESSANDO...';
            btn.disabled = true;

            let id = document.getElementById('totem-id').value;
            if (!id) id = 't-' + Date.now();

            const newData = {
                id: id,
                label: document.getElementById('totem-label').value,
                locationId: document.getElementById('totem-location-id').value,
                hardware: document.getElementById('totem-hardware').value,
                type: document.getElementById('totem-type').value,
                status: document.getElementById('totem-status').value,
                capacity: document.getElementById('totem-capacity').value
            };

            // Prepare payload with keys expected by PHP
            const payload = {
                type: 'locker',
                item: {
                    id: newData.id,
                    label: newData.label,
                    location_id: newData.locationId,
                    hardware: newData.hardware,
                    type: newData.type,
                    status: newData.status,
                    capacity: newData.capacity
                }
            };

            try {
                const response = await fetch('endpoints/save_sync_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.ok) {
                    totensData[id] = newData;
                    renderTotensTable();
                    closeModal('modal-toten');
                    alert('Hardware salvo e sincronizado!');
                } else {
                    alert('Erro ao salvar: ' + (result.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                alert('Erro na conexão com o servidor.');
            } finally {
                btn.innerText = originalText;
                btn.disabled = false;
            }
        }

        window.addEventListener('load', () => {
            console.log("Admin panel loaded, initializing sync...");
            initSyncData();
            fetchOrders();
            fetchUsers();
            
            // Initialize AI & FAQ
            const faqContent = document.getElementById('ai-faq-content').value;
            renderFaqs(faqContent);
            setInterval(fetchOrders, 10000);
            setInterval(fetchUsers, 30000);
            setInterval(initSyncData, 60000); // Sync every minute
        });

        async function deleteItem(type, id) {
            const labels = {
                'order': 'esta encomenda',
                'location': 'esta unidade de coleta',
                'locker': 'este totem/locker',
                'user': 'este usuário',
                'service': 'este serviço/produto'
            };
            
            if (!confirm(`Tem certeza que deseja excluir ${labels[type]}? Esta ação não pode ser desfeita.`)) {
                return;
            }

            try {
                const response = await fetch('endpoints/delete_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type, id })
                });
                const result = await response.json();
                
                if (result.ok) {
                    alert('Item excluído com sucesso!');
                    // Refresh data based on type
                    if (type === 'order') fetchOrders();
                    else if (type === 'user') fetchUsers();
                    else initSyncData(); // Most other things are in syncData
                } else {
                    alert('Erro ao excluir: ' + (result.error || 'Erro desconhecido.'));
                }
            } catch (err) {
                console.error("Delete error:", err);
                alert('Erro na conexão com o servidor.');
            }
        }

        async function saveAiConfig() {
            const kbContent = document.getElementById('ai-kb-content-config').value;
            
            // Collect FAQs
            const faqLines = [];
            document.querySelectorAll('.faq-item').forEach(item => {
                const q = item.querySelector('.faq-q').value.trim();
                const a = item.querySelector('.faq-a').value.trim();
                if (q && a) {
                    faqLines.push(`${q}|${a}`);
                }
            });
            const faqContent = faqLines.join("\n");

            const btn = document.getElementById('btn-save-ai-config');
            const originalHtml = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> SALVANDO...';

                const formData = new FormData();
                formData.append('kb_content', kbContent);
                formData.append('faq_content', faqContent);

                const response = await fetch('endpoints/admin_save_ai_kb.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    alert(data.success);
                } else {
                    alert('Erro: ' + (data.error || 'Erro desconhecido'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Erro ao processar a solicitação.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // FAQ UI Functions
        function renderFaqs(content) {
            const container = document.getElementById('faq-list-container');
            container.innerHTML = '';
            
            if (!content) return;
            
            const lines = content.split('\n');
            lines.forEach(line => {
                const parts = line.split('|');
                if (parts.length >= 2) {
                    addFaqItem(parts[0], parts[1]);
                }
            });
        }

        function addFaqItem(q, a) {
            const container = document.getElementById('faq-list-container');
            const div = document.createElement('div');
            div.className = 'faq-item';
            div.style = 'background: #f8fafc; border: 1px solid #e2e8f0; padding: 1rem; border-radius: 12px; position: relative;';
            div.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; border: none; background: none; color: #ef4444; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
                <div style="margin-bottom: 10px;">
                    <label style="font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase;">Pergunta</label>
                    <input type="text" class="faq-q" value="${q}" placeholder="Ex: Como rastrear?" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; margin-top: 4px;">
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 700; color: #718096; text-transform: uppercase;">Resposta Curta</label>
                    <textarea class="faq-a" placeholder="Ex: Vá na aba Rastrear..." style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; margin-top: 4px; min-height: 60px;">${a}</textarea>
                </div>
            `;
            container.appendChild(div);
            // Scroll to bottom
            container.scrollTop = container.scrollHeight;
        }
    </script>
</body>

</html>