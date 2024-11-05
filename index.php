
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochi Daifuku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add Swiper for smooth slider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">Mochi Daifuku</div>
        <div class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="nav-links">
            <li><a href="#home">HOME</a></li>
            <li><a href="#menu">MENU</a></li>
            <li><a href="#locations">LOCATIONS</a></li>
            <li><a href="#about">ABOUT</a></li>
            <li><a href="products.php" class="cart-link">
                <i class="fas fa-shopping-cart"></i>
            <li><a href="Login.php" class="btn-order">LOGIN</a></li>
        </ul>
    </nav>

    <!-- Hero Slider -->
    <div class="swiper hero-slider">
        <div class="swiper-wrapper">
            <div class="swiper-slide" style="background-image: url('https://th.bing.com/th/id/R.a2b1815a5fd5dad6061be43749cb2385?rik=x%2b51%2bllXi1RkOg&riu=http%3a%2f%2fgoinjapanesque.com%2fwpos%2fwp-content%2fuploads%2f2015%2f03%2fichigo-daifuku5.jpg&ehk=HvabiX5WC%2fudzj7hhcF5VOALb9LswEnonC8RESW28y4%3d&risl=&pid=ImgRaw&r=0')">
                <div class="slide-overlay"></div>
                <div class="hero-content">
                    <h1>Mochi Daifuku</h1>
                    <p>Discover our delightful selection of Japanese-inspired mochi</p>
                    <a href="#menu" class="btn-primary">View Menu</a>
                    <a href="#locations" class="btn-secondary">Find Us</a>
                </div>
            </div>
            <div class="swiper-slide" style="background-image: url('https://cdn0-production-images-kly.akamaized.net/-fCgsSKxrXKPGEbdFQ7ucQ7ebBY=/1x77:1000x640/1200x675/filters:quality(75):strip_icc():format(jpeg)/kly-media-production/medias/4419017/original/010936200_1683522187-shutterstock_2238139593.jpg')">
                <div class="slide-overlay"></div>
                <div class="hero-content">
                    <h1>FRESH & TASTY</h1>
                    <p>Made with premium ingredients for the perfect bite</p>
                    <a href="#order" class="btn-primary">Order Now</a>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <section id="menu" class="section menu-section">
    <div class="section-header">
        <h2>MOCHI DAIFUKU by FUDALOVA</h2>
        <p>Discover our delicious mochi selections</p>
    </div>
    
    <div class="menu-filters">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="classic">Classic (5k)</button>
        <button class="filter-btn" data-filter="premium">Premium (6k)</button>
        <button class="filter-btn" data-filter="special">Special (7k)</button>
    </div>

    <div class="menu-grid">
        <!-- Classic 5k -->
        <div class="menu-card" data-category="classic">
            <div class="menu-card-image">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='100%' height='100%' fill='%23FFE4E1'/><text x='50%' y='50%' font-size='24' fill='%23FF69B4' text-anchor='middle'>Choco Crunchy</text></svg>" alt="Choco Crunchy">
            </div>
            <div class="menu-card-content">
                <h3>Choco Crunchy</h3>
                <p>Delicious chocolate mochi with crunchy texture</p>
                <div class="menu-card-price">Rp 5.000</div>
                <button class="order-product-btn" onclick="window.location.href='products.php'">Place Order</button>
            </div>
        </div>

        <div class="menu-card" data-category="classic">
            <div class="menu-card-image">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='100%' height='100%' fill='%23FFE4E1'/><text x='50%' y='50%' font-size='24' fill='%23FF69B4' text-anchor='middle'>Marshmallow</text></svg>" alt="Marshmallow Creamcheese">
            </div>
            <div class="menu-card-content">
                <h3>Marshmallow Creamcheese</h3>
                <p>Sweet marshmallow mochi with cream cheese filling</p>
                <div class="menu-card-price">Rp 5.000</div>
                <button class="order-product-btn" onclick="window.location.href='products.php'">Place Order</button>
            </div>
        </div>

        <div class="menu-card" data-category="classic">
            <div class="menu-card-image">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='100%' height='100%' fill='%23FFE4E1'/><text x='50%' y='50%' font-size='24' fill='%23FF69B4' text-anchor='middle'>Cookies n Cream</text></svg>" alt="Cookies n Cream">
            </div>
            <div class="menu-card-content">
                <h3>Cookies 'n Cream</h3>
                <p>Classic cookies and cream flavored mochi</p>
                <div class="menu-card-price">Rp 5.000</div>
                <button class="order-product-btn" onclick="window.location.href='products.php'">Place Order</button>
            </div>
        </div>

        <!-- Add the rest of classic items similarly -->

        <!-- Premium 6k -->
        <div class="menu-card" data-category="premium">
            <div class="menu-card-image">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='100%' height='100%' fill='%23FFE4E1'/><text x='50%' y='50%' font-size='24' fill='%23FF69B4' text-anchor='middle'>Mango</text></svg>" alt="Mango Creamcheese">
            </div>
            <div class="menu-card-content">
                <h3>Mango Creamcheese</h3>
                <p>Fresh mango flavored mochi with cream cheese</p>
                <div class="menu-card-price">Rp 6.000</div>
                <button class="order-product-btn" onclick="window.location.href='products.php'">Place Order</button>
            </div>
        </div>

        <!-- Add the rest of premium items similarly -->

        <!-- Special 7k -->
        <div class="menu-card" data-category="special">
            <div class="menu-card-image">
                <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='100%' height='100%' fill='%23FFE4E1'/><text x='50%' y='50%' font-size='24' fill='%23FF69B4' text-anchor='middle'>Choco Strawberry</text></svg>" alt="Choco Strawberry">
            </div>
            <div class="menu-card-content">
                <h3>Choco Strawberry</h3>
                <p>Premium chocolate mochi with fresh strawberry</p>
                <div class="menu-card-price">Rp 7.000</div>
                <button class="order-product-btn" onclick="window.location.href='products.php'">Place Order</button>
            </div>
        </div>

        <!-- Add the rest of special items similarly -->
    </div>

