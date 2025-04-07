(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();
    
    
    // Initiate the wowjs
    new WOW().init();


    // Fixed Navbar
    $(window).scroll(function () {
        if ($(window).width() < 992) {
            if ($(this).scrollTop() > 45) {
                $('.fixed-top').addClass('bg-white shadow');
            } else {
                $('.fixed-top').removeClass('bg-white shadow');
            }
        } else {
            if ($(this).scrollTop() > 45) {
                $('.fixed-top').addClass('bg-white shadow').css('top', -45);
            } else {
                $('.fixed-top').removeClass('bg-white shadow').css('top', 0);
            }
        }
    });
    
    
    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        margin: 25,
        loop: true,
        center: true,
        dots: false,
        nav: true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ],
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });

    
})(jQuery);

// Cart persistence functions
function handleLoginResponse(response) {
    if (response.success) {
        localStorage.setItem('loggedIn', 'true');
        
        // Always preserve local cart unless server has items
        const localCart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (response.cart && response.cart.length > 0) {
            // Convert server cart to same format as local cart
            const serverCart = response.cart.map(item => ({
                id: item.product_id,
                name: item.name || `Product ${item.product_id}`,
                price: item.price || 0,
                quantity: item.quantity
            }));
            
            // Merge carts - server items take precedence
            const mergedCart = [...localCart.filter(localItem => 
                !serverCart.some(serverItem => serverItem.id === localItem.id)
            ), ...serverCart];
            
            localStorage.setItem('cart', JSON.stringify(mergedCart));
        }
    }
}

function handleLogout() {
    // Always preserve current cart
    const currentCart = JSON.parse(localStorage.getItem('cart')) || [];
    
    fetch('api/logout.php', {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Only clear authentication items
            localStorage.removeItem('loggedIn');
            localStorage.removeItem('user');
            sessionStorage.clear();
            
            // Convert server cart to local format if exists
            let preservedCart = currentCart;
            if (data.cart && data.cart.length > 0) {
                preservedCart = data.cart.map(item => ({
                    id: item.product_id,
                    name: item.name || `Product ${item.product_id}`,
                    price: item.price || 0,
                    quantity: item.quantity
                }));
            }
            
            // Ensure cart is preserved
            localStorage.setItem('cart', JSON.stringify(preservedCart));
            
            // Redirect after short delay to ensure localStorage is updated
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 100);
        } else {
            console.error('Logout failed:', data.message);
            // Still preserve cart even if logout fails
            localStorage.setItem('cart', JSON.stringify(currentCart));
            window.location.href = 'login.html';
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        // Preserve cart on network errors too
        localStorage.setItem('cart', JSON.stringify(currentCart));
        window.location.href = 'login.html';
    });
}

// Initialize cart persistence
document.addEventListener('DOMContentLoaded', function() {
    // Check login status on page load
    if (localStorage.getItem('loggedIn') === 'true') {
        // Optionally refresh cart from server
    }
});

// Add click handler to all logout buttons
document.querySelectorAll('.logout-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        handleLogout();
    });
});