document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(function (tab, index) {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            // Remove active class from all tabs and hide all content
            tabs.forEach(function (item) {
                item.classList.remove('nav-tab-active');
            });
            contents.forEach(function (content) {
                content.style.display = 'none';
            });

            // Add active class to clicked tab and show corresponding content
            tab.classList.add('nav-tab-active');
            contents[index].style.display = 'block';
        });
    });
});
