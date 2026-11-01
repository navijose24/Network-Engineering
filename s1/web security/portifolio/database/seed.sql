-- ===================================================================
-- Portfolio Seed Data
-- ===================================================================

USE `portfolio_db`;

-- 1. Default Admin Account
-- Username: admin
-- Email: admin@portfolio.local
-- Password: Admin@12345 (BCRYPT hash)
INSERT INTO `admins` (`id`, `username`, `email`, `password_hash`)
VALUES (1, 'admin', 'admin@portfolio.local', '$2y$10$wT5gWd992e59y2t53i84g.xMh/631/iA4sV2wWkZz2m7mQ5JdE3eC')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- 2. Profile
INSERT INTO `profile` (`id`, `name`, `tagline`, `intro`, `email`, `phone`, `linkedin`, `github`, `instagram`, `avatar`)
VALUES (
    1,
    'Navaneetha Jose',
    'Developer • Innovator • Solution Architect',
    'I\'m a developer driven by a deep curiosity for how intelligent systems can shape real-world change. My work lies at the intersection of AI, blockchain, and sustainability, where technology becomes a tool for impact—not just innovation. I focus on building systems that are practical, scalable, and future-oriented—from AI-powered waste detection to decentralized energy trading platforms. I believe good engineering is not just about writing efficient code, but about asking the right questions, designing responsibly, and continuously evolving with technology.',
    'navaneethajose004@gmail.com',
    '+91 8281252386',
    'linkedin.com/in/navaneetha-jose',
    'github.com/navijose24',
    'instagram.com/navaneetha_jose',
    '/Life/1.png'
)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 3. Projects
INSERT INTO `projects` (`id`, `title`, `subtitle`, `description`, `detailed_description`, `category`, `image`, `video`, `github`, `link`, `is_featured`, `display_order`, `is_published`)
VALUES
(11, 'SkyLooms', 'AI-Powered Smart Airline Booking Platform',
 'A full-stack airline booking platform. Features flight, hotel, and cab bookings, an AI-powered travel assistant, and a comprehensive QA suite with 100+ Playwright automated tests.',
 'SkyLooms is a modern travel platform that combines airline ticket booking, accommodation reservations, airport cab services, and an AI-powered travel assistant into a seamless experience. The application follows a modular architecture with a React frontend, Django REST backend, and a FastAPI-based AI microservice powered by Groq\'s Llama 3.3 70B model. It includes personalized travel recommendations, conversational booking assistance, secure authentication, and extensive quality assurance through manual testing, Postman API testing, and over 100 Playwright automated end-to-end test cases.',
 'Full Stack • AI • QA', '/project/skylooms_demo.mp4', '/project/skylooms_demo.mp4', 'https://github.com/navijose24/Skylooms', 'https://github.com/navijose24/Skylooms', 1, 1, 1),

(10, 'System & DB Monitoring Suite', 'Real-time Infrastructure Observability with Prometheus & Grafana',
 'Built a robust, real-time system and database monitoring suite utilizing Prometheus, Grafana, Node Exporter, and MySQL Exporter to collect, visualize, and analyze system-level and database performance metrics.',
 'This observability platform offers comprehensive tracking of system-level metrics (CPU loads, memory footprints, disk inputs/outputs, network interfaces) and core database operations (active connection count, queries per second, InnoDB pool utilization, database health). The deployment uses Docker Compose to containerize each service, enabling a fast and modular infrastructure-as-code setup for staging and production diagnostics.',
 'DevOps', '/project/prometheus-grafana-system.png', NULL, 'https://github.com/navijose24/monitoring-system-prometheus-grafana/', 'https://github.com/navijose24/monitoring-system-prometheus-grafana/', 1, 2, 1),

