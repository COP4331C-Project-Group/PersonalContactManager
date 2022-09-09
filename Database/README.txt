In order to use the .sql file, you must have some form of sql installed on your computer, as well as a "server" that will be used as
the repository for the databases.
You can use the command prompt to execute the following command:
mysql -u *username* < CreateTables.sql
which will create the database, and, using the database, will create the two tables and fill it with test data.
If you wish to remove everything from the database, log in to your sql server and execute:
DROP DATABASE COP4331;
If you want to make sure that the database is gone, use:
SHOW DATABASES;