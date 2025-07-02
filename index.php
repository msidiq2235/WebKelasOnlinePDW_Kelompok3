<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Selamat Datang di Kelas Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            background-color: #f4f7f6;
        }
        .navbar-brand {
            font-weight: 700;
        }
        .hero-section {
            background: linear-gradient(135deg, rgba(41, 128, 185, 0.9) 0%, rgba(142, 68, 173, 0.9) 100%), url('https://www.toptal.com/designers/subtlepatterns/uploads/watercolor.png');
            background-size: cover;
            padding: 100px 0;
            color: white;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-section p {
            font-size: 1.25rem;
            max-width: 600px;
            margin: 0 auto 30px auto;
        }
        .section-title {
            font-weight: 700;
            margin-bottom: 50px;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 4px;
            background-color: var(--bs-primary);
            border-radius: 2px;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--bs-primary);
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>Kelas Online
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-primary">Login</a>
                    </li>
                     <li class="nav-item ms-2">
                        <a href="register.php" class="btn btn-outline-primary">Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1>Pendidikan Berkualitas di Ujung Jari Anda</h1>
            <p>Bergabunglah dengan platform e-learning kami yang modern dan interaktif. Belajar kapan saja, di mana saja.</p>
            <a href="register.php" class="btn btn-light btn-lg">Mulai Belajar Sekarang</a>
        </div>
    </header>

    <section class="py-5">
        <div class="container text-center">
            <h2 class="section-title">Fitur Unggulan Kami</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 feature-card">
                        <div class="feature-icon mb-3"><i class="fas fa-book-open"></i></div>
                        <h4>Materi Interaktif</h4>
                        <p>Akses materi pembelajaran yang disusun oleh guru-guru berpengalaman dalam format yang mudah dipahami.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 feature-card">
                        <div class="feature-icon mb-3"><i class="fas fa-pencil-alt"></i></div>
                        <h4>Kuis & Ujian Online</h4>
                        <p>Uji pemahaman Anda melalui kuis interaktif dan dapatkan hasil instan untuk mengukur kemajuan belajar.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 feature-card">
                        <div class="feature-icon mb-3"><i class="fas fa-users"></i></div>
                        <h4>Manajemen Kelas</h4>
                        <p>Guru dan admin dapat dengan mudah mengelola kelas, materi, dan memantau perkembangan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer text-center">
        <div class="container">
            <p class="mb-0">© 2025 Kelas Online. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>