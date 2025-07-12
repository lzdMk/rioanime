// Main Admin Dashboard JavaScript - Dark Theme

// Execute when the document is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add dark theme class to body
    document.body.classList.add('dark-theme');
    // Sidebar toggle functionality
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 768) {
            sidebar.classList.remove('show');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
        }
    });
    
    // Enable dropdown menus
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');
        
        // Use Bootstrap's dropdown functionality if available
        // If not available, implement a simple toggle
        if (typeof bootstrap !== 'undefined') {
            // Bootstrap 5 is loaded
            const dropdownInstance = new bootstrap.Dropdown(dropdownToggle);
        } else {
            // Fallback if Bootstrap JS is not loaded
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    dropdownMenu.classList.toggle('show');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!dropdown.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });
    
    // Add active class to current page nav link
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    navLinks.forEach(link => {
        // Remove 'active' class from all links
        link.classList.remove('active');
        
        // Get the href attribute
        const href = link.getAttribute('href');
        
        // Check if the current path matches the link's href
        if (href && currentPath.endsWith(href)) {
            link.classList.add('active');
        }
    });
});