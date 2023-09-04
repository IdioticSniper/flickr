# Snippr
 A faithful open-source revival of Flickr, circa late June-early July of 2004.
 
 # Requirements
 * A brain
 * Apache or nginx, a database setup, and PHP (8.*+).
 * php_gd
 * PDO
 
 # Setup
 1. Extract all of the files to your webserver.
 2. Create a new database and import the database file "db.sql"
 3. Rename config.sample.php (in /incl/) to config.php
 4. Modify the values on the file to match your desired branding settings and matching database credentials
 5. Create the following directories on the root: "/buddyicons/" and "/photos/"
 
 # Contributing
 * If it's a new feature or improvement, please look at Flickr archives on Web Archive and/or other reference material, as this wants to be as faithful as possible.
 * If it's code refactoring (such as optimizing), please specify what you are changing.
 * If you find a bug, please create an issue specifying what happened in particular.
 
 # Credits
 * dudebloke - Originally providing web hosting
 * jrs - Making the original logo for the project and coming up with the name
 * tai7k - Creating the current logo for the project
 * mortalkombatlegend92 - Currently providing the project's web hosting
 * genosmrpg7899 - Providing PDO query code (which is currently used on everything)
 * ohno - Helped with the port from MySQLi to PDo, added gd support to the uploader, originally fixed and/or reported a few vulnerabilities.
 * You - for taking interest on the project.