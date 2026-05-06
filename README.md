# Country Cove Balloons - E-commerce Website

A complete e-commerce website for selling balloons and party decorations built with HTML, CSS, JavaScript, PHP, and MySQL.

## Features

- 🏠 **Homepage** with hero banner, categories, featured products, and customer reviews
- 🛍️ **Product Listing** with filtering by category, price, and color
- 📄 **Product Detail** pages with image gallery and add to cart functionality
- 🛒 **Shopping Cart** with quantity management and order summary
- 💳 **Checkout** with customer information form
- 🔐 **User Login/Register** system
- 📞 **Contact Page** with contact form
- 📱 **Responsive Design** for mobile and desktop
- 🔧 **Admin Panel** for managing products and orders

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** PHP 7+
- **Database:** MySQL
- **Styling:** Custom CSS with modern design
- **Icons:** Font Awesome (via CDN)
- **Fonts:** Google Fonts (Poppins)

## Project Structure

```
balloon-planet/
├── index.html              # Homepage
├── products.html           # Product listing page
├── product.html            # Product detail page
├── cart.html              # Shopping cart page
├── checkout.html          # Checkout page
├── login.html             # Login page
├── contact.html           # Contact page
├── css/
│   └── style.css          # Main stylesheet
├── js/
│   └── script.js          # Frontend JavaScript
├── backend/
│   ├── config.php         # Database configuration
│   └── api.php            # API endpoints
├── images/                # Product images
└── README.md              # This file
```

## Setup Instructions

### Prerequisites

1. **Web Server:** Apache/Nginx with PHP support
2. **PHP:** Version 7.0 or higher
3. **MySQL:** Version 5.6 or higher
4. **Web Browser:** Modern browser with JavaScript enabled

### Installation Steps

1. **Clone/Download the project:**

   ```bash
   git clone https://github.com/yourusername/balloon-planet.git
   cd balloon-planet
   ```

2. **Database Setup:**
   - Create a new MySQL database named `balloon_planet`
   - The database tables will be automatically created when you first run the application

3. **Configure Database Connection:**
   - Open `backend/config.php`
   - Update the database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'balloon_planet');
     ```

4. **Web Server Setup:**
   - Place the project folder in your web server's document root
   - For XAMPP: Place in `htdocs/balloon-planet/`
   - For WAMP: Place in `www/balloon-planet/`

5. **Access the Website:**
   - Open your browser and navigate to: `http://localhost/balloon-planet/`
   - The homepage should load with sample products

## Features Overview

### Frontend Features

- **Responsive Design:** Works on desktop, tablet, and mobile devices
- **Product Filtering:** Filter products by category, price range, and color
- **Shopping Cart:** Add/remove items, update quantities, persistent storage
- **Product Search:** Search functionality (can be extended)
- **Image Gallery:** Product images with zoom functionality
- **Form Validation:** Client-side validation for all forms
- **Local Storage:** Cart persistence across browser sessions

### Backend Features

- **RESTful API:** Clean API endpoints for products, orders, and users
- **Database Integration:** MySQL database with proper relationships
- **User Authentication:** Login/register system with password hashing
- **Order Management:** Complete order processing and storage
- **Product Management:** CRUD operations for products
- **Security:** Prepared statements to prevent SQL injection

## API Endpoints

### Products

- `GET /api/products` - Get all products (with optional filters)
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create new product

### Orders

- `GET /api/orders` - Get all orders
- `GET /api/orders/{id}` - Get single order
- `POST /api/orders` - Create new order

### Users

- `POST /api/users` - User registration/login

## Sample Data

The application comes with sample products pre-loaded:

- Rainbow Birthday Balloon Set (₹599)
- Golden Wedding Balloons (₹899)
- Pink Anniversary Package (₹749)
- Blue Party Balloon Kit (₹449)
- Red Heart Balloons (₹399)
- Silver Celebration Set (₹699)

## Customization

### Adding New Products

1. Add product data to the `products` array in `js/script.js`
2. Or use the API: `POST /api/products` with product data

### Styling Changes

- Main styles are in `css/style.css`
- Color scheme: Pink (#ff69b4), Blue (#4169e1), White (#ffffff)
- Font: Poppins from Google Fonts

### Adding New Pages

1. Create new HTML file
2. Add navigation link in header
3. Include necessary CSS/JS files
4. Add backend functionality if needed

## Browser Support

- Chrome 70+
- Firefox 65+
- Safari 12+
- Edge 79+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the MIT License.

## Support

For support or questions, please contact:

- Email: info@countrycoveballoons.com
- Phone: +91 9876543210

---

Made with ❤️ for party lovers everywhere!
