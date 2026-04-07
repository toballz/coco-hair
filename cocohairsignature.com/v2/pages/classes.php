<?php include_once("../config.php"); ?>
<!DOCTYPE html>
<html>
<head>
    <?php include(dirr . "/template/head.php"); ?>
    <title>1on1 Classes | CocoHairSignature</title>
    <style>
        .classes-page {
            padding-top: 92px;
            padding-bottom: 44px;
            background: linear-gradient(180deg, #f9f6ef 0%, #ffffff 100%);
        }

        .classes-hero {
            background: linear-gradient(130deg, #141414 0%, #272727 55%, #3a3a3a 100%);
            border-radius: 18px;
            color: #fff;
            box-shadow: 0 24px 45px rgba(0, 0, 0, .2);
        }

        .classes-label {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: #f4d47f;
        }

        .classes-hero h1 {
            font-size: clamp(1.7rem, 3vw, 2.7rem);
            margin-top: .4rem;
            margin-bottom: .65rem;
        }

        .inquiry-alert {
            border-left: 5px solid #d68a1f;
            background: #fff8e9;
            border-radius: 12px;
        }

        .course-card {
            border: 1px solid #ece7dd;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 26px rgba(0, 0, 0, .06);
            height: 100%;
        }

        .course-head {
            background: #111;
            color: #fff;
            padding: 16px 18px;
        }

        .course-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #f4d47f;
            margin: 0;
        }

        .course-meta {
            color: #d3d3d3;
            font-size: .9rem;
            margin: .2rem 0 0;
        }

        .course-list li {
            margin-bottom: .4rem;
        }

        .cta-panel {
            border-radius: 16px;
            border: 1px solid #ece7dd;
            background: #fff;
        }
    </style>
</head>
<body>
    <?php include(dirr . "/template/header.php"); ?>

    <main class="classes-page">
        <div class="container">
            <section class="classes-hero p-4 p-lg-5 mb-4 mb-lg-5">
                <div class="classes-label">Hands-On Training</div>
                <h1>1on1 Braiding Classes</h1>
                <p class="mb-0 text-white-50">
                    Private, focused training designed for stylists who want real confidence behind the chair.
                    Each class is 8 hours and tailored to your current level.
                </p>
            </section>

            <section class="inquiry-alert p-3 p-md-4 mb-4 mb-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <div class="fw-bold text-uppercase">Please message before booking this service</div>
                        <a class="fw-semibold text-decoration-none"
                            href="mailto:cocohairsignature@gmail.com?subject=1on1%20Class%20Inquiry">
                            cocohairsignature@gmail.com
                        </a>
                    </div>
                    <a class="btn btn-warning fw-semibold"
                        href="mailto:cocohairsignature@gmail.com?subject=1on1%20Class%20Inquiry">
                        Send Inquiry
                    </a>
                </div>
            </section>

            <section class="row g-4">
                <div class="col-12 col-lg-6">
                    <article class="course-card bg-white">
                        <header class="course-head">
                            <h2 class="h4 mb-1">Beginners Braiding Course</h2>
                            <p class="course-price">$1,100</p>
                            <p class="course-meta">8 hours</p>
                        </header>
                        <div class="p-4">
                            <p>
                                Learn the foundations of braiding and complete one full head of braids.
                                Mannequin head, starter kit, and certificate of completion are included.
                            </p>
                            <h3 class="h6 text-uppercase fw-bold mt-4">What We Cover</h3>
                            <ul class="course-list ps-3 mb-0">
                                <li>Products used</li>
                                <li>Different braiding hair and prepping hair</li>
                                <li>Parting and brick layering</li>
                                <li>Sizing and consistency</li>
                                <li>Cornrows with and without extensions</li>
                                <li>Individual plaits with and without knots</li>
                                <li>Finishing techniques</li>
                                <li>Growing your clientele</li>
                                <li>How to price while practicing</li>
                            </ul>
                        </div>
                    </article>
                </div>

                <div class="col-12 col-lg-6">
                    <article class="course-card bg-white">
                        <header class="course-head">
                            <h2 class="h4 mb-1">Advanced Braiding Course</h2>
                            <p class="course-price">$1,100</p>
                            <p class="course-meta">8 hours</p>
                        </header>
                        <div class="p-4">
                            <p>
                                For stylists with basic braiding knowledge who want to level up speed,
                                polish, and business presentation. A live model is required.
                                Certificate of completion included.
                            </p>
                            <h3 class="h6 text-uppercase fw-bold mt-4">What We Cover</h3>
                            <ul class="course-list ps-3 mb-0">
                                <li>Hair and products used</li>
                                <li>Parting and brick layering</li>
                                <li>Sizing and section control</li>
                                <li>Extending braids</li>
                                <li>Tuck method</li>
                                <li>Custom ombre</li>
                                <li>Finishing techniques</li>
                                <li>Pro photography tips</li>
                                <li>Social media branding and marketing</li>
                            </ul>
                        </div>
                    </article>
                </div>
            </section>

            <section class="cta-panel p-4 p-lg-5 mt-4 mt-lg-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h3 class="h4 mb-1">Ready to train with Coco?</h3>
                        <p class="text-secondary mb-0">Send an inquiry to confirm fit, schedule, and class prep details.</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-dark"
                            href="mailto:cocohairsignature@gmail.com?subject=1on1%20Class%20Inquiry">Email Coco</a>
                        <a class="btn btn-outline-dark" href="<?php echo site::url_hostdir(); ?>/pages/hairlist.php">Book Hair Appointment</a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <?php include(dirr . "/template/footer.php"); ?>
</body>
</html>
