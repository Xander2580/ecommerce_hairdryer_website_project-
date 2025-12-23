# StylePro Essentials – Hairdryer E‑Commerce Website

StylePro Essentials is a dynamic web application for buying and selling hairdryers, built as the final project for EC3352 Dynamic Web Programming.  
It implements a B2C electronic appliances store focused on hairdryers, with vendor and customer features plus basic reporting.

## Features

- Separate vendor and customer registration and login.
- Vendors can add, view, edit, and delete hairdryer products with images and full details.
- Customers can browse products, add to cart, update or remove items, and checkout with cash‑on‑delivery.
- Order status tracking (e.g., vendor accepted, ready for delivery, shipped, delivered).
- Simple analytics reports for vendors and customers (orders, products sold, date filters).

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP (procedural / mysqli)  
- **Database:** MySQL (XAMPP / phpMyAdmin)  
- **Browser target:** Google Chrome
- 
## Core Functionality

### Vendor Section

- Register/login as a vendor or business.
- Add new hairdryer products (name, brand, power (W), heat settings, price, stock, image, description).
- View list of products and perform **CRUD** operations (create, read, update, delete).
- View orders for their products, accept or discard order requests.
- View reports such as:
  - Number of products added and sold.
  - Most viewed and most purchased hairdryers.
  - Transaction reports with date filters (daily, weekly, monthly, custom). 

### Product Order Process (Customer)

- View all active hairdryers on the landing page with title, price, category, and image.
- Filter and search hairdryers by price (low–high, high–low), brand/category, and possibly power/feature.
- Add items to cart, update quantities, and remove items.
- Checkout after login or registration, choosing delivery or collection.
- Payment simulated as **cash on delivery** (no real card details). 
- Track order status (vendor accepted, ready for delivery, shipped, delivered).
- View product details and submit product reviews/ratings.

### Report Generation

- **Vendor reports:** products added, products sold, most viewed products, transaction summaries with date ranges.  
- **Customer reports:** total products ordered, past orders, and transaction history with date ranges. 
