# ODOWeb
<P>ODOWeb is yet another content management system. It is geared towards developers with small businesses as clients. ODOWeb provides group level access to pages, menus, objects, and methods of objects. This means one user could have access to methods a, b, and c on an object while another user may have access to a, b, c, and d. For users that do not have access rights to methods of an object the method is simply not loaded or available for that user. Some of ODOweb's features are listed below. </P>
<UL>
<LI>Logging system that records a user's ID, asign a severity level, and add custom comments.</LI>
<LI>E-mail the admin when logging reaches a given severity level.</LI>
<LI>Dynamic Menu system based off of user rights, conditional request variables, and CSS styles</LI>
<LI>Group level permissions on pages, menus, objects, and methods.</LI>
<LI>Mycrpt encryption support with per user Initialization Vectors.</LI>
<LI>MD5SUM, SHA256, SHA512 hashing support.</LI>
<LI>Basic news system with categorical viewing which can be associated to groups.</LI>

## License
<P>See docs/odowebLicense.txt</P>

## How to Install
<ol>
<li><B>wget https://github.com/BPTOpenWare/ODOWeb/archive/master.zip</B></li>
<BR>
<li><B>unzip master.zip</B></li>
<BR>
<li><B>cp -R ODOWeb-master/src/images /path/to/your/apache/htdocs/images</B></li>
<BR>
<li><B>cp -R ODOWeb-master/src/css /path/to/your/apache/htdocs/css</B></li>
<BR>
<li><B>cp -R ODOWeb-master/src/JavaScript /path/to/your/apache/htdocs/js</B></li>
<BR>
<li><B>cp -R ODOWeb-master/src/php/*.php /path/to/your/apache/htdocs/</B></li> - You do not need the Objects or Pages folders as they are loaded into the database.
<BR>
<li><B>mkdir /path/to/your/apache/htdocs/mobile</B></li>
<BR>
<li><B>mv /path/to/your/apache/htdocs/mobileindex.php /path/to/your/apache/htdocs/mobile/index.php</B></li>
<BR>
<li><B>mv /path/to/your/apache/htdocs/mobilelogin.php /path/to/your/apache/htdocs/mobile/login.php</B></li>
<BR>
<li><B>You should create a new mysql username and password for the new BPTPOINT database. This is not covered here.</B></li>
<BR>
<li><B>mysql -u USERNAME -p BPTPOINT < ODOWeb-master/db/ODOWebv3.sql</B></li>
<BR>
<li><B>Change the group owner on the newly created files to your apache2 server group. On some systems this is www-data. chgrp -R www-data /path/to/your/apache/htdocs/*</B></li>
</ol>