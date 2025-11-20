=== Dam Spam ===

Contributors: webguyio
Donate link: https://webguy.io/donate
Tags: spam, security, anti-spam, spam protection, no spam
Tested up to: 6.8
Stable tag: 0.5
License: GPL
License URI: https://www.gnu.org/licenses/gpl.html

Comprehensive spam protection for WordPress registration, login, comments, and contact forms.

== Description ==

Dam Spam is a comprehensive spam protection plugin that blocks spam registrations, login attempts, comments, and contact form submissions. It provides multiple layers of protection including IP blocking, email validation, CAPTCHA challenges, and integration with third-party spam detection services.

**How it Works**

Dam Spam runs a series of configurable checks on registrations, logins, comments, and form submissions. When a submission is flagged as potentially suspicious, you can choose to block it outright or present a CAPTCHA challenge. Legitimate users are cached to speed up future submissions, while known spam sources are permanently blocked.

**Features**

* **Multiple Protection Layers** - Block spam using IP lists, disposable email detection, and behavioral analysis
* **CAPTCHA Support** - Integrate with Google reCAPTCHA or hCaptcha to challenge suspicious submissions
* **Third-Party API Integration** - Connect with Akismet, Stop Forum Spam, BotScout, and other spam detection services
* **Customizable Challenges** - Present challenges only to suspicious users while allowing legitimate users through
* **Allow and Block Lists** - Maintain custom lists of allowed and blocked IPs, emails, and user IDs
* **Smart Caching** - Cache known good and bad IPs to improve performance and reduce API calls
* **Comprehensive Logging** - Track all blocked attempts and approved submissions for review
* **Custom Login/Registration Forms** - Optional custom forms with built-in spam protection
* **User Management** - Identify and manage inactive or suspicious user accounts

**Configuration**

After installation, go to the Dam Spam settings in your WordPress admin to:

1. Enable the protection types you need (registration, login, comments, contact forms)
2. Choose which spam detection methods to use (IP checks, email validation, third-party APIs)
3. Configure CAPTCHA settings if desired
4. Set up allow and block lists for your specific needs
5. Review logs to fine-tune your protection settings

**Differences from Stop Spammers**

Dam Spam is a fork of the Stop Spammers plugin with ongoing maintenance, bug fixes, and security improvements. While the core spam protection functionality remains similar, Dam Spam's file and code structure has been significantly cleaned up and will continue to be further modernized.

== Installation ==

**Automatic**

1. Go to *Plugins > Add New* from your WordPress admin menu
2. Search for "Dam Spam"
3. Click "Install Now" and then "Activate"
4. Go to *Dam Spam > Protections* to configure your spam protection settings

**Manual**

1. Download the plugin ZIP file
2. Go to *Plugins > Add New > Upload Plugin*
3. Click "Choose File" and select the ZIP file
4. Click "Install Now"
5. Click "Activate"
6. Configure settings under *Dam Spam > Protections*

== Frequently Asked Questions ==

= I'm locked out of my admin! =

Don't panic. Access your site via FTP, navigate to `wp-content/plugins`, and rename the "dam-spam" folder to "1dam-spam" to disable it (remove the "1" to enable again). Then you can log in and adjust your settings.

= Can I use Dam Spam with Cloudflare? =

Yes. But you may need to [restore visitor IPs](https://developers.cloudflare.com/support/troubleshooting/restoring-visitor-ips/restoring-original-visitor-ips/).

= Can I use Dam Spam with Wordfence (and other security plugins)? =

Yes. They can even complement each other. However, if you have limited hosting resources or don't allow registration on your site, using both might be overkill.

= Can I use Dam Spam with WooCommerce (and other ecommerce plugins)? =

Yes. In some configurations, you may need to go to *Dam Spam > Protections* and toggle on "Only Check Native WordPress Forms" if you experience any issues.

= Can I use Dam Spam with Akismet? =

Yes. Dam Spam can check Akismet for an additional layer of protection.

= Can I use Dam Spam with Jetpack? =

Yes. You can use all Jetpack features except for Jetpack Protect, which conflicts with Dam Spam.

= Why is 2FA failing? =

Toggle off the "Check Credentials on All Login Attempts" option under *Dam Spam > Protections* and try again.

= Will Dam Spam slow down my site? =

Dam Spam uses smart caching to minimize performance impact. Known good IPs are cached and bypass most checks, while API calls are only made for suspicious submissions.

= How can I test my spam protection settings? =

Go to *Dam Spam > Testing* to run tests and see how your current settings would handle different types of submissions.

= Is Dam Spam GDPR-compliant? =

Yes. Dam Spam does not collect any data for marketing or tracking purposes. All IP logging is for legitimate security purposes only. All data can be deleted upon user request.

= What third-party services are used and what data is sent to them? =

There are several optional services you may use that involve sending data to third parties including: [Google reCAPTCHA](https://policies.google.com/privacy), [hCaptcha](https://www.hcaptcha.com/privacy), [Spamhaus](https://www.spamhaus.org/privacy-notice/), [Stop Forum Spam](https://www.stopforumspam.com/privacy), [Project Honeypot](https://www.projecthoneypot.org/privacy_policy.php), and [BotScout](https://botscout.com/w3c/privacy.htm). You may wish to read each services' privacy policy to see if you're comfortable using them, but generally speaking, whenever someone for example tries to use a contact form on your website, their IP address, name, and email may be sent to these services to check against spam blocklists.

== Changelog ==

= 0.5 =
* Removed country checks
* Removed dated SolveMedia CAPTCHA
* Improved form checking

= 0.4 =
* Bug fixes

= 0.3 =
* Bug fixes
* Security improvements

= 0.2 =
* Bug fixes

= 0.1 =
* New fork of [Stop Spammers](https://wordpress.org/plugins/stop-spammer-registrations-plugin/)