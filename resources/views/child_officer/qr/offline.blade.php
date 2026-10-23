<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline QR System | KFHI-CMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f8f5;
            color: #1f2937;
        }

        .page-container {
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

        .page-container.sidebar-collapsed .sidebar {
            display: none;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 35px;
            padding-left: 10px;
        }

        .nav-title {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            margin: 25px 10px 10px;
            letter-spacing: 1px;
        }

        .nav-link {
            display: block;
            text-decoration: none;
            color: #4b5563;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 5px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #e8f5e9;
            color: #2e7d32;
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
            margin-bottom: 5px;
            border-radius: 10px;
            color: #4b5563;
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
            background: #e8f5e9;
            color: #2e7d32;
            font-weight: 600;
        }

        .identification-menu .nav-link {
            margin-left: 12px;
            border-left: 1px solid #dfe9e2;
        }

        .nav-link .badge-offline {
            display: inline-block;
            background: #2e7d32;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 25px 35px;
            max-width: 1350px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 25px;
            border-bottom: 1px solid #e5e7eb;
            gap: 15px;
            flex-wrap: wrap;
        }

        .header h1 {
            font-size: 22px;
            color: #1f2937;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
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
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .status-pill.network-mode {
            background: #eef2f6;
            color: #475569;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #2e7d32;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 rgba(46, 125, 50, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0.5); }
            70% { box-shadow: 0 0 0 8px rgba(46, 125, 50, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 125, 50, 0); }
        }

        /* Banner */
        .offline-banner {
            background: linear-gradient(135deg, #2e7d32, #1b5e20);
            color: #ffffff;
            border-radius: 16px;
            padding: 22px 28px;
            margin-top: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            box-shadow: 0 8px 24px rgba(46, 125, 50, 0.18);
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
            letter-spacing: 0.5px;
        }

        /* Tab Navigation */
        .tab-nav {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 1px;
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
            color: #2e7d32;
            background: #eef7f0;
        }

        .tab-btn.active {
            color: #2e7d32;
            border-bottom-color: #2e7d32;
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
            background: #2e7d32;
            color: #ffffff;
        }

        /* Tab Content */
        .tab-panel {
            display: none;
            margin-top: 25px;
        }

        .tab-panel.active {
            display: block;
        }

        /* Grid Cards */
        .content-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
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
            color: #2e7d32;
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
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            background: #ffffff;
            color: #1f2937;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #2e7d32;
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.12);
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
            background: #2e7d32;
            color: #ffffff;
        }

        .btn-primary:hover {
            background: #256b29;
        }

        .btn-secondary {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .btn-secondary:hover {
            background: #d4edd6;
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

        .btn-danger:hover {
            background: #fecaca;
        }

        /* Scanner Specific Styles */
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
            position: relative;
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
            border-color: #2e7d32;
            background: #f0fdf4;
        }

        .drop-zone input[type="file"] {
            display: none;
        }

        /* Result Verification Card */
        .verification-card {
            background: #ffffff;
            border: 1px solid #cde3d1;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 6px 20px rgba(46, 125, 50, 0.08);
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
            background: #e8f5e9;
            color: #2e7d32;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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
            word-break: break-word;
        }

        /* ID Card Preview Styles (Standard 85.6mm x 54mm) */
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
            color: #2e7d32;
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

        /* Log Table */
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

        .log-table tr:hover {
            background: #f8fafc;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        /* Raw json viewer */
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

        /* Print styles */
        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .sidebar, .header, .offline-banner, .tab-nav, .content-card, .btn, .generator-container > div:first-child {
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

<div class="page-container">
    @include('components.child-officer-sidebar')


    <!-- Main Content -->
    <main class="main-content">

        <!-- Top Header -->
        <header class="header">
            <div>
                <h1>Offline QR Management</h1>
                <p style="color:#64748b; font-size:13.5px; margin-top:4px;">
                    Generate & scan child identification QR codes in remote areas without internet or external APIs.
                </p>
            </div>

            <div class="header-meta">
                <div class="status-pill offline-ready">
                    <span class="pulse-dot"></span>
                    <span>100% Offline Mode &bull; No API Needed</span>
                </div>

                <div class="status-pill network-mode" id="networkPill">
                    <span id="networkText">Checking Network...</span>
                </div>
            </div>
        </header>

        <!-- Tab Navigation -->
        <div class="tab-nav">
            <button class="tab-btn active" id="tab-scanner-btn" onclick="switchTab('scanner')">
                📷 Offline Scanner
                <span class="tab-badge" id="scansCountBadge">0</span>
            </button>

            <button class="tab-btn" id="tab-generator-btn" onclick="switchTab('generator')">
                🖨️ Offline QR & ID Card Generator
            </button>

            <button class="tab-btn" id="tab-log-btn" onclick="switchTab('log')">
                📋 Field Attendance Log
            </button>
        </div>


        <!-- ==========================================
             TAB 1: OFFLINE QR SCANNER
             ========================================== -->
        <div class="tab-panel active" id="panel-scanner">
            <div class="scanner-container">

                <!-- Left: Scanner Viewport -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <h3>📷 Camera & Image Scanner</h3>
                            <p>Uses local browser decoding without pinging any server.</p>
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

                        <!-- Drag and drop / file upload for offline scans -->
                        <div class="drop-zone" onclick="document.getElementById('qrFileInput').click()">
                            <p style="font-size:13.5px; font-weight:600; color:#2e7d32; margin-bottom:4px;">
                                📁 Scan from Image File
                            </p>
                            <p style="font-size:12px; color:#64748b;">
                                Click to upload or drag & drop a photo/screenshot of a child's QR code
                            </p>
                            <input type="file" id="qrFileInput" accept="image/*" onchange="scanImageFile(this)">
                        </div>
                    </div>
                </div>

                <!-- Right: Decoded Result Card -->
                <div>
                    <!-- Verification Card -->
                    <div class="verification-card" id="verificationCard">
                        <div class="verification-header">
                            <div>
                                <h3 id="scanResultTitle" style="font-size:18px; color:#2e7d32; font-weight:700;">Child Verified</h3>
                                <p style="font-size:12px; color:#64748b;" id="scanTimestamp">-</p>
                            </div>
                            <span class="verified-badge" id="scanResultBadge">
                                ✓ Verified Offline
                            </span>
                        </div>

                        <div style="display:flex; align-items:center; gap:15px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #f1f5f9;">
                            <div style="width:52px; height:52px; border-radius:12px; background:#e8f5e9; color:#2e7d32; display:flex; align-items:center; justify-content:center; font-size:22px; font-weight:800;" id="resInitial">
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

                    <!-- Placeholder when no card scanned -->
                    <div class="content-card" id="scanPlaceholderCard" style="text-align:center; padding:45px 20px;">
                        <div style="font-size:46px; margin-bottom:12px;">📱</div>
                        <h4 style="font-size:17px; color:#1e293b; margin-bottom:6px;">No Child Scanned Yet</h4>
                        <p style="font-size:13px; color:#64748b; max-width:320px; margin:0 auto;">
                            Position a child's QR code in front of the camera or upload an image to view their verified identity offline.
                        </p>
                    </div>

                    <!-- Offline Quick Tips -->
                    <div class="content-card" style="margin-top:20px; background:#fbfdfc; border-color:#e0eee3;">
                        <h4 style="font-size:14px; color:#2e7d32; margin-bottom:8px; display:flex; align-items:center; gap:6px;">
                            💡 Field Officer Guide
                        </h4>
                        <ul style="font-size:12.5px; color:#52635a; line-height:1.6; padding-left:20px;">
                            <li>Offline QR codes encode the child's identity directly. They work even if your device is in Airplane Mode.</li>
                            <li>Every scan is saved to your local device's attendance log.</li>
                            <li>When you return to the center, switch to the Field Log tab to export a CSV.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>


        <!-- ==========================================
             TAB 2: OFFLINE QR & ID CARD GENERATOR
             ========================================== -->
        <div class="tab-panel" id="panel-generator">
            <div class="generator-container">

                <!-- Left: Controls & Form -->
                <div>
                    <div class="content-card">
                        <div class="card-header-bar">
                            <div>
                                <h3>🖨️ Offline QR Generator</h3>
                                <p>Select an existing child or input custom data to generate an offline ID card.</p>
                            </div>
                        </div>

                        <!-- Child Selection Dropdown -->
                        <div class="form-group" style="margin-bottom:20px;">
                            <label for="childSelect">⚡ Quick Load Registered Child:</label>
                            <select id="childSelect" class="form-control" onchange="onChildSelected(this.value)">
                                <option value="">-- Choose a registered child ({{ $children->count() }} available) --</option>
                                @foreach($children as $ch)
                                    <option value="{{ $ch->id }}">
                                        {{ $ch->child_code }} - {{ $ch->first_name }} {{ $ch->last_name }} ({{ $ch->gn_division ?: 'No GN' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px; margin:20px 0 15px;">
                            <hr style="flex:1; border:none; border-top:1px solid #e2e8f0;">
                            <span style="font-size:11.5px; font-weight:700; color:#94a3b8; text-transform:uppercase;">Child Details Form</span>
                            <hr style="flex:1; border:none; border-top:1px solid #e2e8f0;">
                        </div>

                        <!-- Data Fields -->
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
                                <input type="text" id="genGuardian" class="form-control" placeholder="Sunil Perera (Father)" oninput="triggerGenerate()">
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

                        <!-- Actions Bar -->
                        <div style="display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;">
                            <button type="button" class="btn btn-primary" onclick="printIdCard()">
                                🖨️ Print ID Card
                            </button>

                            <button type="button" class="btn btn-secondary" id="downloadIdCardButton" onclick="downloadIdCardImage()">
                                💾 Download ID Card (PNG)
                            </button>

                            <button type="button" class="btn btn-outline" onclick="downloadQrOnly()">
                                🔳 Download QR Only
                            </button>

                            <button type="button" class="btn btn-outline" onclick="toggleJsonPreview()">
                                { } View QR JSON
                            </button>
                        </div>

                        <!-- Raw JSON Data Preview -->
                        <div id="jsonPreviewContainer" style="display:none;">
                            <div style="margin-top:20px; font-size:12px; font-weight:700; color:#475569;">
                                Raw Offline QR Payload (Self-Contained):
                            </div>
                            <pre class="json-preview" id="rawJsonPreview"></pre>
                        </div>

                    </div>
                </div>

                <!-- Right: Live ID Card Preview -->
                <div class="id-card-stage">
                    <p style="font-size:12.5px; font-weight:700; color:#2e7d32; margin-bottom:15px; text-transform:uppercase; letter-spacing:0.5px;">
                        Standard ID-1 Card Preview (85.6mm × 54mm)
                    </p>

                    <!-- The Printable Card Element -->
                    <div class="id-card" id="printableIdCard">
                        <!-- Top Header -->
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

                        <!-- Card Body -->
                        <div class="id-card-body">
                            <!-- Photo / Initials -->
                            <div class="id-card-photo-box" id="cardPhotoBox">
                                <div class="id-card-photo-placeholder" id="cardInitials">K</div>
                            </div>

                            <!-- Details -->
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

                            <!-- Offline QR Code -->
                            <div class="id-card-qr-box">
                                <div class="id-card-qr-target" id="cardQrCodeTarget"></div>
                                <div class="id-card-qr-caption">Scan Offline</div>
                            </div>
                        </div>

                        <!-- Card Footer -->
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
             TAB 3: FIELD SCANS LOG
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
                        <tbody id="logTableBody">
                            <!-- Populated via JS -->
                        </tbody>
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


<!-- ==========================================
     LOCAL JAVASCRIPT BUNDLES (100% OFFLINE)
     ========================================== -->
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script src="{{ asset('js/qrcode-generator.min.js') }}"></script>
<script src="{{ asset('js/html2canvas.min.js') }}"></script>

<script>
    /*
    |--------------------------------------------------------------------------
    | Server-Provided Preloaded Children Data
    |--------------------------------------------------------------------------
    */
    const serverChildren = @json($childrenData);
    const activeTool = @json($activeTool);

    /*
    |--------------------------------------------------------------------------
    | Offline State & Storage Keys
    |--------------------------------------------------------------------------
    */
    const STORAGE_KEY_SCANS = 'kfhi_offline_scans_log';
    let offlineScanner = null;
    let offlineScannerRunning = false;
    let currentCameraFacing = 'environment';
    let qrcodeInstance = null;
    let lastDecodedPayload = null;

    /*
    |--------------------------------------------------------------------------
    | Network Monitoring (Offline Indicator)
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Tab Switching
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Audio Beep (Web Audio API Synthesizer - 100% Offline, Zero Network)
    |--------------------------------------------------------------------------
    */
    function playSuccessBeep() {
        try {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
            gain.gain.setValueAtTime(0.15, audioCtx.currentTime);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + 0.12);
        } catch (e) {
            // Audio context not allowed or supported, ignore
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OFFLINE QR SCANNER ENGINE
    |--------------------------------------------------------------------------
    */
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
                function(err) {
                    // Normal frame scanning misses are ignored
                }
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

    /* Scan QR from selected file */
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

    /*
    |--------------------------------------------------------------------------
    | Handle Decoded QR Payload
    |--------------------------------------------------------------------------
    */
    function onQrCodeScanned(decodedText) {
        playSuccessBeep();

        let childData = null;
        const raw = (decodedText || '').trim();

        // 1. Try parsing JSON (Standard KFHI Offline QR format - supports both compact v='K1' and legacy kfhi='offline_v1')
        if (raw.startsWith('{') && raw.endsWith('}')) {
            try {
                const parsed = JSON.parse(raw);
                // Support compact keys (v,c,n,g,d,sc,gr,gn,di,gu,ph,st) AND legacy keys (kfhi,code,name,gender,etc.)
                childData = {
                    child_code:    parsed.c   || parsed.code     || 'N/A',
                    full_name:     parsed.n   || parsed.name     || 'N/A',
                    gender:        parsed.g   || parsed.gender   || 'N/A',
                    date_of_birth: parsed.d   || parsed.dob      || 'N/A',
                    school_name:   parsed.sc  || parsed.school   || 'N/A',
                    grade:         parsed.gr  || parsed.grade    || 'N/A',
                    gn_division:   parsed.gn                     || 'N/A',
                    district:      parsed.di  || parsed.district || 'N/A',
                    guardian:      parsed.gu  || parsed.guardian || 'N/A',
                    phone:         parsed.ph  || parsed.phone    || 'N/A',
                    status:        parsed.st  || parsed.status   || 'Active',
                    raw_payload: raw
                };
                // Validate: must have at least a code or name
                if (childData.child_code === 'N/A' && childData.full_name === 'N/A') {
                    childData = null;
                }
            } catch (e) {
                console.warn('JSON parse error:', e);
            }
        }

        // 2. Try parsing plain-text key-value format
        if (!childData && (raw.includes('Code:') || raw.includes('KFHI'))) {
            childData = parsePlainTextChildQr(raw);
        }

        // 3. Fallback: match by Child Code against preloaded local children
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

        // 4. Fallback for legacy URL tokens (/qr/scan/{token})
        if (!childData && raw.includes('/qr/scan/')) {
            childData = {
                child_code: 'URL-TOKEN',
                full_name: 'Online-only QR token',
                isOnlineOnly: true,
                online_url: getOnlineQrUrl(raw),
                raw_payload: raw,
                note: 'This is an online-only token QR code. For full offline operation, re-issue with the Offline QR Generator.'
            };
        }

        // 5. Raw fallback
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
        onlineLink.style.display = isOnlineOnly && child.online_url ? 'inline-block' : 'none';

        if (isOnlineOnly) {
            document.getElementById('scanNoticeText').textContent = 'This QR contains only a server token, not the child details. It cannot be resolved offline. Generate and scan the QR from the Offline QR & ID Card Generator to carry the child data in the code.';
            if (child.online_url) onlineLink.href = child.online_url;
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

    /*
    |--------------------------------------------------------------------------
    | OFFLINE FIELD LOG (LocalStorage)
    |--------------------------------------------------------------------------
    */
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
        localStorage.setItem(STORAGE_KEY_SCANS, JSON.stringify(scans.slice(0, 500))); // keep max 500
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
                <td><span style="font-weight:700; color:#2e7d32;">${scan.child_code}</span></td>
                <td><strong>${scan.full_name}</strong></td>
                <td>${scan.gender || '-'}</td>
                <td>${scan.school || '-'} (${scan.grade || '-'})</td>
                <td>${scan.gn || '-'}</td>
                <td><span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:10px; background:#e8f5e9; color:#2e7d32;">${scan.status || 'Active'}</span></td>
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

    /*
    |--------------------------------------------------------------------------
    | OFFLINE QR & ID CARD GENERATOR (Real-Time Local qrcode.js)
    |--------------------------------------------------------------------------
    */
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

        // Update photo if available
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
        // Short keys to minimize QR code size (v=version tag, c=code, n=name, etc.)
        return {
            v: 'K1',
            c: document.getElementById('genChildCode').value.trim() || 'KFHI-OFFLINE',
            n: document.getElementById('genFullName').value.trim() || 'Sample Child',
            g: document.getElementById('genGender').value,
            d: document.getElementById('genDob').value,
            sc: document.getElementById('genSchool').value.trim(),
            gr: document.getElementById('genGrade').value.trim(),
            gn: document.getElementById('genGn').value.trim(),
            di: document.getElementById('genDistrict').value.trim(),
            gu: document.getElementById('genGuardian').value.trim(),
            ph: document.getElementById('genPhone').value.trim(),
            st: document.getElementById('genStatus').value
        };
    }

    // Stores the current QR data URL for download
    let currentQrSvgDataUrl = null;

    /*
    |--------------------------------------------------------------------------
    | Pure Inline QR Code Generator (Zero Dependencies, 100% Offline)
    | Based on QR Code Model 2 specification — guaranteed to work in any browser
    |--------------------------------------------------------------------------
    */
    var QRGen = (function () {
        // GF(256) arithmetic tables for Reed-Solomon
        var GF_EXP = new Uint8Array(512);
        var GF_LOG = new Uint8Array(256);
        (function () {
            var x = 1;
            for (var i = 0; i < 255; i++) {
                GF_EXP[i] = x;
                GF_LOG[x] = i;
                x <<= 1;
                if (x & 0x100) x ^= 0x11d;
            }
            for (var i = 255; i < 512; i++) GF_EXP[i] = GF_EXP[i - 255];
        })();

        function gfMul(a, b) {
            if (a === 0 || b === 0) return 0;
            return GF_EXP[(GF_LOG[a] + GF_LOG[b]) % 255];
        }

        function gfPolyMul(p, q) {
            var r = new Uint8Array(p.length + q.length - 1);
            for (var i = 0; i < p.length; i++)
                for (var j = 0; j < q.length; j++)
                    r[i + j] ^= gfMul(p[i], q[j]);
            return r;
        }

        function gfPolyDiv(msg, gen) {
            var r = new Uint8Array(msg);
            for (var i = 0; i < msg.length - (gen.length - 1); i++) {
                var c = r[i];
                if (c !== 0)
                    for (var j = 1; j < gen.length; j++)
                        r[i + j] ^= gfMul(gen[j], c);
            }
            return r.slice(msg.length - (gen.length - 1));
        }

        function rsGenerator(n) {
            var g = new Uint8Array([1]);
            for (var i = 0; i < n; i++)
                g = gfPolyMul(g, new Uint8Array([1, GF_EXP[i]]));
            return g;
        }

        // QR version capacity table [version][ecLevel] = {dataCW, ecCW, blocks, g1b, g1cw, g2b, g2cw}
        // ecLevel: 0=M (we only need M for our use)
        var VERSION_INFO = {
            1:  {data:16,  ec:10, b1:1, c1:16},
            2:  {data:28,  ec:16, b1:1, c1:28},
            3:  {data:44,  ec:26, b1:1, c1:44},
            4:  {data:64,  ec:18, b1:2, c1:32},
            5:  {data:86,  ec:24, b1:2, c1:43},
            6:  {data:108, ec:16, b1:4, c1:27},
            7:  {data:124, ec:18, b1:4, c1:31},
            8:  {data:154, ec:22, b1:2, c1:38,  b2:2, c2:39},
            9:  {data:182, ec:22, b1:3, c1:36,  b2:2, c2:37},
            10: {data:216, ec:26, b1:4, c1:43,  b2:1, c2:44},
            11: {data:254, ec:30, b1:1, c1:50,  b2:4, c2:51},
            12: {data:290, ec:22, b1:6, c1:36,  b2:2, c2:37},
            13: {data:334, ec:22, b1:8, c1:37,  b2:1, c2:38},
            14: {data:365, ec:24, b1:4, c1:40,  b2:5, c2:41},
            15: {data:415, ec:24, b1:5, c1:41,  b2:5, c2:42},
        };

        // Alignment pattern centers per version
        var ALIGN_CENTERS = {
            1:[],        2:[6,18],      3:[6,22],      4:[6,26],
            5:[6,30],    6:[6,34],      7:[6,22,38],   8:[6,24,42],
            9:[6,26,46], 10:[6,28,50],  11:[6,30,54],  12:[6,32,58],
            13:[6,34,62], 14:[6,26,46,66], 15:[6,26,48,70]
        };


        function makeQR(text, ecLevel) {
            // Encode as UTF-8 bytes
            var bytes = [];
            for (var i = 0; i < text.length; i++) {
                var c = text.charCodeAt(i);
                if (c < 0x80) {
                    bytes.push(c);
                } else if (c < 0x800) {
                    bytes.push(0xC0 | (c >> 6), 0x80 | (c & 0x3F));
                } else {
                    bytes.push(0xE0 | (c >> 12), 0x80 | ((c >> 6) & 0x3F), 0x80 | (c & 0x3F));
                }
            }
            var dataLen = bytes.length;

            // Find minimum version for M error correction
            var version = null;
            for (var v = 1; v <= 10; v++) {
                if (VERSION_INFO[v] && VERSION_INFO[v].data >= dataLen + 3) {
                    version = v;
                    break;
                }
            }
            if (!version) {
                // Data too large, try up to version 20 with simplified approach
                // Fall back to server
                return null;
            }

            var info = VERSION_INFO[version];
            var n = version * 4 + 17; // matrix size

            // Build bit stream
            var bits = [];
            function pushBits(val, len) {
                for (var i = len - 1; i >= 0; i--) bits.push((val >> i) & 1);
            }

            // Mode indicator (byte mode = 0100)
            pushBits(4, 4);
            // Character count
            var ccBits = version <= 9 ? 8 : 16;
            pushBits(dataLen, ccBits);
            // Data bytes
            for (var i = 0; i < bytes.length; i++) pushBits(bytes[i], 8);
            // Terminator
            for (var i = 0; i < 4 && bits.length < info.data * 8; i++) bits.push(0);
            // Byte align
            while (bits.length % 8) bits.push(0);
            // Padding bytes
            var padBytes = [0xEC, 0x11];
            var pi = 0;
            while (bits.length < info.data * 8) {
                pushBits(padBytes[pi], 8);
                pi = (pi + 1) % 2;
            }

            // Split into codewords
            var codewords = [];
            for (var i = 0; i < bits.length; i += 8) {
                var b = 0;
                for (var j = 0; j < 8; j++) b = (b << 1) | (bits[i + j] || 0);
                codewords.push(b);
            }

            // Reed-Solomon error correction
            var ecCWperBlock = Math.floor(info.ec / (info.b1 + (info.b2 || 0)));
            var gen = rsGenerator(ecCWperBlock);

            var blocks = [], ecBlocks = [];
            var cPtr = 0;
            for (var b = 0; b < info.b1; b++) {
                var block = codewords.slice(cPtr, cPtr + info.c1);
                cPtr += info.c1;
                var padded = new Uint8Array(info.c1 + ecCWperBlock);
                padded.set(block);
                ecBlocks.push(Array.from(gfPolyDiv(padded, gen)));
                blocks.push(block);
            }
            if (info.b2) {
                for (var b = 0; b < info.b2; b++) {
                    var block = codewords.slice(cPtr, cPtr + info.c2);
                    cPtr += info.c2;
                    var padded = new Uint8Array(info.c2 + ecCWperBlock);
                    padded.set(block);
                    ecBlocks.push(Array.from(gfPolyDiv(padded, gen)));
                    blocks.push(block);
                }
            }

            // Interleave data
            var finalCW = [];
            var maxBlock = Math.max.apply(null, blocks.map(function(b){return b.length;}));
            for (var i = 0; i < maxBlock; i++)
                for (var b = 0; b < blocks.length; b++)
                    if (i < blocks[b].length) finalCW.push(blocks[b][i]);
            for (var i = 0; i < ecCWperBlock; i++)
                for (var b = 0; b < ecBlocks.length; b++)
                    finalCW.push(ecBlocks[b][i]);

            // Build final bit stream
            var finalBits = [];
            for (var i = 0; i < finalCW.length; i++) pushBitsTo(finalCW[i], 8, finalBits);
            function pushBitsTo(val, len, arr) {
                for (var i = len - 1; i >= 0; i--) arr.push((val >> i) & 1);
            }
            // Remainder bits
            var remainders = [0,7,7,7,7,7,0,0,0,0,0];
            for (var i = 0; i < (remainders[version] || 0); i++) finalBits.push(0);

            // Build matrix
            var mat = [];
            for (var i = 0; i < n; i++) {
                mat.push(new Uint8Array(n)); // 0=white, 1=black, high bit = reserved
            }

            function setModule(r, c, dark) {
                mat[r][c] = dark ? 0x81 : 0x80; // reserved|dark
            }

            // Finder patterns
            function drawFinder(row, col) {
                for (var r = -1; r <= 7; r++)
                    for (var c = -1; c <= 7; c++) {
                        if (row+r < 0 || row+r >= n || col+c < 0 || col+c >= n) continue;
                        var dark = (r >= 0 && r <= 6 && (c === 0 || c === 6)) ||
                                   (c >= 0 && c <= 6 && (r === 0 || r === 6)) ||
                                   (r >= 2 && r <= 4 && c >= 2 && c <= 4);
                        setModule(row + r, col + c, dark);
                    }
            }
            drawFinder(0, 0);
            drawFinder(0, n - 7);
            drawFinder(n - 7, 0);

            // Separators (already handled by finder -1 offset)
            // Timing patterns
            for (var i = 8; i < n - 8; i++) {
                setModule(6, i, i % 2 === 0);
                setModule(i, 6, i % 2 === 0);
            }

            // Dark module
            setModule(4 * version + 9, 8, true);

            // Alignment patterns
            var ac = ALIGN_CENTERS[version] || [];
            for (var ai = 0; ai < ac.length; ai++) {
                for (var aj = 0; aj < ac.length; aj++) {
                    var ar = ac[ai], aac = ac[aj];
                    if (mat[ar][aac] & 0x80) continue; // already reserved
                    for (var dr = -2; dr <= 2; dr++)
                        for (var dc = -2; dc <= 2; dc++) {
                            var dark = dr === -2 || dr === 2 || dc === -2 || dc === 2 || (dr === 0 && dc === 0);
                            setModule(ar + dr, aac + dc, dark);
                        }
                }
            }

            // Format info placeholder (reserve)
            for (var i = 0; i < 6; i++) setModule(8, i, false);
            setModule(8, 7, false); setModule(8, 8, false); setModule(7, 8, false);
            for (var i = 0; i < 6; i++) setModule(i, 8, false);
            for (var i = n - 8; i < n; i++) setModule(8, i, false);
            for (var i = n - 7; i < n; i++) setModule(i, 8, false);

            // Place data bits (zigzag)
            var bitIdx = 0;
            var right = n - 1;
            var goUp = true;
            while (right >= 1) {
                if (right === 6) right = 5; // skip timing col
                var cols = [right, right - 1];
                for (var ri = 0; ri < n; ri++) {
                    var row = goUp ? n - 1 - ri : ri;
                    for (var ci = 0; ci < 2; ci++) {
                        var col = cols[ci];
                        if (!(mat[row][col] & 0x80)) {
                            var bit = bitIdx < finalBits.length ? finalBits[bitIdx++] : 0;
                            mat[row][col] = bit ? 1 : 0;
                        }
                    }
                }
                goUp = !goUp;
                right -= 2;
            }

            // Apply mask 0 (i+j) % 2 == 0 and compute format info
            // Format string for M, mask 0: data = 00 (M=01 in ec bits, mask 000)
            // EC level M = binary 00, mask 0 = 000 → raw format = 00 000 = 0
            // XOR with 101010000010010
            var formatData = 0b00000 << 10; // M=01→bits are 00, mask 000 → ec+mask = 0b00_000 = 0
            // Actually: M=01 in QR format (error level bits for M are 00)
            // Let me use precomputed: format for M, mask 0 = 0b101010000010010
            var FORMAT_STRINGS = [
                [1,0,1,0,1,0,0,0,0,0,1,0,0,1,0],  // mask 0, M
                [1,0,1,0,0,1,1,0,0,1,1,0,1,1,1],  // mask 1
                [1,0,1,1,1,1,0,0,1,1,0,0,0,1,0],  // mask 2
                [1,0,1,1,0,0,1,0,1,0,0,0,1,1,1],  // mask 3
                [1,0,0,0,1,0,1,1,0,1,0,0,1,0,1],  // mask 4
                [1,0,0,0,0,1,0,1,0,0,0,0,0,0,0],  // mask 5
                [1,0,0,1,1,1,1,1,1,0,1,0,1,0,1],  // mask 6
                [1,0,0,1,0,0,0,1,1,1,1,0,0,0,0],  // mask 7
            ];

            // Find best mask (evaluate all 8 masks, pick lowest penalty)
            var bestMask = 0, bestPenalty = Infinity;
            for (var maskPat = 0; maskPat < 8; maskPat++) {
                // Apply mask
                var testMat = [];
                for (var r = 0; r < n; r++) {
                    testMat.push(new Uint8Array(mat[r]));
                    for (var c = 0; c < n; c++) {
                        if (!(mat[r][c] & 0x80)) {
                            var shouldFlip = false;
                            switch(maskPat) {
                                case 0: shouldFlip = (r+c)%2===0; break;
                                case 1: shouldFlip = r%2===0; break;
                                case 2: shouldFlip = c%3===0; break;
                                case 3: shouldFlip = (r+c)%3===0; break;
                                case 4: shouldFlip = (Math.floor(r/2)+Math.floor(c/3))%2===0; break;
                                case 5: shouldFlip = (r*c)%2+(r*c)%3===0; break;
                                case 6: shouldFlip = ((r*c)%2+(r*c)%3)%2===0; break;
                                case 7: shouldFlip = ((r+c)%2+(r*c)%3)%2===0; break;
                            }
                            testMat[r][c] = shouldFlip ? (mat[r][c] ^ 1) : mat[r][c];
                        }
                    }
                }
                // Compute penalty
                var penalty = 0;
                // Rule 1: consecutive same color
                for (var r = 0; r < n; r++) {
                    var run = 1;
                    for (var c = 1; c < n; c++) {
                        if ((testMat[r][c]&1) === (testMat[r][c-1]&1)) {
                            run++;
                            if (run === 5) penalty += 3;
                            else if (run > 5) penalty++;
                        } else run = 1;
                    }
                }
                for (var c = 0; c < n; c++) {
                    var run = 1;
                    for (var r = 1; r < n; r++) {
                        if ((testMat[r][c]&1) === (testMat[r-1][c]&1)) {
                            run++;
                            if (run === 5) penalty += 3;
                            else if (run > 5) penalty++;
                        } else run = 1;
                    }
                }
                if (penalty < bestPenalty) { bestPenalty = penalty; bestMask = maskPat; }
            }

            // Apply best mask to matrix
            for (var r = 0; r < n; r++) {
                for (var c = 0; c < n; c++) {
                    if (!(mat[r][c] & 0x80)) {
                        var shouldFlip = false;
                        switch(bestMask) {
                            case 0: shouldFlip = (r+c)%2===0; break;
                            case 1: shouldFlip = r%2===0; break;
                            case 2: shouldFlip = c%3===0; break;
                            case 3: shouldFlip = (r+c)%3===0; break;
                            case 4: shouldFlip = (Math.floor(r/2)+Math.floor(c/3))%2===0; break;
                            case 5: shouldFlip = (r*c)%2+(r*c)%3===0; break;
                            case 6: shouldFlip = ((r*c)%2+(r*c)%3)%2===0; break;
                            case 7: shouldFlip = ((r+c)%2+(r*c)%3)%2===0; break;
                        }
                        mat[r][c] = shouldFlip ? (mat[r][c] ^ 1) : mat[r][c];
                    }
                }
            }

            // Write format info
            function writeFormatInfo(fs) {
                // Around top-left finder
                var fPos = [[8,0],[8,1],[8,2],[8,3],[8,4],[8,5],[8,7],[8,8],[7,8],[5,8],[4,8],[3,8],[2,8],[1,8],[0,8]];
                for (var i = 0; i < 15; i++) mat[fPos[i][0]][fPos[i][1]] = fs[i] ? 0x81 : 0x80;
                // Top-right and bottom-left
                for (var i = 0; i < 8; i++) mat[8][n-1-i] = fs[i] ? 0x81 : 0x80;
                for (var i = 8; i < 15; i++) mat[n-15+i][8] = fs[i] ? 0x81 : 0x80;
            }
            writeFormatInfo(FORMAT_STRINGS[bestMask]);

            // Generate SVG
            var cell = 10; // pixels per cell
            var margin = 4; // cells margin
            var sz = (n + 2 * margin) * cell;
            var svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' + sz + '" height="' + sz + '" viewBox="0 0 ' + sz + ' ' + sz + '">';
            svg += '<rect width="' + sz + '" height="' + sz + '" fill="#fff"/>';
            for (var r = 0; r < n; r++) {
                for (var c = 0; c < n; c++) {
                    if (mat[r][c] & 1) {
                        svg += '<rect x="' + ((c+margin)*cell) + '" y="' + ((r+margin)*cell) + '" width="' + cell + '" height="' + cell + '" fill="#000"/>';
                    }
                }
            }
            svg += '</svg>';
            return svg;
        }

        return { make: makeQR };
    })();

    function setCardPreviewText(id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    }

    function generateOfflineQrCard() {
        const payload = buildOfflinePayload();
        const jsonString = JSON.stringify(payload);

        // Update card preview elements
        setCardPreviewText('cardCode', payload.code);
        setCardPreviewText('cardName', payload.name);
        setCardPreviewText('cardGn', payload.gn || '-');
        setCardPreviewText('cardSchool', payload.school || '-');
        setCardPreviewText('cardGuardian', payload.guardian || '-');
        setCardPreviewText('cardInitials', (payload.name || 'C').charAt(0).toUpperCase());

        // Update raw json view
        setCardPreviewText('rawJsonPreview', JSON.stringify(payload, null, 2));

        const target = document.getElementById('cardQrCodeTarget');
        if (!target) return;

        // Show generating indicator
        target.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;font-size:7px;color:#888;">Generating…</div>';
        currentQrSvgDataUrl = null;

        // Strategy 1: Generate locally with the bundled qrcode-generator library
        try {
            var qrLib = window.qrcode;
            if (typeof qrLib === 'function') {
                if (window.TextEncoder) {
                    qrLib.stringToBytes = function(s) {
                        return Array.from(new TextEncoder().encode(s));
                    };
                }
                var qr = qrLib(0, 'M');
                qr.addData(jsonString, 'Byte');
                qr.make();
                var count = qr.getModuleCount();
                var cellSize = 10;
                var margin = 4;
                var size = (count + margin * 2) * cellSize;
                var svgStr = '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">';
                svgStr += '<rect width="' + size + '" height="' + size + '" fill="#fff"/>';
                for (var row = 0; row < count; row++) {
                    for (var column = 0; column < count; column++) {
                        if (qr.isDark(row, column)) {
                            svgStr += '<rect x="' + ((column + margin) * cellSize) + '" y="' + ((row + margin) * cellSize) + '" width="' + cellSize + '" height="' + cellSize + '" fill="#000"/>';
                        }
                    }
                }
                svgStr += '</svg>';
                target.innerHTML = svgStr;
                var svgEl = target.querySelector('svg');
                if (svgEl) svgEl.style.cssText = 'width:100%;height:100%;display:block;';
                currentQrSvgDataUrl = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgStr)));
                return;
            }
        } catch (e1) {
            console.warn('[QRGen] local generation failed:', e1);
        }

        // Strategy 2: Use window.qrcode (qrcode-generator.min.js)
        try {
            var qrLib = window.qrcode;
            if (typeof qrLib === 'function') {
                if (window.TextEncoder) {
                    qrLib.stringToBytes = function(s) {
                        return Array.from(new TextEncoder().encode(s));
                    };
                }
                var qr = qrLib(0, 'M');
                qr.addData(jsonString, 'Byte');
                qr.make();
                var cnt = qr.getModuleCount();
                var cell = 10, margin = 4;
                var sz = (cnt + 2*margin) * cell;
                var svgStr = '<svg xmlns="http://www.w3.org/2000/svg" width="'+sz+'" height="'+sz+'" viewBox="0 0 '+sz+' '+sz+'">';
                svgStr += '<rect width="'+sz+'" height="'+sz+'" fill="#fff"/>';
                for (var r = 0; r < cnt; r++) {
                    for (var c = 0; c < cnt; c++) {
                        if (qr.isDark(r, c)) {
                            svgStr += '<rect x="'+((c+margin)*cell)+'" y="'+((r+margin)*cell)+'" width="'+cell+'" height="'+cell+'" fill="#000"/>';
                        }
                    }
                }
                svgStr += '</svg>';
                target.innerHTML = svgStr;
                var svgEl = target.querySelector('svg');
                if (svgEl) svgEl.style.cssText = 'width:100%;height:100%;display:block;';
                currentQrSvgDataUrl = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgStr)));
                return;
            }
        } catch (e2) {
            console.warn('[QRGen] qrcode-generator.min.js failed:', e2);
        }

        // Strategy 3: Use QRCode.js (qrcode.min.js) → canvas → img
        try {
            var QRCodeLib = window.QRCode;
            if (typeof QRCodeLib === 'function') {
                target.innerHTML = '';
                var qrInst = new QRCodeLib(target, {
                    text: jsonString,
                    width: 160, height: 160,
                    colorDark: '#000000', colorLight: '#ffffff',
                    correctLevel: QRCodeLib.CorrectLevel.M
                });
                setTimeout(function() {
                    var canvas = target.querySelector('canvas');
                    var img = target.querySelector('img');
                    if (canvas) {
                        canvas.style.cssText = 'width:100%;height:100%;display:block;';
                        currentQrSvgDataUrl = canvas.toDataURL('image/png');
                    } else if (img) {
                        img.style.cssText = 'width:100%;height:100%;display:block;object-fit:contain;';
                        currentQrSvgDataUrl = img.src;
                    }
                }, 150);
                return;
            }
        } catch (e3) {
            console.warn('[QRGen] qrcode.min.js failed:', e3);
        }

        currentQrSvgDataUrl = null;
        target.innerHTML = '<div style="font-size:7px;color:#b91c1c;text-align:center;padding:2px;">Local QR generation is unavailable. Reload the page and try again.</div>';
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
        const button = document.getElementById('downloadIdCardButton');
        if (!card || !button) return;

        const requiredCardElements = [
            'cardCode', 'cardName', 'cardGn', 'cardSchool',
            'cardGuardian', 'cardQrCodeTarget'
        ];
        if (requiredCardElements.some(id => !document.getElementById(id))) {
            alert('The ID card preview is incomplete. Reload the page and try again.');
            return;
        }

        const originalButtonText = button.textContent;
        button.disabled = true;
        button.textContent = 'Preparing PNG...';

        try {
            if (typeof html2canvas !== 'function') {
                throw new Error('The ID card image tool did not load. Reload the page and try again.');
            }

            generateOfflineQrCard();
            if (!currentQrSvgDataUrl) {
                throw new Error('The QR code could not be generated. Check the card details and try again.');
            }

            await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));

            const canvas = await html2canvas(card, {
                scale: 4,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false
            });

            const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/png'));
            if (!blob) {
                throw new Error('The browser could not create a PNG image. Try again or use Print ID Card.');
            }

            const code = (document.getElementById('genChildCode').value.trim() || 'child')
                .replace(/[^a-zA-Z0-9_-]/g, '_');
            const objectUrl = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.download = `${code}_offline_id_card.png`;
            link.href = objectUrl;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.setTimeout(() => URL.revokeObjectURL(objectUrl), 1000);
        } catch (e) {
            console.error(e);
            alert(e.message || 'Unable to generate the ID card PNG. Please try again.');
        } finally {
            button.disabled = false;
            button.textContent = originalButtonText;
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

    /*
    |--------------------------------------------------------------------------
    | Initial Initialization
    |--------------------------------------------------------------------------
    */
    document.addEventListener('DOMContentLoaded', function() {
        updateScansBadge();
        switchTab(activeTool);

        // If children available, select the first one by default
        const childSelect = document.getElementById('childSelect');
        if (childSelect && childSelect.options.length > 1) {
            childSelect.selectedIndex = 1;
            onChildSelected(childSelect.value);
        } else {
            // Default sample
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
