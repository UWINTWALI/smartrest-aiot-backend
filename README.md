# Smart Mattress for Health and Comfort

## Overview

We are developing an intelligent mattress that enhances sleep quality and monitors health using IoT, AI, and mobile/web applications. Through our research, we discovered that people spend a significant part of their lives sleeping; thus, it is an ideal time to track and improve health without disrupting daily routines.

This project is being developed in **PHP** and will require a database to store and manage data.

## Key Features

1. **Temperature Control:**  
   The mattress can adjust its temperature to improve sleep quality.

2. **Heart Rate Monitoring:**  
   Built-in sensors detect heart rate and provide real-time analytics.

3. **Breathing & Sweat Detection (Future):**  
   Future sensors will analyze breathing patterns and sweat to offer deeper health insights.

4. **Air Humidifier Connection (Future):**  
   The system can link to an external air humidifier to create the perfect sleep environment.

## Technology & Integration

-   **Sensors:**  
    Temperature sensors, heart rate sensors (e.g., ECG/PPG), humidity sensors, and pressure sensors.

-   **IoT:**  
    The mattress connects to the internet, allowing for real-time monitoring and control.

-   **Mobile App:**  
    Users can adjust settings, track health data, and receive personalized recommendations.

-   **Web App:**  
    A dashboard for detailed health reports and analytics.

-   **Future AI Integration:**  
     AI will eventuall```markdown
    y analyze sleep patterns, predict health risks, and provide customized recommendations.

## Prerequisites

-   **PHP:**  
    The project is built in PHP. Please ensure you have PHP installed on your system.

-   **Database:**  
    A supported database (e.g., MySQL, PostgreSQL, or SQLite) is required to run the application. Configure your database connection settings accordingly.

-   **Composer:**  
    Dependency management is handled by Composer.

-   **Web Server:**  
    You can use Apache, Nginx, or PHP’s built-in development server (e.g., via `php artisan serve` if using Laravel).

## Setup Instructions

1. **Clone the Repository:**

    ```bash
    git clone <repository-url>
    cd smart-mattress
    ```

2. **Install Dependencies:**

    ```bash
    composer install
    ```

3. **Configuration:**

    - Copy the environment configuration file:
        ```bash
        cp .env.example .env
        ```
    - Edit the `.env` file to set your database connection details and other environment variables.

4. **Run Database Migrations:**

    ```bash
    php artisan migrate
    ```

    ```bash
    php artisan serve
    ```

## Important Note

This version of the README is a preliminary draft. As the project evolves, this document will be updated with more detailed information, enhanced setup instructions, and additional guidance.

## License

[MIT License](LICENSE)
