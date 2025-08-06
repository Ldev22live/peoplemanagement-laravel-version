To run the app use the setup_people.sql and execute all sql statements.

Ensure your virtualhost points to the public folder 

For example if web server is apache:

<VirtualHost *:80>

	ServerName peoplemanage.com
	ServerAlias www.peoplemanage.com

	DocumentRoot /var/www/people-manage/public

	<Directory /var/www/people-manage/public/>
		AllowOverride All
		Require None
	</Directory>

</VirtualHost>

Of course if ssl involved use port 443 and then point to the relevant certificate.

Mysql username and password can be found in the .env file.
