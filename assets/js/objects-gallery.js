document.addEventListener('DOMContentLoaded', () => {
	let galleries = document.querySelectorAll('.swiper-slide .object-done__slide')

	galleries.forEach(gallery => {
		let myGallery = lightGallery(gallery, {
			selector: '.gallery__item',
			speed: 500,
			subHtmlSelectorRelative: true,
			download: false
		})

		gallery.addEventListener('click', () => myGallery.openGallery(0))
	})
})
