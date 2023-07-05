// Load more galleries
jQuery(document).ready(function($) {
	let galleries = $('div.gallery')
	let galleriesPerPage = 3
	let currentPage = 1
	let totalGalleries = galleries.length
	let totalPages = Math.ceil(totalGalleries / galleriesPerPage)

	const isLastPageCheck = () => {
		if (currentPage === totalPages) {
			$('#load-more').hide()
		}
	}

	$('#load-more').on('click', e => {
		e.preventDefault()

		if (currentPage < totalPages) {
			let startIndex = currentPage * galleriesPerPage
			let endIndex = startIndex + galleriesPerPage

			$('div.gallery').slice(startIndex, endIndex).fadeIn()

			currentPage++
		}

		isLastPageCheck()
	})

	galleries.slice(galleriesPerPage).hide()

	isLastPageCheck()
})
