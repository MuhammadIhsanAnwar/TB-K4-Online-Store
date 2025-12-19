<?php
include "admin/koneksi.php";
session_start();

$user = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $result = mysqli_query($koneksi, "SELECT * FROM akun_user WHERE id='$user_id'");
    $user = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/images/icon/logo.png">
    <title>UrbanHype - Fashion Store</title>
    <link rel="stylesheet" href="user/css_user/index.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="icons/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <?php include "navbar.php"; ?>
    <div class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <div class="bg-animation">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="hero-content">
                <p class="subtitle">New Season Collection</p>
                <h1>Timeless Style<br>for Everyone</h1>
                <p class="description">Discover our curated collection of unisex fashion pieces that blend contemporary design with classic elegance.</p>
                <a href="user/produk_pembayaran/shop.php" class="hero-btn">Explore Collection</a>
            </div>
        </div>
        <div class="hero-image">
        </div>
    </section>

    <section id="about" class="py-5 bg-light scroll-reveal">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="text-primary">About <span class="text-dark">UrbanHype</span></h2>
                    <p class="lead mt-3">UrbanHype was founded in 2025 with a vision to break fashion boundaries. We believe style has no gender, only expression.</p>
                    <p>Our unisex collections are designed for the bold, the thoughtful, and the effortlessly cool. Every piece is crafted with sustainable materials and timeless aesthetics.</p>
                </div>
                <div class="col-lg-6">
                    <div id="aboutCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel" data-bs-interval="4000">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="0" class="active"></button>
                            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="1"></button>
                            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="images/slide/slide11.jpg" class="d-block w-100" alt="UrbanHype Collection 1">
                            </div>
                            <div class="carousel-item">
                                <img src="images/slide/slide12.jpg" class="d-block w-100" alt="UrbanHype Collection 2">
                            </div>
                            <div class="carousel-item">
                                <img src="images/slide/slide13.jpg" class="d-block w-100" alt="UrbanHype Collection 3">
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#aboutCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#aboutCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5 scroll-reveal">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="text-primary">Get In <span class="text-dark">Touch</span></h2>
                <p class="text-muted mt-2">Have questions? We’re here to help.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="text" name="nama" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="subjek" class="form-control" placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" name="pesan" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="row mt-5 text-center">
                <div class="col-md-4 mb-4">
                    <i class="bi bi-geo-alt fs-2 text-primary"></i>
                    <h6 class="mt-3">Location</h6>
                    <p>Jl. Dr. Mansyur,<br>Kota Medan, Indonesia</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-envelope fs-2 text-primary"></i>
                    <h6 class="mt-3">Email</h6>
                    <p>helpdesk@urbanhype.neoverse.my.id</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="bi bi-telephone fs-2 text-primary"></i>
                    <h6 class="mt-3">Phone</h6>
                    <p>+62 852-7978-8815</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-4">URBANHYPE</h5>
                    <p>Where streetwear meets sophistication. Discover the latest unisex trends that defy gender norms and redefine urban style.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                        <li><a href="shop.php" class="text-white text-decoration-none">Shop</a></li>
                        <li><a href="#about" class="text-white text-decoration-none">About Us</a></li>
                        <li><a href="#contact" class="text-white text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6>Subscribe to Our Newsletter</h6>
                    <p>Get the latest updates on new arrivals and exclusive offers.</p>
                    <form id="subscribeForm">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Your Email" aria-label="Your Email" name="email" required>
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </form>

                    <script>
                        document.getElementById('subscribeForm').addEventListener('submit', function(e) {
                            e.preventDefault();

                            const email = this.querySelector('input[name="email"]').value;
                            const button = this.querySelector('button');
                            const originalText = button.textContent;

                            button.disabled = true;
                            button.textContent = 'Mengirim...';

                            fetch('proses_subscribe.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: 'email=' + encodeURIComponent(email)
                                })
                                .then(response => response.json())
                                .then(data => {
                                    button.disabled = false;
                                    button.textContent = originalText;

                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Subscribe Berhasil!',
                                            html: `<p>Terima kasih telah subscribe dengan <strong>${email}</strong></p><p>Email konfirmasi telah kami kirim ke inbox Anda!</p>`,
                                            confirmButtonColor: '#1E5DAC',
                                            backdrop: true,
                                            allowOutsideClick: false
                                        }).then(() => {
                                            this.reset();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Subscribe',
                                            text: data.message || 'Terjadi kesalahan. Coba lagi nanti.',
                                            confirmButtonColor: '#1E5DAC'
                                        });
                                    }
                                })
                                .catch(error => {
                                    button.disabled = false;
                                    button.textContent = originalText;

                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Gagal menghubungi server. Periksa koneksi internet Anda.',
                                        confirmButtonColor: '#1E5DAC'
                                    });
                                });
                        });
                    </script>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 UrbanHype. Kelompok 4 Tugas Besar Pemrograman Web. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
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

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.scroll-reveal').forEach(el => {
            observer.observe(el);
        });

        window.addEventListener('load', function() {
            const spinner = document.querySelector('.spinner-overlay');
            setTimeout(() => {
                spinner.style.opacity = '0';
                setTimeout(() => {
                    spinner.style.display = 'none';
                }, 500);
            }, 1500);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch('simpan_pesan_ajax.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: data.status,
                        title: data.status === 'success' ? 'Success' : 'Error',
                        text: data.message,
                        confirmButtonColor: '#1E5DAC'
                    });

                    if (data.status === 'success') {
                        form.reset();
                    }
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server',
                        confirmButtonColor: '#1E5DAC'
                    });
                })
                .finally(() => {
                    document.getElementById('btnText').classList.remove('d-none');
                    document.getElementById('btnLoading').classList.add('d-none');
                });
        });
    </script>
</body>

</html>