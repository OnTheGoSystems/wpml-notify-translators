# WPML Notify Translators

## Disclaimer

Users may ask for a feature that we won't or can't add to our plugins for several reasons (low demand, or higher priorities).

Sometimes, we create feature plugins that address one or more of these features.

However, until they are officially merged into our main plugins, OnTheGoSystem is unable to ensure support for such plugins.

On the other hand, these plugins are public and everyone is welcome to contribute.

## Feature

This plugins, once enabled, will send a notification every time the original version of a translated post is updated.

The notification is set to all the translators that meet the language pair.

For instance, if a post in English is translated into French and Dutch, when updating the original post, a notifications will be set to all those translators that can translated from English to Franch and from English to Dutch.

## Installation

Just copy the whole project in the "plugins" directory of your WordPress site, then activate the plugin from the Plugins page of your website.

## Configuration

In the current iteration, the plugin has no customizable settings.

However, you may leverage the WordPress and WPML API (actions and filters) to modify the behavior.

In addition, this plugin provides three filters:

- `wpml_notify_on_post_update_send_email_subject`
- `wpml_notify_on_post_update_send_email_body`
- `wpml_notify_on_post_update_send_email_recipient`

All these filters accept the value mentioned at the end of their name (subject, body, recipient) as a first parameter (the actual value to filter).

In addition, they all receive two optional parameters:

- `\WP_Post $post`: The post that has been updated. 
- `\WP_User $recipient`: The recipient (as an instance of `\WP_User`) of the email.