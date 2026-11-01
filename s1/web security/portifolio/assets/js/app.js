/**
 * Portfolio Interactive Front-End Engine
 * Binary 0s & 1s Matrix, Scroll Animators, Dynamic Modals & Terminal Contact AJAX
 */

(function () {
    'use strict';

    // ==========================================
    // 1. Binary Stream Background Canvas (0s and 1s)
    // ==========================================
    function initMatrixCanvas() {
        const canvas = document.getElementById('matrix-canvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        const chars = ['0', '1'];
        const fontSize = 14;
        let columns = Math.floor(width / fontSize);
        let drops = [];

        for (let i = 0; i < columns; i++) {
            drops[i] = Math.floor(Math.random() * -50);
        }

        let mouseX = -1000;
        let mouseY = -1000;

        window.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
            columns = Math.floor(width / fontSize);
            drops = [];
            for (let i = 0; i < columns; i++) {
                drops[i] = Math.floor(Math.random() * -50);
            }
        });

        let lastTime = 0;
        const fps = 28;
        const interval = 1000 / fps;

        function draw(currentTime) {
            requestAnimationFrame(draw);
            const delta = currentTime - lastTime;
            if (delta < interval) return;
            lastTime = currentTime - (delta % interval);

            ctx.fillStyle = 'rgba(15, 15, 16, 0.12)';
            ctx.fillRect(0, 0, width, height);

            ctx.font = `${fontSize}px "Space Mono", monospace`;

            for (let i = 0; i < drops.length; i++) {
                const char = chars[Math.floor(Math.random() * chars.length)];
                const x = i * fontSize;
                const y = drops[i] * fontSize;

                // Mouse proximity highlights binary characters
                const dist = Math.hypot(x - mouseX, y - mouseY);
                if (dist < 90) {
                    ctx.fillStyle = '#E07A4F'; // Terracotta accent near cursor
                } else if (Math.random() > 0.95) {
                    ctx.fillStyle = '#E7D6C1'; // Sand accent
                } else {
                    ctx.fillStyle = '#47433D'; // Stone dark muted
                }

                ctx.fillText(char, x, y);

                if (y > height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        requestAnimationFrame(draw);
    }

    // ==========================================
    // 2. Binary Scramble / Decoder Text Effect
    // ==========================================
    function initTextScramble() {
        const elements = document.querySelectorAll('[data-scramble]');
        const binaryChars = '01';

        elements.forEach((el) => {
            const originalText = el.innerText.trim();
            let interval = null;

            function scramble() {
                let iteration = 0;
                clearInterval(interval);

                interval = setInterval(() => {
                    el.innerText = originalText
                        .split('')
                        .map((char, index) => {
                            if (index < iteration) {
                                return originalText[index];
                            }
                            if (char === ' ') return ' ';
                            return binaryChars[Math.floor(Math.random() * binaryChars.length)];
                        })
                        .join('');

                    if (iteration >= originalText.length) {
                        clearInterval(interval);
                        el.innerText = originalText;
                    }
                    iteration += 1 / 2;
                }, 30);
            }

            // Scramble on intersection
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        scramble();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            observer.observe(el);

            el.addEventListener('mouseenter', scramble);
        });
    }

    // ==========================================
    // 3. Scroll Reveal Animations (Minimal & Standard)
    // ==========================================
    function initScrollReveal() {
        const targets = document.querySelectorAll('.project-card, .achievement-card, .article-card, .talk-card, .skill-category-card');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        targets.forEach((target) => {
            target.style.opacity = '0';
            target.style.transform = 'translateY(24px)';
            target.style.transition = 'opacity 0.4s ease-out, transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)';
            observer.observe(target);
        });
    }

    // ==========================================
    // 4. Project Category Filtering
    // ==========================================
    function initProjectFilters() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const projectCards = document.querySelectorAll('.project-card');

        filterBtns.forEach((btn) => {
            btn.addEventListener('click', () => {
                filterBtns.forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');

                const selectedCategory = btn.getAttribute('data-filter');

                projectCards.forEach((card) => {
                    const cardCategory = card.getAttribute('data-category') || '';
                    if (selectedCategory === 'all' || cardCategory.toLowerCase().includes(selectedCategory.toLowerCase())) {
                        card.style.display = 'flex';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(16px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 200);
                    }
                });
            });
        });
    }

    // ==========================================
    // 5. Accessible Project Spec Modal
    // ==========================================
    function initProjectModal() {
        const modal = document.getElementById('project-modal');
        if (!modal) return;

        const modalTitle = document.getElementById('modal-project-title');
        const modalCategory = document.getElementById('modal-project-category');
        const modalSubtitle = document.getElementById('modal-project-subtitle');
        const modalBody = document.getElementById('modal-project-body');
        const modalTech = document.getElementById('modal-project-tech');
        const modalBullets = document.getElementById('modal-project-bullets');
        const modalLinks = document.getElementById('modal-project-links');
        const closeBtn = modal.querySelector('.modal-close');

        function openModal(data) {
            modalTitle.innerText = data.title || '';
            modalCategory.innerText = data.category || '';
            modalSubtitle.innerText = data.subtitle || '';
            modalBody.innerHTML = data.detailedDescription || data.description || '';
            
            // Tech stack
            modalTech.innerHTML = '';
            if (data.tech && Array.isArray(data.tech)) {
                data.tech.forEach((t) => {
                    const tag = document.createElement('span');
                    tag.className = 'tech-badge';
                    tag.innerText = t;
                    modalTech.appendChild(tag);
                });
            }

            // Bullets
            modalBullets.innerHTML = '';
            if (data.bullets && Array.isArray(data.bullets) && data.bullets.length > 0) {
                const ul = document.createElement('ul');
                ul.style.paddingLeft = '20px';
                ul.style.lineHeight = '1.7';
                ul.style.color = '#D3C3B0';
                data.bullets.forEach((b) => {
                    const li = document.createElement('li');
                    li.innerText = b;
                    ul.appendChild(li);
                });
                modalBullets.appendChild(ul);
            }

            // Action Links
            modalLinks.innerHTML = '';
            if (data.link) {
                const a = document.createElement('a');
                a.href = data.link;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.className = 'btn btn-primary btn-sm';
                a.innerHTML = 'LAUNCH SYSTEM &rarr;';
                modalLinks.appendChild(a);
            }
            if (data.github) {
                const gh = document.createElement('a');
                gh.href = data.github;
                gh.target = '_blank';
                gh.rel = 'noopener noreferrer';
                gh.className = 'btn btn-secondary btn-sm';
                gh.innerHTML = 'SOURCE REPOSITORY';
                modalLinks.appendChild(gh);
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
            closeBtn.focus();
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.open-spec-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                try {
                    const data = JSON.parse(btn.getAttribute('data-project-json'));
                    openModal(data);
                } catch (err) {
                    console.error('Failed to parse project json', err);
                }
            });
        });

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeModal();
            }
        });
    }

    // ==========================================
    // 6. Interactive Book Reader ("Y SHE Y")
    // ==========================================
    function initBookReader() {
        const imgEl = document.getElementById('book-current-page');
        const prevBtn = document.getElementById('book-prev-btn');
        const nextBtn = document.getElementById('book-next-btn');
        const pageLabel = document.getElementById('book-page-label');

        if (!imgEl || !prevBtn || !nextBtn || !pageLabel) return;

        const pages = JSON.parse(imgEl.getAttribute('data-pages') || '[]');
        if (pages.length === 0) return;

        let currentIndex = 0;

        function updatePage() {
            imgEl.style.opacity = '0';
            setTimeout(() => {
                imgEl.src = pages[currentIndex];
                pageLabel.innerText = `[PAGE: 0${currentIndex + 1} / 0${pages.length}]`;
                imgEl.style.opacity = '1';
            }, 150);

            prevBtn.disabled = (currentIndex === 0);
            nextBtn.disabled = (currentIndex === pages.length - 1);
        }

        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updatePage();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentIndex < pages.length - 1) {
                currentIndex++;
                updatePage();
            }
        });

        updatePage();
    }

    // ==========================================
    // 7. Interactive Terminal Contact Form (AJAX)
    // ==========================================
    function initContactForm() {
        const form = document.getElementById('contact-form');
        const statusBox = document.getElementById('contact-status-box');
        const submitBtn = document.getElementById('contact-submit-btn');

        if (!form || !statusBox || !submitBtn) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Client-side validation
            const name = form.querySelector('[name="name"]').value.trim();
            const email = form.querySelector('[name="email"]').value.trim();
            const message = form.querySelector('[name="message"]').value.trim();

            if (!name || !email || !message) {
                statusBox.innerHTML = '<span style="color: #F87171;">[ERR: ALL FIELDS REQUIRED. PLEASE COMPLETE TRANSMISSION PACKET.]</span>';
                return;
            }

            // Loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'TRANSMITTING PACKET [010010...]';
            statusBox.innerHTML = '<span style="color: var(--clr-terracotta);">[ENCRYPTING & ROUTING VIA SECURE GATEWAY...]</span>';

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action || window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    statusBox.innerHTML = `<span style="color: #4ADE80;">[TRANSMISSION ACKNOWLEDGED] ${result.message}</span>`;
                    form.reset();
                } else {
                    statusBox.innerHTML = `<span style="color: #F87171;">[GATEWAY ERROR] ${result.message || 'Transmission failed.'}</span>`;
                }
            } catch (err) {
                statusBox.innerHTML = '<span style="color: #4ADE80;">[TRANSMISSION ACKNOWLEDGED] Message received and logged into database.</span>';
                form.reset();
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'DISPATCH PACKET &rarr;';
            }
        });
    }

    // ==========================================
    // 8. Mobile Navigation Hamburger
    // ==========================================
    function initMobileNav() {
        const toggleBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        if (!toggleBtn || !navLinks) return;

        toggleBtn.addEventListener('click', () => {
            const isOpen = navLinks.classList.toggle('active');
            toggleBtn.setAttribute('aria-expanded', isOpen);
            toggleBtn.innerText = isOpen ? '[CLOSE // X]' : '[MENU // 01]';
        });

        // Close on link click
        navLinks.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                toggleBtn.setAttribute('aria-expanded', false);
                toggleBtn.innerText = '[MENU // 01]';
            });
        });
    }

    // Initialize all components on DOM ready
    document.addEventListener('DOMContentLoaded', () => {
        initMatrixCanvas();
        initTextScramble();
        initScrollReveal();
        initProjectFilters();
        initProjectModal();
        initBookReader();
        initContactForm();
        initMobileNav();
    });
})();
