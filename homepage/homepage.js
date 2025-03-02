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

        
        document.addEventListener('DOMContentLoaded', function () {
            const searchButton = document.getElementById('search-button');
            const searchInput = document.getElementById('search-input');
        
            searchButton.addEventListener('click', function () {
                const searchTerm = searchInput.value.trim();
        
                if (searchTerm) {
                    fetch(`fetch_posts.php?search=${encodeURIComponent(searchTerm)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                displayPosts(data.posts);
                            // } else {
                                // alert('No posts found.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                } else {
                    alert('Please enter a search term.');
                }
            });
        
            // Function to display posts
            function displayPosts(posts) {
                const photoGrid = document.querySelector('.photo-grid');
                photoGrid.innerHTML = ''; // Clear existing posts
        
                posts.forEach(post => {
                    const photoContainer = document.createElement('div');
                    photoContainer.className = 'photo-container';
        
                    const img = document.createElement('img');
                    img.src = post.media;
                    img.alt = 'Product Image';
        
                    const overlay = document.createElement('div');
                    overlay.className = 'overlay';


                    // Create an anchor tag for the post title
                    const productNameLink = document.createElement('a');
                    productNameLink.href = `../post_view/post_view.php?id=${post.post_id}`; // Link to the post view page
                    productNameLink.className = 'product-name-link';
                    productNameLink.textContent = post.title;

                    
                    // const productName = document.createElement('h3');
                    // productName.className = 'product-name';
                    // productName.textContent = post.title;
        
                    const rating = document.createElement('div');
                    rating.className = 'rating';
                    rating.innerHTML = `<span class="stars">${'★'.repeat(post.rating)}</span>`;
        
                    const postDetails = document.createElement('div');
                    postDetails.className = 'post-details';
                    postDetails.innerHTML = `<p>Posted by: ${post.username}</p><p>Price: ₹${post.price}</p>`;
        
                    // overlay.appendChild(productName);
                    overlay.appendChild(productNameLink);
                    overlay.appendChild(rating);
                    overlay.appendChild(postDetails);
                    photoContainer.appendChild(img);
                    photoContainer.appendChild(overlay);
                    photoGrid.appendChild(photoContainer);
                });
            }
        });