document.addEventListener('DOMContentLoaded', function() {
    const filterContainer = document.getElementById('news-year-filter');
    if (!filterContainer) return;

    const filterButtons = filterContainer.querySelectorAll('.btn-filter');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            if (this.classList.contains('btn_primary')) {
                return;
            }

            const year = this.dataset.year;

            filterButtons.forEach(btn => {
                btn.classList.remove('btn_primary', 'btn_hollow', 'btn_inactive');
                btn.classList.add('btn_grey');
            });
            this.classList.remove('btn_grey');
            this.classList.add('btn_primary', 'btn_hollow', 'btn_inactive');

            let url = new URL(window.location.href);
            if (year !== 'all') {
                url.searchParams.set('FILTER_YEAR', year);
            } else {
                url.searchParams.delete('FILTER_YEAR');
            }

            BX.onCustomEvent('onChangeNewsFilter', {url: url.toString()});
        });
    });
});