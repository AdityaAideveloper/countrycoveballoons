// Sample product data - Mother's Day Collection
const products = [
    {
        id: 1,
        name: "Happy Mother's Day Heart Bouquet #1",
        price: 599,
        category: "occasion",
        color: "multi",
        image: "images/mothers-day/heart-shape-latex-rubber-balloon-525.jpg",
        description: "Beautiful heart-shaped balloons with Happy Mother's Day message. Pastel colors with floral accents.",
        rating: 4.9
    },
    {
        id: 2,
        name: "Happy Mother's Day Heart Bouquet #2",
        price: 649,
        category: "occasion",
        color: "pink",
        image: "images/mothers-day/il_1588xN.7951440279_p4js.webp",
        description: "Elegant pink heart balloons celebrating Mom. Rose gold and pink tones with sweet messages.",
        rating: 4.8
    },
    {
        id: 3,
        name: "Best Mom Ever Balloon Set #3",
        price: 699,
        category: "occasion",
        color: "multi",
        image: "images/mothers-day/istockphoto-1432655308-2048x2048.jpg",
        description: "Gorgeous floral design with Best Mom Ever message. Perfect for Mother's Day celebration.",
        rating: 4.9
    },
    {
        id: 4,
        name: "Love You Mom Balloon Bouquet #4",
        price: 549,
        category: "occasion",
        color: "pink",
        image: "images/mothers-day/il_1588xN.7658434801_11ph.webp",
        description: "Sweet 'Love You Mom' message balloons with beautiful floral patterns and pastel colors.",
        rating: 4.7
    },
    {
        id: 5,
        name: "Best Mom Ever Premium Set #5",
        price: 799,
        category: "premium",
        color: "multi",
        image: "images/mothers-day/Black-and-Gold-50th-Birthday-Hot-Air-Balloon-Gifts-Large_1600x.webp",
        description: "Premium Best Mom Ever balloons with gold accents. Luxurious design for special moms.",
        rating: 5.0
    },
    {
        id: 6,
        name: "Happy Mother's Day Classic #6",
        price: 499,
        category: "occasion",
        color: "pink",
        image: "images/mothers-day/happy-birthday-full-balloon-set-woman-happy.webp",
        description: "Classic Happy Mother's Day balloons in soft pink and white. Timeless celebration choice.",
        rating: 4.6
    },
    {
        id: 7,
        name: "Purple Mom's Love Balloon Set #7",
        price: 649,
        category: "occasion",
        color: "purple",
        image: "images/mothers-day/il_1588xN.7616080661_eo9p.webp",
        description: "Elegant purple Happy Mother's Day balloons. Rich colors with floral decorations.",
        rating: 4.8
    },
    {
        id: 8,
        name: "Pink Love You Mom Set #8",
        price: 599,
        category: "occasion",
        color: "pink",
        image: "images/mothers-day/H801898welcomeballoonset_2.webp",
        description: "Adorable Love You Mom balloon bouquet in beautiful pink shades. Perfect surprise gift.",
        rating: 4.7
    },
    {
        id: 9,
        name: "Floral Best Mom Ever #9",
        price: 749,
        category: "premium",
        color: "multi",
        image: "images/mothers-day/istockphoto-1464259187-2048x2048.jpg",
        description: "Stunning floral Best Mom Ever balloons with intricate rose patterns. Premium quality.",
        rating: 4.9
    },
    {
        id: 10,
        name: "Purple Love Mom Balloon #10",
        price: 679,
        category: "occasion",
        color: "purple",
        image: "images/mothers-day/il_1588xN.2822930225_1abd.webp",
        description: "Elegant purple and pink Love You Mom balloons. Sophisticated design for modern moms.",
        rating: 4.8
    }
];

// Cart functionality
let cart = JSON.parse(localStorage.getItem('balloonCart')) || [];

// Update cart count in header
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = cart.reduce((total, item) => total + item.quantity, 0);
    }
}

