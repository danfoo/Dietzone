<?php
/**
 * Portal Legal Page View - Ultra Modern Mobile-First Design
 * Displays Privacy Policy and Terms of Service
 */

// Set active page based on the title
if (strpos($title, 'Confidentialité') !== false) {
    $active_page = 'privacy';
} elseif (strpos($title, 'Utilisation') !== false) {
    $active_page = 'terms';
} else {
    $active_page = 'legal';
}
$page_title = $title;
$this->load->view('portal/includes/portal_header');
?>

<style>
/* ============================================
   MODERN MOBILE-FIRST LEGAL PAGE DESIGN
   ============================================ */

/* CSS Variables - Dietzone Branding Colors */
:root {
    --primary-gradient: linear-gradient(135deg, #01807B 0%, #01655f 100%);
    --primary-color: #01807B;
    --primary-dark: #01655f;
    --accent-color: #019690;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --bg-primary: #ffffff;
    --bg-secondary: #f7fafc;
    --bg-glass: rgba(255, 255, 255, 0.85);
    --border-color: #e2e8f0;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    --shadow-xl: 0 20px 60px rgba(0, 0, 0, 0.15);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 20px;
    --radius-xl: 28px;
    --transition-fast: 0.2s ease;
    --transition-base: 0.3s ease;
    --transition-slow: 0.5s ease;
}

/* Modern font stack */
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Animated gradient background */
.legal-page-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    background-size: 200% 200%;
    animation: gradientShift 15s ease infinite;
    padding: 0;
    position: relative;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Floating particles effect (optional decorative elements) */
.legal-page-wrapper::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        radial-gradient(circle at 20% 50%, rgba(1, 128, 123, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(1, 101, 95, 0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

/* Container - Mobile First */
.legal-page-container {
    position: relative;
    z-index: 1;
    max-width: 100%;
    margin: 0 auto;
    padding: 16px;
    padding-bottom: 32px;
}

/* Hero Header with Glassmorphism */
.legal-page-header {
    background: var(--primary-gradient);
    background-size: 200% 200%;
    animation: gradientFlow 8s ease infinite;
    color: white;
    padding: 32px 20px;
    border-radius: var(--radius-lg);
    margin-bottom: 24px;
    text-align: center;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

@keyframes gradientFlow {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Decorative circles in header */
.legal-page-header::before,
.legal-page-header::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.legal-page-header::before {
    width: 200px;
    height: 200px;
    top: -100px;
    right: -50px;
}

.legal-page-header::after {
    width: 150px;
    height: 150px;
    bottom: -75px;
    left: -30px;
}

.legal-page-header .icon {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.95;
    display: inline-block;
    animation: floatIcon 3s ease-in-out infinite;
    position: relative;
    z-index: 1;
}

@keyframes floatIcon {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.legal-page-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    color: white;
    position: relative;
    z-index: 1;
    line-height: 1.3;
}

/* Content Card with Glassmorphism */
.legal-content-wrapper {
    background: var(--bg-glass);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: var(--radius-xl);
    padding: 24px 20px;
    box-shadow: var(--shadow-xl);
    margin-bottom: 24px;
    animation: fadeInUp 0.6s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Content Typography */
.legal-content {
    line-height: 1.75;
    font-size: 15px;
    color: var(--text-secondary);
}

.legal-content h1 {
    font-size: 22px;
    margin-top: 28px;
    margin-bottom: 12px;
    color: var(--text-primary);
    font-weight: 700;
    border-bottom: 3px solid transparent;
    border-image: var(--primary-gradient);
    border-image-slice: 1;
    padding-bottom: 8px;
    position: relative;
}

.legal-content h1:first-child {
    margin-top: 0;
}

.legal-content h2 {
    font-size: 19px;
    margin-top: 24px;
    margin-bottom: 10px;
    color: var(--primary-color);
    font-weight: 600;
    display: flex;
    align-items: center;
}

.legal-content h2::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 20px;
    background: var(--primary-gradient);
    margin-right: 10px;
    border-radius: 2px;
}

.legal-content h3 {
    font-size: 17px;
    margin-top: 20px;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-weight: 600;
}

.legal-content p {
    margin-bottom: 16px;
    text-align: left;
    line-height: 1.8;
}

.legal-content ul,
.legal-content ol {
    margin-bottom: 16px;
    padding-left: 24px;
}

.legal-content li {
    margin-bottom: 10px;
    position: relative;
    line-height: 1.7;
}

.legal-content ul li::marker {
    color: var(--primary-color);
}

.legal-content strong {
    color: var(--text-primary);
    font-weight: 600;
}

.legal-content a {
    color: var(--primary-color);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: var(--transition-base);
    font-weight: 500;
}

.legal-content a:hover {
    color: var(--primary-dark);
    border-bottom-color: var(--primary-color);
}

.legal-content code {
    background: var(--bg-secondary);
    padding: 3px 8px;
    border-radius: var(--radius-sm);
    font-family: 'SF Mono', Monaco, 'Cascadia Code', 'Courier New', monospace;
    font-size: 13px;
    color: var(--accent-color);
    border: 1px solid var(--border-color);
}

.legal-content blockquote {
    border-left: 4px solid var(--primary-color);
    padding: 16px 20px;
    margin: 20px 0;
    background: var(--bg-secondary);
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    color: var(--text-secondary);
    font-style: italic;
}

/* Info Footer Card */
.legal-footer {
    background: var(--bg-glass);
    backdrop-filter: blur(10px);
    padding: 16px 20px;
    border-radius: var(--radius-lg);
    text-align: center;
    color: var(--text-muted);
    font-size: 13px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: var(--shadow-sm);
}

.legal-footer i {
    margin-right: 6px;
    color: var(--primary-color);
}

/* ============================================
   TABLET STYLES (768px+)
   ============================================ */
@media (min-width: 768px) {
    .legal-page-container {
        max-width: 720px;
        padding: 24px;
        padding-bottom: 40px;
    }

    .legal-page-header {
        padding: 48px 40px;
        border-radius: var(--radius-xl);
        margin-bottom: 32px;
    }

    .legal-page-header .icon {
        font-size: 64px;
        margin-bottom: 16px;
    }

    .legal-page-header h1 {
        font-size: 32px;
    }

    .legal-content-wrapper {
        padding: 40px 36px;
        border-radius: var(--radius-xl);
    }

    .legal-content {
        font-size: 16px;
    }

    .legal-content h1 {
        font-size: 28px;
        margin-top: 32px;
        margin-bottom: 16px;
    }

    .legal-content h2 {
        font-size: 22px;
        margin-top: 28px;
        margin-bottom: 12px;
    }

    .legal-content h3 {
        font-size: 19px;
    }

    .legal-footer {
        padding: 20px;
        font-size: 14px;
    }
}

/* ============================================
   DESKTOP STYLES (1024px+)
   ============================================ */
@media (min-width: 1024px) {
    .legal-page-container {
        max-width: 920px;
        padding: 40px;
    }

    .legal-page-header {
        padding: 60px 50px;
        margin-bottom: 40px;
    }

    .legal-page-header .icon {
        font-size: 72px;
        margin-bottom: 20px;
    }

    .legal-page-header h1 {
        font-size: 40px;
    }

    .legal-content-wrapper {
        padding: 50px 48px;
    }

    .legal-content {
        font-size: 17px;
        line-height: 1.85;
    }

    .legal-content h1 {
        font-size: 32px;
        margin-top: 40px;
        margin-bottom: 20px;
    }

    .legal-content h2 {
        font-size: 25px;
        margin-top: 32px;
        margin-bottom: 14px;
    }

    .legal-content h3 {
        font-size: 21px;
    }

    .legal-content ul,
    .legal-content ol {
        padding-left: 32px;
    }
}

/* ============================================
   LARGE DESKTOP STYLES (1280px+)
   ============================================ */
@media (min-width: 1280px) {
    .legal-page-container {
        max-width: 1100px;
    }

    .legal-content-wrapper {
        padding: 60px 60px;
    }
}

/* ============================================
   PRINT STYLES
   ============================================ */
@media print {
    .legal-page-wrapper {
        background: white;
    }

    .legal-page-header,
    .legal-content-wrapper {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .legal-content {
        color: black;
    }
}

/* ============================================
   ACCESSIBILITY
   ============================================ */
@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Selection color */
::selection {
    background: var(--primary-color);
    color: white;
}

::-moz-selection {
    background: var(--primary-color);
    color: white;
}
</style>

<div class="legal-page-wrapper">
    <div class="legal-page-container">
        <!-- Hero Header -->
        <div class="legal-page-header">
            <div class="icon">
                <i class="fa fa-<?php echo strpos($title, 'Confidentialité') !== false ? 'shield' : 'file-text'; ?>"></i>
            </div>
            <h1><?php echo htmlspecialchars($title); ?></h1>
        </div>

        <!-- Glass Content Card -->
        <div class="legal-content-wrapper">
            <div class="legal-content">
                <?php echo $content; ?>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="legal-footer">
            <i class="fa fa-clock-o"></i> Dernière mise à jour : <?php echo date('d/m/Y'); ?>
        </div>
    </div>
</div>

<script>
// Smooth scroll for anchor links
(function() {
    document.querySelectorAll('.legal-content a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
})();
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
