<?php
/**
 * Main Portfolio Single-Page Architecture
 * Built with Core PHP 8+ and Neo-Brutalist 0s & 1s Design System
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/db_queries.php';

// Check if this is an AJAX contact submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact') {
    header('Content-Type: application/json');

    if (!csrf_verify()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'CSRF security verification failed.']);
        exit;
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? 'Direct Transmission');
    $message = trim($_POST['message'] ?? '');
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are mandatory.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email coordinate.']);
        exit;
    }

    $saved = record_contact_submission($name, $email, $subject, $message, $ip);
    if ($saved) {
        echo json_encode(['success' => true, 'message' => 'Transmission routed to Navaneetha Jose. Status: Logged in queue.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Packet acknowledged. Thank you for your inquiry.']);
    }
    exit;
}

// Fetch dynamic portfolio data
$isDbLive = is_db_connected();
$profile = get_profile_data();
$projects = get_projects();
$skillsGrouped = get_skills_grouped();
$achievements = get_achievements_list();
$talks = get_talks_list();
$articles = get_articles_list(true, 6);
$settings = get_site_settings();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (!$isDbLive): ?>
<div style="background: var(--clr-terracotta); color: #000; padding: 16px; font-family: var(--font-mono); font-size: 0.9rem; text-align: center; font-weight: bold; border-bottom: 2px solid var(--clr-sand);">
    [NOTICE // DATABASE MIGRATION PENDING]: Running in static fallback mode. 
    <a href="/database/setup.php" style="color: #000; text-decoration: underline; margin-left: 10px;">[CLICK HERE TO INITIALIZE DATABASE IN 1-CLICK]</a>
</div>
<?php endif; ?>

<!-- ==========================================
     HERO SECTION
     ========================================== -->
<section id="hero" class="hero-section system-dots">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-meta">
                    <span class="binary-tag accent">
                        <span class="live-indicator"></span>
                        <span class="mono-data">SYS.EXEC // 0x01</span>
                    </span>
                    <span class="binary-tag">
                        <?= e($settings['binary_hero_sub'] ?? '01000100 01000101 01010110') ?>
                    </span>
                </div>

                <h1 class="hero-title" data-scramble>
                    Navaneetha Jose,<span class="highlight"></span>
                </h1>

                <p class="hero-bio">
                    <?= e($profile['intro']) ?>
                </p>

                <div class="hero-cta">
                    <a href="#projects" class="btn btn-primary cut-corner">
                        EXPLORE ARCHITECTURE &rarr;
                    </a>
                    <a href="#contact" class="btn btn-secondary cut-corner">
                        INITIATE TRANSMISSION
                    </a>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-num"><?= count($projects) > 0 ? count($projects) : '8' ?>+</span>
                        <span class="stat-label">Systems Shipped</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num"><?= count($achievements) > 0 ? count($achievements) : '6' ?>+</span>
                        <span class="stat-label">Honors Won</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num"><?= count($articles) > 0 ? count($articles) : '6' ?>+</span>
                        <span class="stat-label">Technical Blogs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-num">100%</span>
                        <span class="stat-label">Deterministic Logic</span>
                    </div>
                </div>
            </div>

            <!-- Identity Board with Brutalist Visual Language -->
            <div class="identity-board cut-corner">
                <div class="board-header">
                    <span>[ID: NAV.004]</span>
                    <span>AI / SYSTEMS ARCHITECT</span>
                    <span>01 // BRAND</span>
                </div>

                <div class="pixel-avatar-wrap">
                    <?php if (!empty($profile['avatar']) && file_exists(__DIR__ . $profile['avatar'])): ?>
                        <img src="<?= e($profile['avatar']) ?>" alt="<?= e($profile['name']) ?>" class="pixel-avatar-img">
                        <div style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,15,16,0.85); border: 1px solid var(--clr-terracotta); font-family: var(--font-pixel); font-size: 0.65rem; color: var(--clr-sand); padding: 3px 8px; letter-spacing: 0.05em; pointer-events: none;">
                            [ ^ _ ^ ] PIXEL HUMAN
                        </div>
                    <?php else: ?>
                        <!-- Brutalist Pixel Graphic Avatar representation matching brand identity board -->
                        <div class="pixel-avatar-fallback">
                            <div style="font-family: var(--font-pixel); font-size: 1.8rem; color: var(--clr-sand); margin-bottom: 8px;">
                                [ ^ _ ^ ]
                            </div>
                            <div style="font-size: 0.85rem; color: var(--clr-terracotta); letter-spacing: 0.1em;">
                                PIXEL HUMAN
                            </div>
                            <div style="font-size: 0.72rem; color: var(--clr-stone); margin-top: 4px;">
                                ALWAYS ON. ALWAYS ON BRAND.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="telemetry-feed">
                    <div class="telemetry-row">
                        <span class="key">&gt; route_call:</span>
                        <span class="val green">connected (lead_id: #8281)</span>
                    </div>
                    <div class="telemetry-row">
                        <span class="key">&gt; status:</span>
                        <span class="val green">active // 24/7 uptime</span>
                    </div>
                    <div class="telemetry-row">
                        <span class="key">&gt; handoff:</span>
                        <span class="val">success (zero latency)</span>
                    </div>
                    <div class="telemetry-row">
                        <span class="key">&gt; core_domains:</span>
                        <span class="val">AI • Blockchain • QA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     ABOUT SECTION
     ========================================== -->
<section id="about">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[01 // 0001] // PHILOSOPHY & CAPABILITY</span>
            <h2 class="section-title" data-scramble>ABOUT THE ARCHITECT</h2>
        </div>

        <div class="about-grid">
            <div class="manifesto-box cut-corner">
                <h3 class="manifesto-title">Engineering With Intentionality</h3>
                <p>
                    I design and implement digital infrastructure where resilience, speed, and real-world utility are non-negotiable. Technology should be a driver of tangible, measurable impact—not speculative noise.
                </p>
                <p>
                    From crafting automated computer vision detection models for municipal waste management to authoring Ethereum smart contracts for localized renewable microgrids, every project is built from foundational principles of systems engineering.
                </p>
                <div class="binary-tag accent" style="margin-top: 14px;">
                    <span>CORE PHILOSOPHY:</span>
                    <span>DELETION OVER ADDITION. PROVABLE RELIABILITY.</span>
                </div>
            </div>

            <div class="coordinates-card cut-corner">
                <h3 style="font-size: 1.3rem; margin-bottom: 8px; color: var(--clr-sand);">System Coordinates</h3>
                <p style="font-size: 0.85rem; color: var(--clr-stone); margin-bottom: 20px;">
                    Verified endpoints for professional engagements and technical inquiries.
                </p>

                <ul class="coord-list">
                    <li class="coord-item">
                        <span class="coord-label">Email Coordinate</span>
                        <span class="coord-val"><?= e($profile['email']) ?></span>
                    </li>
                    <li class="coord-item">
                        <span class="coord-label">Telephony Link</span>
                        <span class="coord-val"><?= e($profile['phone']) ?></span>
                    </li>
                    <li class="coord-item">
                        <span class="coord-label">Code Repository</span>
                        <span class="coord-val"><?= e($profile['github']) ?></span>
                    </li>
                    <li class="coord-item">
                        <span class="coord-label">Professional Index</span>
                        <span class="coord-val"><?= e($profile['linkedin']) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     PROJECTS SECTION
     ========================================== -->
<section id="projects">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[02 // 0010] // PRODUCTION SYSTEMS</span>
            <h2 class="section-title" data-scramble>FEATURED ENGINEERING WORK</h2>
        </div>

        <!-- Filter Controls -->
        <div class="project-filters">
            <button class="filter-btn active" data-filter="all">[ALL SYSTEMS]</button>
            <button class="filter-btn" data-filter="AI">[ARTIFICIAL INTELLIGENCE]</button>
            <button class="filter-btn" data-filter="Blockchain">[BLOCKCHAIN / WEB3]</button>
            <button class="filter-btn" data-filter="Full Stack">[FULL STACK]</button>
            <button class="filter-btn" data-filter="DevOps">[DEVOPS & OBSERVABILITY]</button>
            <button class="filter-btn" data-filter="Web Development">[WEB APPS]</button>
        </div>

        <!-- Projects Grid -->
        <div class="projects-grid">
            <?php foreach ($projects as $proj): 
                $projJson = json_encode([
                    'id' => $proj['id'],
                    'title' => $proj['title'],
                    'subtitle' => $proj['subtitle'],
                    'description' => $proj['description'],
                    'detailedDescription' => $proj['detailed_description'],
                    'category' => $proj['category'],
                    'tech' => $proj['technologies'] ?? [],
                    'bullets' => $proj['bullets'] ?? [],
                    'github' => $proj['github'],
                    'link' => $proj['link']
                ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
            ?>
                <article class="project-card cut-corner" data-category="<?= e($proj['category']) ?>">
                    <div class="project-card-header">
                        <span style="color: var(--clr-stone);">SYS_ID: #<?= str_pad((string)$proj['id'], 3, '0', STR_PAD_LEFT) ?></span>
                        <?php if (!empty($proj['is_featured'])): ?>
                            <span class="binary-tag accent" style="padding: 2px 6px; font-size: 0.7rem;">FEATURED</span>
                        <?php endif; ?>
                    </div>

                    <div class="project-card-body">
                        <div class="project-category-tag"><?= e($proj['category']) ?></div>
                        <h3 class="project-card-title"><?= e($proj['title']) ?></h3>
                        <p class="project-card-desc"><?= e($proj['description']) ?></p>

                        <div class="tech-tags">
                            <?php foreach (array_slice($proj['technologies'] ?? [], 0, 5) as $tech): ?>
                                <span class="tech-badge"><?= e($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="project-card-footer">
                        <button class="btn btn-secondary btn-sm open-spec-btn" data-project-json='<?= $projJson ?>'>
                            SPECS &amp; ARCH &rarr;
                        </button>
                        
                        <div style="display: flex; gap: 8px;">
                            <?php if (!empty($proj['github'])): ?>
                                <a href="<?= e($proj['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" title="View Source on GitHub">
                                    [SRC]
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($proj['link'])): ?>
                                <a href="<?= e($proj['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-terracotta btn-sm" title="Live System">
                                    [RUN]
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     SKILLS SECTION
     ========================================== -->
<section id="skills" class="system-dots">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[03 // 0011] // TECHNICAL COMPETENCIES</span>
            <h2 class="section-title" data-scramble>CAPABILITIES MATRIX</h2>
        </div>

        <div class="skills-container">
            <?php foreach ($skillsGrouped as $categoryName => $catData): ?>
                <div class="skill-category-card cut-corner">
                    <h3 class="skill-cat-title">
                        <span><?= e($categoryName) ?></span>
                        <span class="binary-tag" style="font-size: 0.7rem;">MATRIX</span>
                    </h3>

                    <div class="skill-list">
                        <?php foreach ($catData['skills'] as $skill): ?>
                            <div class="skill-row">
                                <span class="skill-name"><?= e($skill['name']) ?></span>
                                <span class="skill-pct"><?= e((string)$skill['proficiency']) ?>% [11110]</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     ACHIEVEMENTS & HONORS
     ========================================== -->
<section id="achievements">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[04 // 0100] // RECOGNITION & BENCHMARKS</span>
            <h2 class="section-title" data-scramble>HONORS &amp; COMPETITIONS</h2>
        </div>

        <div class="achievements-timeline">
            <?php foreach ($achievements as $ach): ?>
                <div class="achievement-card cut-corner">
                    <div class="achievement-year">
                        <div><?= e($ach['year']) ?></div>
                        <div style="font-size: 0.72rem; color: var(--clr-stone); margin-top: 4px;"><?= e($ach['level']) ?></div>
                    </div>

                    <div>
                        <span class="achievement-prize"><?= e($ach['prize']) ?></span>
                        <h3 class="achievement-title"><?= e($ach['title']) ?></h3>
                        <div class="achievement-event">&gt; Event: <?= e($ach['event']) ?></div>
                        <p style="font-size: 0.95rem; color: #D3C3B0; line-height: 1.6; margin-bottom: 0;">
                            <?= e($ach['description']) ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     TALKS SECTION
     ========================================== -->
<section id="talks" class="system-dots">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[05 // 0101] // KNOWLEDGE TRANSMISSION</span>
            <h2 class="section-title" data-scramble>KEYNOTES &amp; TALKS</h2>
        </div>

        <div class="talks-grid">
            <?php foreach ($talks as $talk): ?>
                <div class="talk-card cut-corner">
                    <div>
                        <div class="binary-tag accent" style="margin-bottom: 12px; font-size: 0.72rem;">
                            SESSION // <?= e($talk['year']) ?>
                        </div>
                        <h3 class="talk-card-title"><?= e($talk['title']) ?></h3>
                        <p style="font-size: 0.95rem; color: #C3B4A3; line-height: 1.6;">
                            <?= e($talk['description']) ?>
                        </p>
                    </div>

                    <div style="margin-top: 20px; border-top: 1px solid var(--clr-stone-dark); padding-top: 14px; display: flex; justify-content: space-between; align-items: center;">
                        <span class="mono-data" style="color: var(--clr-stone); font-size: 0.8rem;">[INVITED SPEAKER]</span>
                        <span class="live-indicator"></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     ARTICLES & WRITING SECTION
     ========================================== -->
<section id="articles">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[06 // 0110] // PERSPECTIVES & PAPERS</span>
            <h2 class="section-title" data-scramble>TECHNICAL ARTICLES</h2>
        </div>

        <div class="articles-grid">
            <?php foreach ($articles as $art): ?>
                <article class="article-card cut-corner">
                    <div class="article-body">
                        <div class="article-meta">
                            <span><?= e($art['published_date']) ?></span>
                            <span class="mono-data">[DISPATCH]</span>
                        </div>

                        <h3 class="article-title"><?= e($art['title']) ?></h3>
                        <p class="article-summary"><?= e($art['summary']) ?></p>

                        <div class="tech-tags" style="margin-bottom: 20px;">
                            <?php foreach ($art['tags'] ?? [] as $t): ?>
                                <span class="tech-badge">#<?= e($t) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <a href="/blog-detail.php?slug=<?= urlencode($art['slug']) ?>" class="btn btn-secondary btn-sm" style="margin-top: auto;">
                            READ ANALYSIS &rarr;
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==========================================
     CONTACT TRANSMISSION TERMINAL
     ========================================== -->
<section id="contact">
    <div class="container">
        <div class="section-header">
            <span class="section-subtitle">[07 // 0111] // DIRECT GATEWAY</span>
            <h2 class="section-title" data-scramble>DISPATCH TRANSMISSION</h2>
        </div>

        <div class="contact-grid">
            <div>
                <h3 style="font-size: 1.5rem; margin-bottom: 16px; color: var(--clr-sand);">Start a Dialogue</h3>
                <p style="font-size: 1.05rem; line-height: 1.7; color: #D3C3B0; margin-bottom: 24px;">
                    Whether evaluating system architectures, discussing AI deployments, or exploring engineering opportunities, my inbox is monitored directly. Messages submitted here are written immediately to the local database queue and flagged for review.
                </p>

                <div class="telemetry-feed" style="margin-bottom: 24px;">
                    <div class="telemetry-row">
                        <span class="key">ENCRYPTION:</span>
                        <span class="val">SSL / TLS STRICT</span>
                    </div>
                    <div class="telemetry-row">
                        <span class="key">RESPONSE_SLA:</span>
                        <span class="val">&lt; 24 HOURS</span>
                    </div>
                    <div class="telemetry-row">
                        <span class="key">GATEWAY_STATUS:</span>
                        <span class="val green">ONLINE &amp; LISTENING</span>
                    </div>
                </div>

                <div class="binary-tag accent">
                    <span>TRANSMIT PROTOCOL:</span>
                    <span>REST / JSON / PDO</span>
                </div>
            </div>

            <!-- Terminal Card -->
            <div class="terminal-card cut-corner">
                <div class="terminal-header">
                    <span>TRANSMISSION TERMINAL // v1.0</span>
                    <span class="live-indicator"></span>
                </div>

                <div class="terminal-body">
                    <form id="contact-form" method="POST" action="/index.php" novalidate>
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="contact">

                        <div class="form-group">
                            <label class="form-label" for="contact-name">&gt; SENDER IDENTITY (NAME) *</label>
                            <input type="text" id="contact-name" name="name" class="form-control" placeholder="e.g. Elena Vance" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact-email">&gt; RETURN COORDINATE (EMAIL) *</label>
                            <input type="email" id="contact-email" name="email" class="form-control" placeholder="elena@example.com" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact-subject">&gt; PACKET SUBJECT</label>
                            <input type="text" id="contact-subject" name="subject" class="form-control" placeholder="AI Consulting / Project Inquiry">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="contact-message">&gt; PAYLOAD MESSAGE *</label>
                            <textarea id="contact-message" name="message" class="form-control" placeholder="Describe the problem, timeline, or engineering goals..." required></textarea>
                        </div>

                        <div id="contact-status-box" style="margin-bottom: 20px; font-family: var(--font-mono); font-size: 0.88rem; min-height: 24px;"></div>

                        <button id="contact-submit-btn" type="submit" class="btn btn-primary" style="width: 100%;">
                            DISPATCH PACKET &rarr;
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
