# Hardware requirements

### Server

For testing purposes you can run LabDB of any computer hardware, including
embedded systems like [Raspberry PI] (http://www.raspberrypi.org).
When you start using the database in your lab the amounts of data stored
quickly become enormous, therefore please make sure that you have proper
dedicated (or cloud-based) server for running LabDB. In fact we recommend
two servers: one running the webserver and memcached and another server
running the database platform of your choice. Please take time and
optimize your database server configuration for high load use, especially
by increasing the query cache size.

### Scanners and printers

For best user experience we recommend purchasing a 2D barcode scanner and a
dedicated label printer. Currently the label size is fixed at 51mm x 25mm,
so please take this into account when choosing your label provider.

# Software Requirements:
* apache / lighttpd / nginx / other web servers with PHP support
* php >= 5.3
* php5-intl module
* php5-memcache and a memcached server
* php5 module for your database platform (php5-mysql, php5-pgsql)
* java :( is required for cssembed and yuicompressor

### Optional PHP modules:
* php5-sqlite is required for tests to run
* php5-xcache speeds up the database