<!-- Locations Section -->
<section id="locations" class="section locations-section">
    <div class="section-header">
        <h2>Find Us</h2>
        <p>Visit our store in Sukabumi</p>
    </div>

    <div class="locations-grid">
        <div class="location-card">
            <div class="location-image">
                <svg width="100%" height="300" viewBox="0 0 400 300">
                    <!-- Map background -->
                    <rect width="400" height="300" fill="#E8EAF6"/>
                    <!-- Roads -->
                    <path d="M50 150 L350 150" stroke="#90A4AE" stroke-width="8"/>
                    <path d="M200 50 L200 250" stroke="#90A4AE" stroke-width="8"/>
                    <!-- Location marker -->
                    <circle cx="200" cy="150" r="15" fill="#FF69B4"/>
                    <circle cx="200" cy="150" r="5" fill="white"/>
                    <!-- Text -->
                    <text x="200" y="200" text-anchor="middle" fill="#333" font-size="14">
                        Baros, Sukabumi
                    </text>
                </svg>
            </div>
            <div class="location-content">
                <h3>Baros Store</h3>
                <p><i class="fas fa-map-marker-alt"></i> Jl. Baros No.123, Baros, Kota Sukabumi, Jawa Barat</p>
                <p><i class="fas fa-phone"></i> +62 857-9875-4461</p>
                <p><i class="fas fa-clock"></i> 10:00 AM - 10:00 PM</p>
                <p><i class="fas fa-info-circle"></i> Near Baros Traditional Market</p>
                <a href="https://maps.google.com/?q=-6.9631071,106.9400253" class="btn-directions" target="_blank">Get Directions</a>
            </div>

            <div class="location-card">
            <div class="location-content" style="padding: 2rem;">
                <h3>Online Orders</h3>
                <p><i class="fas fa-shopping-bag"></i> Order via:</p>
                <div style="margin-top: 1rem;">
                    <p><i class="fab fa-whatsapp"></i> WhatsApp: +62 857-9875-4461</p>
                    <p><i class="fab fa-instagram"></i> Instagram: @fudalova.id</p>
                    <p><i class="fas fa-motorcycle"></i> GoFood & GrabFood available</p>
                </div>
                <div style="margin-top: 1rem;">
                    <p><i class="fas fa-clock"></i> Order Processing Hours:</p>
                    <p>Monday - Sunday: 10:00 AM - 8:00 PM</p>
                </div>
                <a href="https://wa.me/6285798754461" class="btn-directions" style="margin-top: 1rem; background-color: #25D366;">
                    <i class="fab fa-whatsapp"></i> Order via WhatsApp
                </a>
            </div>
        </div>
</section>

