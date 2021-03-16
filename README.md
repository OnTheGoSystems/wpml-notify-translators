# WPML Notify Translators

## Disclaimer

Users may ask for a feature that we won't or can't add to our plugins for several reasons (low demand or higher priorities).

Sometimes, we create feature plugins that address one or more of these features.

However, OnTheGoSystem cannot ensure support for such plugins unless we decide to merge them into the core plugins.

On the other hand, these plugins are public, and everyone is welcome to contribute.

## Feature

This plugin, once enabled, will send a notification every time the original version of a translated post is updated.

All the translators that meet the language pair receive the notification.

Example:
A post in English has translations in French and Dutch.
When updating the original post, a notification is sent to all English to Franch and English to Dutch translators.

## Installation

Just copy the whole project in the "plugins" directory of your WordPress site, then activate the plugin from your website's Plugins page.

## Configuration

In the current iteration, the plugin has no customizable settings.

However, you may leverage the WordPress and WPML API (actions and filters) to modify the behaviour.

In addition, this plugin provides three filters:

- `wpml_notify_on_post_update_send_email_subject`
- `wpml_notify_on_post_update_send_email_body`
- `wpml_notify_on_post_update_send_email_recipient`

All these filters accept the value mentioned at the end of their name (subject, body, recipient) as a first parameter (the actual value to filter).

Also, they all receive two optional parameters:

- `\WP_Post $post`: The updated post.
- `\WP_User $recipient`: The recipient (as an instance of `\WP_User`) of the email.
