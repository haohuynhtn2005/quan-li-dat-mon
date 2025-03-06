<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt món</title>
    <link rel="stylesheet" href="{{ asset('css/nunito.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
</head>
<style>
    button:hover {
        background: pink;
        color: black;
    }

    * {
        transition: 0.5s;
    }

    .col-lg-3>.card:hover {
        translate: 0 -0.6em;
        filter: none;
        box-shadow: 0 0 1em gray;
    }
</style>

<body>
    @include('components.navbar')
    <!-- Hero Section -->
    <header class="">
        <div id="carouselExampleCaptions" class="carousel slide">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('img/spaces/space1.jpg') }}" class="d-block w-100" alt="..."
                        style="height: 400px; object-fit: cover; object-position: bottom; filter: brightness(85%);">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                        <h1 class="text-center">Welcome to Our Restaurant</h1>
                        <p class="lead text-center">Trải nghiệm không gian ấm cúng <br> Hương vị khó quên!</p>
                        <a href="/" class="btn btn-outline-light mt-3">Đặt bàn ngay</a>
                    </div>
                </div>
                @foreach ($slogans as $slogan)
                    <div class="carousel-item">
                        <img src="{{ asset($slogan['image']) }}" class="d-block w-100" alt="..."
                            style="height: 400px; object-fit: cover; object-position: bottom; filter: brightness(70%);">
                        <div class="carousel-caption d-flex flex-column justify-content-center align-items-center h-100">
                            <h1 class="text-center">{!! $slogan['slogan'] !!}</h1>
                            <a href="/" class="btn btn-outline-light mt-3">Đặt bàn ngay</a>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </header>

    <!-- Menu Section -->
    <section id="menu" class="container py-5">
        <h2 class="text-center mb-4">Special Dishes</h2>
        <div class="row">
            @foreach ($foodItems as $foodItem)           
                <div class="col-lg-3 col-md-4 mb-3">
                    <div class="card">
                        <img src="{{asset('storage/' . $foodItem->image)}}" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title">{{$foodItem->name}}</h5>
                            <p class="card-text">Creamy Alfredo sauce with fresh herbs.</p>
                            <p class="text-primary">{{$foodItem->price}}đ</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5" style="background-image: url('{{ asset('img/spaces/space3.jpg') }}');
           background-position:center;
           background-size:cover;
           color:white;">
        <div class="container">
            <h2 class="text-center">ABOUT US</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Team</h3>
                    <ul>
                        <li><strong>Đầu bếp</strong>: Huỳnh Chí Hào</li>
                        <li><strong>Phục vụ</strong>:
                            <ol>- Trần Minh Quốc Thái</ol>
                            <ol>- Bùi Thảo Nhi</ol>
                        </li>
                        <li><strong>Thu ngân</strong>: Nguyễn Thị Mỹ Hiền</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3>Delima Restaurant</h3>
                    <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Enim aperiam repellat accusantium
                        mollitia, aspernatur impedit nam, sunt hic dolore amet consequuntur qui aliquam tempora delectus
                        alias fuga possimus doloribus nisi?</p>
                </div>
            </div>
            <!-- <p>We bring the finest ingredients and recipes from around the world to your plate.</p> -->
        </div>
    </section>

    <!-- Testimonials -->
    <section class="container py-5">
        <h2 class="text-center">What Our Customers Say</h2>
        <div class="row">
            <div class="col-md-6">
                <blockquote class="blockquote">
                    <p>"Absolutely the best dining experience I've had in years!"</p>
                    <footer class="blockquote-footer">John Doe</footer>
                </blockquote>
            </div>
            <div class="col-md-6">
                <blockquote class="blockquote">
                    <p>"Delicious food, great ambiance, and fantastic service!"</p>
                    <footer class="blockquote-footer">Jane Smith</footer>
                </blockquote>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="bg-dark text-white py-5 text-center">
        <h2>Contact & Reservations</h2>
        <p>📍 123 Main Street, Your City | 📞 (123) 456-7890</p>
        <a href="tel:1234567890" class="btn btn-warning">Call Now</a>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white text-center py-3">
        <p>&copy; 2025 DELIMA. All Rights Reserved.</p>
    </footer>

</body>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{asset('vendor/sweetalert2/sweetalert2.all.min.js')}}"></script>
@if (session('error'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Lỗi',
            text: '{{ session('error') }}',
            confirmButtonColor: '#4e73df',
        })
    </script>
@endif

</html>
