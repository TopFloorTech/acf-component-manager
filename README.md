# ACF Component Manager

The goal of this plugin is to make handling ACF components slightly easier.

## The problem

Managing ACF component deployments can be tedious, exporting and moving JSON files between environments, and / or PHP files that are hard to change.

When moving JSON export files between environments, if these files are not committed to version control, it is easy for components to become stale, and questions arise as to which environment has the most current version of the component definitions.  Additionally, it's challenging to rollback to earlier versions.

When using the PHP registration pattern (exporting the ACF component to PHP and placing in a theme), it becomes difficult to make changes to the components.

## The solution

This plugin discovers components defined in the theme, and allows selecting from available versions of the ACF JSON files.

## Requirements

1. Advanced Custom Fields.
2. A theme with a specific structure.

### Theme structure

The component manager expects a specific theme layout for discovering and loading ACF components.

theme-name/
 - components/
   - ComponentName/
     - assets/
       - xxx.json
     - functions.php

Where xxx.json is a JSON export from ACF.

The functions.php must have a docblock like:
```
<?php
/**
 * Component: ComponentName
 */ 
```

## Usage

In the WordPress dashboard, visit Settings > ACF Component Manager.

On the "Manage components" tab to review components that are currently managed and newly discovered components.  

Click the "Edit components" button.

On the Edit form, select the components to Enable.  Enabled components will be loaded via ACF Component Manager.

**NOTE:** Components must be saved at least once for ACF Component Manager to manage them.  If you change themes, you'll need to edit the components.

The Edit form will display the discovered ACF JSON files.  If more than one file is discovered in the `/assets` directory, you can choose which file will be loaded.

This is an important feature, it allows for versioning the components, changing which component is loaded on the fly.

Example:
Let's say you have a component already in production, and you need to make some changes.
1. Uncheck Enabled.
2. Import the JSON file.
3. Make the changes.
4. Export the JSON file.
5. Place the JSON file in the component assets directory.
6. Commit, deploy to QA.
7. On QA, Edit components, select the new version.


