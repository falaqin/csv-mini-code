# Setup
> [!NOTE]
> This project requires dependencies such as Redis installed on your machine.
> You can use something else other than bun as your package manager, such as pnpm.


On first load, please ensure all the dependencies are installed.
```
composer update
bun install
```

Create a dotEnv file by executing `touch .env` inside of the root project if you are using Unix system.

Run `php artisan migrate` to migrate all the required tables.

Then, run these commands to get them up and running:
```
php artisan serve
bun dev
```

Background processing for queues is powered by Laravel Horizon. Please make sure the daemon is kept running
```
php artisan horizon
```
