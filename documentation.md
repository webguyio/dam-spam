# Dam Spam Documentation

Dam Spam is a comprehensive spam protection plugin for WordPress that blocks spam registrations, login attempts, comments, and contact form submissions through multiple layers of protection.

## How Dam Spam Works

Dam Spam runs a series of configurable checks whenever someone tries to register, log in, comment, or submit a form on your WordPress site. Each submission goes through two main stages:

1. **Allow Checks** - First, Dam Spam checks if the user should be automatically allowed (trusted IPs, payment processors, Google crawlers, etc.)
2. **Block Checks** - If not on the allow list, Dam Spam runs various spam detection checks (disposable emails, known spam IPs, suspicious behavior, etc.)

When a submission is flagged as suspicious, you can either block it outright or present a CAPTCHA challenge. Legitimate users are cached to speed up future submissions, while known spam sources are permanently blocked.

---

## Summary

The Summary page displays statistics about blocked spam and allows you to clear your statistics.

---

### Clear Summary

Reset all spam blocking statistics to zero.

> **Why Use:** Useful for starting fresh after testing or making significant configuration changes.

> **Why Not:** None. This only clears statistics and doesn't affect your protection settings or lists.

---

## Protections

The Protections page is where you enable or disable specific spam detection methods.

---

### Form Checking

#### Only Check Native WordPress Forms

Bypass spam checks for third-party form plugins (Contact Form 7, Gravity Forms, WooCommerce, etc.).

> **Why Use:** Prevents conflicts with ecommerce checkout processes or complex forms that have their own validation.

> **Why Not:** WooCommerce users experiencing checkout issues should enable this. May reduce spam protection on third-party forms.

---

#### Skip Payment Forms

Skip spam checking on payment processor forms (PayPal, Stripe, Square, etc.).

> **Why Use:** Prevents blocking legitimate payment transactions.

> **Why Not:** Reduces spam protection on payment forms. Most payment processors have their own fraud protection.

---

#### Skip WooCommerce Forms

Skip spam checking specifically on WooCommerce checkout and registration forms.

> **Why Use:** Prevents checkout failures and order processing issues.

> **Why Not:** May allow spam orders or fake customer accounts. Only enable if experiencing issues.

---

#### Skip Gravity Forms

Skip spam checking on Gravity Forms submissions.

> **Why Use:** Prevents conflicts with Gravity Forms' built-in spam protection.

> **Why Not:** May reduce spam protection on Gravity Forms. Only enable if experiencing form submission issues.

---

#### Skip WP Forms

Skip spam checking on WP Forms submissions.

> **Why Use:** Prevents conflicts with WP Forms' built-in spam protection.

> **Why Not:** May reduce spam protection on WP Forms. Only enable if experiencing form submission issues.

---

### Private Mode

#### Users Must Be Logged in to View Site

Require authentication to view any part of your site.

> **Why Use:** Creates a private members-only site or protects sites during development.

> **Why Not:** Makes your entire site inaccessible to the public, including search engines. Only use for private communities or development sites.

---

### Prevent Lockouts

#### Automatically Add Admins to Allow List

Automatically add administrator IP addresses to the allow list upon login.

> **Why Use:** Ensures you won't accidentally lock yourself out when testing strict settings.

> **Why Not:** If an attacker gains admin access, their IP will be whitelisted. Best for single-admin sites or trusted team environments.

---

#### Check Credentials on All Login Attempts

Verify username and password exist before running other spam checks.

> **Why Use:** Prevents logging failed spam attempts with non-existent credentials.

> **Why Not:** May interfere with 2FA plugins or custom authentication systems. Disable if experiencing 2FA failures.

---

### Validate Requests

#### Block Missing HTTP_ACCEPT Header

Block requests that don't include an HTTP_ACCEPT header.

> **Why Use:** Most legitimate browsers send this header; bots often don't.

> **Why Not:** May block some legitimate API requests, mobile apps, or older browsers. Monitor logs after enabling.

---

#### Block Invalid HTTP_REFERER

Block requests with suspicious or invalid referrer headers.

> **Why Use:** Catches bots that spoof or omit proper referrer information.

> **Why Not:** May block users with referrer-blocking browser extensions or privacy tools. Check logs for false positives.

---

#### Block Disposable Email Addresses

Block registration attempts using temporary/disposable email services.

