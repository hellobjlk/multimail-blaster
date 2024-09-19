document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            tabs.forEach(function (item) {
                item.classList.remove('nav-tab-active');
            });
            tab.classList.add('nav-tab-active');

            contents.forEach(function (content) {
                content.style.display = 'none';
            });
            contents[index].style.display = 'block';
        });
    });
});
