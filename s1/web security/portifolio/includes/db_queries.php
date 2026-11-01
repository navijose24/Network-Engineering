<?php
/**
 * Data Access Layer (DAL)
 * Isolates all SQL statements from presentation templates.
 * All queries use prepared statements via PDO.
 */

require_once __DIR__ . '/../config/db.php';

// ==========================================
// 1. PROFILE
// ==========================================

function get_profile_data(): array {
    try {
        $db = get_db();
        $stmt = $db->query("SELECT * FROM profile ORDER BY id ASC LIMIT 1");
        $res = $stmt->fetch();
        if ($res) return $res;
    } catch (PDOException $e) {
        error_log("DAL Error (get_profile_data): " . $e->getMessage());
    }

    // Default fallback structure
    return [
        'id' => 1,
        'name' => 'Navaneetha Jose',
        'tagline' => 'Developer • Innovator • Solution Architect',
        'intro' => 'I\'m a developer driven by a deep curiosity for how intelligent systems can shape real-world change.',
        'email' => 'navaneethajose004@gmail.com',
        'phone' => '+91 8281252386',
        'linkedin' => 'linkedin.com/in/navaneetha-jose',
        'github' => 'github.com/navijose24',
        'instagram' => 'instagram.com/navaneetha_jose',
        'avatar' => '/Life/1.png',
        'resume_url' => ''
    ];
}

function update_profile_data(array $data): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("
            UPDATE profile SET
                name = :name,
                tagline = :tagline,
                intro = :intro,
                email = :email,
                phone = :phone,
                linkedin = :linkedin,
                github = :github,
                instagram = :instagram,
                avatar = :avatar,
                resume_url = :resume_url
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name' => $data['name'],
            ':tagline' => $data['tagline'],
            ':intro' => $data['intro'],
            ':email' => $data['email'],
            ':phone' => $data['phone'] ?? null,
            ':linkedin' => $data['linkedin'] ?? null,
            ':github' => $data['github'] ?? null,
            ':instagram' => $data['instagram'] ?? null,
            ':avatar' => $data['avatar'] ?? null,
            ':resume_url' => $data['resume_url'] ?? null,
            ':id' => $data['id'] ?? 1
        ]);
    } catch (PDOException $e) {
        error_log("DAL Error (update_profile_data): " . $e->getMessage());
        return false;
    }
}

// ==========================================
// 2. PROJECTS
// ==========================================

function get_projects(?int $limit = null, bool $onlyFeatured = false, bool $onlyPublished = true, ?string $category = null): array {
    try {
        $db = get_db();
        $conditions = [];
        $params = [];

        if ($onlyPublished) {
            $conditions[] = "is_published = 1";
        }
        if ($onlyFeatured) {
            $conditions[] = "is_featured = 1";
        }
        if (!empty($category) && $category !== 'All') {
            $conditions[] = "category LIKE :cat";
            $params[':cat'] = "%{$category}%";
        }

        $sql = "SELECT * FROM projects";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY display_order ASC, id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $projects = $stmt->fetchAll();

        // Attach technologies and bullets
        foreach ($projects as &$p) {
            $tStmt = $db->prepare("SELECT name FROM project_technologies WHERE project_id = :pid");
            $tStmt->execute([':pid' => $p['id']]);
            $p['technologies'] = $tStmt->fetchAll(PDO::FETCH_COLUMN);

            $bStmt = $db->prepare("SELECT bullet FROM project_bullets WHERE project_id = :pid ORDER BY display_order ASC");
            $bStmt->execute([':pid' => $p['id']]);
            $p['bullets'] = $bStmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $projects;
    } catch (PDOException $e) {
        error_log("DAL Error (get_projects): " . $e->getMessage());
        return [];
    }
}

function get_project_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM projects WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $project = $stmt->fetch();
        if (!$project) return null;

        $tStmt = $db->prepare("SELECT name FROM project_technologies WHERE project_id = :pid");
        $tStmt->execute([':pid' => $id]);
        $project['technologies'] = $tStmt->fetchAll(PDO::FETCH_COLUMN);

        $bStmt = $db->prepare("SELECT bullet FROM project_bullets WHERE project_id = :pid ORDER BY display_order ASC");
        $bStmt->execute([':pid' => $id]);
        $project['bullets'] = $bStmt->fetchAll(PDO::FETCH_COLUMN);

        $iStmt = $db->prepare("SELECT image_url, caption FROM project_images WHERE project_id = :pid");
        $iStmt->execute([':pid' => $id]);
        $project['gallery'] = $iStmt->fetchAll();

        return $project;
    } catch (PDOException $e) {
        error_log("DAL Error (get_project_by_id): " . $e->getMessage());
        return null;
    }
}

