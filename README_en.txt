============
README
============

--------------------------------------------
RELEASE INFORMATION
--------------------------------------------
SetucoCMS version 1.0.0
Released on 2011/03/04

SetucoCMS version 1.1.0
Released on 2011/12/30

SetucoCMS version 1.2.0
Released on 2012/02/26

SetucoCMS version 1.3.0
Released on 2012/03/17

SetucoCMS version 1.4.0
Released on 2012/06/17

SetucoCMS version 1.5.0
Released on 2012/09/08

SetucoCMS version 1.6.0
Released on 2013/02/23

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
       Options FollowSymLinks
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
Mail: setucocms@gmail.com
Twitter: https://twitter.com/setucocms

--------------------------------------------
LICENSE
--------------------------------------------
The files in this archive are released under the SetucoCMS license.
see /docs/COPYING.txt