> **Why Use:** Prevents spam accounts using throwaway email addresses.

> **Why Not:** May block legitimate users who prefer privacy-focused temporary email services. Rare false positives with lesser-known email providers.

---

#### Check for Long Emails, Usernames, and Passwords

Flag suspiciously long email addresses, usernames, or passwords.

> **Why Use:** Spammers often use extremely long strings to try to break validation.

> **Why Not:** May block legitimate users with very long email addresses (rare). Adjust length thresholds if needed.

---

#### Check for Short Emails and Usernames

Flag unusually short email addresses or usernames.

> **Why Use:** Catches bots using minimal character combinations.

> **Why Not:** May block users with legitimate short usernames. Consider the minimum length that makes sense for your site.

---

#### Check for BBCode

Detect BBCode markup in submissions.

> **Why Use:** BBCode in registration forms or comments often indicates spam.

> **Why Not:** Will block submissions from users who use BBCode if your site supports it (like forums). Disable for BBCode-enabled sites.

---

#### Check for Periods

Flag email addresses with unusual period patterns.

> **Why Use:** Spammers often use multiple periods or period patterns that real users don't.

> **Why Not:** May block some legitimate international email addresses with unusual formatting. Monitor logs.

---

#### Check for Hyphens

Flag excessive hyphens in usernames or emails.

> **Why Use:** Multiple hyphens are common in spam patterns.

> **Why Not:** May block legitimate users with hyphenated names. Adjust threshold if needed.

---

#### Check for Quick Responses

Block form submissions that happen too quickly after page load.

> **Why Use:** Humans need time to read and fill forms; bots submit instantly.

> **Why Not:** May block users with form auto-fill tools or very fast typists. May not work with caching plugins. Adjust timeout value if getting false positives.

---

#### Block 404 Exploit Probing

Block IPs that hit multiple non-existent URLs in a short time.

> **Why Use:** Attackers scan for vulnerable files by trying many URLs; this detects that behavior.

> **Why Not:** May block legitimate users if they repeatedly mistype URLs. Rare false positives.

---

#### Block IPs Detected by Akismet

Block IPs that Akismet has flagged as spam.

> **Why Use:** Leverages Akismet's spam database for additional protection.

> **Why Not:** Requires Akismet to be installed and configured. May block users who were falsely flagged by Akismet.

---

#### Check for Exploits

Scan submissions for common exploit patterns (SQL injection, XSS, etc.).

> **Why Use:** Blocks malicious code injection attempts.

> **Why Not:** Very rare false positives with legitimate technical content. May flag code examples in comments.

---

#### Block Login Attempts for "admin" Username

Block all login attempts using "admin" as the username.

> **Why Use:** "admin" is the most commonly attacked username; blocking it stops many automated attacks.

> **Why Not:** If you actually have an admin user named "admin", you'll need to create a new admin account with a different username first.

---

#### Check Ubiquity-Nobis and Other Blacklists

Check against Ubiquity server networks and other hosting provider blocklists.

> **Why Use:** These networks host many spam operations.

> **Why Not:** May block legitimate users on shared hosting from these providers. Rare false positives.

---

#### Check for Major Hosting Companies and Cloud Services

Flag traffic from major hosting providers and cloud services.

> **Why Use:** Spammers often use hosting/cloud IPs rather than residential connections.

> **Why Not:** May block legitimate users who browse from their work environment if their company uses these services. May block legitimate API requests.

---

#### Check for VPNs

Detect and block VPN connections.

> **Why Use:** Spammers and attackers commonly use VPNs to hide their real location.

> **Why Not:** Will block legitimate users who use VPNs for privacy. Not recommended for privacy-conscious audiences.

---

#### Check for Tor

Detect and block Tor exit nodes.

> **Why Use:** Tor is often used by spammers and attackers to hide their identity.

> **Why Not:** Blocks legitimate users who use Tor for privacy. Only enable if Tor users aren't part of your target audience.

---

#### Check for Many Hits in a Short Time

Block IPs that make multiple requests in a short time period.

> **Why Use:** Catches bots that rapidly attempt multiple registrations or submissions.

> **Why Not:** May block legitimate users who refresh pages frequently or submit forms multiple times due to errors.

---

#### Check for Amazon Cloud

Flag traffic from Amazon AWS IP ranges.

> **Why Use:** AWS hosting is popular with spam operations.

