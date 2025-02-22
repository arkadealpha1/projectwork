// Purpose: Javascript for homepage.html
        function adjustMasonryLayout() {
            const grid = document.querySelector('.photo-grid');
            const containers = document.querySelectorAll('.photo-container');
    
            // Reset column count
            grid.style.columnCount = '3';
    
            // Adjust column count based on screen width
            if (window.innerWidth <= 768) {
                grid.style.columnCount = '2';
            }
            if (window.innerWidth <= 480) {
                grid.style.columnCount = '1';
            }
        }
    
        // Adjust layout on page load and window resize
        window.addEventListener('load', adjustMasonryLayout);
        window.addEventListener('resize', adjustMasonryLayout);