// Add to cart
function addToCart(productId, quantity = 1) {
    const product = products.find(p => p.id === productId);
    if (!product) return;

    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            quantity: quantity
        });
    }

    localStorage.setItem('balloonCart', JSON.stringify(cart));
    updateCartCount();
    alert('Product added to cart!');
}

// Remove from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('balloonCart', JSON.stringify(cart));
    updateCartCount();
    renderCart();
}

// Update cart item quantity
function updateCartQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }

    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = newQuantity;
        localStorage.setItem('balloonCart', JSON.stringify(cart));
        updateCartCount();
        renderCart();
    }
}

// Render featured products on homepage
function renderFeaturedProducts() {
    const featuredContainer = document.getElementById('featured-products');
    if (!featuredContainer) return;

    const featuredProducts = products.slice(0, 3);
    featuredContainer.innerHTML = featuredProducts.map(product => `
        <div class="product-card">
            <img src="${product.image}" alt="${product.name}">
            <div class="product-info">
                <h3>${product.name}</h3>
                <div class="price">₹${product.price}</div>
                <div class="rating">${'⭐'.repeat(Math.floor(product.rating))}</div>
                <button class="btn btn-primary" onclick="addToCart(${product.id})">Add to Cart</button>
            </div>
        </div>
    `).join('');
}

// Render products on products page
function renderProducts(category = 'all', priceRange = 'all', color = 'all') {
    const productsContainer = document.getElementById('products-grid');
    if (!productsContainer) return;

    let filteredProducts = products;

    if (category !== 'all') {
        filteredProducts = filteredProducts.filter(p => p.category === category);
    }

    if (priceRange !== 'all') {
        const [min, max] = priceRange.split('-').map(p => parseInt(p));
        filteredProducts = filteredProducts.filter(p => {
            if (max) {
                return p.price >= min && p.price <= max;
            } else {
                return p.price >= min;
            }
        });
    }

    if (color !== 'all') {
        filteredProducts = filteredProducts.filter(p => p.color === color);
    }

    productsContainer.innerHTML = filteredProducts.map(product => `
        <div class="product-card">
            <img src="${product.image}" alt="${product.name}">
            <div class="product-info">
                <h3>${product.name}</h3>
                <div class="price">₹${product.price}</div>
                <div class="rating">${'⭐'.repeat(Math.floor(product.rating))}</div>
                <button class="btn btn-primary" onclick="addToCart(${product.id})">Add to Cart</button>
                <a href="product.html?id=${product.id}" class="btn btn-secondary" style="margin-top: 0.5rem;">View Details</a>
            </div>
        </div>
    `).join('');
}

// Render product detail
function renderProductDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id'));
    const product = products.find(p => p.id === productId);

    if (!product) {
        document.getElementById('product-content').innerHTML = '<p>Product not found.</p>';
        return;
    }

    document.getElementById('product-content').innerHTML = `
        <div class="product-images">
            <img src="${product.image}" alt="${product.name}" class="main-image">
        </div>
        <div class="product-info">
            <h1>${product.name}</h1>
            <div class="product-price">₹${product.price}</div>
            <div class="rating">${'⭐'.repeat(Math.floor(product.rating))} (${product.rating})</div>
            <div class="product-description">${product.description}</div>
            <div class="quantity-selector">
                <button class="quantity-btn" onclick="changeQuantity(-1)">-</button>
                <input type="number" class="quantity-input" value="1" min="1" id="quantity">
                <button class="quantity-btn" onclick="changeQuantity(1)">+</button>
            </div>
            <button class="btn btn-primary" onclick="addToCart(${product.id}, parseInt(document.getElementById('quantity').value))">Add to Cart</button>
        </div>
    `;
}

// Change quantity in product detail
function changeQuantity(delta) {
    const quantityInput = document.getElementById('quantity');
    const newQuantity = parseInt(quantityInput.value) + delta;
    if (newQuantity >= 1) {
        quantityInput.value = newQuantity;
    }
}

