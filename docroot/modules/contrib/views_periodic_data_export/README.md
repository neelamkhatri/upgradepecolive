CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

The Views Periodic Data Export module provides the following formats for views:

 * CSV (via csv_serialization module)

Drupal 8.x already provides XML and JSON formats for Views via the Rest module.

 * For a full description of the module visit:
   https://www.drupal.org/project/views_periodic_data_export

 * To submit bug reports and feature suggestions, or to track changes visit:
   https://www.drupal.org/project/issues/views_periodic_data_export


REQUIREMENTS
------------

This module requires the following outside of Drupal core.

 * CSV Serialization - https://www.drupal.org/project/csv_serialization
 * Views data export - https://www.drupal.org/project/views_data_export


INSTALLATION
------------

 * Install the Views Periodic Data Export module as you would normally install a
   contributed Drupal module. Visit https://www.drupal.org/node/1897420 for
   further information.


CONFIGURATION
-------------

    1. Navigate to Administration > Extend and enable the module.
    2. Navigate to Administration > Structure > Views and create a View Page
       that will display the information that you want to filter and export
       (You will go to the path of this page to generate the export once done).
       The View can be whatever display/format type that you need as it will be
       the interface that the user will filter by and then export data from.
    3. Add the fields that you want to display and include in the export, then
       add the exposed filters that you want to filter by in order to create
       custom exports (user role, status, pretty much any field you need).
    4. Add a new display and select Data export.
    5. From the Format field set, csv format settings. Apply.
    6. From the Pager field set, set to 'display all items' for the data export.
       Otherwise the results will be limited to the number per page in the pager
       settings.
    7. From the Path Settings field set, change the "Attach to" settings to
       "Master".
    8. Ensure that the 'path' for the data export is a file, with an extension
       matching the format. ie: /export/foo.csv. Otherwise, the file will be
       downloaded without a file association.
    9. Run cron to generate the report.


MAINTAINERS
-----------

 * Mohamed Gomma (mhmd) - https://www.drupal.org/u/mhmd
