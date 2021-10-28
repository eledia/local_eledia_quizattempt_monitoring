# Quizattempt Monitoring

This plugin may be used to gain a quick overview of all quiz attempts within a specific quiz instance.
The page providing the overview may be accessed through the quiz administration menu.

The page provides an easy to use filter form and displays a list of attempts with relevant summarized
information. Filter settings are persistent throughout a user session, i.e. refreshing the page or 
navigating away from it and coming back to it will not mean you have to set your filters again.

The page provides the option to add quiz overrides to users whose attempts are being displayed. It offers
two override options:

* Setting the closing time of the quiz instance
* Setting the time limit available for completing attempts

You may do this by either using the specific link available for each attempt being displayed or
selecting multiple attempts and using the buttons below the list to add the same overrides to 
all of the selected attempts.  

Since this plugin is mostly meant to monitor quiz attempts in progress, only a subset of the
possible overrides may be added to users using the above options. Using these options will update
existing overrides and not change any options on existing overrides not being editable through
this plugin. **Beware though:** Deleting an override through this plugin will completely delete
it!

## Capabilities

The plugin defines two capabilities:

* local/eledia_quizattempt_monitoring:view (Required to display the monitoring page)
* local/eledia_quizattempt_monitoring:override (Required to add/update/delete overrides)

