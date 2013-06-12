jetForm
=======

Simple framework application to create dinamic and validated form in 2 seconds.
In only few click you obtain a form in the sub directory "form" that make and insert into your database with all field validated.
Very useful for example, for newsletter subscrition form


Installation
============

Unzip the package into your root directory.
Now you have a directory tree like this:
    - conf
    - creation 
    - form
    - lib
    include.php

Create two directory and set apache user like owner:
    - logs 
    - data 

Set apache user like owner also to the directory form and all files inside, for example if you use linux debian:
    chown -Rf www-data:www-data form 


CONFIGURATION
============

Open conf/db.php and set database connection parameters.
Open conf/vars.php and set form creation variables:

```php
<?php
    $timezone = "Europe/Dublin";
    //field form, separeted by ","
    $db_fields = "name,surname,email,address"; 
    //field to validate
    //automatic validate for: 'name'||'surname'||'sex'||'job'||'phone'|| 'address' || 'city' || 'cap' || 'nation' || age || email || privacy1 || privacy2
    $mandatory_fields = "email"; 
    
    $logName = "projectname_form_subscribe"; //name for file log
?>
```

Usage
=====

Just time in your browser the url for form creation: http://www.yoursite.com/creation 
You have your db, your form and all the action for subscribtion.
The only things you have to do is customize your confirm.html page inside form directory and personalize with some color your new form :)


License
=======

This bundle is under MIT license