function save_project(array $data, array $techs = [], array $bullets = []): int {
    $db = get_db();
    $isUpdate = !empty($data['id']);

    if ($isUpdate) {
        $stmt = $db->prepare("
            UPDATE projects SET
                title = :title,
                subtitle = :subtitle,
                description = :description,
                detailed_description = :detailed_desc,
                category = :category,
                image = :image,
                video = :video,
                github = :github,
                link = :link,
                is_featured = :is_featured,
                display_order = :display_order,
                is_published = :is_published
            WHERE id = :id
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'] ?? null,
            ':description' => $data['description'],
            ':detailed_desc' => $data['detailed_description'] ?? null,
            ':category' => $data['category'],
            ':image' => $data['image'] ?? null,
            ':video' => $data['video'] ?? null,
            ':github' => $data['github'] ?? null,
            ':link' => $data['link'] ?? null,
            ':is_featured' => !empty($data['is_featured']) ? 1 : 0,
            ':display_order' => (int) ($data['display_order'] ?? 0),
            ':is_published' => isset($data['is_published']) ? (int) $data['is_published'] : 1,
            ':id' => $data['id']
        ]);
        $projectId = (int) $data['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO projects (
                title, subtitle, description, detailed_description, category,
                image, video, github, link, is_featured, display_order, is_published
            ) VALUES (
                :title, :subtitle, :description, :detailed_desc, :category,
                :image, :video, :github, :link, :is_featured, :display_order, :is_published
            )
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':subtitle' => $data['subtitle'] ?? null,
            ':description' => $data['description'],
            ':detailed_desc' => $data['detailed_description'] ?? null,
            ':category' => $data['category'],
            ':image' => $data['image'] ?? null,
            ':video' => $data['video'] ?? null,
            ':github' => $data['github'] ?? null,
            ':link' => $data['link'] ?? null,
            ':is_featured' => !empty($data['is_featured']) ? 1 : 0,
            ':display_order' => (int) ($data['display_order'] ?? 0),
            ':is_published' => isset($data['is_published']) ? (int) $data['is_published'] : 1
        ]);
        $projectId = (int) $db->lastInsertId();
    }

    // Refresh Technologies
    $delTech = $db->prepare("DELETE FROM project_technologies WHERE project_id = :pid");
    $delTech->execute([':pid' => $projectId]);
    $insTech = $db->prepare("INSERT INTO project_technologies (project_id, name) VALUES (:pid, :name)");
    foreach ($techs as $tech) {
        $tech = trim($tech);
        if ($tech !== '') {
            $insTech->execute([':pid' => $projectId, ':name' => $tech]);
        }
    }

    // Refresh Bullets
    $delBullets = $db->prepare("DELETE FROM project_bullets WHERE project_id = :pid");
    $delBullets->execute([':pid' => $projectId]);
    $insBullet = $db->prepare("INSERT INTO project_bullets (project_id, bullet, display_order) VALUES (:pid, :bullet, :order)");
    foreach ($bullets as $idx => $bullet) {
        $bullet = trim($bullet);
        if ($bullet !== '') {
            $insBullet->execute([':pid' => $projectId, ':bullet' => $bullet, ':order' => $idx + 1]);
        }
    }

    return $projectId;
}

