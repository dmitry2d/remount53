

document.addEventListener('DOMContentLoaded', () => {
	let galleries = document.querySelectorAll('.gallery-link')

	galleries.forEach(gallery => {
		let myGallery = lightGallery(gallery, {
			selector: '.gallery-link__item',
			speed: 500,
			subHtmlSelectorRelative: true,
			download: false,
		})

		gallery.addEventListener('click', () => myGallery.openGallery(0))
		
	})

	let years = document.querySelectorAll('a.gallery-tag')

	function initGalleryTags() {
		let thisYear = new Date().getFullYear();
		years.forEach(year => {
			if (year.dataset.year == thisYear) {
				year.click()
			}
		})
	}

	function filterGallery(gallery, year) {
		let galleries = gallery.querySelectorAll('.gallery-link')

		galleries.forEach(gallery => {
			if (year === 'all' || gallery.dataset.year === year) {
				gallery.style.display = 'block'
				return;
			}

			gallery.style.display = 'none'
		})
	}

	years.forEach(year => {
		year.addEventListener('click', event => {
			event.preventDefault()

			let currentGalleryCategory = event.target.parentNode.parentNode
			let yearFilter = year.dataset.year
			let yearFilters = currentGalleryCategory.querySelectorAll('a.gallery-tag')

			filterGallery(currentGalleryCategory, yearFilter)

			yearFilters.forEach(el => {
				el.classList.remove('active')
			})

			year.classList.add('active')
		})
	})

	initGalleryTags ()

})