(1, 'Wattrix', 'Blockchain-based P2P Energy Trading System',
 'Engineered a decentralized peer-to-peer renewable energy trading platform using Ethereum-based Solidity smart contracts, integrated with time-series ML models for real-time surplus forecasting.',
 'Wattrix is a revolutionary decentralized platform designed to simulate renewable energy trading between smart houses. By leveraging Ethereum smart contracts, it ensures secure, transparent, and tamper-proof transactions. It features LSTM forecasting for energy surplus/deficit prediction and weather-based generation forecasting, all integrated into a real-time monitoring dashboard.',
 'Blockchain • AI • Full Stack', '/project-wattrix.jpg', 'https://www.canva.com/design/DAHNlfuGqsk/T0GoXaloJZg6DLJDAQwKDw/view?embed', 'https://github.com/navijose24/WATTRIX_final', 'https://github.com/navijose24/WATTRIX_final', 1, 3, 1),

(2, 'DumpDetec', 'AI-powered Real-time Waste Detection System',
 'Built an AI-powered object detection system using YOLO v8 to identify waste dumping events in real-time, leveraging transfer learning on a custom augmented dataset.',
 'DumpDetec is a state-of-the-art AI solution for detecting illegal waste disposal from moving vehicles. It optimizes a YOLO v8 model to achieve high mean average precision (mAP) for speed and accuracy in production ML settings.',
 'AI • Full Stack', '/project-dumpdetec.jpg', '/project/DumpDetec.mp4', 'https://github.com/navijose24/DumpDetec', 'https://github.com/navijose24/DumpDetec', 1, 4, 1),

(3, 'Pathfinder', 'Intelligent Career Guidance System',
 'Designed a hybrid career recommendation engine using rule-based decision trees and machine learning classification models to guide students based on their unique skillsets.',
 'Pathfinder uses a blend of machine learning (such as Decision Trees and K-Nearest Neighbors) and custom rule logic trees to recommend personalized career paths. The system guides users towards opportunities that align with their skills, interests, and academic backgrounds.',
 'AI • Full Stack', '/project-pathfinder.jpg', 'https://pathfinder-78xc.onrender.com/', 'https://github.com/navijose24/PathFinder-', 'https://pathfinder-78xc.onrender.com/', 1, 5, 1),

(4, 'Bloom', 'Algal Bloom Forecasting & Visualisation System',
 'Developed an interactive 3D web platform utilizing NASA satellite imagery and ML time-series models to visualize and forecast global algal bloom events.',
 'Bloom is an immersive 3D visualization and forecasting tool for monitoring vegetation and algal bloom health. By analyzing sea surface temperature, chlorophyll concentration, and nutrients, the platform helps researchers identify and predict environmental concerns.',
 'AI • Full Stack', '/project-bloom.jpg', '/project/Bloom.mp4', 'https://github.com/navijose24/Bloom', 'https://github.com/navijose24/Bloom', 0, 6, 1),

(5, 'Aadhaar OCR', 'Intelligent Document Data Extraction Pipeline',
 'Created an automated identity verification pipeline using Tesseract OCR and OpenCV to preprocess images and extract structured text fields into formatted JSON.',
 'This OCR-based document processing system is designed to automate key-value extraction from Indian Aadhaar cards. By applying advanced OpenCV preprocessing steps (greyscale, Gaussian blur, morphological ops), the pipeline drastically improves character recognition accuracy on low-resolution scans.',
 'AI', '/project-ocr.jpg', NULL, 'https://github.com/navijose24/Aadhar-OCR-Extraction-using-Tesseract', 'https://github.com/navijose24/Aadhar-OCR-Extraction-using-Tesseract', 0, 7, 1),

(6, 'MediAlert+', 'Full-Stack Medication Reminder & Alert System',
 'Built a full-stack web and notification system that allows nurses to create and manage patient medication schedules, while patients automatically receive timely alerts via SMS, with confirmation tracking.',
 'MediAlert+ bridges the communication gap between healthcare providers and patients regarding medication adherence. This full-stack solution allows nurses to input complex medication schedules into a centralized dashboard. The system then automates the delivery of SMS reminders to patients at precise times. Crucially, it includes a feedback loop where patients can confirm intake, updating the nurse\'s dashboard in real-time.',
 'Full Stack', '/project-medialert.jpg', '/project/medialert.mp4', NULL, '/project/medialert.mp4', 0, 8, 1),