<!-- About Section -->
<section id="about" class="section about-section">
    <div class="about-content">
        <div class="about-text">
            <h2>Our Sweet Journey</h2>
            <p class="about-description">Founded in 2017, Mochi Daifuku brings the authentic taste of Japanese mochi to your doorstep. Our dedication to traditional recipes combined with innovative flavors has made us the go-to destination for mochi lovers. Each daifuku is handcrafted with care using premium ingredients to ensure the perfect balance of taste and texture.</p>
            
            <div class="about-features">
                <div class="feature">
                    <i class="fas fa-heart"></i>
                    <h3>Handcrafted</h3>
                    <p>Made fresh daily with love</p>
                </div>
                <div class="feature">
                    <i class="fas fa-certificate"></i>
                    <h3>Premium Quality</h3>
                    <p>Finest rice flour & fillings</p>
                </div>
                <div class="feature">
                    <i class="fas fa-magic"></i>
                    <h3>Unique Flavors</h3>
                    <p>Creative fusion recipes</p>
                </div>
            </div>
        </div>
        <div class="about-image">
            <img src="Gallery/Mochi.jpg" alt="Mochi Making Process">
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Mochi Daifuku</h3>
            <p>Crafting sweet moments since 2017</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/fudalova.id?igsh=MW81cWR3NXY5ZjRseg=="><i class="fab fa-instagram"></i></a>
                <a href="081383886499"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#locations">Locations</a></li>
                <li><a href="#about">About</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>Contact Us</h3>
            <p><i class="fas fa-phone"></i> +62 857-9875-4461  </p>
            <p><i class="fas fa-envelope"></i> hello@mochidaifuku.com</p>
            <p><i class="fas fa-map-marker-alt"></i> Sukabumi Jawa barat, Indonesia</p>
        </div>
        
        <div class="footer-section">
            <h3>Stay Sweet</h3>
            <p>Subscribe for special offers and new flavor updates!</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Enter your email">
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; 2024 Mochi Daifuku. All rights reserved.</p>
    </div>
</footer>


    <!-- Add Swiper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/8.4.5/swiper-bundle.min.js"></script>
    
    <script>
        function openWhatsApp() {
    window.location.href = "https://wa.me/6285798754461";
}
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper
            const swiper = new Swiper('.hero-slider', {
                loop: true,
                effect: 'fade',
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            // Navigation Toggle
            const navToggle = document.querySelector('.nav-toggle');
            const navLinks = document.querySelector('.nav-links');
            
            navToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                navToggle.classList.toggle('active');
            });

            // Smooth Scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                        navLinks.classList.remove('active');
                    }
                });
            });

            // Scroll Effects
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });

            // Intersection Observer for scroll animations
            const sections = document.querySelectorAll('.section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            sections.forEach(section => observer.observe(section));
        });
        document.addEventListener('DOMContentLoaded', function() {
    // Menu Filtering
    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuCards = document.querySelectorAll('.menu-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add active class to clicked button
            btn.classList.add('active');
            
            const filterValue = btn.getAttribute('data-filter');
            
            menuCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 400);
                }
            });
        });
    });

    // Add to Cart Animation
const cartBtns = document.querySelectorAll('.order-product-btn');

cartBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault(); // Mencegah pengalihan halaman secara langsung jika menggunakan <a> atau form
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = 'cart-notification';
        notification.textContent = 'Terima Kasih Sudah Order';
        document.body.appendChild(notification);
        
        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Remove notification
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
                // Redirect to a new page after delay
                window.location.href = 'products.php'; // Ganti URL sesuai dengan halaman yang dituju
            }, 400);
        }, 2000);
    });
});

    // Scroll Animation
    const sections = document.querySelectorAll('.section');
    
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-up');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    sections.forEach(section => {
        observer.observe(section);
    });

    // Newsletter Form
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            const submitBtn = newsletterForm.querySelector('button');
            
            // Show loading state
            const originalBtnText = submitBtn.textContent;
            submitBtn.innerHTML = '<span class="loading-spinner"></span>';
            submitBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                submitBtn.textContent = 'Subscribed!';
                emailInput.value = '';
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    submitBtn.textContent = originalBtnText;
                    submitBtn.disabled = false;
                }, 2000);
            }, 1500);
        });
    }

    // Get Directions
    const directionBtns = document.querySelectorAll('.btn-directions');
    
    directionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const location = this.closest('.location-content').querySelector('h3').textContent;
            const address = this.closest('.location-content').querySelector('.fa-map-marker-alt').nextSibling.textContent.trim();
            
            // Open in Google Maps
            window.open(`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address + ' ' + location)}`);
        });
    });

    // Smooth Scroll for Internal Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
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
});
const filterButtons = document.querySelectorAll('.filter-btn');
        const menuCards = document.querySelectorAll('.menu-card');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                button.classList.add('active');
                
                const filter = button.dataset.filter;
                
                menuCards.forEach(card => {
                    if(filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>