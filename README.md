# NTU_Eval

The project aims to develop an evaluation and classification application for assessing the quality of units, staff, and employees at Nha Trang University. This web-based application will facilitate the systematic collection and analysis of performance data, enabling the university to enhance its operational efficiency and improve the quality of education and services provided.

## Introduction

This project is a web application built with Laravel, using Vite for front-end asset management and Tailwind CSS for UI design. The application allows users to perform tasks related to evaluation and document management.

## System Requirements

- PHP >= 8.0
- Composer
- Node.js >= 14.x
- MySQL or SQLite

## Installation

1. **Clone the Repository**

   ```bash
   git clone https://github.com/username/repo.git
   cd repo
   ```

2. **Install PHP Packages**

   ```bash
   composer install
   ```

3. **Install Node.js Packages**

   ```bash
   npm install
   ```

4. **Configure Environment**

   Copy the `.env.example` file to `.env` and configure the necessary information.

   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key**

   ```bash
   php artisan key:generate
   ```

6. **Run Migrations**

   ```bash
   php artisan migrate
   ```

7. **Run Seeder (Optional)**

   If you want to add sample data to the database, run:

   ```bash
   php artisan db:seed
   ```

8. **Run the Application**

   ```bash
   npm run dev
   php artisan serve
   ```

   Access the application at `http://localhost:8000`.

## Directory Structure

- `app/`: Contains the main application code.
- `database/`: Contains migration and seeder files.
- `resources/`: Contains resources such as views, styles, and scripts.
- `routes/`: Contains route definitions for the application.
- `public/`: Contains publicly accessible files like CSS, JavaScript, and images.

## Using jQuery

This project uses jQuery to handle interactions on the interface. jQuery has been installed via npm and imported in the `resources/js/bootstrap.js` file.

## Contributing

If you would like to contribute to the project, please create a pull request or open an issue to discuss changes.

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).