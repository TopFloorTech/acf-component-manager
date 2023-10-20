# ACF Component Manager

The goal of this plugin is to make your WordPress theme the single source of truth for your ACF-based components.

## The problem

Managing ACF component deployments can be tedious, exporting and moving JSON files between environments, and / or PHP files that are hard to change.

When moving JSON export files between environments, if these files are not committed to version control, it is easy for components to become stale, and questions may arise as to which environment has the most current version of the component definitions.  Additionally, it's challenging to rollback to earlier versions.

Using the PHP registration pattern (exporting the ACF component to PHP and loading from your theme), making changes to the components becomes more challenging, as you lose the ability to edit those fields in the UI.

## The solution

This plugin discovers components defined in the theme which contain ACF Field Group JSON files, and allows selectively enabling the Field Groups.

### Why not just use Local JSON

Behind the sceens, ACF Component Manager is leveraging ACF [Local JSON](https://www.advancedcustomfields.com/resources/local-json/).  Local JSON is a fantastic feature, but there are limitations.  

Primarily, it is complex to organize the JSON files into separate directories.  ACF Component Manager supports automatically loading components from dedicated directories, allowing for easier distribution and sharing of components across projects.         
## Requirements

1. Advanced Custom Fields.
2. A theme with a specific structure.

### Theme structure

The component manager expects a specific theme layout for discovering and loading ACF components.

theme-name
 - /components
   - /ComponentName
     - /assets
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
Where ComponentName is the component directory name.
## Usage
### Initial component setup.
1. In the local environment, create a field group.
2. In the theme, create the component directories.
3. Export your field group as JSON, placing the resultant file in the component assets directory.
4. In the Component Manager (Settings -> ACF Component Manager), visit the Manage Components tab, click "Edit components".
5. Locate the component, check "Enable" and save. 
6. Go back to ACF, edit the field group and click "Save".  ACF will add a "modified" key to the JSON file.
The component is now synced with ACF.

### Deployment
1. Commit and push.
2. In production, in ACF Component Manager, enable the component from the "Manage components" tab.

### Component changes
If you need to make changes to your component:
1. Pull the live database.
2. Visit ACF -> Field Groups -> Sync Available.
3. Select the component, choose "Sync changes" from the Bulk actions, and Apply.
Change will be automatically synced to the JSON file in the theme.
