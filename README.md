SQLite + SpatiaLite + PHP mapscript extension seg faults when you execute InitSpatialMetadata().

```
$ docker build -t mapserver -f .docker/Dockerfile .

$ docker run -it --rm mapserver /bin/bash

# php /test.php
Segmentation fault (core dumped)
```

test.php contains:

```
<?php

$db = new SQLite3('test.db');
$db->loadExtension('mod_spatialite.so');
$db->exec('SELECT InitSpatialMetadata()');

```

Stack trace:

```
root@e629d8ddb1d3:/var/www/html# gdb --args php /test.php 
GNU gdb (Debian 7.12-6) 7.12.0.20161007-git
Copyright (C) 2016 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <http://gnu.org/licenses/gpl.html>
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.  Type "show copying"
and "show warranty" for details.
This GDB was configured as "x86_64-linux-gnu".
Type "show configuration" for configuration details.
For bug reporting instructions, please see:
<http://www.gnu.org/software/gdb/bugs/>.
Find the GDB manual and other documentation resources online at:
<http://www.gnu.org/software/gdb/documentation/>.
For help, type "help".
Type "apropos word" to search for commands related to "word"...
Reading symbols from php...(no debugging symbols found)...done.
(gdb) run
Starting program: /usr/local/bin/php /test.php
warning: Error disabling address space randomization: Operation not permitted
[Thread debugging using libthread_db enabled]
Using host libthread_db library "/lib/x86_64-linux-gnu/libthread_db.so.1".

Program received signal SIGSEGV, Segmentation fault.
0x00007fd414044814 in ?? () from /usr/lib/x86_64-linux-gnu/libsqlite3.so.0
(gdb) bt
#0  0x00007fd414044814 in ?? () from /usr/lib/x86_64-linux-gnu/libsqlite3.so.0
#1  0x00007fd4140ab371 in sqlite3_exec () from /usr/lib/x86_64-linux-gnu/libsqlite3.so.0
#2  0x00007fd4140cad52 in sqlite3_get_table () from /usr/lib/x86_64-linux-gnu/libsqlite3.so.0
#3  0x00007fd4181ff640 in exists_spatial_ref_sys (p_sqlite=p_sqlite@entry=0x563da7c49248) at srs_init.c:627
#4  0x00007fd4181ff6d8 in spatial_ref_sys_init2 (handle=handle@entry=0x563da7c49248, mode=mode@entry=-9999, verbose=verbose@entry=0)
    at srs_init.c:750
#5  0x00007fd412f61bb4 in fnct_InitSpatialMetaData (context=0x563da7c88008, argc=<optimized out>, argv=<optimized out>)
    at spatialite.c:2016
#6  0x0000563da55f512f in ?? ()
#7  0x0000563da55fef8f in ?? ()
#8  0x0000563da55ffe4d in ?? ()
#9  0x0000563da5572126 in ?? ()
#10 0x0000563da592e3dc in execute_ex ()
#11 0x0000563da592e7a4 in zend_execute ()
#12 0x0000563da587d8d3 in zend_execute_scripts ()
#13 0x0000563da5818a58 in php_execute_script ()
#14 0x0000563da5930b2f in ?? ()
#15 0x0000563da54df2d9 in ?? ()
#16 0x00007fd42a6f82e1 in __libc_start_main (main=0x563da54dee70, argc=2, argv=0x7ffc322975d8, init=<optimized out>, fini=<optimized out>, 
    rtld_fini=<optimized out>, stack_end=0x7ffc322975c8) at ../csu/libc-start.c:291
#17 0x0000563da54df3fa in _start ()
(gdb) 
```

This is the latest head revision of MapServer and SpatiaLite 4.3.  I have tried with MapServer 7.4 as well.  See https://www.gaia-gis.it/fossil/libspatialite/tktview/f65f05b71a2f8ca15ca6a2316d0e53db77ffbe70 .
