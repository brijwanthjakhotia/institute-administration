# School Management System

A comprehensive school management system built with Laravel and Vue.js, designed to streamline administrative tasks and improve communication between schools, teachers, students, and parents.

## Features

### School Management
- Create and manage multiple schools
- Track school details including contact information, principal details, and establishment year
- Manage school status and configurations

### Class Room Management
- Create and organize class rooms
- Assign teachers to class rooms
- Track class room capacity and details

### Student Management
- Comprehensive student profiles
- Track student enrollment and academic progress
- Manage student attendance and grades
- Link students with parent/guardian information

### Teacher Management
- Complete teacher profiles
- Assign teachers to subjects and class rooms
- Track teacher qualifications and specializations

### Subject Management
- Create and manage subjects
- Assign subjects to teachers
- Track subject details and requirements

### Attendance System
- Record daily student attendance
- Generate attendance reports
- Track attendance patterns and statistics

### Grade Management
- Record and manage student grades
- Generate grade reports
- Track academic performance

### Parent/Guardian Management
- Maintain parent/guardian contact information
- Link parents with students
- Track parent-student relationships

### Fee Management
- Create and manage different types of fees
- Set fee amounts and frequencies
- Track fee payments and status
- Generate fee reports

### Payment System
- Record and track payments
- Support multiple payment methods
- Generate payment receipts
- Track payment history

### Notification System
- Send notifications to different user groups
- Support multiple notification types
- Schedule notifications
- Track notification status

## Technical Stack

### Backend
- Laravel 10.x
- PHP 8.1+
- MySQL 8.0+
- RESTful API architecture

### Frontend
- Vue.js 3
- Tailwind CSS
- Axios for API communication
- Composition API

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- MySQL >= 8.0
- NPM or Yarn

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/school-management-system.git
cd school-management-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

7. Run database migrations:
```bash
php artisan migrate
```

8. Seed the database (optional):
```bash
php artisan db:seed
```

9. Compile assets:
```bash
npm run dev
```

10. Start the development server:
```bash
php artisan serve
```

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── common/
│   │   │   ├── schools/
│   │   │   ├── class-rooms/
│   │   │   ├── students/
│   │   │   ├── teachers/
│   │   │   ├── subjects/
│   │   │   ├── attendances/
│   │   │   ├── grades/
│   │   │   ├── parent-guardians/
│   │   │   ├── fees/
│   │   │   ├── payments/
│   │   │   └── notifications/
│   └── views/
└── routes/
    ├── api.php
    └── web.php
```

## API Documentation

The API documentation is available at `/api/documentation` when running the application.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, email support@schoolmanagementsystem.com or create an issue in the repository.
