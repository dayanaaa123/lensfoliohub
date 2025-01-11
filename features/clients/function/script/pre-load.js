document.addEventListener('DOMContentLoaded', function () {
    const newsSection = document.querySelector('.news');
    const preloader = document.getElementById('preloader');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    newsSection.classList.add('visible');
                }, 1050); 
            }
        });
    });

    observer.observe(newsSection);


    setTimeout(() => {
        preloader.style.display = 'none';
    }, 1500); 
});
