<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline QR Management - Admin | KFHI-CMS</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #eef6f0;
            color: #26352b;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            flex: 0 0 250px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 25px 18px;
        }

        .layout.sidebar-collapsed .sidebar {
            display: none;
        }

        .brand {
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .brand h2 {
            color: #2e7d32;
            font-size: 24px;
        }

        .nav {
            margin-top: 25px;
            padding-bottom: 70px;
        }

        .nav-title {
            font-size: 11px;
            color: #8a978d;
            text-transform: uppercase;
            margin: 0 10px 10px;
            letter-spacing: 1px;
        }

        .nav a {
            display: block;
            text-decoration: none;
            color: #53665a;
            padding: 12px 14px;
            margin-bottom: 6px;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .nav a:hover,
        .nav a.active {
            background: #e0f0e3;
            color: #28733a;
            font-weight: 600;
        }

        .sidebar-toggle {
            border: 1px solid #dfe9e2;
            border-radius: 7px;
            padding: 8px 10px;
            background: #ffffff;
            color: #365344;
            font: inherit;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .identification-menu summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            margin-bottom: 6px;
            border-radius: 10px;
            color: #53665a;
            font-size: 14px;
            cursor: pointer;
            list-style: none;
        }

        .identification-menu summary::-webkit-details-marker {
            display: none;
        }

        .identification-menu summary::after {
            content: '+';
            font-size: 16px;
        }

        .identification-menu[open] summary::after {
            content: '-';
        }

        .identification-menu summary:hover,
        .identification-menu summary.active {
            background: #e0f0e3;
            color: #28733a;
            font-weight: 600;
        }

        .identification-menu .nav-link {
            margin-left: 12px;
            border-left: 1px solid #dfe9e2;
        }

        .badge-offline {
            display: inline-block;
            background: #236b3b;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 6px;
            vertical-align: middle;
        }

        .logout-form {
            margin-top: 20px;
        }

        .logout-button {
            width: 100%;
            border: none;
            padding: 11px;
            border-radius: 10px;
            background: #fbeaea;
            color: #b44949;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }

        /* Content Area */
        .content {
            flex: 1;
            padding: 35px;
            max-width: 1400px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #d9e7dd;
            flex-wrap: wrap;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 26px;
            color: #215c34;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pill.offline-ready {
            background: #e0f0e3;
            color: #28733a;
            border: 1px solid #a5d6a7;
        }

        .status-pill.network-mode {
            background: #eef2f6;
            color: #475569;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #28733a;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(40, 115, 58, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 115, 58, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(40, 115, 58, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 115, 58, 0); }
        }

        /* Banner */
        .offline-banner {
            background: linear-gradient(135deg, #246b43, #164e2e);
            color: #ffffff;
            border-radius: 16px;
            padding: 22px 28px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 8px 24px rgba(33, 92, 52, 0.16);
        }

        .offline-banner-content h2 {
            font-size: 20px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .offline-banner-content p {
            font-size: 13.5px;
            color: #d1fae5;
            line-height: 1.5;
            max-width: 800px;
        }

        .banner-tag {
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Tabs */
        .tab-nav {
            display: flex;
            gap: 10px;
            border-bottom: 2px solid #dce9df;
            padding-bottom: 1px;
            margin-bottom: 25px;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 13px 22px;
            font-size: 14px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .tab-btn:hover {
            color: #246b43;
            background: #eef7f0;
        }

        .tab-btn.active {
            color: #246b43;
            border-bottom-color: #246b43;
            background: #ffffff;
        }

        .tab-badge {
            background: #e2e8f0;
            color: #475569;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 700;
        }

        .tab-btn.active .tab-badge {
            background: #246b43;
            color: #ffffff;
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* Content Card */
        .content-card {
            background: #ffffff;
            border: 1px solid #d8e6dc;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            margin-bottom: 25px;
        }

        .card-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-header-bar h3 {
            font-size: 18px;
            color: #246b43;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .card-header-bar p {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
        }

        .form-control {
            border: 1px solid #cfe0d4;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            background: #ffffff;
            color: #1f2937;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #246b43;
            box-shadow: 0 0 0 3px rgba(36, 107, 67, 0.12);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #246b43;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #1b5433;
        }

        .btn-secondary {
            background: #e0f0e3;
            color: #246b43;
            border: 1px solid #c0dfc6;
        }

        .btn-secondary:hover {
            background: #cee9d5;
        }

        .btn-outline {
            background: #ffffff;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        .btn-outline:hover {
            background: #f9fafb;
            color: #111827;
        }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        /* Scanner */
        .scanner-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        @media (max-width: 992px) {
            .scanner-container {
                grid-template-columns: 1fr;
            }
        }

        .scanner-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        #offlineReader {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            background: #000000;
            min-height: 280px;
        }

        .scanner-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .drop-zone {
            margin-top: 20px;
            border: 2px dashed #94a3b8;
            border-radius: 12px;
            padding: 20px 15px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .drop-zone:hover {
            border-color: #246b43;
            background: #f0fdf4;
        }

        .drop-zone input[type="file"] {
            display: none;
        }

        /* Verification Card */
        .verification-card {
            background: #ffffff;
            border: 1px solid #cde3d1;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 6px 20px rgba(36, 107, 67, 0.08);
            display: none;
        }

        .verification-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 1px solid #e2ece4;
            margin-bottom: 18px;
        }

        .verified-badge {
            background: #e0f0e3;
            color: #246b43;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .online-only-badge {
            background: #fff7ed;
            color: #9a3412;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px solid #f1f5f9;
        }

        .info-table tr:last-child {
            border-bottom: none;
        }

        .info-table td {
            padding: 10px 4px;
            font-size: 13.5px;
        }

        .info-table td.label-col {
            color: #64748b;
            font-weight: 500;
            width: 38%;
        }

        .info-table td.value-col {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
        }

        /* ID Card Preview */
        .generator-container {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 25px;
            align-items: start;
        }

        @media (max-width: 1080px) {
            .generator-container {
                grid-template-columns: 1fr;
            }
        }

        .id-card-stage {
            background: #e2ece5;
            padding: 30px;
            border-radius: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: sticky;
            top: 20px;
        }

        .id-card {
            width: 85.6mm;
            height: 54mm;
            background: #ffffff;
            border: 1px solid #cfe0d4;
            border-radius: 3.5mm;
            padding: 3.5mm 4mm;
            box-shadow: 0 14px 35px rgba(30, 70, 45, 0.16);
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .id-card-header {
            height: 9mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 0.35mm solid #dce9df;
            padding-bottom: 1.2mm;
        }

        .id-card-brand {
            display: flex;
            align-items: center;
            gap: 2mm;
        }

        .id-card-logo {
            width: 10mm;
            height: 6.5mm;
            object-fit: contain;
        }

        .id-card-brand-name {
            font-size: 3mm;
            font-weight: 800;
            color: #246b43;
            line-height: 1.1;
        }

        .id-card-brand-sub {
            font-size: 1.7mm;
            color: #64748b;
        }

        .id-card-badge-type {
            text-align: right;
            font-size: 2mm;
            font-weight: 700;
            color: #246b43;
        }

        .id-card-badge-type span {
            display: block;
            font-size: 1.5mm;
            color: #64748b;
            font-weight: 500;
        }

        .id-card-body {
            height: 35mm;
            display: flex;
            gap: 2.5mm;
            padding-top: 2.5mm;
        }

        .id-card-photo-box {
            width: 18mm;
            height: 22mm;
            border-radius: 1.8mm;
            border: 0.4mm solid #cde2d3;
            background: #eef6f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .id-card-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .id-card-photo-placeholder {
            font-size: 7mm;
            font-weight: 700;
            color: #246b43;
        }

        .id-card-details {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .id-card-code-label {
            font-size: 1.6mm;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.2mm;
        }

        .id-card-code {
            font-size: 3.2mm;
            font-weight: 800;
            color: #246b43;
            letter-spacing: 0.2mm;
            line-height: 1.2;
        }

        .id-card-name {
            font-size: 3mm;
            font-weight: 700;
            color: #1e293b;
            margin-top: 1mm;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 36mm;
        }

        .id-card-meta-line {
            font-size: 1.9mm;
            color: #334155;
            margin-top: 1mm;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 36mm;
        }

        .id-card-meta-line span {
            color: #64748b;
            font-size: 1.6mm;
        }

        .id-card-qr-box {
            width: 22mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .id-card-qr-target {
            width: 21mm;
            height: 21mm;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .id-card-qr-target canvas,
        .id-card-qr-target img,
        .id-card-qr-target svg {
            width: 100% !important;
            height: 100% !important;
            max-width: 100% !important;
            max-height: 100% !important;
            display: block !important;
            object-fit: contain !important;
        }

        .id-card-qr-caption {
            font-size: 1.35mm;
            color: #64748b;
            margin-top: 0.8mm;
            text-align: center;
            font-weight: 600;
        }

        .id-card-footer {
            height: 4mm;
            border-top: 0.3mm solid #e2ebe4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.5mm;
            color: #64748b;
            padding-top: 0.5mm;
        }

        .id-card-footer-right {
            font-weight: 700;
            color: #246b43;
        }

        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .log-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .log-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .json-preview {
            background: #0f172a;
            color: #38bdf8;
            padding: 16px;
            border-radius: 10px;
            font-family: monospace;
            font-size: 12px;
            overflow-x: auto;
            max-height: 200px;
            white-space: pre-wrap;
            word-break: break-all;
            margin-top: 15px;
        }

        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .sidebar, .topbar, .offline-banner, .tab-nav, .content-card, .btn, .generator-container > div:first-child {
                display: none !important;
            }
            .id-card-stage {
                background: none !important;
                padding: 0 !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                visibility: visible !important;
            }
            #printableIdCard,
            #printableIdCard * {
                visibility: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .id-card-qr-box,
            .id-card-qr-target,
            .id-card-qr-target canvas,
            .id-card-qr-target img,
            .id-card-qr-target svg {
                display: block !important;
                visibility: visible !important;
                width: 21mm !important;
                height: 21mm !important;
                max-width: 21mm !important;
                max-height: 21mm !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            #printableIdCard {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 85.6mm !important;
                height: 54mm !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

<div class="layout" id="pageShell">
    @include('components.admin-sidebar')

    <!-- Main Content -->
    <main class="content">

        <!-- Topbar -->
        <div class="topbar">
            <div>
                <h1>Offline QR Management (Admin)</h1>
                <p style="color:#64748b; font-size:13.5px; margin-top:4px;">
                    Centralized offline QR generation and scanning studio for remote field programs.
                </p>
            </div>

            <div style="display:flex; align-items:center; gap:12px;">
                <div class="status-pill offline-ready">
                    <span class="pulse-dot"></span>
                    <span>100% Offline Mode &bull; No API Needed</span>
                </div>

                <div class="status-pill network-mode" id="networkPill">
                    <span id="networkText">Checking Network...</span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tab-nav">
            <button class="tab-btn active" id="tab-generator-btn" onclick="switchTab('generator')">
                🖨️ Offline QR & ID Card Generator
            </button>

            <button class="tab-btn" id="tab-scanner-btn" onclick="switchTab('scanner')">
                📷 Offline Scanner
                <span class="tab-badge" id="scansCountBadge">0</span>
            </button>

            <button class="tab-btn" id="tab-log-btn" onclick="switchTab('log')">
                📋 Field Attendance Log
            </button>
        </div>


        <!-- ==========================================
             TAB: OFFLINE QR & ID CARD GENERATOR
             ========================================== -->
        <div class="tab-panel active" id="panel-generator">
            <div class="generator-container">

                <div>
                    <div class="content-card">
                        <div class="card-header-bar">
                            <div>
                                <h3>🖨️ Offline Child ID Generator</h3>
                                <p>Select an existing child or enter custom field data to generate an offline card.</p>
                            </div>
                        </div>

                        <!-- Dropdown -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label for="childSelect">⚡ Select Registered Child:</label>
                            <select id="childSelect" class="form-control" onchange="onChildSelected(this.value)">
                                <option value="">-- Choose a child ({{ $children->count() }} registered) --</option>
                                @foreach($children as $ch)
                                    <option value="{{ $ch->id }}">
                                        {{ $ch->child_code }} - {{ $ch->first_name }} {{ $ch->last_name }} ({{ $ch->gn_division ?: 'No GN' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px; margin:20px 0 15px;">
                            <hr style="flex:1; border:none; border-top:1px solid #e2e8f0;">
                            <span style="font-size:11.5px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Identity Fields</span>
                            <hr style="flex:1; border:none; border-top:1px solid #e2e8f0;">
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="genChildCode">Child Code *</label>
                                <input type="text" id="genChildCode" class="form-control" placeholder="KFHI-2026-0001" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genFullName">Full Name *</label>
                                <input type="text" id="genFullName" class="form-control" placeholder="Kamal Perera" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genGender">Gender</label>
                                <select id="genGender" class="form-control" onchange="triggerGenerate()">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="genDob">Date of Birth</label>
                                <input type="date" id="genDob" class="form-control" onchange="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genSchool">School Name</label>
                                <input type="text" id="genSchool" class="form-control" placeholder="St. Anthony's College" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genGrade">Grade</label>
                                <input type="text" id="genGrade" class="form-control" placeholder="Grade 5" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genGn">GN Division</label>
                                <input type="text" id="genGn" class="form-control" placeholder="GN-12" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genDistrict">District</label>
                                <input type="text" id="genDistrict" class="form-control" placeholder="Colombo" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genGuardian">Primary Guardian</label>
                                <input type="text" id="genGuardian" class="form-control" placeholder="Sunil Perera" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genPhone">Emergency Phone</label>
                                <input type="text" id="genPhone" class="form-control" placeholder="0771234567" oninput="triggerGenerate()">
                            </div>

                            <div class="form-group">
                                <label for="genStatus">Status</label>
                                <select id="genStatus" class="form-control" onchange="triggerGenerate()">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div style="display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;">
                            <button type="button" class="btn btn-primary" onclick="printIdCard()">
                                🖨️ Print ID Card
                            </button>

                            <button type="button" class="btn btn-secondary" onclick="downloadIdCardImage()">
                                💾 Download ID Card (PNG)
                            </button>

                            <button type="button" class="btn btn-outline" onclick="downloadQrOnly()">
                                🔳 Download QR Only
                            </button>

                            <button type="button" class="btn btn-outline" onclick="toggleJsonPreview()">
                                { } View QR JSON
                            </button>
                        </div>

                        <div id="jsonPreviewContainer" style="display:none;">
                            <div style="margin-top:20px; font-size:12px; font-weight:700; color:#475569;">
                                Self-Contained Offline QR Payload:
                            </div>
                            <pre class="json-preview" id="rawJsonPreview"></pre>
                        </div>
                    </div>
                </div>

                <!-- Right: Preview -->
                <div class="id-card-stage">
                    <p style="font-size:12.5px; font-weight:700; color:#246b43; margin-bottom:15px; text-transform:uppercase; letter-spacing:0.5px;">
                        Standard ID-1 Card Preview (85.6mm × 54mm)
                    </p>

                    <div class="id-card" id="printableIdCard">
                        <div class="id-card-header">
                            <div class="id-card-brand">
                                <img src="{{ asset('images/logo.png') }}" alt="KFHI" class="id-card-logo">
                                <div>
                                    <div class="id-card-brand-name">KFHI</div>
                                    <div class="id-card-brand-sub">Friends of Hope</div>
                                </div>
                            </div>
                            <div class="id-card-badge-type">
                                CHILD ID CARD
                                <span>OFFLINE IDENTIFIER</span>
                            </div>
                        </div>

                        <div class="id-card-body">
                            <div class="id-card-photo-box" id="cardPhotoBox">
                                <div class="id-card-photo-placeholder" id="cardInitials">K</div>
                            </div>

                            <div class="id-card-details">
                                <div class="id-card-code-label">Child ID</div>
                                <div class="id-card-code" id="cardCode">KFHI-2026-0001</div>
                                <div class="id-card-name" id="cardName">Kamal Perera</div>

                                <div class="id-card-meta-line" id="cardGnLine">
                                    <span>GN:</span> <strong id="cardGn">GN-12</strong>
                                </div>
                                <div class="id-card-meta-line" id="cardSchoolLine">
                                    <span>School:</span> <strong id="cardSchool">St. Anthony's</strong>
                                </div>
                                <div class="id-card-meta-line" id="cardGuardianLine">
                                    <span>Guardian:</span> <strong id="cardGuardian">Sunil Perera</strong>
                                </div>
                            </div>

                            <div class="id-card-qr-box">
                                <div class="id-card-qr-target" id="cardQrCodeTarget"></div>
                                <div class="id-card-qr-caption">Scan Offline</div>
                            </div>
                        </div>

                        <div class="id-card-footer">
                            <div>Secure Child Identification</div>
                            <div class="id-card-footer-right">KFHI-CMS</div>
                        </div>
                    </div>

                    <p style="font-size:11.5px; color:#52635a; margin-top:15px; text-align:center; max-width:320px;">
                        Contains complete child profile directly inside the QR code. Any camera can verify this ID without network.
                    </p>
                </div>

            </div>
        </div>


        <!-- ==========================================
             TAB: OFFLINE QR SCANNER
             ========================================== -->
        <div class="tab-panel" id="panel-scanner">
            <div class="scanner-container">

                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <h3>📷 Camera & Image Scanner</h3>
                            <p>Decodes offline QR badges locally without sending data across network.</p>
                        </div>
                    </div>

                    <div class="scanner-box">
                        <div id="offlineReader"></div>

                        <div class="scanner-controls">
                            <button type="button" class="btn btn-primary" id="btnStartScan" onclick="startOfflineScanner()">
                                ▶ Start Camera
                            </button>

                            <button type="button" class="btn btn-secondary" id="btnStopScan" onclick="stopOfflineScanner()" style="display:none;">
                                ⏹ Stop Camera
                            </button>

                            <button type="button" class="btn btn-outline" id="btnSwitchCam" onclick="switchCameraFacing()" style="display:none;">
                                🔄 Switch Camera
                            </button>
                        </div>

                        <p id="scannerMessage" style="margin-top:14px; font-size:13px; color:#64748b;">
                            Ready. Allow camera access or drop a QR code image below.
                        </p>

                        <div class="drop-zone" onclick="document.getElementById('qrFileInput').click()">
                            <p style="font-size:13.5px; font-weight:600; color:#246b43; margin-bottom:4px;">
                                📁 Scan from Image File
                            </p>
                            <p style="font-size:12px; color:#64748b;">
                                Click to upload or drag & drop a photo/screenshot of a child's QR code
                            </p>
                            <input type="file" id="qrFileInput" accept="image/*" onchange="scanImageFile(this)">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="verification-card" id="verificationCard">
                        <div class="verification-header">
                            <div>
                                <h3 id="scanResultTitle" style="font-size:18px; color:#246b43; font-weight:700;">Child Verified</h3>
                                <p style="font-size:12px; color:#64748b;" id="scanTimestamp">-</p>
                            </div>
                            <span class="verified-badge" id="scanResultBadge">
                                ✓ Verified Offline
                            </span>
                        </div>

                        <div style="display:flex; align-items:center; gap:15px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">
                            <div style="width:52px; height:52px; border-radius:12px; background:#e0f0e3; color:#246b43; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800;" id="resInitial">
                                C
                            </div>
                            <div>
                                <h4 style="font-size:18px; color:#1e293b;" id="resFullName">-</h4>
                                <span style="font-size:12px; font-weight:700; background:#e2e8f0; color:#334155; padding:3px 8px; border-radius:6px;" id="resCode">
                                    -
                                </span>
                            </div>
                        </div>

                        <div id="scanNotice" role="status" style="display:none; margin:16px 0; padding:14px; border:1px solid #fed7aa; border-radius:8px; background:#fff7ed; color:#7c2d12; font-size:13px; line-height:1.5;">
                            <p id="scanNoticeText" style="margin:0 0 8px;"></p>
                            <a id="scanOnlineLink" href="#" style="display:none; color:#9a3412; font-weight:700;">Open online child record</a>
                        </div>

                        <table class="info-table" id="scanInfoTable">
                            <tr>
                                <td class="label-col">Gender</td>
                                <td class="value-col" id="resGender">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">Date of Birth</td>
                                <td class="value-col" id="resDob">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">School</td>
                                <td class="value-col" id="resSchool">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">Grade</td>
                                <td class="value-col" id="resGrade">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">GN Division</td>
                                <td class="value-col" id="resGn">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">District</td>
                                <td class="value-col" id="resDistrict">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">Primary Guardian</td>
                                <td class="value-col" id="resGuardian">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">Emergency Phone</td>
                                <td class="value-col" id="resPhone">-</td>
                            </tr>
                            <tr>
                                <td class="label-col">Registration Status</td>
                                <td class="value-col" id="resStatus">-</td>
                            </tr>
                        </table>

                        <div style="display:flex; gap:10px; margin-top:20px; flex-wrap:wrap;">
                            <button type="button" class="btn btn-primary" style="flex:1;" onclick="startOfflineScanner()">
                                🔍 Scan Next Child
                            </button>

                            <button type="button" class="btn btn-secondary" id="copyDecodedButton" onclick="copyDecodedData()">
                                📋 Copy Info
                            </button>
                        </div>
                    </div>

                    <div class="content-card" id="scanPlaceholderCard" style="text-align:center; padding:45px 20px;">
                        <div style="font-size:46px; margin-bottom:12px;">📱</div>
                        <h4 style="font-size:17px; color:#1e293b; margin-bottom:6px;">No Child Scanned Yet</h4>
                        <p style="font-size:13px; color:#64748b; max-width:320px; margin:0 auto;">
                            Position a child's QR code in front of the camera or upload an image to view their verified identity offline.
                        </p>
                    </div>
                </div>

            </div>
        </div>


        <!-- ==========================================
             TAB: FIELD LOG
             ========================================== -->
        <div class="tab-panel" id="panel-log">
            <div class="content-card">
                <div class="card-header-bar">
                    <div>
                        <h3>📋 Field Attendance / Scans Log</h3>
                        <p>Cached locally on this device. Export as CSV when returning from remote field visits.</p>
                    </div>

                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn btn-primary" onclick="exportLogCsv()">
                            📥 Export CSV
                        </button>

                        <button type="button" class="btn btn-outline" onclick="exportLogJson()">
                            📥 Export JSON
                        </button>

                        <button type="button" class="btn btn-danger" onclick="clearLog()">
                            🗑️ Clear Log
                        </button>
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table class="log-table" id="logTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Child Code</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>School & Grade</th>
                                <th>GN Division</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody"></tbody>
                    </table>
                </div>

                <div class="empty-state" id="logEmptyState">
                    <p style="font-size:16px; font-weight:600; color:#475569;">No Scans Logged Yet</p>
                    <p style="font-size:13px; color:#94a3b8; margin-top:4px;">
                        Use the Offline Scanner tab in the field. Every scanned child will appear here automatically.
                    </p>
                </div>
            </div>
        </div>

    </main>

</div>


<!-- LOCAL SCRIPTS -->
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script src="{{ asset('js/qrcode-generator.min.js') }}"></script>
<script src="{{ asset('js/html2canvas.min.js') }}"></script>

<script>
    const serverChildren = @json($childrenData);
    const activeTool = @json($activeTool);
    const STORAGE_KEY_SCANS = 'kfhi_offline_scans_log';
    let offlineScanner = null;
    let offlineScannerRunning = false;
    let currentCameraFacing = 'environment';
    let qrcodeInstance = null;
    let lastDecodedPayload = null;

    function updateNetworkStatus() {
        const pill = document.getElementById('networkPill');
        const text = document.getElementById('networkText');

        if (navigator.onLine) {
            pill.className = 'status-pill network-mode';
            text.innerHTML = '🌐 Network Online';
        } else {
            pill.className = 'status-pill offline-ready';
            text.innerHTML = '⚡ Field Offline (No Network)';
        }
    }

    window.addEventListener('online', updateNetworkStatus);
    window.addEventListener('offline', updateNetworkStatus);
    updateNetworkStatus();

    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));

        const targetBtn = document.getElementById('tab-' + tabName + '-btn');
        const targetPanel = document.getElementById('panel-' + tabName);

        if (targetBtn && targetPanel) {
            targetBtn.classList.add('active');
            targetPanel.classList.add('active');
        }

        if (tabName !== 'scanner' && offlineScannerRunning) {
            stopOfflineScanner();
        }

        if (tabName === 'generator') {
            generateOfflineQrCard();
        }

        if (tabName === 'log') {
            renderScansLogTable();
        }
    }

    function playSuccessBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + 0.12);
        } catch (e) {}
    }

    async function startOfflineScanner() {
        if (offlineScannerRunning) return;

        const readerElem = document.getElementById('offlineReader');
        const msg = document.getElementById('scannerMessage');
        const btnStart = document.getElementById('btnStartScan');
        const btnStop = document.getElementById('btnStopScan');
        const btnSwitch = document.getElementById('btnSwitchCam');

        msg.textContent = 'Requesting camera access...';

        try {
            offlineScanner = new Html5Qrcode('offlineReader');

            await offlineScanner.start(
                { facingMode: currentCameraFacing },
                {
                    fps: 12,
                    qrbox: { width: 250, height: 250 }
                },
                onQrCodeScanned,
                function(err) {}
            );

            offlineScannerRunning = true;
            btnStart.style.display = 'none';
            btnStop.style.display = 'inline-flex';
            btnSwitch.style.display = 'inline-flex';
            msg.textContent = 'Point camera at child QR code. Works 100% offline.';
        } catch (err) {
            console.error('Camera error:', err);
            offlineScannerRunning = false;
            msg.textContent = 'Camera error or permission denied: ' + (err.message || err);
            btnStart.style.display = 'inline-flex';
            btnStop.style.display = 'none';
            btnSwitch.style.display = 'none';
        }
    }

    async function stopOfflineScanner() {
        if (!offlineScanner || !offlineScannerRunning) return;

        try {
            await offlineScanner.stop();
            offlineScanner.clear();
            offlineScannerRunning = false;

            document.getElementById('btnStartScan').style.display = 'inline-flex';
            document.getElementById('btnStopScan').style.display = 'none';
            document.getElementById('btnSwitchCam').style.display = 'none';
            document.getElementById('scannerMessage').textContent = 'Camera scanner stopped.';
        } catch (err) {
            console.error(err);
        }
    }

    async function switchCameraFacing() {
        currentCameraFacing = currentCameraFacing === 'environment' ? 'user' : 'environment';
        await stopOfflineScanner();
        await startOfflineScanner();
    }

    async function scanImageFile(fileInput) {
        if (!fileInput.files || fileInput.files.length === 0) return;
        const file = fileInput.files[0];

        const msg = document.getElementById('scannerMessage');
        msg.textContent = 'Decoding image file offline...';

        try {
            const html5QrCode = new Html5Qrcode('offlineReader');
            const decodedText = await html5QrCode.scanFile(file, true);
            html5QrCode.clear();

            onQrCodeScanned(decodedText);
        } catch (err) {
            console.error('File scan error:', err);
            msg.textContent = 'Could not find a valid QR code in that image. Please try another photo.';
        } finally {
            fileInput.value = '';
        }
    }

    function onQrCodeScanned(decodedText) {
        playSuccessBeep();

        let childData = null;
        const raw = (decodedText || '').trim();

        if (raw.startsWith('{') && raw.endsWith('}')) {
            try {
                const parsed = JSON.parse(raw);
                childData = {
                    child_code: parsed.code || 'N/A',
                    full_name: parsed.name || 'N/A',
                    gender: parsed.gender || 'N/A',
                    date_of_birth: parsed.dob || 'N/A',
                    school_name: parsed.school || 'N/A',
                    grade: parsed.grade || 'N/A',
                    gn_division: parsed.gn || 'N/A',
                    district: parsed.district || 'N/A',
                    guardian: parsed.guardian || (parsed.relationship ? parsed.relationship : 'N/A'),
                    phone: parsed.phone || 'N/A',
                    status: parsed.status || 'Active',
                    raw_payload: raw
                };
            } catch (e) {
                console.warn('JSON parse error:', e);
            }
        }

        if (!childData && (raw.includes('Code:') || raw.includes('KFHI'))) {
            childData = parsePlainTextChildQr(raw);
        }

        if (!childData) {
            const match = serverChildren.find(c =>
                c.child_code && raw.toLowerCase().includes(c.child_code.toLowerCase())
            );

            if (match) {
                childData = {
                    child_code: match.child_code,
                    full_name: match.full_name,
                    gender: match.gender || 'N/A',
                    date_of_birth: match.date_of_birth || 'N/A',
                    school_name: match.school_name || 'N/A',
                    grade: match.grade || 'N/A',
                    gn_division: match.gn_division || 'N/A',
                    district: match.district || 'N/A',
                    guardian: match.guardian_name || 'N/A',
                    phone: match.guardian_phone || 'N/A',
                    status: match.status || 'Active',
                    raw_payload: raw
                };
            }
        }

        const onlineUrl = getOnlineQrUrl(raw);
        if (onlineUrl) {
            childData = {
                child_code: 'URL-TOKEN',
                full_name: 'Online-only QR token',
                isOnlineOnly: true,
                online_url: onlineUrl,
                raw_payload: raw
            };
        }

        if (!childData) {
            childData = {
                child_code: 'CUSTOM-DATA',
                full_name: raw.substring(0, 40),
                gender: 'N/A',
                date_of_birth: 'N/A',
                school_name: 'N/A',
                grade: 'N/A',
                gn_division: 'N/A',
                district: 'N/A',
                guardian: 'N/A',
                phone: 'N/A',
                status: 'Active',
                raw_payload: raw
            };
        }

        lastDecodedPayload = childData;
        displayVerificationCard(childData);
        if (!childData.isOnlineOnly) {
            saveScanToLocalStorage(childData);
        }
    }

    function getOnlineQrUrl(value) {
        try {
            const url = new URL(value);
            if (url.origin !== window.location.origin || !/^\/qr\/scan\/[^/]+\/?$/.test(url.pathname)) {
                return null;
            }
            return url.href;
        } catch (e) {
            return null;
        }
    }

    function parsePlainTextChildQr(text) {
        const lines = text.split('\n');
        const data = {};
        lines.forEach(l => {
            const parts = l.split(':');
            if (parts.length >= 2) {
                const key = parts[0].trim().toLowerCase();
                const val = parts.slice(1).join(':').trim();
                data[key] = val;
            }
        });

        return {
            child_code: data.code || data['child code'] || 'N/A',
            full_name: data.name || data['child name'] || 'N/A',
            gender: data.gender || 'N/A',
            date_of_birth: data.dob || data['date of birth'] || 'N/A',
            school_name: data.school || data['school name'] || 'N/A',
            grade: data.grade || 'N/A',
            gn_division: data.gn || data['gn division'] || 'N/A',
            district: data.district || 'N/A',
            guardian: data.guardian || 'N/A',
            phone: data.phone || 'N/A',
            status: data.status || 'Active',
            raw_payload: text
        };
    }

    function displayVerificationCard(child) {
        document.getElementById('scanPlaceholderCard').style.display = 'none';
        const card = document.getElementById('verificationCard');
        card.style.display = 'block';
        const isOnlineOnly = child.isOnlineOnly === true;
        const notice = document.getElementById('scanNotice');
        const onlineLink = document.getElementById('scanOnlineLink');

        document.getElementById('scanResultTitle').textContent = isOnlineOnly ? 'Online-only QR code' : 'Child Verified';
        document.getElementById('scanResultBadge').textContent = isOnlineOnly ? 'Not verified offline' : '✓ Verified Offline';
        document.getElementById('scanResultBadge').classList.toggle('online-only-badge', isOnlineOnly);
        document.getElementById('scanInfoTable').style.display = isOnlineOnly ? 'none' : 'table';
        document.getElementById('copyDecodedButton').style.display = isOnlineOnly ? 'none' : 'inline-flex';
        notice.style.display = isOnlineOnly ? 'block' : 'none';
        onlineLink.style.display = isOnlineOnly ? 'inline-block' : 'none';

        if (isOnlineOnly) {
            document.getElementById('scanNoticeText').textContent = 'This QR contains only a server token, not the child details. It cannot be resolved offline. Generate and scan the QR from the Offline QR & ID Card Generator to carry the child data in the code.';
            onlineLink.href = child.online_url;
        }

        const now = new Date();
        document.getElementById('scanTimestamp').textContent = 'Scanned: ' + now.toLocaleTimeString() + ' (' + now.toLocaleDateString() + ')';
        document.getElementById('resInitial').textContent = (child.full_name || 'C').charAt(0).toUpperCase();
        document.getElementById('resFullName').textContent = child.full_name || '-';
        document.getElementById('resCode').textContent = child.child_code || '-';

        if (isOnlineOnly) {
            document.getElementById('scannerMessage').textContent = 'Online-only QR detected. Scan an offline-generated QR to view child details without a network.';
            return;
        }

        document.getElementById('resGender').textContent = child.gender || '-';
        document.getElementById('resDob').textContent = child.date_of_birth || '-';
        document.getElementById('resSchool').textContent = child.school_name || '-';
        document.getElementById('resGrade').textContent = child.grade || '-';
        document.getElementById('resGn').textContent = child.gn_division || '-';
        document.getElementById('resDistrict').textContent = child.district || '-';
        document.getElementById('resGuardian').textContent = child.guardian || '-';
        document.getElementById('resPhone').textContent = child.phone || '-';
        document.getElementById('resStatus').textContent = child.status || 'Active';

        document.getElementById('scannerMessage').textContent = '✅ Child ' + child.child_code + ' verified offline!';
    }

    function copyDecodedData() {
        if (!lastDecodedPayload) return;
        const text = `Child Code: ${lastDecodedPayload.child_code}\nName: ${lastDecodedPayload.full_name}\nSchool: ${lastDecodedPayload.school_name}\nGN: ${lastDecodedPayload.gn_division}\nGuardian: ${lastDecodedPayload.guardian}\nPhone: ${lastDecodedPayload.phone}`;
        navigator.clipboard.writeText(text).then(() => {
            alert('Child information copied to clipboard!');
        });
    }

    function getStoredScans() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY_SCANS) || '[]');
        } catch (e) {
            return [];
        }
    }

    function saveScanToLocalStorage(child) {
        const scans = getStoredScans();
        const entry = {
            id: Date.now(),
            timestamp: new Date().toLocaleString(),
            child_code: child.child_code,
            full_name: child.full_name,
            gender: child.gender,
            school: child.school_name,
            grade: child.grade,
            gn: child.gn_division,
            district: child.district,
            guardian: child.guardian,
            phone: child.phone,
            status: child.status
        };

        scans.unshift(entry);
        localStorage.setItem(STORAGE_KEY_SCANS, JSON.stringify(scans.slice(0, 500)));
        updateScansBadge();
    }

    function updateScansBadge() {
        const count = getStoredScans().length;
        const badge = document.getElementById('scansCountBadge');
        if (badge) badge.textContent = count;
    }

    function renderScansLogTable() {
        const scans = getStoredScans();
        const tbody = document.getElementById('logTableBody');
        const emptyState = document.getElementById('logEmptyState');
        const table = document.getElementById('logTable');

        tbody.innerHTML = '';

        if (scans.length === 0) {
            emptyState.style.display = 'block';
            table.style.display = 'none';
            return;
        }

        emptyState.style.display = 'none';
        table.style.display = 'table';

        scans.forEach((scan, idx) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${idx + 1}</strong></td>
                <td style="color:#64748b; font-size:12.5px;">${scan.timestamp}</td>
                <td><span style="font-weight:700; color:#246b43;">${scan.child_code}</span></td>
                <td><strong>${scan.full_name}</strong></td>
                <td>${scan.gender || '-'}</td>
                <td>${scan.school || '-'} (${scan.grade || '-'})</td>
                <td>${scan.gn || '-'}</td>
                <td><span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; background:#e0f0e3; color:#246b43;">${scan.status || 'Active'}</span></td>
            `;
            tbody.appendChild(tr);
        });
    }

    function exportLogCsv() {
        const scans = getStoredScans();
        if (scans.length === 0) {
            alert('No scans logged to export.');
            return;
        }

        const headers = ['Timestamp', 'Child Code', 'Full Name', 'Gender', 'School', 'Grade', 'GN Division', 'District', 'Guardian', 'Phone', 'Status'];
        const rows = scans.map(s => [
            `"${s.timestamp}"`,
            `"${s.child_code}"`,
            `"${s.full_name}"`,
            `"${s.gender || ''}"`,
            `"${s.school || ''}"`,
            `"${s.grade || ''}"`,
            `"${s.gn || ''}"`,
            `"${s.district || ''}"`,
            `"${s.guardian || ''}"`,
            `"${s.phone || ''}"`,
            `"${s.status || ''}"`
        ]);

        const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `KFHI_Field_Scans_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportLogJson() {
        const scans = getStoredScans();
        if (scans.length === 0) {
            alert('No scans logged to export.');
            return;
        }

        const jsonString = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(scans, null, 2));
        const link = document.createElement('a');
        link.setAttribute('href', jsonString);
        link.setAttribute('download', `KFHI_Field_Scans_${new Date().toISOString().slice(0, 10)}.json`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function clearLog() {
        if (confirm('Are you sure you want to clear all offline field scan logs?')) {
            localStorage.removeItem(STORAGE_KEY_SCANS);
            renderScansLogTable();
            updateScansBadge();
        }
    }

    function onChildSelected(childId) {
        if (!childId) return;

        const child = serverChildren.find(c => c.id == childId);
        if (!child) return;

        document.getElementById('genChildCode').value = child.child_code || '';
        document.getElementById('genFullName').value = child.full_name || '';
        document.getElementById('genGender').value = child.gender || 'Male';
        document.getElementById('genDob').value = child.date_of_birth || '';
        document.getElementById('genSchool').value = child.school_name || '';
        document.getElementById('genGrade').value = child.grade || '';
        document.getElementById('genGn').value = child.gn_division || '';
        document.getElementById('genDistrict').value = child.district || '';
        document.getElementById('genGuardian').value = child.guardian_name ? `${child.guardian_name} (${child.guardian_relation || 'Guardian'})` : '';
        document.getElementById('genPhone').value = child.guardian_phone || '';
        document.getElementById('genStatus').value = child.status || 'Active';

        const photoBox = document.getElementById('cardPhotoBox');
        if (child.profile_photo) {
            photoBox.innerHTML = `<img src="${child.profile_photo}" alt="Child Photo">`;
        } else {
            photoBox.innerHTML = `<div class="id-card-photo-placeholder">${(child.first_name || 'C').charAt(0).toUpperCase()}</div>`;
        }

        generateOfflineQrCard();
    }

    let debounceTimer = null;
    function triggerGenerate() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(generateOfflineQrCard, 200);
    }

    function buildOfflinePayload() {
        return {
            kfhi: 'offline_v1',
            code: document.getElementById('genChildCode').value.trim() || 'KFHI-OFFLINE',
            name: document.getElementById('genFullName').value.trim() || 'Sample Child',
            gender: document.getElementById('genGender').value,
            dob: document.getElementById('genDob').value,
            school: document.getElementById('genSchool').value.trim(),
            grade: document.getElementById('genGrade').value.trim(),
            gn: document.getElementById('genGn').value.trim(),
            district: document.getElementById('genDistrict').value.trim(),
            guardian: document.getElementById('genGuardian').value.trim(),
            phone: document.getElementById('genPhone').value.trim(),
            status: document.getElementById('genStatus').value
        };
    }

    // Stores the current QR image data URL for instant download
    let currentQrSvgDataUrl = null;

    function generateOfflineQrCard() {
        const payload = buildOfflinePayload();
        const jsonString = JSON.stringify(payload);

        document.getElementById('cardCode').textContent = payload.code;
        document.getElementById('cardName').textContent = payload.name;
        document.getElementById('cardGn').textContent = payload.gn || '-';
        document.getElementById('cardSchool').textContent = payload.school || '-';
        document.getElementById('cardGuardian').textContent = payload.guardian || '-';
        document.getElementById('cardInitials').textContent = (payload.name || 'C').charAt(0).toUpperCase();

        document.getElementById('rawJsonPreview').textContent = JSON.stringify(payload, null, 2);

        const target = document.getElementById('cardQrCodeTarget');

        target.innerHTML = '';
        currentQrSvgDataUrl = null;

        try {
            if (typeof window.QRCode !== 'function') {
                throw new Error('The local QR renderer did not load.');
            }

            new window.QRCode(target, {
                text: jsonString,
                width: 320,
                height: 320,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: window.QRCode.CorrectLevel.M
            });

            const canvas = target.querySelector('canvas');
            const image = target.querySelector('img');
            if (canvas) {
                currentQrSvgDataUrl = canvas.toDataURL('image/png');
            } else if (image && image.src) {
                currentQrSvgDataUrl = image.src;
            }
        } catch (e) {
            console.error('Local QR generation failed:', e);
            target.innerHTML = '<div style="font-size:8px;color:#b91c1c;text-align:center;">QR generation failed. Reload the page and try again.</div>';
        }
    }

    function toggleJsonPreview() {
        const box = document.getElementById('jsonPreviewContainer');
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    }

    function printIdCard() {
        if (!currentQrSvgDataUrl) {
            generateOfflineQrCard();
        }
        window.print();
    }

    async function downloadIdCardImage() {
        const card = document.getElementById('printableIdCard');
        if (!card) return;

        try {
            const canvas = await html2canvas(card, {
                scale: 4,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false
            });

            const code = document.getElementById('genChildCode').value.trim() || 'child';
            const link = document.createElement('a');
            link.download = `${code}_offline_id_card.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        } catch (e) {
            console.error(e);
            alert('Unable to generate image. Please try again.');
        }
    }

    function downloadQrOnly() {
        if (!currentQrSvgDataUrl) {
            generateOfflineQrCard();
        }

        if (!currentQrSvgDataUrl) {
            alert('Please select or enter child details first.');
            return;
        }

        const rawCode = document.getElementById('genChildCode').value.trim() || 'child';
        const code = rawCode.replace(/[^a-zA-Z0-9_-]/g, '_');

        const isSvg = currentQrSvgDataUrl.startsWith('data:image/svg');
        const link = document.createElement('a');
        link.download = `${code}_offline_qr.${isSvg ? 'svg' : 'png'}`;
        link.href = currentQrSvgDataUrl;
        link.click();
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateScansBadge();
        switchTab(activeTool);

        const childSelect = document.getElementById('childSelect');
        if (childSelect && childSelect.options.length > 1) {
            childSelect.selectedIndex = 1;
            onChildSelected(childSelect.value);
        } else {
            document.getElementById('genChildCode').value = 'KFHI-2026-0001';
            document.getElementById('genFullName').value = 'Kamal Perera';
            document.getElementById('genGn').value = 'GN-12';
            document.getElementById('genSchool').value = "St. Anthony's College";
            document.getElementById('genGuardian').value = 'Sunil Perera';
            generateOfflineQrCard();
        }
    });
</script>

</body>
</html>
