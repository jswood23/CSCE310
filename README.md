# CSCE 310 Book Club
This is Group 20's final project for CSCE 310. Group members include:
* Joshua Wood
* Sam Hollenbeck
* Mark Matis
* Owen Moore

## Setting Up XAMPP
To install the latest version of XAMPP, first go to [https://www.apachefriends.org/](https://www.apachefriends.org/) to download the installer. Run the normal setup, not excluding any of the recommended packages.

Once you have installed XAMPP, go to the installation path (on Windows the default is C:\xampp) and change the xampp-control file to run as administrator (on Windows right-click->properties->compatibility->"Run this program as administrator")

Then, run the program and make sure that there are no error messages in the console inside the control panel. Once everything is good, click "Config" in the Apache line and then "Apache (http.conf)". This will open up the config file. Search for the line with "DocumentRoot" and change the listed path to the path where you are storing this repository locally. Save and exit the program, then restart XAMPP.

You can check that this is working by starting the Apache service in the control panel then going to [localhost/](localhost/) on your browser.