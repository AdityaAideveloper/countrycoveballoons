# Country Cove Balloons - E-commerce Website

A complete e-commerce website for selling balloons and party decorations built with HTML, CSS, JavaScript, and Django.

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
- **Backend:** Django 6.0+
- **Database:** SQLite (default), easily configurable for MySQL/PostgreSQL
- **Styling:** Custom CSS with modern design
- **Icons:** Font Awesome (via CDN)
- **Fonts:** Google Fonts (Poppins)

## Project Structure

```
balloon-planet/
├── manage.py               # Django management script
├── balloon_planet/         # Django project config
│   ├── settings.py
│   ├── urls.py
│   └── wsgi.py
├── api/                    # Django app (models, views, URLs)
│   ├── models.py
│   ├── views.py
│   └── urls.py
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
├── images/                # Product images
└── README.md              # This file
```

## Setup Instructions

### Prerequisites

1. **Python:** Version 3.10 or higher
2. **Django:** Version 6.0 or higher
3. **Web Browser:** Modern browser with JavaScript enabled

### Installation Steps

1. **Clone/Download the project:**

   ```bash
   git clone https://github.com/yourusername/balloon-planet.git
   cd balloon-planet
   ```

2. **Install dependencies:**

   ```bash
   pip install -r requirements.txt
   ```

3. **Database Setup:**

   ```bash
   python manage.py migrate
   ```

4. **Initialize sample data and admin:**

   ```bash
   python -c "import os; os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'balloon_planet.settings'); import django; django.setup(); from api.models import CustomUser, Product; CustomUser.objects.create_superuser('Admin', 'admin@countrycoveballoons.com', 'admin123', role='admin', phone='9876543210', status='active') if not CustomUser.objects.filter(role='admin').exists() else None; [Product.objects.create(**d) for d in [{'name':'Rainbow Birthday Balloon Set','description':'Beautiful rainbow colored balloons perfect for birthday celebrations.','price':599,'stock':100,'category':'birthday','color':'multi','image':'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=400&q=80','rating':4.5},{'name':'Golden Wedding Balloons','description':'Elegant gold balloons for wedding decorations.','price':899,'stock':100,'category':'wedding','color':'gold','image':'https://images.unsplash.com/photo-1504196606672-aef5c9cefc92?w=400&q=80','rating':4.8},{'name':'Pink Anniversary Package','description':'Romantic pink balloon arrangement perfect for anniversary celebrations.','price':749,'stock':100,'category':'anniversary','color':'pink','image':'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=400&q=80','rating':4.7},{'name':'Blue Party Balloon Kit','description':'Complete blue balloon kit for birthday parties.','price':449,'stock':100,'category':'birthday','color':'blue','image':'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=400&q=80','rating':4.3},{'name':'Red Heart Balloons','description':'Red heart-shaped balloons for romantic occasions.','price':399,'stock':100,'category':'anniversary','color':'red','image':'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400&q=80','rating':4.6},{'name':'Silver Celebration Set','description':'Silver metallic balloons for elegant celebrations.','price':699,'stock':100,'category':'wedding','color':'silver','image':'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=400&q=80','rating':4.4}] if not Product.objects.exists() else None"
   ```

5. **Run the development server:**

   ```bash
   python manage.py runserver
   ```

6. **Access the Website:**
   - Open your browser and navigate to: `http://127.0.0.1:8000/`
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
- **Database Integration:** SQLite with Django ORM (MySQL/PostgreSQL configurable)
- **User Authentication:** Login/register system with Django's password hashing
- **Order Management:** Complete order processing and storage
- **Product Management:** CRUD operations for products
- **Security:** Django's built-in CSRF protection and SQL injection prevention

## API Endpoints

### Products

- `GET /api/products/` - Get all products (with optional filters)
- `GET /api/products/<id>/` - Get single product
- `POST /api/products/` - Create new product

### Orders

- `GET /api/orders/` - Get all orders
- `GET /api/orders/<id>/` - Get single order
- `POST /api/orders/` - Create new order

### Auth

- `POST /api/auth/login/` - Login
- `POST /api/auth/register/user/` - Register user
- `POST /api/auth/register/store/` - Register store
- `POST /api/auth/logout/` - Logout
- `GET /api/auth/session/` - Check session

### Dashboards

- `POST /api/dashboard/user/` - User dashboard data
- `POST /api/dashboard/store/` - Store dashboard data
- `POST /api/dashboard/admin/` - Admin dashboard data
- `POST /api/store/update-status/` - Approve/reject store

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
