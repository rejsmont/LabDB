# Hardware requirements

For best user experience we recommend purchasing a 2D barcode scanner and a
dedicated label printer. In our setup we are using the following hardware:

* Zebra GX430t Thermal Transfer label printer
* Zebra Z-Perform 1000T Paper 51mm x 25mm with Zebra 2300 Wax ribbons
* Motorola / Symbol DS4208 SR barcode scanner

Of course you can use any other equipment. Currently the label size is
fixed at 51mm x 25mm, so please take it into account when choosing labels.

# Software Requirements:
* apache / lighttpd / nginx / other web servers with php support
* php >= 5.3
* php5-intl module
* php5-memcache and a memcached server
* php5 module for your database platform (php5-mysql, php5-pgsql)

###Optional PHP modules:
* php5-sqlite is required for tests to run
* php5-xcache speeds up the database
