============
README
============

--------------------------------------------
RELEASE INFORMATION
--------------------------------------------
SetucoCMS version 1.0.0 Release
Released on 2011/03/04

SetucoCMS version 1.0.1 Release
Released on 2012/01/22

You can see CHANGELOG.txt to see detailed change history.

--------------------------------------------
SETTING
--------------------------------------------
This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.

Setting Up Your VHOST
---------------------

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "/path/to/SetucoCMS/public"
   ServerName localhost

   <Directory "/path/to/SetucoCMS/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>

--------------------------------------------
SYSTEM REQUIREMENTS
--------------------------------------------
SetutcoCMS requires:
PHP 5.2.4 or later
MySQL Server 5.1 or later

--------------------------------------------
QUESTIONS AND FEEDBACK
--------------------------------------------
To the following where to make contact please if there are some any suggestions.
SetucoCMS-public ML:setucocms-public@lists.sourceforge.jp

--------------------------------------------
LICENSE
--------------------------------------------
The files in this archive are released under the SetucoCMS license.
see /docs/COPYING.txt
