
window.addEventListener("DOMContentLoaded",() => {
	let modal = document.getElementById('modalForm')

	let modalContent = document.querySelector('.modal-content')
	let openBtns = document.querySelectorAll('.btn_callback')
	let closeBtn = document.querySelector('.closeModal')

	function openModal() {
		modal.classList.add('fade-in')
		modal.style.display = 'block'
	}

	function closeModal() {
		modal.classList.add('fade-out')

		setTimeout(() => {
			modal.style.display = 'none'
			modal.classList.remove('fade-out')
		}, 300)
	}

	modalContent.addEventListener('click', e => {
		e.stopPropagation()
	})


	closeBtn.addEventListener('click', () => closeModal())

	modal.addEventListener('click', () => closeModal())

	// Close on Esc
	document.onkeydown = e => {
		if (e.keyCode === 27) closeModal()
	}

	openBtns.forEach(btn => {
		btn.addEventListener('click', () => openModal())
	})

	// Form processing
	let contactForm = document.getElementById('modal-form')
	let formMessage = document.getElementById('modal-form-message')
	let errors = contactForm.querySelectorAll('.error-message')

	// Передача формы серверу
	async function sendModalForm(data = {}) {
		return await fetch(ajax.ajaxurl, {
			method: "POST",
			mode: "same-origin",
			cache: "no-cache",
			credentials: "same-origin",
			body: data,
		}).then(response => response.json())
	}

	function resetFormErrors() {
		errors.forEach(error => {
			let formGroup = error.parentNode

			error.style.display = 'none'
			error.textContent = ''
			formGroup.classList.remove('has-error')
		})

		formMessage.innerHTML = ''
	}

	contactForm.addEventListener('submit', e => {
		e.preventDefault()

		resetFormErrors()

		// Получение данных формы
		let formData = new FormData(contactForm)
		formData.append('action', 'modal_form')

		// Отправка формы
		sendModalForm(formData).then(response => {
			// В случае ошибки отображаем ошибки и подсвечиваем поля с ошибками или получаем общую ошибку
			if (! response.success) {
				resetFormErrors()
				let errors = response.data

				if (errors instanceof Object) {
					Object.keys(errors).forEach(fieldName => {
						contactForm.querySelector(`#${fieldName}`).parentNode.classList.add('has-error')
						contactForm.querySelector(`#${fieldName}-error-message`).textContent = errors[fieldName]
						contactForm.querySelector(`#${fieldName}-error-message`).style.display = 'block'
					})
				} else {
					formMessage.style.color = 'red'
					formMessage.innerHTML = response.data
				}
			} else { // Успех
				resetFormErrors()
				formMessage.style.color = 'green'
				formMessage.innerHTML = response.data
				// Очищаем поля
				contactForm.reset()
			}
		})
	})
})