function delete_project(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM projects WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        error_log("DAL Error (delete_project): " . $e->getMessage());
        return false;
    }
}

// ==========================================
// 3. SKILLS
// ==========================================

function get_skills_grouped(): array {
    try {
        $db = get_db();
        $catStmt = $db->query("SELECT * FROM skill_categories ORDER BY display_order ASC, id ASC");
        $categories = $catStmt->fetchAll();

        $grouped = [];
        $skillStmt = $db->prepare("SELECT * FROM skills WHERE category_id = :cid ORDER BY display_order ASC, id ASC");

        foreach ($categories as $cat) {
            $skillStmt->execute([':cid' => $cat['id']]);
            $grouped[$cat['name']] = [
                'category_id' => $cat['id'],
                'skills' => $skillStmt->fetchAll()
            ];
        }

        return $grouped;
    } catch (PDOException $e) {
        error_log("DAL Error (get_skills_grouped): " . $e->getMessage());
        return [];
    }
}

function get_all_skill_categories(): array {
    try {
        $db = get_db();
        return $db->query("SELECT * FROM skill_categories ORDER BY display_order ASC")->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function save_skill(array $data): bool {
    $db = get_db();
    if (!empty($data['id'])) {
        $stmt = $db->prepare("
            UPDATE skills SET
                category_id = :cid,
                name = :name,
                proficiency = :prof,
                display_order = :ord
            WHERE id = :id
        ");
        return $stmt->execute([
            ':cid' => (int) $data['category_id'],
            ':name' => $data['name'],
            ':prof' => (int) ($data['proficiency'] ?? 90),
            ':ord' => (int) ($data['display_order'] ?? 0),
            ':id' => (int) $data['id']
        ]);
    } else {
        $stmt = $db->prepare("
            INSERT INTO skills (category_id, name, proficiency, display_order)
            VALUES (:cid, :name, :prof, :ord)
        ");
        return $stmt->execute([
            ':cid' => (int) $data['category_id'],
            ':name' => $data['name'],
            ':prof' => (int) ($data['proficiency'] ?? 90),
            ':ord' => (int) ($data['display_order'] ?? 0)
        ]);
    }
}

function delete_skill(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM skills WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ==========================================
// 4. ACHIEVEMENTS
// ==========================================

function get_achievements_list(): array {
    try {
        $db = get_db();
        $stmt = $db->query("SELECT * FROM achievements ORDER BY display_order ASC, id DESC");
        $achievements = $stmt->fetchAll();

        $imgStmt = $db->prepare("SELECT image_url FROM achievement_images WHERE achievement_id = :aid ORDER BY display_order ASC");
        foreach ($achievements as &$a) {
            $imgStmt->execute([':aid' => $a['id']]);
            $a['images'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $achievements;
    } catch (PDOException $e) {
        error_log("DAL Error (get_achievements_list): " . $e->getMessage());
        return [];
    }
}

function get_achievement_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM achievements WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $ach = $stmt->fetch();
        if (!$ach) return null;

        $imgStmt = $db->prepare("SELECT image_url FROM achievement_images WHERE achievement_id = :aid ORDER BY display_order ASC");
        $imgStmt->execute([':aid' => $id]);
        $ach['images'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

        return $ach;
    } catch (PDOException $e) {
        return null;
    }
}

function save_achievement(array $data, array $images = []): int {
    $db = get_db();
    $isUpdate = !empty($data['id']);

    if ($isUpdate) {
        $stmt = $db->prepare("
            UPDATE achievements SET
                title = :title,
                event = :event,
                year = :year,
                prize = :prize,
                level = :level,
                description = :description,
                display_order = :ord
            WHERE id = :id
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':event' => $data['event'],
            ':year' => $data['year'],
            ':prize' => $data['prize'],
            ':level' => $data['level'],
            ':description' => $data['description'],
            ':ord' => (int) ($data['display_order'] ?? 0),
            ':id' => $data['id']
        ]);
        $achId = (int) $data['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO achievements (title, event, year, prize, level, description, display_order)
            VALUES (:title, :event, :year, :prize, :level, :description, :ord)
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':event' => $data['event'],
            ':year' => $data['year'],
            ':prize' => $data['prize'],
            ':level' => $data['level'],
            ':description' => $data['description'],
            ':ord' => (int) ($data['display_order'] ?? 0)
        ]);
        $achId = (int) $db->lastInsertId();
    }

    if (!empty($images)) {
        $del = $db->prepare("DELETE FROM achievement_images WHERE achievement_id = :aid");
        $del->execute([':aid' => $achId]);
        $ins = $db->prepare("INSERT INTO achievement_images (achievement_id, image_url, display_order) VALUES (:aid, :url, :ord)");
        foreach ($images as $idx => $url) {
            $url = trim($url);
            if ($url !== '') {
                $ins->execute([':aid' => $achId, ':url' => $url, ':ord' => $idx + 1]);
            }
        }
    }

    return $achId;
}

function delete_achievement(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM achievements WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ==========================================
// 5. TALKS
// ==========================================

function get_talks_list(): array {
    try {
        $db = get_db();
        return $db->query("SELECT * FROM talks ORDER BY display_order ASC, id DESC")->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function get_talk_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM talks WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

function save_talk(array $data): int {
    $db = get_db();
    if (!empty($data['id'])) {
        $stmt = $db->prepare("
            UPDATE talks SET
                title = :title,
                description = :description,
                year = :year,
                video_thumbnail = :thumb,
                video_url = :url,
                display_order = :ord
            WHERE id = :id
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':year' => $data['year'],
            ':thumb' => $data['video_thumbnail'] ?? null,
            ':url' => $data['video_url'] ?? null,
            ':ord' => (int) ($data['display_order'] ?? 0),
            ':id' => $data['id']
        ]);
        return (int) $data['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO talks (title, description, year, video_thumbnail, video_url, display_order)
            VALUES (:title, :description, :year, :thumb, :url, :ord)
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':year' => $data['year'],
            ':thumb' => $data['video_thumbnail'] ?? null,
            ':url' => $data['video_url'] ?? null,
            ':ord' => (int) ($data['display_order'] ?? 0)
        ]);
        return (int) $db->lastInsertId();
    }
}

function delete_talk(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM talks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ==========================================
// 6. ARTICLES / BLOG POSTS
// ==========================================

function get_articles_list(bool $onlyPublished = true, ?int $limit = null): array {
    try {
        $db = get_db();
        $sql = "SELECT * FROM articles";
        if ($onlyPublished) {
            $sql .= " WHERE is_published = 1";
        }
        $sql .= " ORDER BY id DESC";
        if ($limit !== null) {
            $sql .= " LIMIT " . (int) $limit;
        }

        $stmt = $db->query($sql);
        $articles = $stmt->fetchAll();

        $tagStmt = $db->prepare("SELECT tag FROM article_tags WHERE article_id = :aid");
        foreach ($articles as &$art) {
            $tagStmt->execute([':aid' => $art['id']]);
            $art['tags'] = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $articles;
    } catch (PDOException $e) {
        error_log("DAL Error (get_articles_list): " . $e->getMessage());
        return [];
    }
}

function get_article_by_slug(string $slug): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM articles WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $art = $stmt->fetch();
        if (!$art) return null;

        $tagStmt = $db->prepare("SELECT tag FROM article_tags WHERE article_id = :aid");
        $tagStmt->execute([':aid' => $art['id']]);
        $art['tags'] = $tagStmt->fetchAll(PDO::FETCH_COLUMN);

        return $art;
    } catch (PDOException $e) {
        return null;
    }
}

function get_article_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $art = $stmt->fetch();
        if (!$art) return null;

        $tagStmt = $db->prepare("SELECT tag FROM article_tags WHERE article_id = :aid");
        $tagStmt->execute([':aid' => $id]);
        $art['tags'] = $tagStmt->fetchAll(PDO::FETCH_COLUMN);

        return $art;
    } catch (PDOException $e) {
        return null;
    }
}

function save_article(array $data, array $tags = []): int {
    $db = get_db();
    $isUpdate = !empty($data['id']);
    $slug = !empty($data['slug']) ? slugify($data['slug']) : slugify($data['title']);

    if ($isUpdate) {
        $stmt = $db->prepare("
            UPDATE articles SET
                title = :title,
                slug = :slug,
                summary = :summary,
                content = :content,
                image = :image,
                published_date = :pdate,
                is_published = :is_pub
            WHERE id = :id
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $slug,
            ':summary' => $data['summary'],
            ':content' => $data['content'],
            ':image' => $data['image'] ?? null,
            ':pdate' => $data['published_date'] ?? date('M d, Y'),
            ':is_pub' => !empty($data['is_published']) ? 1 : 0,
            ':id' => $data['id']
        ]);
        $artId = (int) $data['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO articles (title, slug, summary, content, image, published_date, is_published)
            VALUES (:title, :slug, :summary, :content, :image, :pdate, :is_pub)
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':slug' => $slug,
            ':summary' => $data['summary'],
            ':content' => $data['content'],
            ':image' => $data['image'] ?? null,
            ':pdate' => $data['published_date'] ?? date('M d, Y'),
            ':is_pub' => !empty($data['is_published']) ? 1 : 0
        ]);
        $artId = (int) $db->lastInsertId();
    }

    // Refresh Tags
    $del = $db->prepare("DELETE FROM article_tags WHERE article_id = :aid");
    $del->execute([':aid' => $artId]);
    $ins = $db->prepare("INSERT INTO article_tags (article_id, tag) VALUES (:aid, :tag)");
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if ($tag !== '') {
            $ins->execute([':aid' => $artId, ':tag' => $tag]);
        }
    }

    return $artId;
}

