# easynova-prod

full tutorial for create NextCloud App - https://docs.nextcloud.com/server/19/developer_manual/app/intro.html

for starting:

1. Need to init project: can be downloaded starter-kit from nextcloud https://apps.nextcloud.com/developer/apps/generate (just fill all required fields).
2. Need put your new app to folder /va/www/nextcllud/apps.
3. The app can now be enabled on the Nextcloud apps page.
4. Init Git repository on that app folder (in your case easynova)
5. Create deploy script for delivering production code to the your NextCloud server (it is preconfigured to use Github Action or Gitlab Pipelines with ftp-deploy action script to copy files over ftp protocol)

For apply some change (for example add new tables to DB) need to reinstall (disable then enable) app or can increase version app in appinfo/info.xml file.

After those steps you can develop your app locally, use control version Git, simple delivering code to your server.

###
Postman collection with test api methods:
https://www.getpostman.com/collections/c1f4e543d11410bf4e67


