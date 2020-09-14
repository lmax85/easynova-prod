# easynova-prod

full tutorial for create NextCloud App - https://docs.nextcloud.com/server/19/developer_manual/app/intro.html

for starting:

1. Need to init project: can be downloaded starter-kit from nextcloud https://apps.nextcloud.com/developer/apps/generate (just fill all required fields).
2. Need put your new app to folder /va/www/nextcllud/apps.
3. The app can now be enabled on the Nextcloud apps page.
4. Limit app using for admin group (paste image)
5. Init Git repository on that app folder (in your case easynova)
6. Create deploy script for delivering production code to the your NextCloud server (in your case it use Github Action with ftp-deploy action script to copy files over ftp protocol)
7. Don't forget increase version number in appinfo/info.xml file to apply changes to your server.

After those steps you can develop your app locally, use control version Git, simple delivering code to your server.


