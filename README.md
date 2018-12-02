# General Information
This is the job tracker project for cps276.  The purpose of this project is to give students an experience with working on a larger application so they can really see how all the pieces fit together, using an MVC type architecture.  Most of the application is already written and all the students are responsible for is to write the code for two back-end controller files.

NOTE: It is assumed you have already watched and followed along with the videos for (creating a droplet Digitalocean, Setting up your Digitalocean instance, changing mysql password, installing git, cloning job tracker application)

# Installation Instructions
1. ssh into your server

2. Go to where the php files are used (for digital ocean it should be /var/www/html) 

3. Go to the github location where the job_tracker_student_version_php files are located https://github.com/sshaper/job_tracker_student_version_php

4. Click clone or download and then click the copy icon.

5. Go to your terminal window and enter "git clone" then paste what you had copied.

6. A folder named job_tracker_student_version_php will be created, containing all the supporting files.  Go into the sql directory

7. Log into mysql (mysql -u root -p;) and create the job_tracker database (CREATE DATABASE job_tracker;). When done exit MySQL shell

8. Import the tables from the sql file (mysql -u root -p job_tracker < job_tracker.sql;).  Make sure you are in the sql directory of the job_tracker_student_version_php directory.

9. You must enable the rewrite engine and restrt apache

	9a sudo a2enmod rewrite

	9b sudo service apache2 restart

10. YOU MUST CHANGE THE CODE IN THE FOLLOWING FILES FOR THE APPLICATION TO WORK

	10a line 83 of controller/login.php change the url to yours.

	10b line 49 of public/js/login.js change the url to yours.

	10c line 3 of views/partials/navigation.php change the url to yours.

	10d go into the public directory and give full permissions to the account_folders directory (chmod 777 account_folders).

	10e lines 20 and 28 of the invoice.php change the url to yours.

	10f line 23 of controller/logout.php change the url to yours.

	10g line 6 of classes/General.php change to the server path to the index.php file from where your root folder is staring from the html folder (digital ocean)

11. Go to the application login page and login. NOTE: The application page is the browser url to the application.  So (on digitalocean) if the job tracker application is located at /var/www/html/job_tracker_student_version_php, then the browser url is (your ip address)/job_tracker_student_version_php. 

12. Click Accounts and then add account.

13. Add an account by clicking add account.

14. Click update account, select the account you just created, and change something the update account.

15. Check to make sure the update took by selecting update account again.

16. Click add account asset, enter an name and select a file to upload (I have included a folder named "testdocuments" you can get a file from there).

17. Click view account assets, select your account, click on the file name, you should have your pdf file open in a new window.

18. Delete the file you just created.

19. If all the above worked then the application is working as expected. 
# jobtracker
