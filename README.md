# Newsletter Signup Form For Sharpspring

A simple WordPress plugin built for Sharpspring users. This plugin adds a popup to your single blog posts that when filled in will add the user as a lead in the Sharpspring system under the campaign of your choosing.

## Endpoint

**/relativemarketing/v1/newsletter/data**

Will return user defined options that are required for the display of the newsletter popup


## Frontend
React along with some minimal css is used on the frontend to display the popup.

**Note - Form will still show success message if the user is already a lead (even though this is technically returned as an error)**

## Options

Below are the options for the plugin which can be found under Settings > Relative Newsletter

### Sharpspring Options

**Campaign Id** The campaign to add the user to

### Message options

**Newsletter Heading**

**Newsletter paragraph**

The initial heading and paragraph shown to the user

**Error Heading**

**Error message**

The heading and message shown on error

**Success Heading**

**Success message**

The heading and message shown on successful signup

**Image @1x resolution**

**Image @2x resolution**

**Image @3x resolution**

Link to image that will be displayed alongside the heading, meesage and form at different resolutions

**Image alt**

The alternative text for the image

**Popup delay**

How long to wait before showing the popup


## TODO's

This list needs to be expanded but some initial thoughts are as follows

[] - Improvements to the options page 

[]	-- Improve image option

[] 	-- Give users a way to retrive campaign id in options page

--[] - When a user is already a lead but not assigned to the target campaign add them-- A user should only be added to a campaign once.