function delete_article(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ==========================================
// 7. CREATIVE WORKS (LINES AND LAYERS)
// ==========================================

function get_creative_works_list(): array {
    try {
        $db = get_db();
        $stmt = $db->query("SELECT * FROM creative_works ORDER BY display_order ASC, id ASC");
        $works = $stmt->fetchAll();

        $imgStmt = $db->prepare("SELECT * FROM creative_work_images WHERE work_id = :wid ORDER BY page_number ASC");
        foreach ($works as &$w) {
            $imgStmt->execute([':wid' => $w['id']]);
            $w['images'] = $imgStmt->fetchAll();
        }

        return $works;
    } catch (PDOException $e) {
        return [];
    }
}

function get_creative_work_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM creative_works WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $work = $stmt->fetch();
        if (!$work) return null;

        $imgStmt = $db->prepare("SELECT * FROM creative_work_images WHERE work_id = :wid ORDER BY page_number ASC");
        $imgStmt->execute([':wid' => $id]);
        $work['images'] = $imgStmt->fetchAll();

        return $work;
    } catch (PDOException $e) {
        return null;
    }
}

function save_creative_work(array $data, array $images = []): int {
    $db = get_db();
    $isUpdate = !empty($data['id']);

    if ($isUpdate) {
        $stmt = $db->prepare("
            UPDATE creative_works SET
                title = :title,
                work_type = :type,
                subtitle = :subtitle,
                description = :description,
                thumbnail = :thumb,
                display_order = :ord
            WHERE id = :id
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':type' => $data['work_type'] ?? 'image',
            ':subtitle' => $data['subtitle'] ?? null,
            ':description' => $data['description'] ?? null,
            ':thumb' => $data['thumbnail'] ?? null,
            ':ord' => (int) ($data['display_order'] ?? 0),
            ':id' => $data['id']
        ]);
        $workId = (int) $data['id'];
    } else {
        $stmt = $db->prepare("
            INSERT INTO creative_works (title, work_type, subtitle, description, thumbnail, display_order)
            VALUES (:title, :type, :subtitle, :description, :thumb, :ord)
        ");
        $stmt->execute([
            ':title' => $data['title'],
            ':type' => $data['work_type'] ?? 'image',
            ':subtitle' => $data['subtitle'] ?? null,
            ':description' => $data['description'] ?? null,
            ':thumb' => $data['thumbnail'] ?? null,
            ':ord' => (int) ($data['display_order'] ?? 0)
        ]);
        $workId = (int) $db->lastInsertId();
    }

    if (!empty($images)) {
        $del = $db->prepare("DELETE FROM creative_work_images WHERE work_id = :wid");
        $del->execute([':wid' => $workId]);
        $ins = $db->prepare("INSERT INTO creative_work_images (work_id, image_url, page_number, caption) VALUES (:wid, :url, :page, :cap)");
        foreach ($images as $idx => $img) {
            $url = is_array($img) ? ($img['image_url'] ?? '') : $img;
            $caption = is_array($img) ? ($img['caption'] ?? '') : '';
            if (trim($url) !== '') {
                $ins->execute([
                    ':wid' => $workId,
                    ':url' => trim($url),
                    ':page' => $idx + 1,
                    ':cap' => $caption ?: "Page " . ($idx + 1)
                ]);
            }
        }
    }

    return $workId;
}