> **Why Not:** May block legitimate services hosted on AWS. Many legitimate sites and services use AWS.

---

#### Filter Login Requests

Apply spam filtering to login attempts.

> **Why Use:** Protects login page from automated attacks.

> **Why Not:** May interfere with programmatic login systems or mobile apps.

---

### Block Countries

#### Block Countries via Cloudflare

Use Cloudflare's firewall to block entire countries.

> **Why Use:** Blocks traffic from countries where you have no legitimate users but receive lots of spam.

> **Why Not:** Requires Cloudflare account and API configuration. Blocks all users from selected countries, including legitimate ones.

---

## Allowed

Manage IP addresses, email addresses, and user IDs that should always be allowed through spam protection.

---

### Requests

View and manage requests from blocked users who asked to be added to your allow list.

> **Why Use:** Legitimate users who were blocked can request access, and you can review and approve them.

> **Why Not:** None. This is a manual review process.

---

### Allow List

Manually add IP addresses that should bypass all spam checks.

> **Why Use:** Whitelist your own IP, office IPs, or specific trusted users.

> **Why Not:** Be careful not to whitelist attacker IPs. These IPs will bypass all protection.

---

### Allow Options

#### Google

Automatically allow verified Google crawlers (keep enabled under most circumstances).

> **Why Use:** Ensures search engines can index your site for SEO.

> **Why Not:** None. Essential for SEO unless you intentionally want to block search engines.

---

#### Other Allow Lists

Allow traffic from miscellaneous trusted sources and services.

> **Why Use:** Provides additional allow list coverage for legitimate services.

> **Why Not:** None. Works in conjunction with other allow list checks.

---

#### Allow PayPal

Automatically allow PayPal's IPs for payment notifications and webhooks.

> **Why Use:** Prevents blocking legitimate payment transactions and notifications.

> **Why Not:** None if you use PayPal. Disable if you don't use PayPal.

---

#### Allow Stripe

Automatically allow Stripe's IPs for payment processing.

> **Why Use:** Ensures Stripe webhooks and payment verifications work.

> **Why Not:** None if you use Stripe. Disable if you don't use Stripe.

---

#### Allow Authorize.Net

Automatically allow Authorize.Net's IPs for payment processing.

> **Why Use:** Ensures payment gateway communication isn't blocked.

> **Why Not:** None if you use Authorize.Net. Disable if you don't.

---

#### Allow Braintree

Automatically allow Braintree's IPs for payment processing.

> **Why Use:** Prevents blocking Braintree webhooks and notifications.

> **Why Not:** None if you use Braintree. Disable if you don't.

---

#### Allow Recurly

Automatically allow Recurly's IPs for subscription billing.

> **Why Use:** Ensures recurring billing webhooks work properly.

> **Why Not:** None if you use Recurly. Disable if you don't.

---

#### Allow Square

Automatically allow Square's IPs for payment processing.

> **Why Use:** Prevents blocking Square transactions and webhooks.

> **Why Not:** None if you use Square. Disable if you don't.

---

#### Allow Amazon Cloud

Automatically allow specific Amazon AWS services and IPs.

> **Why Use:** Prevents blocking legitimate AWS-hosted services and APIs your site depends on.

> **Why Not:** None if you use AWS services. Works in conjunction with "Check for Amazon Cloud" in Protections to selectively allow trusted AWS traffic.

---

## Blocked

Manage IP addresses, email addresses, and patterns that should always be blocked.

---

### Block List

Add specific IP addresses or email addresses to permanently block.

> **Why Use:** Manually block persistent spammers that automated checks don't catch.

> **Why Not:** Blocking entire email domains may block legitimate users from those domains. Be specific.

---

### Spam Words List

Manage the list of words and phrases that trigger spam blocks.

> **Why Use:** Block submissions containing specific spam keywords.

> **Why Not:** May block legitimate discussion of blocked topics. Review and customize your spam word list carefully.

---

### URL Shortening Services List

Manage which URL shortening services are blocked.

> **Why Use:** Many spammers use URL shorteners to hide malicious links.

> **Why Not:** May block legitimate users sharing shortened links. Consider which services to block carefully.

---

### Check for URLs

Configure how many URLs are allowed in submissions before blocking.

> **Why Use:** Limit URL spam without blocking all links.

> **Why Not:** May block legitimate posts with multiple helpful links. Adjust threshold as needed.

---

