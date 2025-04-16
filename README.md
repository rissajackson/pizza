<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Cheesy Does It Pizza Restaurant Tech Challenge

Welcome to the **Cheesy Does It** Pizza Orders web app! This web app is a Laravel + Vue.js implementation for enhancing customer experience by allowing Cheesy Does It to update pizza order statuses on their POS system, which communicates updates to the restaurant's website. It uses **Laravel Reverb** for event handling and leverages **Laravel**, **Vue.js**, and **Tailwind CSS** for the backend and frontend.

---

## **Objective**

The challenge requires building a **module** in the restaurant's POS system using **Laravel**. This module integrates with the restaurant’s website to enable real-time updates for customers to track their pizza orders. More specifically:
- Updates occur when the pizza is **started**, **placed in the oven**, or **ready for delivery/pickup**.
- A simple UI for Cheesy Does It is built to manage pizza order statuses using **Vue.js**.

---

## **Features**

- **POS Status Updates:** A basic interface is provided for Cheesy Does It to update pizza statuses.
- **Communication Protocol:** Laravel emits status updates through **Laravel Reverb**, which can be consumed by the restaurant's website for real-time customer updates.
- **Prebuilt Web Integration:** The app is designed to integrate seamlessly with an imagined API provided by the website.
- **User Authentication:** Cheesy Does It login is necessary to access the dashboard and manage pizza statuses.

---

## **Setup Instructions**

Follow these steps to run the project locally:

---

### **Installation**

1. **Clone the Repository**

2. **Install Backend Dependencies**
   Run the following command to install Laravel dependencies:
   ```bash
   composer install
   ```

3. **Install Frontend Dependencies**
   Run the following command to install `npm` dependencies for Vue.js and Tailwind CSS:
   ```bash
   npm install
   ```

4. **Set Up the Environment**
   Copy the `.env.example` file and configure your application environment:
   ```bash
   cp .env.example .env
   ```

5. **Generate the Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations and Seeding**
   To set up the necessary tables and seed the database with test data including pizza orders:
   ```bash
   php artisan migrate --seed
   ```

7. **Start the Backend Server**
   Run the Laravel development server:
   ```bash
   php artisan serve
   ```

8. **Start the Frontend**
   Compile the Vue.js assets using:
   ```bash
   npm run dev
   ```

9. **Start Reverb**
   Laravel Reverb is used to handle event broadcasting. Run the following command:
   ```bash
   php artisan reverb:start
   ```

   Ensure this is running in a terminal tab, as it is essential for real-time communication.

---

### **Access the Application**

1. Open your browser and visit: `pizza.test`
2. After you create a new user or use the Test User to log in (test@example.com, password) you can get to the Pizza Orders from the nav next to the Dashboard.
3. Clicking the buttons will update the status


Note:
I have more work to do on styling the frontend and getting the buttons to work better.
I'll be adding more commits as I get this finished. But please feel free to give me your feeback before I'm done.
Thanks!





## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
