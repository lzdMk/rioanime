/**
 * Custom Admin Pagination Component
 * Modern pagination with smooth animations and responsive design
 */
class AdminPagination {
    constructor(options = {}) {
        this.container = options.container;
        this.currentPage = options.currentPage || 1;
        this.totalPages = options.totalPages || 1;
        this.perPage = options.perPage || 8;
        this.totalItems = options.totalItems || 0;
        this.onPageChange = options.onPageChange || (() => {});
        this.maxVisiblePages = options.maxVisiblePages || 5;
        this.showNavButtons = options.showNavButtons !== false;
        
        this.init();
    }
    
    init() {
        if (!this.container) {
            console.error('AdminPagination: Container element is required');
            return;
        }
        
        this.render();
        this.attachEvents();
    }
    
    render() {
        const wrapper = document.createElement('div');
        wrapper.className = 'admin-pagination-wrapper';
        
        // Create pagination nav only
        const nav = this.createPaginationNav();
        wrapper.appendChild(nav);
        
        // Clear container and append new pagination
        this.container.innerHTML = '';
        this.container.appendChild(wrapper);
    }
    
    createPaginationNav() {
        const nav = document.createElement('nav');
        nav.setAttribute('aria-label', 'Pagination Navigation');
        
        const ul = document.createElement('ul');
        ul.className = 'admin-pagination';
        
        // Previous button
        if (this.showNavButtons) {
            ul.appendChild(this.createPageItem('prev', this.currentPage - 1, 'Previous', this.currentPage === 1));
        }
        
        // Page numbers
        const pageNumbers = this.calculateVisiblePages();
        pageNumbers.forEach(page => {
            if (page === '...') {
                ul.appendChild(this.createEllipsis());
            } else {
                ul.appendChild(this.createPageItem('page', page, page.toString(), false, page === this.currentPage));
            }
        });
        
        // Next button
        if (this.showNavButtons) {
            ul.appendChild(this.createPageItem('next', this.currentPage + 1, 'Next', this.currentPage === this.totalPages));
        }
        
        nav.appendChild(ul);
        return nav;
    }
    
    createPageItem(type, page, text, disabled = false, active = false) {
        const li = document.createElement('li');
        li.className = `page-item ${type}`;
        
        if (disabled) li.classList.add('disabled');
        if (active) li.classList.add('active');
        
        const link = document.createElement('a');
        link.className = 'page-link';
        link.href = '#';
        link.textContent = text;
        link.setAttribute('data-page', page);
        
        if (disabled) {
            link.setAttribute('tabindex', '-1');
            link.setAttribute('aria-disabled', 'true');
        }
        
        if (active) {
            link.setAttribute('aria-current', 'page');
        }
        
        li.appendChild(link);
        return li;
    }
    
    createEllipsis() {
        const li = document.createElement('li');
        li.className = 'page-item ellipsis';
        
        const span = document.createElement('span');
        span.className = 'page-link';
        span.textContent = '...';
        span.setAttribute('aria-hidden', 'true');
        
        li.appendChild(span);
        return li;
    }
    
    calculateVisiblePages() {
        const pages = [];
        const half = Math.floor(this.maxVisiblePages / 2);
        
        if (this.totalPages <= this.maxVisiblePages) {
            // Show all pages if total is less than or equal to max visible
            for (let i = 1; i <= this.totalPages; i++) {
                pages.push(i);
            }
        } else {
            // Always show first page
            pages.push(1);
            
            let start = Math.max(2, this.currentPage - half);
            let end = Math.min(this.totalPages - 1, this.currentPage + half);
            
            // Adjust start and end to maintain consistent number of visible pages
            if (end - start + 1 < this.maxVisiblePages - 2) {
                if (start === 2) {
                    end = Math.min(this.totalPages - 1, start + this.maxVisiblePages - 3);
                } else {
                    start = Math.max(2, end - this.maxVisiblePages + 3);
                }
            }
            
            // Add ellipsis after first page if needed
            if (start > 2) {
                pages.push('...');
            }
            
            // Add middle pages
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            // Add ellipsis before last page if needed
            if (end < this.totalPages - 1) {
                pages.push('...');
            }
            
            // Always show last page if it's not already included
            if (this.totalPages > 1) {
                pages.push(this.totalPages);
            }
        }
        
        return pages;
    }
    
    attachEvents() {
        this.container.addEventListener('click', (e) => {
            if (e.target.classList.contains('page-link') && !e.target.closest('.disabled, .ellipsis')) {
                e.preventDefault();
                
                const page = parseInt(e.target.getAttribute('data-page'));
                if (page && page !== this.currentPage && page >= 1 && page <= this.totalPages) {
                    this.goToPage(page);
                }
            }
        });
        
        // Keyboard navigation
        this.container.addEventListener('keydown', (e) => {
            if (e.target.classList.contains('page-link')) {
                switch (e.key) {
                    case 'ArrowLeft':
                        e.preventDefault();
                        if (this.currentPage > 1) {
                            this.goToPage(this.currentPage - 1);
                        }
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        if (this.currentPage < this.totalPages) {
                            this.goToPage(this.currentPage + 1);
                        }
                        break;
                    case 'Home':
                        e.preventDefault();
                        this.goToPage(1);
                        break;
                    case 'End':
                        e.preventDefault();
                        this.goToPage(this.totalPages);
                        break;
                }
            }
        });
    }
    
    goToPage(page) {
        if (page === this.currentPage || page < 1 || page > this.totalPages) {
            return;
        }
        
        const oldPage = this.currentPage;
        this.currentPage = page;
        
        // Add loading state
        this.container.style.opacity = '0.6';
        this.container.style.pointerEvents = 'none';
        
        // Call the page change callback
        Promise.resolve(this.onPageChange(page, oldPage))
            .then(() => {
                this.render();
            })
            .catch((error) => {
                console.error('Pagination error:', error);
                this.currentPage = oldPage; // Revert on error
            })
            .finally(() => {
                this.container.style.opacity = '';
                this.container.style.pointerEvents = '';
            });
    }
    
    update(options = {}) {
        if (options.currentPage !== undefined) this.currentPage = options.currentPage;
        if (options.totalPages !== undefined) this.totalPages = options.totalPages;
        if (options.totalItems !== undefined) this.totalItems = options.totalItems;
        if (options.perPage !== undefined) this.perPage = options.perPage;
        
        this.render();
    }
    
    destroy() {
        if (this.container) {
            this.container.innerHTML = '';
        }
    }
}

// Export for use in other files
window.AdminPagination = AdminPagination;
