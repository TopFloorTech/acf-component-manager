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