(7, 'SEO-analyser', 'SEO Quality Analysis Tool',
 'An SEO Analyzer is a tool that checks your website or webpage for Search Engine Optimization (SEO) quality—helping you understand how well your content will rank in search engines like Google.',
 'The SEO Analyser is a comprehensive web tool designed to audit websites for search engine optimization best practices. It parses HTML content to check for critical elements like meta tags, alt attributes, heading hierarchies, and broken links. The tool provides a detailed score and actionable recommendations to improve page ranking.',
 'Web Development', '/project-seo.jpg', NULL, 'https://github.com/navijose24/SEO-analyser', 'https://github.com/navijose24/SEO-analyser', 0, 9, 1),

(8, 'park-voting-system', 'Park Information Management System',
 'A Python-based system for extracting and managing park information from HTML sources. This project processes park data and converts it into a structured JSON format for further use in a voting system.',
 'This system automates the extraction and structuring of unstructured park data from various web sources. Using Python\'s scraping libraries, it aggregates information such as park amenities, location, and user reviews. The data is then normalized and exported as structured JSON.',
 'Web Development', '/project-park.jpg', NULL, 'https://github.com/navijose24/park-voting-system-', 'https://github.com/navijose24/park-voting-system-', 0, 10, 1),

(9, 'Flappy Bird Clone', '2D Game Development',
 'Created a simple interactive game using Unity with custom physics and responsive controls.',
 'A faithful recreation of the classic Flappy Bird game, developed to explore the fundamentals of 2D game physics and input handling in Unity. This project involves custom C# scripting to manage bird mechanics, procedural pipe generation, and collision detection.',
 'Game Dev', '/project-game.jpg', NULL, 'https://github.com/navijose24/Flappy-Bird-Clone', 'https://github.com/navijose24/Flappy-Bird-Clone', 0, 11, 1)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- Project Technologies
INSERT INTO `project_technologies` (`project_id`, `name`) VALUES
(11, 'React 19'), (11, 'Django'), (11, 'FastAPI'), (11, 'Groq API'), (11, 'Llama 3.3 70B'), (11, 'SQLite'), (11, 'Agentic AI'), (11, 'Playwright'), (11, 'Postman'),
(10, 'Prometheus'), (10, 'Grafana'), (10, 'Node Exporter'), (10, 'MySQL Exporter'), (10, 'Docker'), (10, 'DevOps'),
(1, 'Solidity'), (1, 'Ethereum'), (1, 'Blockchain'), (1, 'LSTM'), (1, 'React'), (1, 'Python'),
(2, 'YOLO v8'), (2, 'Roboflow'), (2, 'Transfer Learning'), (2, 'OpenCV'), (2, 'Flask'), (2, 'React'),
(3, 'Python'), (3, 'Machine Learning'), (3, 'Decision Tree'), (3, 'KNN'),
(4, 'Three.js'), (4, 'React'), (4, 'Python'), (4, 'NASA Data'), (4, 'LSTM'), (4, 'ARIMA'),
(5, 'Python'), (5, 'Tesseract OCR'), (5, 'OpenCV'), (5, 'Regex'), (5, 'JSON'),
(6, 'React'), (6, 'TypeScript'), (6, 'Python'), (6, 'Node.js'), (6, 'Docker'), (6, 'SMS APIs'),
(7, 'JavaScript'), (7, 'HTML'), (7, 'CSS'),
(8, 'Python'), (8, 'HTML'), (8, 'JSON'),
(9, 'Unity'), (9, 'C#');

