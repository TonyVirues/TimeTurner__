<!-- TimeTurner Login Mobile (Bootstrap) -->
<!DOCTYPE html>

<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TimeTurner - Temporal Architect Login</title>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        :root {
            --bs-primary: #0059b5;
            --bs-primary-rgb: 0, 89, 181;
            --azure-surface: #f7f5ff;
            --azure-container: #ffffff;
            --azure-input: #dde1ff;
            --azure-text-main: #232c51;
            --azure-text-muted: #505a81;
            --azure-accent-gradient: linear-gradient(to right, #0059b5, #67a0ff);
            --azure-bg-gradient: linear-gradient(135deg, #f7f5ff 0%, #dde1ff 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--azure-bg-gradient);
            color: var(--azure-text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        h1, h2, h3, .font-headline {
            font-family: 'Manrope', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        .auth-card {
            background: var(--azure-container);
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 89, 181, 0.1);
            border: none;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--azure-text-muted);
            margin-left: 1rem;
        }

        .form-control {
            background-color: var(--azure-input);
            border: none;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem 0.875rem 3rem;
            color: var(--azure-text-main);
        }

        .form-control:focus {
            background-color: #d5dbff;
            box-shadow: 0 0 0 0.25rem rgba(0, 89, 181, 0.15);
            color: var(--azure-text-main);
        }

        .input-group-text-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            color: #6c759e;
        }

        .btn-signature {
            background: var(--azure-accent-gradient);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 50rem;
            font-weight: 700;
            font-size: 1.125rem;
            letter-spacing: -0.01em;
            transition: all 0.2s ease;
        }

        .btn-signature:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-1px);
        }

        .btn-social {
            background-color: #efefff;
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--azure-text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .btn-social:hover {
            background-color: #dde1ff;
        }

        .btn-social img {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
            opacity: 0.8;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #6c759e;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dde1ff;
        }

        .divider:not(:empty)::before { margin-right: 1rem; }
        .divider:not(:empty)::after { margin-left: 1rem; }

        .logo-box {
            width: 4rem;
            height: 4rem;
            background: white;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }

        .footer-link {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6c759e;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-link:hover {
            color: var(--bs-primary);
        }

        .time-track {
            width: 1px;
            height: 4rem;
            background-color: var(--bs-primary);
            box-shadow: 0 0 12px var(--bs-primary);
            opacity: 0.2;
            margin: 2rem auto;
        }
    </style>
</head>
<body>
<div class="container py-5">
<div class="row justify-content-center">
<div class="col-12 col-sm-10 col-md-8 col-lg-5">
<!-- Hero Identity -->
<header class="text-center mb-5">
<div class="logo-box mx-auto">
<img alt="TimeTurner logo" class="img-fluid w-75" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxviWXmMzhLyytjAa0m3CG8ZAPSltN7HyjQry4w9K8fmDfBtsHMB6UAHyOWIFBSzRDhZrm77kowj2HjrmRIcCXDcx4X42enCtMPBvrLM4ccCW3CbhFgFsJXsD_aUzLlhyz8wc3icGdmlfOnXgU7PUZmtCL8ZfX-lC4SKkJKZoiM1XGsofTZHJ4Og4WkPh75Y_z9eIkYBEg0OeYNCmeCKBTr8O-hGE1y7kMwmSFWg03dOrQYqOgJLX2Hp6saFUTJXAlJQd3p07pOgW2"/>
</div>
<h1 class="fw-bolder display-5 tracking-tighter">TimeTurner</h1>
</header>
<!-- Main Auth Canvas -->
<main>
<div class="auth-card">
<div class="text-center mb-4">
<h2 class="fw-bold h4 mb-2">Welcome Back</h2>
<p class="text-muted small mb-0">Enter your credentials to access your temporal architect workspace.</p>
</div>
<form>
<div class="mb-4 position-relative">
<label class="form-label">Username or Email</label>
<div class="position-relative">
<span class="material-symbols-outlined input-group-text-icon">person</span>
<input class="form-control" placeholder="architect@timeturner.io" type="text"/>
</div>
</div>
<div class="mb-4 position-relative">
<label class="form-label">Password</label>
<div class="position-relative">
<span class="material-symbols-outlined input-group-text-icon">lock</span>
<input class="form-control" placeholder="••••••••" type="password"/>
</div>
<div class="text-end mt-2">
<a class="text-decoration-none small fw-bold" href="#" style="color: var(--bs-primary);">Forgot Password?</a>
</div>
</div>
<button class="btn btn-signature w-100 mb-4 shadow-sm" type="submit">Sign In</button>
</form>
<div class="divider mb-4">Or continue with</div>
<div class="row g-2">
<div class="col-4">
<button class="btn btn-social w-100">
<img alt="Google" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBve-5GBwVq9Qtz8sosFHzZHCVnoDltkAyfaW0YY0o_8yJSb0c7vmZK3CFsxrzRoDcuUJOypQ6ElTtFNKVDSpT1dHr0xoSec_yUs-_4gMGVXIBYNoB-cfRP2VO0lJ7IbcNaTq52OlflwH8jgm73zVpxbmD2O3adeXyjV9vH21jo1GZLbAgj01Q2ugDYQH0_iPZq2dUbOWFPRLgcsuWd9HdWSIm6WGMTdHUjfERnBMeIGJpHeh0cPJI6V7Z7vGvMj4P6bBCvU_PSucO0"/>
                                    Google
                                </button>
</div>
<div class="col-4">
<button class="btn btn-social w-100">
<img alt="Facebook" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjCvoHMmshbHsdE9m6fB_eyH3GltZfVdqGLzSww_WZlwjTnNQKqvRVkoiFYtYjTqK2o5lvIIF0GgdJTg9_hD4NyBAvpoCavLGwrJCmVqc2BTcgp8NTxKXLue4N13ZLKT_hBcQSS71fknvZu52f7Z8abAR8oZ5nR4RAGfKM-hOkuOCytaXomTstHtlc06JkxoLVKm6UUSiZjJB7TDurTk3XBB2WFc1H1bPdWrnLK8LJBeSNHjCuxrUwt4bNC8-_ieoetejlXuk5gTMU"/>
                                    Facebook
                                </button>
</div>
<div class="col-4">
<button class="btn btn-social w-100">
<img alt="LinkedIn" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5-k3CFAuGrcbJq_3wwnRiugN4VrCBuq_90yg2a_TLlnvNzeDGQfvmvKmihhIr-p_srOm1p3MQAKSfh2jy1vgCW_cW0X_wr8G_070RuQPBzy0vKwHq7IYN0IjhjuBIEguVMZvZG7kixndXW7u5jvZI_qRdeGOEKme8DICK-D1rT5MFVLLF74bFSgf3DI6zOGPqNclHE--bFFvqvXFII8b4k8NScBLOnPjSlvcFgaDf8vppE6z4Ydpidf3DVeS9h_l4gIVhfgINASrV"/>
                                    LinkedIn
                                </button>
</div>
</div>
</div>
<div class="text-center mt-4">
<p class="mb-0 fw-medium">
                            New to the Architect? 
                            <a class="fw-bold text-decoration-none" href="#" style="color: var(--bs-primary);">Join TimeTurner</a>
</p>
</div>
</main>
<!-- Decorative Time Track -->
<div class="time-track"></div>
<!-- Footer Links -->
<footer class="mt-4 mb-5">
<div class="d-flex justify-content-center gap-4">
<a class="footer-link" href="#">Privacy Policy</a>
<a class="footer-link" href="#">Help Center</a>
<a class="footer-link" href="#">Terms of Service</a>
</div>
</footer>
</div>
</div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body></html>

<!-- TimeTurner Login Desktop -->
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TimeTurner - Sign In</title>
<!-- Material Symbols Outlined -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Google Fonts: Manrope & Inter -->
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                "surface-dim": "#cad2ff",
                "primary-container": "#67a0ff",
                "on-tertiary-container": "#60136d",
                "surface-container-highest": "#d5dbff",
                "tertiary-fixed-dim": "#e48fed",
                "surface-container-lowest": "#ffffff",
                "on-error-container": "#570008",
                "error": "#b31b25",
                "on-primary-container": "#00224c",
                "surface": "#f7f5ff",
                "on-error": "#ffefee",
                "on-surface-variant": "#505a81",
                "on-tertiary": "#ffeefb",
                "secondary-fixed-dim": "#b3c1ff",
                "secondary-container": "#c6cfff",
                "tertiary": "#883c93",
                "surface-variant": "#d5dbff",
                "on-primary-fixed-variant": "#002b5d",
                "on-secondary-fixed-variant": "#2b48ac",
                "surface-container-high": "#dde1ff",
                "surface-bright": "#f7f5ff",
                "outline-variant": "#a2abd7",
                "inverse-on-surface": "#929bc6",
                "on-tertiary-fixed-variant": "#6a1f77",
                "on-background": "#232c51",
                "on-secondary-container": "#1f3ea2",
                "on-secondary": "#f2f1ff",
                "inverse-primary": "#438fff",
                "on-primary": "#eff2ff",
                "surface-container": "#e4e7ff",
                "tertiary-container": "#f39cfb",
                "primary-fixed": "#67a0ff",
                "primary": "#0059b5",
                "surface-tint": "#0059b5",
                "on-secondary-fixed": "#00298b",
                "primary-dim": "#004e9f",
                "background": "#f7f5ff",
                "inverse-surface": "#020a2f",
                "secondary-dim": "#2a47ab",
                "surface-container-low": "#efefff",
                "tertiary-dim": "#7a2f86",
                "on-surface": "#232c51",
                "outline": "#6c759e",
                "tertiary-fixed": "#f39cfb",
                "on-tertiary-fixed": "#41004d",
                "error-container": "#fb5151",
                "error-dim": "#9f0519",
                "primary-fixed-dim": "#4a92ff",
                "secondary": "#3854b7",
                "on-primary-fixed": "#000000",
                "secondary-fixed": "#c6cfff"
              },
              fontFamily: {
                "headline": ["Manrope"],
                "body": ["Inter"],
                "label": ["Inter"]
              },
              borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
          },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        .signature-gradient {
            background: linear-gradient(135deg, #0059b5 0%, #67a0ff 100%);
        }
        .tonal-glass {
            background: rgba(247, 245, 255, 0.7);
            backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col overflow-x-hidden">
<!-- TopAppBar -->
<header class="fixed top-0 w-full bg-transparent z-50 flex justify-between items-center px-8 py-6">
<div class="text-2xl font-black text-[#232c51] dark:text-[#f7f5ff] tracking-tighter font-headline">
            TimeTurner
        </div>
<nav class="hidden md:flex items-center gap-8">
<button class="material-symbols-outlined text-[#232c51] opacity-70 hover:opacity-100 transition-opacity" data-icon="help">help</button>
<button class="material-symbols-outlined text-[#232c51] opacity-70 hover:opacity-100 transition-opacity" data-icon="language">language</button>
</nav>
</header>
<!-- Main Content Canvas -->
<main class="flex-grow flex items-center justify-center p-6 mt-16 mb-16">
<!-- Auth Card Container -->
<div class="bg-surface-container-lowest w-full max-w-5xl h-[600px] rounded-xl overflow-hidden flex flex-col md:flex-row shadow-tint" style="box-shadow: 0 8px 32px rgba(35, 44, 81, 0.15);">
<!-- Left Side: Form -->
<div class="w-full md:w-1/2 bg-surface-container-lowest p-12 flex flex-col">
<div class="mb-10 text-center md:text-left">
<h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">TimeTurner</h1>
</div>
<div class="flex-grow flex flex-col justify-center max-w-sm mx-auto w-full">
<h2 class="font-headline text-2xl font-bold mb-8 text-on-surface">Sign In</h2>
<form class="space-y-6">
<div>
<label class="block text-xs font-semibold text-outline uppercase tracking-wider mb-2 font-label">Username / Email</label>
<input class="w-full px-4 py-3 bg-surface-container-high rounded-md border-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-200 outline-none placeholder:text-outline/50" placeholder="alex@temporal.com" type="text"/>
</div>
<div>
<label class="block text-xs font-semibold text-outline uppercase tracking-wider mb-2 font-label">Password</label>
<input class="w-full px-4 py-3 bg-surface-container-high rounded-md border-none focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-200 outline-none placeholder:text-outline/50" placeholder="••••••••" type="password"/>
</div>
<div class="flex items-center justify-between text-xs mb-2">
<label class="flex items-center gap-2 cursor-pointer">
<input class="rounded text-primary focus:ring-primary bg-surface-container-high border-none" type="checkbox"/>
<span class="text-on-surface-variant font-medium">Remember me</span>
</label>
<a class="text-primary font-bold hover:underline" href="#">Forgot?</a>
</div>
<button class="w-full py-4 signature-gradient text-on-primary font-headline font-bold rounded-full active:scale-95 transition-transform duration-150 shadow-lg" type="submit">
                            Continue to Vault
                        </button>
</form>
<!-- Divider -->
<div class="relative my-8 text-center">
<div aria-hidden="true" class="absolute inset-0 flex items-center">
<div class="w-full border-t border-surface-container-high"></div>
</div>
<span class="relative px-4 bg-surface-container-lowest text-xs text-outline font-medium">OR CONTINUE WITH</span>
</div>
<!-- Social Logins -->
<div class="flex gap-4 justify-center">
<button class="flex-1 flex items-center justify-center py-2.5 bg-surface-container-low hover:bg-surface-container-high transition-colors rounded-lg group">
<span class="material-symbols-outlined text-xl text-on-surface-variant group-hover:text-primary" data-icon="google">google</span>
</button>
<button class="flex-1 flex items-center justify-center py-2.5 bg-surface-container-low hover:bg-surface-container-high transition-colors rounded-lg group">
<span class="material-symbols-outlined text-xl text-on-surface-variant group-hover:text-primary" data-icon="facebook">social_leaderboard</span>
</button>
<button class="flex-1 flex items-center justify-center py-2.5 bg-surface-container-low hover:bg-surface-container-high transition-colors rounded-lg group">
<span class="material-symbols-outlined text-xl text-on-surface-variant group-hover:text-primary" data-icon="groups">groups</span>
</button>
</div>
</div>
</div>
<!-- Right Side: Visual Brand -->
<div class="hidden md:flex md:w-1/2 signature-gradient relative items-center justify-center p-12 overflow-hidden">
<!-- Abstract Temporal Pattern Background -->
<div class="absolute inset-0 opacity-20 pointer-events-none">
<svg class="w-full h-full" preserveaspectratio="none" viewbox="0 0 100 100">
<defs>
<pattern height="10" id="grid" patternunits="userSpaceOnUse" width="10">
<path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.1"></path>
</pattern>
</defs>
<rect fill="url(#grid)" height="100%" width="100%"></rect>
</svg>
</div>
<div class="relative z-10 flex flex-col items-center text-center">
<div class="relative mb-8">
<!-- White Glow Effect -->
<div class="absolute inset-0 bg-white blur-3xl opacity-30 rounded-full scale-150"></div>
<img alt="TimeTurner Brand Logo" class="relative w-48 h-48 object-contain" data-alt="Minimalist abstract clock geometric logo with rotating hands in white and light blue set against a vibrant gradient background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAc50hGdMJ5gTWSNWgKv92HBmrXI89joLO-pwHJH_3k2vDXRti8W8VsuEKjpNA-CfxEUikMC_GzRtrch7MbAS29YS4gzm82hOzHXpIyaoIx-lmRUfCN33SBvkmTDx6PShs6RG8a-pm9HxZQMUjDNzpPjbiHzAmlso7K7Mc_EQHfs-0y4r0mvEnU5Iag92-JOm_Ebr4kDGd-3p271uyYg4VsfO3ZfNASDNEn9gEbUUIklVlUQxm1_ZlxT6aKOQPjjMdfj2E2o9nv449N"/>
</div>
<h2 class="font-headline text-4xl font-black text-on-primary tracking-tighter mb-4">Architecting Tomorrow</h2>
<p class="text-on-primary/80 max-w-xs font-body leading-relaxed text-sm">
                        Experience the precision of the world's most advanced temporal management system.
                    </p>
</div>
<!-- Corner Decoration -->
<div class="absolute bottom-[-50px] right-[-50px] w-64 h-64 bg-primary-container/20 rounded-full blur-3xl"></div>
</div>
</div>
</main>
<!-- Footer -->
<footer class="fixed bottom-0 w-full bg-[#f7f5ff] dark:bg-[#0a0a0c] flex justify-between items-center px-12 py-4 z-50">
<div class="font-body text-sm tracking-wide text-[#232c51] opacity-60">
            © 2024 TimeTurner. The Temporal Architect.
        </div>
<div class="flex gap-8">
<a class="font-body text-sm tracking-wide text-[#232c51] opacity-60 hover:text-blue-600 dark:hover:text-blue-300 transition-colors duration-200" href="#">Privacy Policy</a>
<a class="font-body text-sm tracking-wide text-[#232c51] opacity-60 hover:text-blue-600 dark:hover:text-blue-300 transition-colors duration-200" href="#">Terms of Service</a>
<a class="font-body text-sm tracking-wide text-[#232c51] opacity-60 hover:text-blue-600 dark:hover:text-blue-300 transition-colors duration-200" href="#">Cookie Settings</a>
</div>
</footer>
</body></html>

<!-- TimeTurner Login Mobile -->
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TimeTurner - Temporal Architect Login</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "on-secondary-fixed-variant": "#2b48ac",
              "secondary": "#3854b7",
              "surface-container-highest": "#d5dbff",
              "tertiary-fixed": "#f39cfb",
              "on-tertiary": "#ffeefb",
              "surface-container-high": "#dde1ff",
              "inverse-primary": "#438fff",
              "secondary-dim": "#2a47ab",
              "on-primary": "#eff2ff",
              "tertiary-dim": "#7a2f86",
              "on-primary-fixed-variant": "#002b5d",
              "error-container": "#fb5151",
              "tertiary-fixed-dim": "#e48fed",
              "secondary-container": "#c6cfff",
              "primary": "#0059b5",
              "on-tertiary-container": "#60136d",
              "on-error": "#ffefee",
              "surface-container-lowest": "#ffffff",
              "primary-fixed-dim": "#4a92ff",
              "inverse-surface": "#020a2f",
              "primary-dim": "#004e9f",
              "on-tertiary-fixed-variant": "#6a1f77",
              "on-secondary": "#f2f1ff",
              "surface-tint": "#0059b5",
              "on-background": "#232c51",
              "secondary-fixed": "#c6cfff",
              "on-surface": "#232c51",
              "primary-fixed": "#67a0ff",
              "surface-variant": "#d5dbff",
              "secondary-fixed-dim": "#b3c1ff",
              "error": "#b31b25",
              "outline-variant": "#a2abd7",
              "surface": "#f7f5ff",
              "primary-container": "#67a0ff",
              "on-surface-variant": "#505a81",
              "surface-bright": "#f7f5ff",
              "inverse-on-surface": "#929bc6",
              "surface-container": "#e4e7ff",
              "on-secondary-container": "#1f3ea2",
              "outline": "#6c759e",
              "on-tertiary-fixed": "#41004d",
              "on-primary-fixed": "#000000",
              "on-primary-container": "#00224c",
              "background": "#f7f5ff",
              "on-secondary-fixed": "#00298b",
              "surface-dim": "#cad2ff",
              "tertiary-container": "#f39cfb",
              "tertiary": "#883c93",
              "error-dim": "#9f0519",
              "surface-container-low": "#efefff",
              "on-error-container": "#570008"
            },
            fontFamily: {
              "headline": ["Manrope"],
              "body": ["Inter"],
              "label": ["Inter"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
        .editorial-gradient {
            background: linear-gradient(135deg, #f7f5ff 0%, #dde1ff 100%);
        }
        .signature-cta {
            background: linear-gradient(to right, #0059b5, #67a0ff);
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
        }
    </style>
<style>
    body {
      min-height: max(884px, 100dvh);
    }
  </style>
  </head>
<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col items-center justify-center p-4 editorial-gradient">
<!-- Hero Identity (TopAppBar Equivalent but centered for Focus Journey) -->
<header class="mb-12 flex flex-col items-center gap-4">
<div class="w-16 h-16 rounded-2xl bg-surface-container-lowest flex items-center justify-center shadow-tint overflow-hidden">
<img alt="Geometric blue hourglass logo representing time architectural software with modern clean lines" class="w-12 h-12 object-contain" data-alt="high-tech geometric hourglass logo with vibrant blue gradients on a clean white background minimalist professional style" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxviWXmMzhLyytjAa0m3CG8ZAPSltN7HyjQry4w9K8fmDfBtsHMB6UAHyOWIFBSzRDhZrm77kowj2HjrmRIcCXDcx4X42enCtMPBvrLM4ccCW3CbhFgFsJXsD_aUzLlhyz8wc3icGdmlfOnXgU7PUZmtCL8ZfX-lC4SKkJKZoiM1XGsofTZHJ4Og4WkPh75Y_z9eIkYBEg0OeYNCmeCKBTr8O-hGE1y7kMwmSFWg03dOrQYqOgJLX2Hp6saFUTJXAlJQd3p07pOgW2"/>
</div>
<h1 class="font-headline font-extrabold text-4xl tracking-tighter text-on-surface">TimeTurner</h1>
</header>
<!-- Main Auth Canvas (Central Card) -->
<main class="w-full max-w-md">
<div class="bg-surface-container-lowest p-8 md:p-10 rounded-[2rem] shadow-tint flex flex-col items-center text-center space-y-8">
<div class="space-y-2">
<h2 class="font-headline text-2xl font-bold tracking-tight text-on-background">Welcome Back</h2>
<p class="text-on-surface-variant text-sm leading-relaxed">Enter your credentials to access your temporal architect workspace.</p>
</div>
<!-- Auth Form -->
<form class="w-full space-y-5">
<div class="space-y-1.5 text-left">
<label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-4 mb-1 block">Username or Email</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">person</span>
<input class="w-full pl-12 pr-4 py-4 bg-surface-container-high border-none rounded-xl text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-200" placeholder="architect@timeturner.io" type="text"/>
</div>
</div>
<div class="space-y-1.5 text-left">
<label class="text-xs font-semibold uppercase tracking-widest text-on-surface-variant ml-4 mb-1 block">Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
<input class="w-full pl-12 pr-4 py-4 bg-surface-container-high border-none rounded-xl text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-200" placeholder="••••••••" type="password"/>
</div>
<div class="flex justify-end pt-1">
<a class="text-xs font-semibold text-primary hover:text-primary-dim transition-colors" href="#">Forgot Password?</a>
</div>
</div>
<button class="signature-cta w-full py-4 rounded-full text-on-primary font-bold tracking-tight text-lg shadow-tint hover:opacity-90 active:scale-[0.98] transition-all" type="submit">
                    Sign In
                </button>
</form>
<!-- Divider -->
<div class="w-full flex items-center gap-4 py-2">
<div class="h-[1px] flex-1 bg-surface-container-high"></div>
<span class="text-[10px] font-bold uppercase tracking-widest text-outline">Or continue with</span>
<div class="h-[1px] flex-1 bg-surface-container-high"></div>
</div>
<!-- Social Logins -->
<div class="flex gap-4 justify-center w-full">
<button class="flex-1 flex items-center justify-center py-3 bg-surface-container-low rounded-xl hover:bg-surface-container-high transition-colors active:scale-95 duration-150">
<img alt="Google icon" class="w-5 h-5 mr-2 opacity-80" data-alt="Google logo icon on a clean white background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBve-5GBwVq9Qtz8sosFHzZHCVnoDltkAyfaW0YY0o_8yJSb0c7vmZK3CFsxrzRoDcuUJOypQ6ElTtFNKVDSpT1dHr0xoSec_yUs-_4gMGVXIBYNoB-cfRP2VO0lJ7IbcNaTq52OlflwH8jgm73zVpxbmD2O3adeXyjV9vH21jo1GZLbAgj01Q2ugDYQH0_iPZq2dUbOWFPRLgcsuWd9HdWSIm6WGMTdHUjfERnBMeIGJpHeh0cPJI6V7Z7vGvMj4P6bBCvU_PSucO0"/>
<span class="text-xs font-bold text-on-surface">Google</span>
</button>
<button class="flex-1 flex items-center justify-center py-3 bg-surface-container-low rounded-xl hover:bg-surface-container-high transition-colors active:scale-95 duration-150">
<img alt="Facebook icon" class="w-5 h-5 mr-2 opacity-80" data-alt="Facebook logo icon on a clean white background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAjCvoHMmshbHsdE9m6fB_eyH3GltZfVdqGLzSww_WZlwjTnNQKqvRVkoiFYtYjTqK2o5lvIIF0GgdJTg9_hD4NyBAvpoCavLGwrJCmVqc2BTcgp8NTxKXLue4N13ZLKT_hBcQSS71fknvZu52f7Z8abAR8oZ5nR4RAGfKM-hOkuOCytaXomTstHtlc06JkxoLVKm6UUSiZjJB7TDurTk3XBB2WFc1H1bPdWrnLK8LJBeSNHjCuxrUwt4bNC8-_ieoetejlXuk5gTMU"/>
<span class="text-xs font-bold text-on-surface">Facebook</span>
</button>
<button class="flex-1 flex items-center justify-center py-3 bg-surface-container-low rounded-xl hover:bg-surface-container-high transition-colors active:scale-95 duration-150">
<img alt="LinkedIn icon" class="w-5 h-5 mr-2 opacity-80" data-alt="LinkedIn logo icon on a clean white background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuD5-k3CFAuGrcbJq_3wwnRiugN4VrCBuq_90yg2a_TLlnvNzeDGQfvmvKmihhIr-p_srOm1p3MQAKSfh2jy1vgCW_cW0X_wr8G_070RuQPBzy0vKwHq7IYN0IjhjuBIEguVMZvZG7kixndXW7u5jvZI_qRdeGOEKme8DICK-D1rT5MFVLLF74bFSgf3DI6zOGPqNclHE--bFFvqvXFII8b4k8NScBLOnPjSlvcFgaDf8vppE6z4Ydpidf3DVeS9h_l4gIVhfgINASrV"/>
<span class="text-xs font-bold text-on-surface">LinkedIn</span>
</button>
</div>
</div>
<!-- Secondary Action -->
<p class="mt-8 text-center text-on-surface-variant font-medium">
            New to the Architect? 
            <a class="text-primary font-bold hover:underline" href="#">Join TimeTurner</a>
</p>
</main>
<!-- Visual Anchor / Decorative "Time Track" -->
<div class="fixed bottom-12 left-0 right-0 flex justify-center pointer-events-none opacity-20">
<div class="w-[1px] h-16 bg-primary glow shadow-[0_0_12px_#0059b5]"></div>
</div>
<!-- Mandatory Footer Support/Help link (Hidden Top Nav equivalent for transactional) -->
<footer class="mt-16 pb-8 flex gap-8 text-[10px] font-bold uppercase tracking-widest text-outline">
<a class="hover:text-primary transition-colors" href="#">Privacy Policy</a>
<a class="hover:text-primary transition-colors" href="#">Help Center</a>
<a class="hover:text-primary transition-colors" href="#">Terms of Service</a>
</footer>
</body></html>