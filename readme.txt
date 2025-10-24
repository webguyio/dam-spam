=== Dam Spam ===

Contributors: webguyio
Donate link: https://webguy.io/donate
Tags: spam, security, anti-spam, spam protection, no spam
Tested up to: 6.8
Stable tag: 0.3
License: GPL
License URI: https://www.gnu.org/licenses/gpl.html

Fork of Stop Spammers.

== Description ==

Fork of Stop Spammers.

== Installation ==

Go to *Plugins > Add New* from your WP admin menu, search for Dam Spam, install, and activate.

OR

1. Download the plugin and unzip it.
2. Upload the plugin folder to your wp-content/**plugins** folder.
3. Activate the plugin from the plugins page in the admin.

== Frequently Asked Questions ==

= What do I do if I lock myself out of my own site? =

You'll need to access your site via FTP, navigate to *wp-content/plugins* and rename the "dam-spam" folder to "1dam-spam" to disable it.

= Can I use Dam Spam with Cloudflare? =

Yes. But, you may need to restore visitor IPs: [https://support.cloudflare.com/hc/sections/200805497-Restoring-Visitor-IPs](https://support.cloudflare.com/hc/sections/200805497-Restoring-Visitor-IPs).

= Can I use Dam Spam with WooCommerce (and other ecommerce plugins)? =

Yes. But, in some configurations, you may need to go to *Dam Spam > Protection Options > Toggle on the option for "Only Check Native WordPress Forms" > Save* if you're running into any issues.

= Can I use Dam Spam with Akismet? =

Yes. Dam Spam can even check Akismet for an extra layer of protection.

= Can I use Dam Spam with Jetpack? =

Yes and no. You can use all Jetpack features except for Jetpack Protect, as it conflicts with Dam Spam.

= Can I use Dam Spam with Wordfence (and other spam and security plugins)? =

Yes. The two can compliment each other. However, if you have only a small amount of hosting resources (mainly memory) or aren't even allowing registration on your website, using both might be overkill.

= Why is 2FA failing? =

Toggle off the "Check Credentials on All Login Attempts" option and try again.

= Is Dam Spam GDPR-compliant? =

Yes. See: [https://law.stackexchange.com/questions/28603/how-to-satisfy-gdprs-consent-requirement-for-ip-logging](https://law.stackexchange.com/questions/28603/how-to-satisfy-gdprs-consent-requirement-for-ip-logging). Dam Spam does not collect any data for any other purpose (like marketing or tracking). It is purely for legitimate security purposes only. Additionally, if any of your users ever requested it, all data can be deleted.

== Changelog ==

= 0.3 =
* Bug fixes
* Security improvements
* Added automatic updates

= 0.2 =
* Fixes

= 0.1 =
* New fork of [Stop Spammers](https://wordpress.org/plugins/stop-spammer-registrations-plugin/)