### Bad User Agents List

Block specific browser user agents known to be used by bots.

> **Why Use:** Catches bots identifying themselves with known spam user agents.

> **Why Not:** May block legitimate browsers if user agents are too broad. Monitor for false positives.

---

### Blocked TLDs

Block email addresses from specific top-level domains.

> **Why Use:** Some TLDs are predominantly used for spam.

> **Why Not:** May block legitimate users from those countries/domains. Be careful with country TLDs.

---

## Challenges

Configure how Dam Spam responds when suspicious activity is detected.

---

### Access Blocked Message

Customize the message shown to blocked users.

> **Why Use:** Provide helpful information or contact details for blocked users.

> **Why Not:** None. This is just the message text.

---

### Routing and Notifications

#### Send Blocked Users to URL

Redirect blocked users to a specific URL instead of showing the default block message.

> **Why Use:** Direct users to a custom page with contact information or alternative access instructions.

> **Why Not:** Users won't see the standard block message. Ensure your redirect URL is accessible.

---

#### Send Blocked Users to Allow Request Form

Show blocked users a form where they can request to be added to your allow list.

> **Why Use:** Gives legitimate users who were blocked a way to request access with their contact details.

> **Why Not:** You'll need to review and manually approve requests. Spammers may submit fake requests.

---

#### Email Admin for New Requests

Receive email notifications when blocked users submit allow list requests.

> **Why Use:** Stay informed about access requests without checking the dashboard regularly.

> **Why Not:** Can generate many emails if you block many users. Emails may be marked as spam by your mail provider.

---

#### Email Blocked Users when They're Allowed

Automatically notify users via email when you approve their allow list request.

> **Why Use:** Provides good user experience by informing approved users they can now access your site.

> **Why Not:** Outgoing emails may be marked as spam. Ensure your server's email is properly configured.

---

### CAPTCHA

Choose which CAPTCHA system to present to suspicious users instead of blocking them outright.

---

#### CAPTCHA Type Options

- **No CAPTCHA (default)** - Block immediately without second chance
- **Cloudflare Turnstile** - Cloudflare's privacy-friendly CAPTCHA
- **Google reCAPTCHA** - Google's CAPTCHA system
- **hCaptcha** - Privacy-focused alternative to reCAPTCHA
- **Math Question** - Simple arithmetic challenge

> **Why Use:** Reduces false positives by giving suspicious users a chance to prove they're human.

> **Why Not:** Requires API keys for Turnstile, reCAPTCHA, or hCaptcha. Adds friction to user experience.

---

#### Enable CAPTCHAs on Forms

After selecting a CAPTCHA type, you can enable it on specific WordPress forms:

**Login** - Show CAPTCHA on the WordPress login page

**Registration** - Display CAPTCHA on the registration form

**Comment** - Display CAPTCHA on the comment form

> **Why Use:** Protects specific forms from automated attacks.

> **Why Not:** Adds extra steps for all users on those forms. May reduce engagement.

---

#### API Keys

