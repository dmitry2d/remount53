

$(document).ready(() => {

    $('.gallery-slider__items').slick({
        autoplay: true,
        dots: false,
        autoplaySpeed: 5000,
        slidesToShow: 3,
        slidesToScroll: 1,
        prevArrow: '<div class="gallery-slider__button gallery-slider__prev"></div>',
        nextArrow: '<div class="gallery-slider__button gallery-slider__next"></div>',
        responsive: [
            {
                breakpoint: 1000,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            },
        ]
    });

})