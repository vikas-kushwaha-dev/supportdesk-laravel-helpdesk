# SupportDesk

SupportDesk is a Laravel helpdesk ticketing system built as a portfolio project for real-world IT support workflows. It lets customers create support tickets, agents manage assigned tickets, and admins oversee the ticket queue.

## Current Features

- Laravel 13 application with Breeze authentication.
- Role-based users: admin, agent, and customer.
- Ticket creation, listing, editing, viewing, and deletion.
- Ticket priorities: low, medium, high, and urgent.
- Ticket statuses: open, in progress, resolved, and closed.
- Customer-scoped ticket access through Laravel policies.
- Agent-scoped assigned ticket access.
- Admin ticket assignment to agents.
- Ticket search and filters by status, priority, category, assigned agent, and keyword.
- Ticket comments.
- Ticket file attachments.
- Rule-based ticket AI insights for suggested priority, category, sentiment, summary, and recommended action.
- Admin action to apply AI-suggested priority and category to a ticket.
- MySQL-ready Docker/Sail setup.

## Planned Portfolio Roadmap

This project follows the SupportDesk build plan from the portfolio discussion:

1. Authentication and roles.
2. Ticket module.
3. Comments and attachments.
4. Staff/agent ticket management.
5. Admin ticket assignment.
6. Departments and categories.
7. Status history logs.
8. Email notifications.
9. Search and filters.
10. SLA due dates and overdue ticket checks.
11. Reports and analytics.
12. Feature tests.
13. Seeded demo data.
14. Screenshots and deployment notes.

## Tech Stack

- PHP 8.3
- Laravel 13
- MySQL
- Laravel Breeze
- Laravel Sail
- Blade
- Tailwind CSS
- Vite
- PHPUnit

## Setup

Install PHP and Node dependencies:

```bash
composer install
npm install
```

Copy the environment file and generate the app key:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`. For Sail, the project expects:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=supportdesk
DB_USERNAME=sail
DB_PASSWORD=password
```

Start the Docker environment:

```bash
./vendor/bin/sail up -d
```

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

Build frontend assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Open the app at:

```text
http://localhost
```

## Useful Commands

Run tests:

```bash
./vendor/bin/sail artisan test
```

Run migrations:

```bash
./vendor/bin/sail artisan migrate
```

Create the storage symlink for attachments:

```bash
./vendor/bin/sail artisan storage:link
```

Format PHP code:

```bash
./vendor/bin/pint
```

## Demo Accounts

Demo seeders are still planned. Recommended accounts to add:

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@example.com | password |
| Agent | agent@example.com | password |
| Customer | customer@example.com | password |

## Next Best Tasks

The app already covers the foundation from the original plan. The strongest next steps are:

1. Add ticket status history logs.
2. Add seeders for demo accounts and example tickets.
3. Add reports and analytics dashboard.
4. Add screenshots and deployment notes.
5. Improve ticket page UI polish.