function delete_creative_work(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM creative_works WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

// ==========================================
// 8. CONTACT SUBMISSIONS
// ==========================================

function record_contact_submission(string $name, string $email, ?string $subject, string $message, ?string $ip): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("
            INSERT INTO contact_submissions (name, email, subject, message, ip_address)
            VALUES (:name, :email, :subject, :msg, :ip)
        ");
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':msg' => $message,
            ':ip' => $ip
        ]);
    } catch (PDOException $e) {
        error_log("DAL Error (record_contact_submission): " . $e->getMessage());
        return false;
    }
}

function get_contact_submissions_list(int $limit = 100): array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function get_contact_submission_by_id(int $id): ?array {
    try {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM contact_submissions WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        return null;
    }
}

function mark_contact_submission_read(int $id, int $status = 1): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("UPDATE contact_submissions SET is_read = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function delete_contact_submission(int $id): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("DELETE FROM contact_submissions WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

function get_unread_contacts_count(): int {
    try {
        $db = get_db();
        return (int) $db->query("SELECT COUNT(*) FROM contact_submissions WHERE is_read = 0")->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

// ==========================================
// 9. SITE SETTINGS & METRICS
// ==========================================

function get_site_settings(): array {
    try {
        $db = get_db();
        $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r['setting_key']] = $r['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

function update_site_setting(string $key, ?string $value): bool {
    try {
        $db = get_db();
        $stmt = $db->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES (:k, :v)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        return $stmt->execute([':k' => $key, ':v' => $value]);
    } catch (PDOException $e) {
        return false;
    }
}

function get_admin_dashboard_metrics(): array {
    $db = get_db();
    return [
        'projects'     => (int) $db->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
        'achievements' => (int) $db->query("SELECT COUNT(*) FROM achievements")->fetchColumn(),
        'articles'     => (int) $db->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
        'talks'        => (int) $db->query("SELECT COUNT(*) FROM talks")->fetchColumn(),
        'unread_msgs'  => (int) $db->query("SELECT COUNT(*) FROM contact_submissions WHERE is_read = 0")->fetchColumn(),
        'total_msgs'   => (int) $db->query("SELECT COUNT(*) FROM contact_submissions")->fetchColumn()
    ];
}
