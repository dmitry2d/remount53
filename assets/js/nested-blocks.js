document.addEventListener('DOMContentLoaded', () => {
	let galleries = document.querySelectorAll('.work-flex')

	galleries.forEach(gallery => {
		let myGallery = lightGallery(gallery, {
			selector: '.work-item__image',
			speed: 500,
			subHtmlSelectorRelative: true,
			download: false
		})

		gallery.addEventListener('click', () => myGallery.openGallery(0))
	})
})
