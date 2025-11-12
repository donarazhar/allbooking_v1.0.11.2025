<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $developer['application'] }} — Developer Info</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0053C5 0%, #003d91 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #1e293b;
        }

        .container {
            max-width: 800px;
            width: 100%;
        }

        .card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #0053C5 0%, #003d91 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header-icon {
            font-size: 48px;
            margin-bottom: 15px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header .subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
        }

        .info-section {
            margin-bottom: 30px;
        }

        .section-title {
            display: flex;
            align-items: center;
            font-size: 18px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-title i {
            font-size: 22px;
            margin-right: 12px;
            color: #0053C5;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            background: #f8fafc;
            padding: 18px;
            border-radius: 12px;
            border-left: 4px solid #0053C5;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 83, 197, 0.15);
        }

        .info-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .info-value {
            font-size: 16px;
            color: #1e293b;
            font-weight: 500;
            word-break: break-word;
        }

        .info-value a {
            color: #0053C5;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .info-value a:hover {
            color: #003d91;
            text-decoration: underline;
        }

        .signature-box {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
            border: 2px dashed #cbd5e1;
        }

        .signature-title {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .signature-title i {
            margin-right: 8px;
            color: #0053C5;
        }

        .signature-hash {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #334155;
            background: white;
            padding: 12px;
            border-radius: 8px;
            word-break: break-all;
            border: 1px solid #e2e8f0;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0053C5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #003d91;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 83, 197, 0.3);
        }

        .copy-btn:active {
            transform: translateY(0);
        }

        .proof-statement {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin-top: 30px;
        }

        .proof-statement p {
            font-size: 15px;
            color: #92400e;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .proof-statement strong {
            color: #78350f;
        }

        .footer {
            background: #f8fafc;
            padding: 25px 30px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .timestamp {
            font-size: 13px;
            color: #64748b;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #0053C5;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .warning {
            background: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .warning i {
            color: #dc2626;
            font-size: 20px;
            flex-shrink: 0;
        }

        .warning-text {
            font-size: 13px;
            color: #991b1b;
            line-height: 1.5;
        }

        @media (max-width: 640px) {
            .header {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .content {
                padding: 30px 20px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 16px 24px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast i {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Header -->
            <div class="header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h1>{{ $developer['application'] }}</h1>
                    <p class="subtitle">Developer Verification & Copyright Information</p>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <!-- Developer Information -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Developer Information
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value">{{ $developer['name'] }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value">
                                <a href="mailto:{{ $developer['email'] }}">{{ $developer['email'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Information -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-code"></i>
                        Application Details
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Application Name</div>
                            <div class="info-value">{{ $developer['application'] }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Version</div>
                            <div class="info-value">{{ $developer['version'] }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Build Date</div>
                            <div class="info-value">{{ $developer['build_date'] }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Framework</div>
                            <div class="info-value">Laravel 12.x</div>
                        </div>
                    </div>
                </div>

                <!-- Digital Signature -->
                <div class="info-section">
                    <div class="section-title">
                        <i class="fas fa-fingerprint"></i>
                        Digital Signature
                    </div>
                    <div class="signature-box">
                        <div class="signature-title">
                            <i class="fas fa-key"></i>
                            SHA-256 Hash Signature
                        </div>
                        <div class="signature-hash" id="signature">
                            {{ hash('sha256', $developer['name'] . $developer['email'] . $developer['build_date']) }}
                        </div>
                        <button class="copy-btn" onclick="copySignature()">
                            <i class="fas fa-copy"></i>
                            Copy Signature
                        </button>
                    </div>
                </div>

                <!-- Proof Statement -->
                <div class="proof-statement">
                    <p>
                        <i class="fas fa-certificate" style="color: #f59e0b; margin-right: 8px;"></i>
                        <strong>Proof of Ownership:</strong>
                    </p>
                    <p>
                        This application "<strong>{{ $developer['application'] }}</strong>" was custom-built
                        and is exclusively owned by <strong>{{ $developer['name'] }}</strong>.
                    </p>
                    <p style="margin-bottom: 0;">
                        Verification timestamp: <strong>{{ $timestamp }}</strong>
                    </p>
                </div>

                <!-- Warning -->
                <div class="warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="warning-text">
                        <strong>Confidential Information:</strong> This page contains proprietary developer information
                        and should not be shared publicly. Unauthorized reproduction or distribution of this
                        application is strictly prohibited.
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="footer-content">
                    <div class="timestamp">
                        <i class="far fa-clock"></i>
                        {{ now()->format('l, d F Y - H:i:s') }} WIB
                    </div>
                    <div class="badge">
                        <i class="fas fa-check-circle"></i>
                        Verified
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span>Signature copied to clipboard!</span>
    </div>

    <script>
        function copySignature() {
            const signature = document.getElementById('signature').textContent.trim();
            navigator.clipboard.writeText(signature).then(() => {
                showToast();
            }).catch(err => {
                console.error('Failed to copy:', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = signature;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    showToast();
                } catch (err) {
                    alert('Failed to copy signature');
                }
                document.body.removeChild(textArea);
            });
        }

        function showToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Add some interactivity - animate on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.info-section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'all 0.5s ease';
            observer.observe(section);
        });
    </script>
</body>

</html>
