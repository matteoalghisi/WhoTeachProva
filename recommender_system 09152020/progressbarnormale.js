function progressbaractive() {
	const progress = document.querySelector('.progress-done');

			progress.style.opacity = 1;

			progress.style.width = progress.getAttribute('data-done') + '%';
			progress.innerHTML =  progress.getAttribute('data-done') + '%';
}