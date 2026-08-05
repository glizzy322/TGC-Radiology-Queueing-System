# Application bootstrap

This folder is reserved for shared bootstrap code when the Laragon/MySQL deployment is implemented.

The bootstrap should load `config/app.php`, configure the timezone, create the MySQL connection, register routes, and provide shared error logging. Keep database credentials in environment variables rather than source files.
