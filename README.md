Panels everywhere for Drupal 8. Takes over the page.html.twig and places layout configurations in layouts.

## Requirements
Panels - https://www.drupal.org/project/panels
Page Manager - https://www.drupal.org/project/page_manager
Layout Plugin - https://www.drupal.org/project/layout_plugin
Ctools - https://www.drupal.org/project/ctools

## Install

Visit `/admin/modules` and install Panels Everywhere through the ui. This will turn on required modules.

Turning on "Page Manager UI" module will allow users to see the new site template.


## Usage

After the module is enabled, a new page will show on the page manager edit page that is disabled. `/admin/structure/page_manager`

Users can now edit that page and add "Main Page Content" to the layout, as well as other pieces of the site required for the site's page template.

## What is working

So far the site template gets taken over and elements can be moved to different regions through the dropdown in page manager. This is a work in progress so there will be bugs!
