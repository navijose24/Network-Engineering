<?php
/**
 * Public Footer Component
 */
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db_queries.php';

$profile = get_profile_data();
$settings = get_site_settings();
?>
    <!-- Project Specification Modal -->
    <div id="project-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-project-title">
        <div class="modal-dialog">
            <button class="modal-close" aria-label="Close Project Specifications">&times;</button>
            <div id="modal-project-category" class="project-category-tag">[CATEGORY]</div>
            <h2 id="modal-project-title" class="section-title" style="margin-bottom: 8px;">[PROJECT TITLE]</h2>
            <div id="modal-project-subtitle" style="font-family: var(--font-mono); font-size: 0.95rem; color: var(--clr-terracotta); margin-bottom: 20px;">[SUBTITLE]</div>
            
            <div id="modal-project-body" class="project-card-desc" style="margin-bottom: 24px; font-size: 1.05rem;">[DESCRIPTION]</div>
            
            <h4 style="font-size: 0.95rem; margin-bottom: 12px; text-transform: uppercase; color: var(--clr-sand);">System Highlights & Architecture</h4>
            <div id="modal-project-bullets" style="margin-bottom: 24px;"></div>

            <h4 style="font-size: 0.95rem; margin-bottom: 12px; text-transform: uppercase; color: var(--clr-sand);">Technology Stack</h4>
            <div id="modal-project-tech" class="tech-tags" style="margin-bottom: 28px;"></div>

            <div id="modal-project-links" style="display: flex; gap: 14px; flex-wrap: wrap;"></div>
        </div>
    </div>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div style="font-family: var(--font-pixel); font-size: 1.3rem; margin-bottom: 14px; color: var(--clr-sand);">
                        navaneetha <span style="font-size: 0.75rem; color: var(--clr-terracotta);">// [SYS.01]</span>
                    </div>
                    <p style="font-size: 0.92rem; color: var(--clr-stone); max-width: 440px; margin-bottom: 16px;">
                        Engineering resilient systems at the nexus of AI, blockchain, and real-world infrastructure. Structured with zero bloat and intentional code.
                    </p>
                    <div class="binary-tag accent">
                        <span>BYTECODE:</span>
                        <span>01001110 01001010</span>
                    </div>
                </div>

                <div>
                    <h4 style="font-size: 0.9rem; color: var(--clr-sand); margin-bottom: 16px; text-transform: uppercase;">
                        Quick Coordinates
                    </h4>
                    <ul style="list-style: none; font-size: 0.88rem; display: flex; flex-direction: column; gap: 10px;">
                        <li><a href="/#about" style="color: #C7BAAE;">&gt; 01. About System</a></li>
                        <li><a href="/#projects" style="color: #C7BAAE;">&gt; 02. Project Index</a></li>
                        <li><a href="/#achievements" style="color: #C7BAAE;">&gt; 04. Honors & Awards</a></li>
                        <li><a href="/#articles" style="color: #C7BAAE;">&gt; 06. Technical Articles</a></li>
                        <li><a href="/#contact" style="color: #C7BAAE;">&gt; 07. Transmit Message</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="font-size: 0.9rem; color: var(--clr-sand); margin-bottom: 16px; text-transform: uppercase;">
                        Network Links
                    </h4>
                    <ul style="list-style: none; font-size: 0.88rem; display: flex; flex-direction: column; gap: 10px;">
                        <?php if (!empty($profile['github'])): ?>
                            <li><a href="https://<?= e(ltrim($profile['github'], 'https://')) ?>" target="_blank" rel="noopener noreferrer" style="color: #C7BAAE;">&gt; GitHub: /<?= e(basename($profile['github'])) ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($profile['linkedin'])): ?>
                            <li><a href="https://<?= e(ltrim($profile['linkedin'], 'https://')) ?>" target="_blank" rel="noopener noreferrer" style="color: #C7BAAE;">&gt; LinkedIn: /in/navaneetha-jose</a></li>
                        <?php endif; ?>
                        <?php if (!empty($profile['instagram'])): ?>
                            <li><a href="https://<?= e(ltrim($profile['instagram'], 'https://')) ?>" target="_blank" rel="noopener noreferrer" style="color: #C7BAAE;">&gt; Instagram: @navaneetha_jose</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="footer-copy">
                <div>
                    &copy; <?= date('Y') ?> <?= e($profile['name']) ?>. <?= e($settings['footer_text'] ?? 'BUILT OF 0s AND 1s.') ?>
                </div>
                <div>
                    <span>STATUS: 200 OK</span> // <span>LATENCY: 14MS</span> // <a href="/admin/login.php" style="color: var(--clr-stone); opacity: 0.45; text-decoration: none; font-size: 0.75rem;" title="Admin Gateway">[ADMIN]</a>
                </div>
            </div>
        </div>
    </footer>
</div><!-- /.content-wrapper -->

<!-- Scripts -->
<script src="/assets/js/app.js"></script>
</body>
</html>