-- Project Bullets
INSERT INTO `project_bullets` (`project_id`, `bullet`, `display_order`) VALUES
(11, 'Developed a full-stack travel platform enabling flight, hotel, and cab bookings using React, Django, and PostgreSQL.', 1),
(11, 'Built SkyMind, an AI-powered travel assistant using FastAPI and Groq Llama 3.3 70B to provide conversational booking, personalized recommendations, trip bundling, and cancellation assistance.', 2),
(11, 'Designed secure REST APIs with JWT authentication and modular backend architecture, integrating React, Django, and FastAPI seamlessly.', 3),
(11, 'Implemented a comprehensive testing strategy including 100+ Playwright automated end-to-end tests, manual functional testing, Postman API testing, and detailed bug reporting.', 4),
(10, 'Orchestrated a containerized telemetry stack using Docker Compose, linking Prometheus, Grafana, Node Exporter, and MySQL Exporter under a single isolated network.', 1),
(10, 'Designed dynamic Grafana dashboards containing system performance gauges, network traffic line-charts, and critical query rate logs.', 2),
(10, 'Configured Prometheus targets and custom alerts to automatically monitor container memory levels, CPU saturation thresholds, and database responsiveness.', 3),
(10, 'Monitored key MySQL database KPIs, analyzing metrics like InnoDB buffer pools, thread activities, query-per-second ratios, and connection pools.', 4),
(1, 'Engineered a decentralised platform simulating renewable energy trading between smart houses using Ethereum-based Solidity smart contracts.', 1),
(1, 'Built deep learning forecasting models using Bidirectional LSTM networks and lightweight polynomial regression for real-time energy surplus/deficit prediction.', 2),
(1, 'Implemented AI-based energy generation forecasting using weather parameters (temperature, humidity, solar irradiance).', 3),
(1, 'Designed and developed a real-time monitoring dashboard to visualise energy flow, blockchain transactions, and CO2 savings.', 4),
(2, 'Built a real-time object detection system using YOLO v8, a single-stage architecture optimised for speed and accuracy.', 1),
(2, 'Created and annotated a custom dataset using Roboflow with data augmentation; applied transfer learning from pre-trained COCO weights.', 2),
(2, 'Achieved high mAP (mean average precision) after hyperparameter tuning and confidence threshold optimisation.', 3),
(3, 'Designed a hybrid career recommendation system combining rule-based logic trees with ML classification models (Decision Tree / KNN) to suggest personalised career paths.', 1),
(4, 'Built an interactive 3D web platform visualising global algal bloom events using NASA satellite imagery and oceanographic datasets.', 1),
(4, 'Applied ML time-series forecasting models (LSTM / ARIMA) to predict future bloom patterns based on sea surface temperature and chlorophyll levels.', 2),
(5, 'Developed an OCR-based pipeline to extract structured data fields (name, DOB, UID, address) from Aadhaar card images using Tesseract with regex post-processing.', 1),
(5, 'Applied OpenCV image preprocessing — greyscale conversion, Gaussian blur, adaptive thresholding, and morphological operations.', 2);

-- Project Images
INSERT INTO `project_images` (`project_id`, `image_url`) VALUES
(10, '/project/prometheus-grafana-system.png'),
(10, '/project/prometheus-grafana-mysql.png');

-- 4. Skill Categories & Skills
INSERT INTO `skill_categories` (`id`, `name`, `display_order`) VALUES
(1, 'Languages', 1),
(2, 'Frameworks', 2),
(3, 'Domains', 3)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `skills` (`category_id`, `name`, `proficiency`, `display_order`) VALUES
(1, 'Python', 95, 1),
(1, 'C', 85, 2),
(1, 'C++', 85, 3),
(1, 'SQL', 92, 4),
(1, 'JavaScript', 92, 5),
(1, 'Solidity', 80, 6),
(2, 'React', 95, 1),
(2, 'Django', 90, 2),
(2, 'Flask', 88, 3),
(2, 'Unity', 80, 4),
(2, 'Three.js', 82, 5),
(3, 'Artificial Intelligence', 95, 1),
(3, 'Machine Learning', 94, 2),
(3, 'Web Development', 96, 3),
(3, 'Blockchain', 86, 4),
(3, 'Full-Stack Engineering', 94, 5);

