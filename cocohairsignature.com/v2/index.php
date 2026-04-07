<?php include_once("./config.php"); ?>
<!DOCTYPE html>
<html>

<head>
    <?php include(dirr . "/template/head.php"); ?>
    <title>Home</title>
    <meta
        description="Coco Hair Signature, LLC is a braiding service located in Grayslake Illinois. We provide the best braiding services at your appointment. Book now!" />
</head>

<body>
    <?php include(dirr . "/template/header.php"); ?>

    <section class=" hero-section min-vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-12 col-md-6 ">
                    <div class="carousel slide carousel-fade hero-carousel" data-bs-ride="carousel"
                        data-bs-interval="3200" data-bs-wrap="true" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?php echo site::url_hostdir(); ?>/img/n/1.jpg" alt="Coco Hair style 1">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo site::url_hostdir(); ?>/img/n/2.jpg" alt="Coco Hair style 2">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo site::url_hostdir(); ?>/img/n/3.jpg" alt="Coco Hair style 3">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo site::url_hostdir(); ?>/img/n/4.jpg" alt="Coco Hair style 4">
                            </div>
                            <div class="carousel-item">
                                <img src="<?php echo site::url_hostdir(); ?>/img/n/5.jpg" alt="Coco Hair style 5">
                            </div>
                        </div>
                        <div class="carousel-indicators mb-2">
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"
                                aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"
                                aria-label="Slide 2"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"
                                aria-label="Slide 3"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"
                                aria-label="Slide 4"></button>
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4"
                                aria-label="Slide 5"></button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="text-wrapper">
                        <h1 class="mb-3 text-white text-center"><strong>Welcome To Coco Hair Signature, LLC</strong>
                        </h1>
                        <div class="hero-buttons mt-3">
                            <a class="btn btn-secondary" style="background:#f773c1 !important"
                                href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php">Book Now</a>
                            <a class="btn btn-info" href="http://shop.<?php echo site::url_domain(); ?>">Shop Online</a>
                            <a class="btn btn-warning" href="<?php echo site::url_hostdir(); ?>/pages/classes.php">1on1
                                Classes</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background:linear-gradient(to right, #9e3f28, #232323,#232323);color:#faebd7;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg">
                    <div class="card-wrapper">
                        <h4 class="card-title mb-4 h2"><strong>ABOUT ME</strong></h4>
                        <div class="card-box row">
                            <div class="col-12 col-md-4"><img src="<?php echo site::url_hostdir(); ?>/img/n/coco.jpeg"
                                    alt="Coco portrait" class="img-fluid"></div>
                            <div class="col-12 col-md-8 align-items-center d-flex justify-content-center">
                                <div>HI, I'M COCO! <b
                                        style="border-top:1px dashed #ccc;border-bottom:1px dashed #ccc;font-size:24px;color:#ffe6ff;">Ms.
                                        3hrs or less!</b>
                                    <br>
                                    <div style="margin-left:16px;">
                                        <br>
                                        I'm a young African-American talented self taught braider.
                                        <br>
                                        I have been braiding ever since <wbr>I was 7 years old.
                                        <wbr>I have a gift/passion for braiding and so I am here to offer you the best service at your braiding appointment.
                                        <wbr>I'm located in Grayslake Illinois.
                                        <br>
                                        Thank you for choosing Coco and considering me as your braider. I look forward
                                        to
                                        slaying your braids!
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="  py-5" style="background:linear-gradient(to right, #4c2834,#f9f9f9,#6a744d)">
        <div class="container">
            <div class="row g-4 mt-0">
                <div class="col-12 col-md-4 gallery-image">
                    <div class="item-wrapper" data-bs-toggle="modal" data-bs-target="#sRH7Kx4CvK-modal">
                        <img class="w-100" src="<?php echo site::url_hostdir(); ?>/img/n/dowwenload-4-400x721.jpeg"
                            alt="Braiding style 1" data-bs-slide-to="0" data-bs-target="#lb-sRH7Kx4CvK">
                        <div class="icon-wrapper"><span class="gallery-icon">View</span></div>
                    </div>
                </div>
                <div class="col-12 col-md-4 gallery-image">
                    <div class="item-wrapper" data-bs-toggle="modal" data-bs-target="#sRH7Kx4CvK-modal">
                        <img class="w-100" src="<?php echo site::url_hostdir(); ?>/img/n/thumbnail-6-1080x1478.jpeg"
                            alt="Braiding style 2" data-bs-slide-to="1" data-bs-target="#lb-sRH7Kx4CvK">
                        <div class="icon-wrapper"><span class="gallery-icon">View</span></div>
                    </div>
                </div>
                <!-- <div class="col-12 col-md-4 gallery-image">
                    <div class="item-wrapper" data-bs-toggle="modal" data-bs-target="#sRH7Kx4CvK-modal">
                        <img class="w-100" src="<?php echo site::url_hostdir(); ?>/img/n/thumbnail-7-506x824.jpeg"
                            alt="Braiding style 3" data-bs-slide-to="2" data-bs-target="#lb-sRH7Kx4CvK">
                        <div class="icon-wrapper"><span class="gallery-icon">View</span></div>
                    </div>
                </div> -->
            </div>

            <div class="modal fade" tabindex="-1" aria-hidden="true" id="sRH7Kx4CvK-modal">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-3"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="carousel slide carousel-fade" id="lb-sRH7Kx4CvK" data-bs-interval="7000">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img class="d-block w-100"
                                            src="<?php echo site::url_hostdir(); ?>/img/n/dowwenload-4-400x721.jpeg"
                                            alt="Braiding style 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img class="d-block w-100"
                                            src="<?php echo site::url_hostdir(); ?>/img/n/thumbnail-6-1080x1478.jpeg"
                                            alt="Braiding style 2">
                                    </div>
                                    <!-- <div class="carousel-item">
                                        <img class="d-block w-100"
                                            src="<?php echo site::url_hostdir(); ?>/img/n/thumbnail-7-506x824.jpeg"
                                            alt="Braiding style 3">
                                    </div> -->
                                </div>
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#lb-sRH7Kx4CvK" data-bs-slide-to="0"
                                        class="active" aria-current="true" aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#lb-sRH7Kx4CvK" data-bs-slide-to="1"
                                        aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#lb-sRH7Kx4CvK" data-bs-slide-to="2"
                                        aria-label="Slide 3"></button>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#lb-sRH7Kx4CvK"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#lb-sRH7Kx4CvK"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class=" " style="background:linear-gradient(to right, #d66142,#d66142,#6a744d,#d66142);">
        <div class="container">
            <div class="card bg-transparent border-0">
                <div class="card-wrapper">
                    <div class="row align-items-center row">
                        <div class="col-12 col-md-4 px-0 py-0">
                            <div class="">
                                <img class="img-fluid" src="<?php echo site::url_hostdir(); ?>/img/n/thumbnail-695x704.jpg"
                                    alt="Coco service preview">
                            </div>
                        </div>
                        <div class="col-12 col-md px-0 py-0">
                            <div class="card-box px-4 py-3">
                                <h5 class="card-title m-0 mb-3 h4"><strong>SERVICE</strong></h5>
                                <p class="mb-0">I provide the following braiding services: BoxBraids, Knotless Braids,
                                    Goddess Locs,
                                    Passion Twists, Lemonade Braids, Tribal Braids, Feed-in Braids, Crotchet Braids,
                                    Cornrows and more.<br><br>Deposit is required to secure appointment.<br><strong>3hrs
                                        or Less</strong><br>Follow Us @cocohairsignature.</p>
                                <div class="social-row d-flex">
                                    <div style="margin-right: 12px;" class="soc-item">
                                        <a href="https://www.facebook.com/cocohairsignature/" target="_blank"
                                            rel="noopener noreferrer" style="width:30px;display: block;">
                                            <img src="<?php echo site::url_hostdir(); ?>/img/n/facebooklogo.png?-695x704.jpg"
                                                alt="Facebook" style="width:100%">
                                        </a>
                                    </div>
                                    <div class="soc-item">
                                        <a href="https://instagram.com/cocohairsignature" target="_blank"
                                            rel="noopener noreferrer" style="width:30px;display: block;">
                                            <img src="<?php echo site::url_hostdir(); ?>/img/n/instagramlogo.png?-695x704.jpg"
                                                alt="Instagram" style="width:100%">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include(dirr . "/template/footer.php"); ?>
</body>

</html>