**Cloudflare Turnstile** ([Get Keys](https://dash.cloudflare.com/?to=/:account/turnstile))

**Google reCAPTCHA** ([Get Keys](https://www.google.com/recaptcha/admin/create))

**hCaptcha** ([Get Keys](https://dashboard.hcaptcha.com/sites))

> **Why Use:** Required to enable Turnstile, reCAPTCHA, or hCaptcha protection.

> **Why Not:** Keep keys secure. Don't share them publicly or commit to version control.

---

## APIs

Configure third-party spam detection services and API keys.

---

### Cloudflare Integration

Configure Cloudflare API access for advanced features like country blocking, ban list syncing, and cache management.

**Required Fields:**
- **Cloudflare Email** - Your Cloudflare account email address
- **Cloudflare Global API Key** - Found in your Cloudflare profile under API Tokens
- **Cloudflare Zone ID** - Found in your domain's Cloudflare dashboard overview

**Actions:**
- **Clear Cloudflare Cache** - Purge Cloudflare's cache for your site (requires API configuration)

> **Why Use:** Enables Cloudflare-specific features including DNS-level ban list blocking, country blocking, and cache clearing.

> **Why Not:** Requires Cloudflare account. Keep API keys secure. Global API Key has full account access.

---

### Blacklist Checking

#### Check DNSBLs (like Spamhaus.org)

Query DNS-based blacklists to check if IPs are known spam sources.

> **Why Use:** Leverages Spamhaus and other DNSBLs maintained by anti-spam organizations.

> **Why Not:** Requires DNS queries which may add slight latency. Some false positives possible.

---

#### Check Stop Forum Spam

Query the Stop Forum Spam database for known spammer IPs, emails, and usernames.

> **Why Use:** Accesses one of the largest spam databases on the internet.

> **Why Not:** Requires API access. Free tier has query limits.

---

#### API Keys and Thresholds

**StopForumSpam.com API Key** (optional - only needed to report spam)
- Frequency threshold: Block spammers with more than X incidents
- Age threshold: Only block if seen within X days

**Project Honeypot API Key**
- Threat level threshold: Block threats above X level (25 is average, 5 is low)
- Age threshold: Only block if seen within X days

**BotScout API Key**
- Frequency threshold: Block spammers with more than X incidents

**Google Safe Browsing API Key**
- Checks URLs in submissions against Google's malware and phishing database

> **Why Use:** Provides configurable spam detection from multiple trusted databases.

> **Why Not:** Requires API key registration. Some services have daily query limits. May add latency to checks.

---

## Cache

Configure how Dam Spam caches known good and bad IPs.

---

### Bad Cache Size

Configure how many days to cache known bad IPs before removing them from cache.

> **Why Use:** Keeps known spammers blocked without repeated API checks. Longer duration = better performance.

> **Why Not:** Very long cache times may keep reformed or reassigned IPs blocked longer than necessary.

---

### Good Cache Size

Configure how many days to cache known good IPs before rechecking them.

> **Why Use:** Dramatically improves performance by skipping checks for trusted IPs. Longer duration = better performance but slower to detect compromised IPs.

> **Why Not:** Very long cache times may keep compromised IPs whitelisted longer.

---

### Clear Cache

Remove all cached IP addresses.

> **Why Use:** Forces fresh checks on all IPs. Useful after major configuration changes.

> **Why Not:** Temporarily increases API usage and reduces performance while cache rebuilds.

---

## Logs

View and manage logs of blocked and allowed requests.

---

### Update Log Size

Configure how many log entries to retain (options: 25, 50, 100, 200, 500, 1000).

> **Why Use:** Balance database size with historical data retention. Smaller logs = less database space.

> **Why Not:** Changing log size will wipe current logs. Export logs first if you need to keep them.

---

### Search Logs

Search and filter through all blocked and allowed attempts by date, IP, email, or reason. The search box filters results in real-time as you type.

> **Why Use:** Quickly find specific blocks to identify false positives or verify spam patterns.

> **Why Not:** None. This is a search/filter tool for viewing existing logs.

---

### Clear Logs

Delete all log entries.

> **Why Use:** Free up database space or start fresh after testing.

> **Why Not:** Permanently deletes historical data.

---

## Testing

Test your spam protection settings against sample data and view diagnostic information.

---

### Option Testing

Test how your current settings would handle specific IP addresses, emails, usernames, subjects, and comments. Enter test data in the form fields and click "Test Options" to see which checks would trigger.

**Test Fields:**
- IP Address
- Email
- Username
- Subject
- Comment

> **Why Use:** Verify settings work as expected before going live. Test if specific IPs or emails would be blocked or allowed. Debug false positives.

> **Why Not:** None. This is a testing tool that doesn't affect live traffic.

---

### Display All Options

Dump all Dam Spam configuration options in a readable format.

> **Why Use:** Useful for debugging, support requests, or backing up settings manually.

> **Why Not:** None. Displays settings only without modifying anything.

---

### Display All Stats

Dump all Dam Spam statistics in a readable format.

> **Why Use:** View all counter values for every check type and protection method.

> **Why Not:** None. Read-only display of statistics.

---

### Show PHP Info

Toggle display of PHP configuration information from phpinfo().

> **Why Use:** Debug PHP-related issues, verify server configuration, check installed extensions.

> **Why Not:** Contains sensitive server information. Only use when needed and don't share publicly.

---

### Threat Scan

Scan your WordPress installation for potential security threats and vulnerabilities.

> **Why Use:** Identify security issues that spam protection alone won't catch.

> **Why Not:** Scans can be resource-intensive. Run during low-traffic periods.

---

## Cleanup

Tools for managing users and database maintenance.

---

### Disable Users

Identify and disable inactive or suspicious user accounts.

> **Why Use:** Clean up spam accounts that got through or remove inactive users.

> **Why Not:** Disabling users prevents them from logging in. Be careful not to disable legitimate accounts.

---

### Delete Comments

Bulk delete spam comments.

> **Why Use:** Remove spam comments that made it through protection.

> **Why Not:** Deletion is permanent. Review before deleting to avoid removing legitimate comments.

---

### Database Cleanup

View and manage Dam Spam database options.

> **Why Use:** Advanced troubleshooting and optimization.

> **Why Not:** Modifying database options directly can break plugin functionality. For advanced users only.

---

## Advanced

Advanced features for server-level protection and custom login forms.

---

### Firewall Settings

#### Server-side Security Rules

Add security rules to your .htaccess file for server-level protection.

> **Why Use:** Blocks common exploits and attacks before they reach WordPress, reducing server load.

> **Why Not:** Incorrect .htaccess rules can break your site. Only enable if you know how to manually edit .htaccess. May conflict with other security plugins.

---

### Login Settings

#### Limit Login Attempts

Automatically ban IPs after a specified number of failed login attempts within a time window.

> **Why Use:** Protects against brute force password attacks.

> **Why Not:** May lock out legitimate users who forget their password. May conflict with other login protection plugins.

---

#### Require Email Verification for New Users

Users must click activation link in email before they can log in. Works with all registration forms.

> **Why Use:** Verifies email addresses are real, prevents spam bot registrations, automatically cleans up unverified accounts.

> **Why Not:** May conflict with plugins that have their own email verification (e.g., Ultimate Member Pro). Users must have working email to complete registration.

---

#### Auto-Delete Unverified Users After 7 Days

Automatically remove user accounts that haven't clicked their activation link within 7 days.

> **Why Use:** Prevents buildup of spam accounts with fake email addresses. Keeps user database clean.

> **Why Not:** Deletes legitimate users who didn't check their email in time (they can re-register).

---

#### Themed Login (disables wp-login.php)

Replace default WordPress login system with custom themed pages.

> **Why Use:** Provides prettier login/registration pages and disables the default wp-login.php (which attackers target).

> **Why Not:** Conflicts with custom login plugins. Disables wp-login.php which some services expect.

---

#### Login Type

Choose whether users can log in with username, email, or both.

> **Why Use:** Simplify login or increase security by restricting login method.

> **Why Not:** Changing this may confuse existing users. Username-only blocks users who only remember their email.

---

### Honeypot Settings

#### Contact Form 7 Honeypot

Add invisible honeypot field to Contact Form 7 forms.

> **Why Use:** Catches bots that fill all form fields including hidden ones.

> **Why Not:** Requires Contact Form 7 plugin installed.

---

#### bbPress Honeypot

Add honeypot protection to bbPress forums.

> **Why Use:** Protects forum registrations and posts.

> **Why Not:** Requires bbPress plugin installed.

---

#### Elementor Honeypot

Add honeypot to Elementor forms.

> **Why Use:** Protects Elementor form submissions.

> **Why Not:** Requires Elementor Pro plugin installed.

---

#### Divi Honeypot

Add honeypot to Divi forms.

> **Why Use:** Protects Divi form submissions.

> **Why Not:** Requires Divi theme or builder installed.

---

### Ban List Settings

**Important:** The Ban List is different from the Block List. The Block List prevents form submissions, while the Ban List blocks access to the entire website.

---

#### Manual Ban List

Add specific IP addresses, email addresses, or email domains to permanently ban from the entire website.

> **Why Use:** Completely block persistent attackers from accessing any part of your site, not just forms.

> **Why Not:** Bans entire site access. Be certain before adding IPs. Blocking email domains may affect legitimate users.

---

#### Automatic Ban List

View IP addresses that were automatically banned by various protections (like login attempt limits). The list auto-culls at 100,000 entries.

> **Why Use:** Review automated bans to ensure legitimate users weren't caught.

> **Why Not:** These are temporary automatic bans. May include false positives.

---

#### Copy to Cloudflare

Copy the ban list to Cloudflare's firewall for DNS-level blocking.

> **Why Use:** Blocks banned IPs before they even reach WordPress, reducing server load.

> **Why Not:** Requires Cloudflare account and API configuration. Firewall rules count against Cloudflare limits.

---

#### Clear All Automatic Bans

Remove all automatically-generated ban entries.

> **Why Use:** Clear false positives or start fresh after testing.

> **Why Not:** Removes all automatic bans including legitimate blocks. Manual bans are not affected.

---

### Shortcodes

Dam Spam provides several shortcodes for adding forms and user info to pages:

- `[dam-spam-contact-form]` - Display the Dam Spam contact form
- `[dam-spam-login]` - Display custom login form
- `[dam-spam-show-displayname-as]` - Show logged-in user's display name
- `[dam-spam-show-fullname-as]` - Show logged-in user's first and last name
- `[dam-spam-show-email-as]` - Show logged-in user's email address

> **Why Use:** Create custom page layouts with login forms and user information.

> **Why Not:** None. Shortcodes only display content on pages where you add them.

---

### Export Settings

Download your Dam Spam settings as a JSON file for backup or transfer to another site.

> **Why Use:** Back up your configuration or replicate settings across multiple sites.

> **Why Not:** None. This is a backup/export feature.

---

### Import Settings

Upload a Dam Spam settings JSON file to restore configuration.

> **Why Use:** Restore backed-up settings or apply settings from another site.

> **Why Not:** Overwrites current settings. Make sure to export current settings first if you want to keep them.

---

### Reset Settings

Reset all Dam Spam settings to defaults.

> **Why Use:** Start over with a clean configuration if settings become problematic.

> **Why Not:** Permanently deletes all custom settings. Export first if you want to preserve current configuration.

---

## Under the Hood

These features work automatically in the background or integrate with WordPress in ways that don't have dedicated settings pages.

---

### Registration IP Tracking

Dam Spam automatically tracks and stores the IP address when users register, displayed in a "Signup IP" column in *WordPress Admin > Users*.

> **Why Use:** Helps identify spam account patterns (multiple registrations from same IP) and verify suspicious users against spam databases.

> **Why Not:** None. IP tracking for spam prevention is a legitimate security measure.

---

### Account Locking

Dam Spam adds Lock and Unlock action links to the WordPress Users page, allowing you to quickly disable/enable user accounts.

**How to Use:**
1. Go to WordPress *Admin > Users*
2. Hover over a user row to see action links
3. Click "Lock account" to prevent login or "Unlock account" to restore access

> **Why Use:** Quickly disable suspicious accounts without deleting them. Useful for temporarily blocking users while investigating.

> **Why Not:** Locked users cannot log in. Make sure to communicate with legitimate users before locking their accounts.

---

### Comment Spam Actions

Dam Spam adds quick-action icons to the WordPress Comments page for checking IPs against WHOIS, Stop Forum Spam, Project Honeypot, BotScout, and reporting spam.

> **Why Use:** Quickly verify suspicious comments without leaving WordPress admin.

> **Why Not:** None. These are optional quick-action links.

---

### Dashboard Widget

Dam Spam adds a dashboard widget showing total spammers blocked, users awaiting allow list approval, and a settings link.

> **Why Use:** At-a-glance view of Dam Spam activity.

> **Why Not:** None. Only visible to administrators.

---

### Action Hooks for Developers

Dam Spam fires two WordPress action hooks:

- `dam_spam_caught` - Fires when spam is blocked (passes `$ip` and `$reason`)
- `dam_spam_ok` - Fires when submission passes all checks (passes `$ip` and `$post_data`)

> **Why Use:** Integrate Dam Spam events with logging services, analytics, Slack alerts, or custom workflows.

> **Why Not:** Requires custom code. For developers only.

---

### Add-on Integration System

Dam Spam supports custom spam detection through add-on plugins that hook into two filters:

- `dam_spam_addons_block` - Runs during block checking phase
- `dam_spam_addons_allow` - Runs during allow list checking phase

Example:
```php
add_filter( 'dam_spam_addons_block', function( $addons ) {
	$addons[] = array( __FILE__, 'My_Spam_Check' );
	return $addons;
} );

class My_Spam_Check {
	public function process( $ip, &$stats, &$options, &$post ) {
		// Return false to continue, or string to block with reason
		if ( $ip === '123.45.67.89' ) {
			return 'Blocked by custom check';
		}
		return false;
	}
}
```

> **Why Use:** Extend Dam Spam with custom business logic, third-party APIs, or site-specific spam patterns without modifying core files.

> **Why Not:** Requires creating a separate plugin. For developers only.