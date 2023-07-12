window.addEventListener("DOMContentLoaded",() => {
	let galleries = document.querySelectorAll('.gallery-slider')

	galleries.forEach(gallery => {
		lightGallery(gallery, {
			selector: '.gallery-slider__image',
			speed: 500,
			subHtmlSelectorRelative: true,
			download: false
		})
	})
})
