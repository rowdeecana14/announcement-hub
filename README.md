# Announcement Hub Application

## Project Description

It is an **Announcement Hub** application that features a **Single Page Application (SPA)** frontend and a **RESTful API** backend. The platform allows users to manage announcements, with an easy-to-use interface for creating, updating, and viewing announcements. The backend provides a secure API for handling data and authentication.

# Project Setup

This document provides instructions for setting up both the backend and frontend of the project.

## Backend Setup

Follow these steps to set up the backend:

1. **Navigate to the backend directory**:
   - Open a terminal and run:
     ```bash
     cd backend
     ```

2. **Install project dependencies**:
   - Use Composer to install the PHP dependencies by running:
     ```bash
     composer install
     ```

3. **Initialize the application**:
   - Run the following Artisan command to initialize the application:
     ```bash
     php artisan app:initialize
     ```

4. **Start the backend server**:
   - You can start the server with the following command:
     ```bash
     php artisan serve
     ```
   - This will run the backend server on `http://localhost:8000`.

### Admin Credentials

The default admin credentials for the backend application are:

- **Email**: admin@gmail.com
- **Password**: password

You can use these credentials to log into the admin dashboard after the backend server is running.

### Technologies Used (Backend)

The backend of the project is built using the following technologies:

- **PHP**: A popular general-purpose scripting language, used for server-side development.
- **Laravel**: A PHP framework for building robust and scalable web applications with elegant syntax.
- **Sanctum**: A simple package for API token authentication in Laravel, used for secure authentication of API requests.
- **PHPUnit**: A testing framework for PHP, used to perform unit testing of the backend code.

### Running Unit Tests (Backend)

To run the unit tests for the backend, you can use the following command:

```bash
php artisan test
``

## Frontend Setup

Follow these steps to set up the frontend:

1. **Navigate to the frontend directory**:
   - Open a terminal and run:
     ```bash
     cd frontend
     ```

2. **Install frontend dependencies**:
   - Use npm to install the required frontend packages by running:
     ```bash
     npm install
     ```

3. **Start the frontend development server**:
   - Run the following command to start the frontend development server:
     ```bash
     npm run dev
     ```
   - This will run the frontend server on `http://localhost:5173`.

### Technologies Used (Frontend)

The frontend of the project is built using the following technologies:

- **Vue.js**: A progressive JavaScript framework used for building user interfaces.
- **Vee-Validate**: A form validation library for Vue.js that provides an easy way to handle form validation.
- **Quasar**: A high-performance Vue.js framework used to create responsive web and mobile applications with a rich set of components and tools.
- **Vuex**: A state management pattern and library for Vue.js applications, used to manage the state of the frontend application.

Now you should have both the backend and frontend running locally, and you can start developing or testing the project.

Let me know if you need further details or clarification!
