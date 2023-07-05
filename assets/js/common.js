window.addEventListener("DOMContentLoaded", () => {
    [].forEach.call( document.querySelectorAll('input[type=tel]'), input => {
        let keyCode;
        function mask(event) {
            event.keyCode && (keyCode = event.keyCode);
            let pos = this.selectionStart;
            if (pos < 3) event.preventDefault();
            let matrix = "+7 (___) ___-__-__",
                i = 0,
                def = matrix.replace(/\D/g, ""),
                val = this.value.replace(/\D/g, ""),
                new_value = matrix.replace(/[_\d]/g, function(a) {
                    return i < val.length ? val.charAt(i++) || def.charAt(i) : a
                });
            i = new_value.indexOf("_");

			if (i !== -1) {
                i < 5 && (i = 3);
                new_value = new_value.slice(0, i)
            }

            let reg = matrix.substr(0, this.value.length).replace(/_+/g,
                function(a) {
                    return "\\d{1," + a.length + "}"
                }).replace(/[+()]/g, "\\$&");
            reg = new RegExp("^" + reg + "$");
            if (!reg.test(this.value) || this.value.length < 5 || keyCode > 47 && keyCode < 58) this.value = new_value;
            if (event.type === "blur" && this.value.length < 5)  this.value = ""
        }

        input.addEventListener("input", mask, false);
        input.addEventListener("focus", mask, false);
        input.addEventListener("blur", mask, false);
        input.addEventListener("keydown", mask, false)
    })

    if (document.querySelector('.index-mobile__burger')) {
        document.querySelector('.index-mobile__burger').addEventListener('click', () => {
            document.body.classList.add('panel-active')
            document.querySelector('.header-fixed').classList.add('active')
        })
    }

    document.querySelector('.header-hamburger').addEventListener('click', e => {
        e.preventDefault();

        if (getBodyScrollTop() < 160 && document.body.classList.contains('index-page')) {
            document.querySelector('.header-fixed').classList.remove('active')
        }

		if (document.body.classList.contains('menu-active')) {
            document.body.classList.remove('menu-active')
        } else {
            if (document.body.classList.contains('panel-active')) {
                document.body.classList.remove('panel-active')
            } else {
                document.body.classList.add('panel-active')
            }
        }

    })

    document.querySelector('.header-icon-services').addEventListener('click', e => {
        e.preventDefault()

        if (document.body.classList.contains('menu-active')) {
            document.body.classList.remove('menu-active')
        } else {
            document.body.classList.add('menu-active')
        }
    })

    window.addEventListener('scroll', () => {
        if (getBodyScrollTop() > 160) {
            document.querySelector('.header-fixed').classList.add('active')
        } else {
            document.querySelector('.header-fixed').classList.remove('active')
        }
    })

    function getBodyScrollTop() {
        return self.pageYOffset
			|| (document.documentElement && document.documentElement.scrollTop)
			|| (document.body && document.body.scrollTop);
    }

	// Scroll to top
	const scrollToTop = () => {
		let c = document.documentElement.scrollTop || document.body.scrollTop

		if (c > 0) {
			window.requestAnimationFrame(scrollToTop)
			window.scrollBy(0, -Math.max(c / 10, 10))
		}
	}

	document.querySelectorAll('.scroll-to-top').forEach(btn => {
		btn.addEventListener('click', e => {
			e.preventDefault()
			scrollToTop()
		})
	})

	// Галереи на страницах услуг
    document.querySelectorAll('.slider-items').forEach(item => {
        let swiper = new Swiper(item, {
            spaceBetween: 25,
            currentClass: 'swiper-pagination-current',
            navigation: {
                nextEl: item.closest('.slider-items__block').querySelector('.swiper-button-next'),
                prevEl: item.closest('.slider-items__block').querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        })
    })

	// Отзывы на главной
    document.querySelectorAll('.slider-reviews').forEach(item => {
        let swiper = new Swiper(item, {
            spaceBetween: 0,
            centeredSlides: true,
            loop: true,
            navigation: {
                nextEl: item.closest('.slider-reviews__container').querySelector('.swiper-button-next'),
                prevEl: item.closest('.slider-reviews__container').querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                991: {
                    spaceBetween: 0,
                    slidesPerView: 'auto',
                },
            },
        })
    })

	// Готовые объекты (слайдер на главной)
    let swiperObject = new Swiper('.object-done__slider', {
        spaceBetween: 0,
        centeredSlides: true,
		clickable: true,
		// Disable touch drag (move)
		allowTouchMove: false,
        loop: true,
        pagination: {
            el: ".swiper-pagination",
        },
        navigation: {
            nextEl: document.querySelector('.container-pagination .swiper-button-next'),
            prevEl: document.querySelector('.container-pagination .swiper-button-prev'),
        },
        breakpoints: {
            320: {
                slidesPerView: 'auto',
                spaceBetween: 20,
            },
            991: {
                spaceBetween: 20,
                slidesPerView: 3,
            },
        },
		// Loop fix
		on: {
			slideChangeTransitionStart: swiper => {
				let $wrapperEl = swiper.$wrapperEl
				let params = swiper.params

				$wrapperEl.children(('.' + (params.slideClass) + '.' + (params.slideDuplicateClass)))
					.each(function() {
						let idx = this.getAttribute('data-swiper-slide-index');
						this.innerHTML = $wrapperEl.children('.' + params.slideClass + '[data-swiper-slide-index="' + idx + '"]:not(.' + params.slideDuplicateClass + ')').html();
					})
			},
			slideChangeTransitionEnd: swiper => {
				swiper.slideToLoop(swiper.realIndex, 0, false)
			}
		}
    })

	// Баннеры
	let swiperBanner = new Swiper('.banner', {
		spaceBetween: 0,
		loop: true,
		pagination: {
			el: ".swiper-pagination",
		},
		breakpoints: {
			320: {
				spaceBetween: 20,
				slidesPerView: 1,
			},
			767: {
				spaceBetween: 20,
				slidesPerView: 2,
			},
		},
	})

    document.querySelectorAll('.content-block').forEach((item) => {
        if (item.innerHTML.length > 200) {
            let itemContent = item.innerHTML;
            let itemVisible = itemContent.slice(0, 200)
            let itemHidden = itemContent.slice(200)
            item.innerHTML = itemVisible + '...'
            item.closest('.content-lazy').querySelector('.content-hidden').innerHTML = itemHidden
        } else {
            item.closest('.content-lazy').classList.add('no-content')
        }
        item.closest('.content-lazy').querySelector('.content-btn').addEventListener('click', function (){
            let newHtml = item.innerHTML.slice(0, -3);
            item.innerHTML = newHtml;
            item.closest('.content-lazy').classList.add('visible-content')
        })
    })

	const adminBarFix = () => {
		if (window.innerWidth < 782) {
			document.body.style.marginTop = '46px'
			document.querySelector('.header-fixed__mobile').style.top = '46px'
		}

		else if (window.innerWidth > 782) {
			document.body.style.marginTop = '32px'
			document.querySelector('.header-fixed__mobile').style.top = '32px'
		}

		if (window.innerWidth > 990) {
			document.body.style.marginTop = 0
			// document.querySelector('.header-fixed').style.top = '-32px'
		}

		if (window.innerWidth <= 600) {
			document.getElementById('wpadminbar').style.position = 'fixed'
		}
	}

	// Fix for admin bar
	if (document.getElementById('wpadminbar')) {
		adminBarFix()

		window.onresize = () => adminBarFix()
	}
})
