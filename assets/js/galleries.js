window.addEventListener("DOMContentLoaded",() => {
	let galleries = document.querySelectorAll('.swiper')

	galleries.forEach(gallery => {
		lightGallery(gallery, {
			selector: '.swiper-slide__pic',
			speed: 500,
			subHtmlSelectorRelative: true,
			download: false
		})
	})
})