// Render cart
function renderCart() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartSummaryContainer = document.getElementById('cart-summary');
    const emptyCartDiv = document.getElementById('empty-cart');
    const progressFill = document.getElementById('progress-fill');
    const shippingText = document.getElementById('shipping-text');
    const remainingAmount = document.getElementById('remaining-amount');

    if (!cartItemsContainer || !cartSummaryContainer) return;

    // Calculate totals
    const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    const shipping = subtotal >= 999 ? 0 : 100;
    const total = subtotal + shipping;

    // Update shipping progress
    if (progressFill && shippingText) {
        const freeShippingThreshold = 999;
        const progress = Math.min((subtotal / freeShippingThreshold) * 100, 100);
        progressFill.style.width = progress + '%';
        
        if (subtotal >= freeShippingThreshold) {
            shippingText.innerHTML = '🎉 Yay! You get FREE shipping!';
            shippingText.style.color = '#00C9A7';
        } else {
            const remaining = freeShippingThreshold - subtotal;
            remainingAmount.textContent = remaining;
            shippingText.innerHTML = `Add ₹<span id="remaining-amount">${remaining}</span> more for free shipping!`;
            shippingText.style.color = 'var(--accent)';
        }
    }

    // Show empty cart state
    if (cart.length === 0) {
        cartItemsContainer.style.display = 'none';
        if (emptyCartDiv) emptyCartDiv.style.display = 'block';
        if (progressFill) progressFill.style.width = '0%';
        cartSummaryContainer.innerHTML = '';
        return;
    }

    // Hide empty cart state
    cartItemsContainer.style.display = 'block';
    if (emptyCartDiv) emptyCartDiv.style.display = 'none';

    // Render cart items with better styling
    cartItemsContainer.innerHTML = cart.map((item, index) => `
        <div class="cart-item" style="display: flex; background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; padding: 1.5rem; margin-bottom: 1rem; align-items: center; gap: 1.5rem; transition: all 0.3s ease; animation: slide-in-left 0.5s ease-out ${index * 0.1}s both;">
            <img src="${item.image}" alt="${item.name}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 15px; border: 2px solid rgba(255,255,255,0.3);">
            <div class="cart-item-details" style="flex: 1;">
                <h3 style="color: white; font-size: 1.2rem; margin-bottom: 0.5rem;">${item.name}</h3>
                <div class="price" style="font-size: 1.3rem; font-weight: 700; background: linear-gradient(135deg, #FF4D8D, #FFD93D); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 0.5rem;">₹${item.price}</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Item Total: ₹${item.price * item.quantity}</div>
            </div>
            <div class="cart-item-actions" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                <div class="quantity-selector" style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.1); border-radius: 25px; padding: 5px;">
                    <button onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})" style="width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); color: white; font-size: 1.2rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">−</button>
                    <span style="color: white; font-weight: 600; min-width: 30px; text-align: center;">${item.quantity}</span>
                    <button onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})" style="width: 32px; height: 32px; border-radius: 50%; border: none; background: rgba(255,255,255,0.2); color: white; font-size: 1.2rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">+</button>
                </div>
                <button onclick="removeFromCart(${item.id})" style="background: transparent; border: none; color: #ff6b6b; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; gap: 0.3rem; transition: all 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">🗑️ Remove</button>
            </div>
        </div>
    `).join('');

    // Calculate savings
    const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
    const savings = shipping === 0 ? 100 : 0;

    // Render summary with better styling
    cartSummaryContainer.innerHTML = `
        <div style="background: rgba(255,255,255,0.1); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.3); border-radius: 25px; padding: 2rem;">
            <h3 style="color: white; font-size: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">📋 Order Summary</h3>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: rgba(255,255,255,0.9);">
                <span>Items (${itemCount}):</span>
                <span>₹${subtotal}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: rgba(255,255,255,0.9);">
                <span>Shipping:</span>
                <span style="${shipping === 0 ? 'color: #00C9A7; font-weight: 600;' : ''}">${shipping === 0 ? 'FREE 🎉' : '₹' + shipping}</span>
            </div>
            
            ${savings > 0 ? `
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: #00C9A7; font-weight: 600;">
                <span>You Saved:</span>
                <span>₹${savings}</span>
            </div>
            ` : ''}
            
            <div style="height: 1px; background: rgba(255,255,255,0.2); margin: 1rem 0;"></div>
            
            <div style="display: flex; justify-content: space-between; font-size: 1.3rem; font-weight: 700; color: white; margin-bottom: 1.5rem;">
                <span>Total:</span>
                <span style="background: linear-gradient(135deg, #FF4D8D, #FFD93D); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">₹${total}</span>
            </div>
            
            <a href="checkout.html" class="btn btn-primary" style="width: 100%; display: block; text-align: center; margin-bottom: 1rem;">✨ Proceed to Checkout</a>
            
            <div style="text-align: center; color: rgba(255,255,255,0.6); font-size: 0.85rem;">
                🔒 Secure checkout powered by Country Cove Balloons
            </div>
        </div>
    `;
}

