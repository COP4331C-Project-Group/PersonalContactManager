In order to use the .sql file, you must have some form of sql installed on your computer, as well as a "server" that will be used as the repository for the databases. You can use the command prompt to execute the following command:

`sudo mysql -u root -p < CreateTables.sql`

You will need to enter in the password for root access in order to finish the execution. This command will create the database and the three tables used for the actual database. The users and contacts tables will have test data, but the images table will be empty. Adding data to this table that would be connected to the contacts would require a lot more to download than just the .sql file.
If you wish to remove everything from the database, log in to your sql server and execute:

`DROP DATABASE COP4331;`

Removing everything from the database will also remove the tables, so there is no need to drop each table individually.
If you want to make sure that the database is gone, use:

`SHOW DATABASES;`