-- 5. Achievements
INSERT INTO `achievements` (`id`, `title`, `event`, `year`, `prize`, `level`, `description`, `display_order`) VALUES
(1, 'Mega Bazaar: Beyond the Aisle, A Space Odyssey', 'Tryzens Launchpad Paper Presentation', '2023', 'First Prize', 'State Level', 'Secured first place for an innovative paper presentation at Technopark, Thiruvananthapuram.', 1),
(2, 'Cupid\'s Pitch for Ecotopia', 'Ecotopia: Al-driven platform democratizing green building', '2023', 'Second Prize', 'State Level', 'Awarded second prize for pitching an AI-driven platform idea focused on green building practices.', 2),
(3, 'SHE HACK', 'Idea Pitching Competition', '2024', 'Second Prize', 'State Level', 'Recognized for \'From Silo to Secure: Bridging the Gap in Data Security\' idea.', 3),
(4, 'KREEPA Green Power Expo 2024', 'Hydrogen Safety, Reimagined', '2024', 'Third Prize', 'State Level', 'Won third prize for using Digital Twin Technology to reimagine Hydrogen Safety.', 4),
(5, 'Pragati-Business Plan Competition', 'National Level Competition', '2025', 'Shortlisted', 'National Level', 'Shortlisted for the final rounds of the prestigious Pragati Business Plan Competition.', 5),
(6, 'Shastra Tech Event, Hackathon', 'State Level Hackathon', '2025', 'Third Prize', 'State Level', 'Secured third prize in a competitive state-level hackathon.', 6),
(7, 'Yukthi Innovation Challenge', 'National Level Innovation Challenge', 'Ongoing', 'Shortlisted', 'National Level', 'Recognized and shortlisted for the Yukthi Innovation Challenge.', 7)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `achievement_images` (`achievement_id`, `image_url`, `display_order`) VALUES
(1, '/achievements/mega-bazaar-1.jpeg', 1),
(1, '/achievements/mega-bazaar-2.jpeg', 2),
(1, '/achievements/mega-bazaar-3.jpeg', 3),
(2, '/achievements/ecotopia-1.jpeg', 1),
(3, '/achievements/she-hack-1.jpeg', 1),
(3, '/achievements/she-hack-2.jpeg', 2),
(4, '/achievements/kreepa-1.jpeg', 1),
(4, '/achievements/kreepa-2.jpeg', 2),
(4, '/achievements/kreepa-3.jpeg', 3),
(5, '/achievements/pragati_1.png', 1),
(5, '/achievements/pragati_2.jpeg', 2),
(6, '/achievements/sasthra-1.png', 1),
(6, '/achievements/sasthra-2.jpeg', 2),
(6, '/achievements/sasthra-3.jpeg', 3),
(7, '/achievements/yukthi-1.jpeg', 1),
(7, '/achievements/yukthi-2.png', 2);

-- 6. Talks
INSERT INTO `talks` (`id`, `title`, `description`, `year`, `video_thumbnail`, `video_url`, `display_order`) VALUES
(1, 'Pitch. Inspire. Transform.', 'Delivered a talk session encouraging idea pitching and creative thinking.', '2025', '/talks/pitch-inspire-transform.jpg', NULL, 1),
(2, 'Introduction to Machine Learning', 'Delivered an introductory machine learning session focusing on regression concepts and practical use cases.', '2025', '/talks/ml-intro.jpg', NULL, 2),
(3, 'Quantum Consciousness Transfer', 'Delivered a session on quantum consciousness transfer, exploring the intersection of quantum mechanics and consciousness.', '2025', '/talks/quantum-consciousness.jpg', NULL, 3)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 7. Articles / Blog Posts
INSERT INTO `articles` (`id`, `title`, `slug`, `summary`, `content`, `image`, `published_date`, `is_published`) VALUES
(1, 'Quantum Consciousness Transfer: Science, Speculation, and Technology', 'quantum-consciousness-transfer-science-speculation-and-technology',
 'Exploring the intersection of quantum mechanics and human consciousness—examining theoretical frameworks like Penrose-Hameroff, future technological possibilities for mind-uploading, and the profound philosophical implications of digital consciousness transfer. This deep dive looks at how exascale computing and neuro-mapping might one day allow us to preserve the human essence beyond biological constraints.',
 '<p>Quantum Consciousness Transfer (QCT) explores one of the most profound questions in science and philosophy: can human consciousness be transferred, preserved, or reconstructed beyond the biological brain? While often portrayed as science fiction, several foundational technologies contributing to this idea are already under active research in neuroscience, artificial intelligence, and computational modeling.</p><h2>Understanding Consciousness Scientifically</h2><p>Modern neuroscience treats consciousness as an emergent property of neural activity. Rather than existing in a single location, consciousness arises from complex electrical and chemical interactions distributed across billions of neurons.</p><h2>Mind Uploading: A Theoretical Framework</h2><p>Mind uploading proposes scanning the brain at an extremely high resolution to recreate its structure and activity within a digital substrate.</p>',
 '/blog/quantum-consciousness.png', 'Jan 12, 2026', 1),

