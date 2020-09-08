<?php

$db = new SQLite3('test.db');

$db->loadExtension('mod_spatialite.so');

$db->exec('SELECT InitSpatialMetadata()');