// Render order summary on checkout
function renderOrderSummary() {
    const orderSummaryContainer = document.getElementById('order-summary');
    if (!orderSummaryContainer) return;

    const subtotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
    const shipping = subtotal >= 999 ? 0 : 100;
    const total = subtotal + shipping;

    orderSummaryContainer.innerHTML = `
        <h3>Order Summary</h3>
        ${cart.map(item => `
            <div class="summary-row">
                <span>${item.name} x ${item.quantity}</span>
                <span>₹${item.price * item.quantity}</span>
            </div>
        `).join('')}
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>₹${subtotal}</span>
        </div>
        <div class="summary-row">
            <span>Shipping:</span>
            <span>${shipping === 0 ? 'Free' : '₹' + shipping}</span>
        </div>
        <div class="summary-row total">
            <span>Total:</span>
            <span>₹${total}</span>
        </div>
    `;
}

// Handle checkout form submission
function handleCheckout(event) {
    event.preventDefault();
    alert('Order placed successfully! Thank you for shopping with Country Cove Balloons.');
    cart = [];
    localStorage.setItem('balloonCart', JSON.stringify(cart));
    updateCartCount();
    window.location.href = 'index.html';
}

// Handle contact form submission
function handleContact(event) {
    event.preventDefault();
    alert('Thank you for your message! We will get back to you soon.');
    event.target.reset();
}

// Handle login form submission
function handleLogin(event) {
    event.preventDefault();
    alert('Login functionality will be implemented with backend.');
    event.target.reset();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();

    // Homepage
    if (document.getElementById('featured-products')) {
        renderFeaturedProducts();
    }

    // Products page
    if (document.getElementById('products-grid')) {
        renderProducts();

        // Filter functionality
        const categoryFilter = document.getElementById('category-filter');
        const priceFilter = document.getElementById('price-filter');
        const colorFilter = document.getElementById('color-filter');

        if (categoryFilter) categoryFilter.addEventListener('change', () => renderProducts(categoryFilter.value, priceFilter.value, colorFilter.value));
        if (priceFilter) priceFilter.addEventListener('change', () => renderProducts(categoryFilter.value, priceFilter.value, colorFilter.value));
        if (colorFilter) colorFilter.addEventListener('change', () => renderProducts(categoryFilter.value, priceFilter.value, colorFilter.value));
    }

    // Product detail page
    if (document.getElementById('product-content')) {
        renderProductDetail();
    }

    // Cart page
    if (document.getElementById('cart-items')) {
        renderCart();
    }

    // Checkout page
    if (document.getElementById('checkout-form')) {
        document.getElementById('checkout-form').addEventListener('submit', handleCheckout);
        renderOrderSummary();
    }

    // Contact page
    if (document.getElementById('contact-form')) {
        document.getElementById('contact-form').addEventListener('submit', handleContact);
    }

    // Login page
    if (document.getElementById('login-form')) {
        document.getElementById('login-form').addEventListener('submit', handleLogin);
    }
});