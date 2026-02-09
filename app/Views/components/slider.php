<!-- Hero Slider Section -->
<?php 
use App\Models\SliderModel;
$sliderModel = new SliderModel();
$sliders = $sliderModel->getActiveSliders();
?>

<?php if (!empty($sliders)): ?>
<section class="hero-slider">
    <div id="mainSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <!-- Indicators -->
        <div class="carousel-indicators">
            <?php foreach ($sliders as $index => $slider): ?>
                <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="<?= $index ?>" 
                        <?= $index === 0 ? 'class="active" aria-current="true"' : '' ?> 
                        aria-label="Slide <?= $index + 1 ?>"></button>
            <?php endforeach; ?>
        </div>

        <!-- Slides -->
        <div class="carousel-inner">
            <?php foreach ($sliders as $index => $slide): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> animation-<?= esc($slide['animation_type']) ?>">
                    <!-- Background Image -->
                    <div class="slide-image" 
                         style="background-image: url('<?= base_url('uploads/sliders/' . $slide['image']) ?>');">
                        <!-- Overlay -->
                        <div class="slide-overlay" style="background: rgba(0, 0, 0, <?= $slide['overlay_opacity'] / 100 ?>);"></div>
                    </div>

                    <!-- Content -->
                    <div class="carousel-caption d-flex align-items-center h-100">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-<?= $slide['text_position'] === 'center' ? '12' : '8' ?> 
                                            <?= $slide['text_position'] === 'right' ? 'ms-auto' : '' ?> 
                                            <?= $slide['text_position'] === 'center' ? 'mx-auto' : '' ?>">
                                    <div class="slide-content text-<?= esc($slide['text_position']) ?>" 
                                         data-aos="fade-<?= $slide['text_position'] === 'left' ? 'right' : ($slide['text_position'] === 'right' ? 'left' : 'up') ?>" 
                                         data-aos-delay="200">
                                        
                                        <?php if ($slide['title']): ?>
                                            <h1 class="slide-title display-3 fw-bold mb-3" 
                                                data-aos="fade-<?= $slide['text_position'] === 'left' ? 'right' : ($slide['text_position'] === 'right' ? 'left' : 'up') ?>" 
                                                data-aos-delay="300">
                                                <?= esc($slide['title']) ?>
                                            </h1>
                                        <?php endif; ?>

                                        <?php if ($slide['subtitle']): ?>
                                            <h3 class="slide-subtitle h2 mb-4" 
                                                data-aos="fade-<?= $slide['text_position'] === 'left' ? 'right' : ($slide['text_position'] === 'right' ? 'left' : 'up') ?>" 
                                                data-aos-delay="400">
                                                <?= esc($slide['subtitle']) ?>
                                            </h3>
                                        <?php endif; ?>

                                        <?php if ($slide['description']): ?>
                                            <p class="slide-description fs-5 mb-5" 
                                               data-aos="fade-<?= $slide['text_position'] === 'left' ? 'right' : ($slide['text_position'] === 'right' ? 'left' : 'up') ?>" 
                                               data-aos-delay="500">
                                                <?= esc($slide['description']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Action Buttons -->
                                        <div class="slide-buttons" 
                                             data-aos="fade-<?= $slide['text_position'] === 'left' ? 'right' : ($slide['text_position'] === 'right' ? 'left' : 'up') ?>" 
                                             data-aos-delay="600">
                                            <?php if ($slide['button1_text'] && $slide['button1_link']): ?>
                                                <a href="<?= esc($slide['button1_link']) ?>" class="btn btn-primary btn-lg me-3">
                                                    <?= esc($slide['button1_text']) ?>
                                                    <i class="fas fa-arrow-right ms-2"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($slide['button2_text'] && $slide['button2_link']): ?>
                                                <a href="<?= esc($slide['button2_link']) ?>" class="btn btn-outline-light btn-lg">
                                                    <?= esc($slide['button2_text']) ?>
                                                    <i class="fas fa-arrow-right ms-2"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
</section>

<style>
.hero-slider {
    position: relative;
    width: 100%;
    overflow: hidden;
}

#mainSlider {
    height: 100vh;
    min-height: 600px;
    max-height: 900px;
}

.carousel-item {
    height: 100vh;
    min-height: 600px;
    max-height: 900px;
    position: relative;
}

.slide-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Animations */
.animation-fade .slide-image {
    animation: fadeIn 1.5s ease-in-out;
}

.animation-slide .slide-image {
    animation: slideIn 1.2s ease-out;
}

.animation-zoom .slide-image {
    animation: zoomIn 1.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateX(-100%); }
    to { transform: translateX(0); }
}

@keyframes zoomIn {
    from { transform: scale(1.3); }
    to { transform: scale(1); }
}

/* Content Styling */
.carousel-caption {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex !important;
    align-items: center;
    padding: 0;
}

.slide-content {
    z-index: 10;
}

.slide-title {
    color: #ffffff;
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
    line-height: 1.2;
    font-weight: 700;
}

.slide-subtitle {
    color: #f0f0f0;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
    font-weight: 500;
}

.slide-description {
    color: #e0e0e0;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.slide-buttons .btn {
    padding: 15px 35px;
    font-size: 1.1rem;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

.slide-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.slide-buttons .btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.2);
    border-color: #ffffff;
}

/* Carousel Controls */
.carousel-control-prev,
.carousel-control-next {
    width: 80px;
    opacity: 0.8;
    transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    width: 50px;
    height: 50px;
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    padding: 10px;
    backdrop-filter: blur(5px);
}

/* Indicators */
.carousel-indicators {
    bottom: 30px;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin: 0 8px;
    background-color: rgba(255, 255, 255, 0.5);
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.carousel-indicators button.active {
    background-color: #ffffff;
    width: 40px;
    border-radius: 6px;
}

/* Responsive */
@media (max-width: 992px) {
    #mainSlider,
    .carousel-item {
        height: 70vh;
        min-height: 500px;
    }

    .slide-title {
        font-size: 2.5rem;
    }

    .slide-subtitle {
        font-size: 1.5rem;
    }

    .slide-description {
        font-size: 1rem;
    }

    .slide-buttons .btn {
        padding: 12px 25px;
        font-size: 1rem;
    }
}

@media (max-width: 768px) {
    #mainSlider,
    .carousel-item {
        height: 60vh;
        min-height: 450px;
    }

    .slide-title {
        font-size: 2rem;
    }

    .slide-subtitle {
        font-size: 1.2rem;
    }

    .slide-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }

    .slide-buttons .btn.me-3 {
        margin-right: 0 !important;
    }
}
</style>

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true
    });
</script>

<?php else: ?>
<!-- Fallback si aucun slider -->
<section class="hero-fallback bg-gradient text-white text-center py-5">
    <div class="container">
        <div class="row justify-content-center py-5 my-5">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Bienvenue sur Rebencia</h1>
                <p class="lead mb-5">Votre partenaire immobilier de confiance</p>
                <a href="<?= base_url('properties') ?>" class="btn btn-light btn-lg">
                    Découvrir nos biens
                    <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