(2, 'Blockchain Beyond Crypto: Real-World Energy Trading Use Cases', 'blockchain-beyond-crypto-real-world-energy-trading-use-cases',
 'How blockchain technology is revolutionizing peer-to-peer energy trading, enabling households to buy and sell renewable energy directly through smart contracts. By removing central intermediaries, decentralized microgrids empower local communities to manage their own carbon footprint and energy costs in real-time.',
 '<p>Blockchain technology is often narrowly associated with cryptocurrencies, yet its most transformative impact lies beyond digital finance. At its core, blockchain enables decentralized, transparent, and tamper-resistant systems—making it particularly well-suited for modern energy markets.</p><h2>Peer-to-Peer Energy Trading</h2><p>Blockchain enables peer-to-peer (P2P) energy trading, allowing prosumers to sell surplus electricity directly to nearby consumers without legacy utility markups.</p>',
 'https://images.unsplash.com/photo-1639762681485-074b7f938ba0?q=80&w=2064&auto=format&fit=crop', 'Jan 8, 2026', 1),

(3, 'AI/ML for Sustainability: From Idea to Implementation', 'aiml-for-sustainability-from-idea-to-implementation',
 'A practical guide to leveraging artificial intelligence and machine learning for environmental sustainability—from forecasting renewable energy production and demand to optimizing resource distribution in smart cities.',
 '<p>Artificial Intelligence and Machine Learning are rapidly becoming essential tools in addressing global sustainability challenges. By enabling prediction, automation, and real-time analysis, AI systems transform sustainability efforts into intelligent, proactive solutions.</p><h2>Case Study: Waste Detection Systems</h2><p>My work on DumpDetec exemplifies the intersection of AI and urban sustainability by combining object detection with vehicle recognition.</p>',
 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?q=80&w=2070&auto=format&fit=crop', 'Dec 28, 2025', 1),

(4, 'Agentic Workflows: How Autonomous AI Systems Think and Act', 'agentic-workflows-how-autonomous-ai-systems-think-and-act',
 'Diving deep into the architecture of autonomous AI agents—exploring decision-making frameworks, task decomposition, and modern AI planning. Learn how agentic workflows allow large language models to use external tools, reason through multi-step problems, and self-correct.',
 '<p>Agentic AI represents a shift from passive artificial intelligence to autonomous systems capable of reasoning, planning, and executing actions independently. Unlike standard models, agentic workflows are loop-based, allowing for continuous refinement.</p>',
 'https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=2070&auto=format&fit=crop', 'Dec 15, 2025', 1),

(5, 'The Metaverse Beyond Gaming: A Space Odyssey', 'the-metaverse-beyond-gaming-a-space-odyssey',
 'Examining the metaverse\'s potential beyond entertainment—from virtual commerce and remote collaboration to digital twins.',
 '<p>The metaverse is often reduced to gaming and entertainment, but its true potential lies in creating immersive spatial environments that extend real-world experiences. It represents the transition from a flat 2D internet to a persistent, 3D Spatial Web.</p>',
 'https://bernardmarr.com/wp-content/uploads/2023/06/AdobeStock_550053139-1-scaled.jpeg', 'Nov 22, 2025', 1),

(6, 'Hydrogen as the Future of Energy: Safety, Reimagined', 'hydrogen-as-the-future-of-energy-safety-reimagined',
 'Exploring hydrogen\'s role in the clean energy transition and how digital twin technology is revolutionizing safety protocols.',
 '<p>Hydrogen is emerging as a key clean energy carrier due to its high energy density and zero-emission combustion. However, its high volatility and leakage risks demand advanced safety mechanisms to enable large-scale adoption.</p>',
 'https://www.bloomenergy.com/wp-content/uploads/hydrogen-blog-2024.jpg', 'Nov 10, 2025', 1)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `article_tags` (`article_id`, `tag`) VALUES
(1, 'Quantum Computing'), (1, 'Neuroscience'), (1, 'Future Tech'),
(2, 'Blockchain'), (2, 'Energy'), (2, 'Sustainability'),
(3, 'AI'), (3, 'ML'), (3, 'Sustainability'),
(4, 'AI'), (4, 'Automation'), (4, 'System Design'),
(5, 'Metaverse'), (5, 'VR/AR'), (5, 'Future Tech'),
(6, 'Hydrogen'), (6, 'Energy'), (6, 'Digital Twin');

-- 8. Creative Works
INSERT INTO `creative_works` (`id`, `title`, `work_type`, `subtitle`, `description`, `thumbnail`, `display_order`) VALUES
(1, 'Y SHE Y', 'book', 'Visual Poetry & Philosophy Collection', 'A reflective visual publication exploring human perception, structure, and abstract forms.', '/Life/y sheshe y.png', 1),
(2, 'Flicker', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/2.png', 2),
(3, 'Remnant', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/3.png', 3),
(4, 'Aperture', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/4.png', 4),
(5, 'Lethe', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/5.png', 5),
(6, 'Echo', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/6.png', 6),
(7, 'Imprint', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/7.png', 7),
(8, 'Is', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/8.png', 8),
(9, 'The Answer', 'image', 'Visual Art & Photography', 'Lines, lights, and conceptual fragments.', '/Life/1.png', 9)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

INSERT INTO `creative_work_images` (`work_id`, `image_url`, `page_number`, `caption`) VALUES
(1, '/Life/y sheshe y/2.png', 1, 'Page 1'),
(1, '/Life/y sheshe y/3.png', 2, 'Page 2'),
(1, '/Life/y sheshe y/4.png', 3, 'Page 3'),
(1, '/Life/y sheshe y/5.png', 4, 'Page 4'),
(1, '/Life/y sheshe y/6.png', 5, 'Page 5'),
(1, '/Life/y sheshe y/7.png', 6, 'Page 6'),
(1, '/Life/y sheshe y/8.png', 7, 'Page 7'),
(1, '/Life/y sheshe y/9.png', 8, 'Page 8'),
(2, '/Life/2.png', 1, 'Flicker'),
(3, '/Life/3.png', 1, 'Remnant'),
(4, '/Life/4.png', 1, 'Aperture'),
(5, '/Life/5.png', 1, 'Lethe'),
(6, '/Life/6.png', 1, 'Echo'),
(7, '/Life/7.png', 1, 'Imprint'),
(8, '/Life/8.png', 1, 'Is'),
(9, '/Life/1.png', 1, 'The Answer');

-- 9. Site Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Navaneetha Jose // Portfolio [0x01]'),
('tagline', 'Developer • Innovator • Solution Architect'),
('status_badge', 'SYSTEM: ONLINE // ACCEPTING CONTRACTS'),
('binary_hero_sub', '01000100 01000101 01010110 // ENGINEERED FOR IMPACT'),
('contact_email', 'navaneethajose004@gmail.com'),
('contact_phone', '+91 8281252386'),
('github_url', 'https://github.com/navijose24'),
('linkedin_url', 'https://linkedin.com/in/navaneetha-jose'),
('instagram_url', 'https://instagram.com/navaneetha_jose'),
('footer_text', 'BUILT OF 0s AND 1s. ALL RIGHTS RESERVED.')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);
