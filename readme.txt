=== SpinupWP ===
Contributors: spinupwp
Tags: cache, caching, performance
Requires at least: 4.7
Tested up to: 6.4
Requires PHP: 7.1
Stable tag: 1.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

SpinupWP is a modern server control panel that's here to help you implement best practices for every server you spin up. Designed for WordPress.

== Description ==

**This plugin ensures that the SpinupWP page cache is cleared when your site's content changes. Not using SpinupWP yet? [Sign up here.](https://spinupwp.com/pricing/)**

SpinupWP is a modern server control panel that's here to help you implement best practices for every server you spin up. Designed for WordPress.

This companion plugin should be installed on sites created using SpinupWP to allow the page cache to be cleared when your site's content changes. Not using SpinupWP yet? [Sign up here.](https://spinupwp.com/pricing/)

**Any Provider**

We support DigitalOcean, Linode, AWS, and any other provider. If your server has an IP address, you can connect SpinupWP. It does need to be a fresh install of Ubuntu though.

**Latest & Greatest Software**

SpinupWP will install the latest stable versions of Nginx, PHP, MySQL/MariaDB, and Redis from the standard apt-get repos. No who-knows-what-they-did custom builds of packages. Disconnect from SpinupWP in the future and you can still keep your packages up-to-date with apt-get upgrade.

**Automatic Security Updates**

SpinupWP will configure your server to install security updates as soon as they are available to reduce the likelihood of a software vulnerability putting your server at risk.

**Free SSL/TLS Certificates**

Serving your site over HTTPS is essential these days, not only for security, but to take advantage of the performance improvements of HTTP/2 as well. When you add a site to SpinupWP, a free Let’s Encrypt SSL/TLS certificate will be acquired, installed, and configured for your site. And SpinupWP will handle certificate renewals as well, so you hardly need to think about certificates.

**Cache All the Things**

One of the keys to a great performing WordPress site is caching. All sites are set up with Redis object caching to greatly reduce database requests. And with the check of a box you can enable full-page caching to serve pages lightning fast without even hitting PHP.

**Git Push-to-Deploy**

Developers! Developers! Developers! Add a git repository to your SpinupWP site and simply push to master to deploy your code. GitHub, Bitbucket, or a custom git repo will work. You can also configure a build script to run some tasks on the server after deployment is complete.

**Error Logs**

WordPress doesn’t enable error logging by default. Probably because the log is saved to a publicly accessible directory and can quickly balloon to take up a lot of hard drive space. SpinupWP enables error logs by default but stores them in a safe place and makes sure they’re rotated regularly like other server logs.

**Security Security Security**

Each server provisioned by SpinupWP is security-hardened from the word go. SSH login is disabled for the root user (you login with your user and use sudo instead). The firewall only allows connections to Nginx and SSH and failed attempts are monitored and blocked when the reach a threshold. Nginx is configured to defend against XSS, clickjacking, MIME sniffing, and other attacks. Software security updates are installed automatically.

**Scheduled Posts Published on Schedule**

For every site you add via SpinupWP, a server-side cron job will be configured to make sure that your WordPress site’s cron is executed every minute, as it should be.

**WP-CLI Preloaded**

If you love WP-CLI (we do! ❤) you’ll be very pleased to find it available on the command line the first time you login to your server.

**Security Isolation for Sites**

For each site that you add to your server via SpinupWP, a new system user is created for that site. All site files are owned by the site user and a PHP-FPM pool is configured to run as that user as well. Each site only has access to its files and so if only one site has a security vulnerability and gets infected with malware for example, only the files for that one site can be infected.

**SFTP Access for Your Clients**

If you’re hosting a site for someone else, you can easily give them SFTP/SSH access to just that site. And because of the security isolation between sites, they will only have access to files for that site.

**Professional Guidance & Best Practices**

SpinupWP will actively point you in the right direction and offer suggestions for maintaining your server. And because it provides detailed feedback about the operations it runs on your server, you can learn what is happening with your server. New release of Ubuntu just came out, should I upgrade? We’ll add a notice to the app about that, why we don’t recommend upgrading your existing servers, and how you can spin up a new server with the new release of Ubuntu and migrate your sites to that server instead. Should I install Varnish to improve page caching performance? We’ve benchmarked Varnish and Nginx FastCGI Cache performed better. Varnish would add complexity too, so one less moving part is another reason. Much of the time SpinupWP will suggest things that you may not have even thought of. Email deliverability for example. SpinupWP will strongly encourage you to configure an email sending plugin for the best email deliverability. SpinupWP’s guidance is especially helpful for those new to managing a server, but can also help those who’ve been at it a while, providing transparency to our decisions.

**Scheduled Backups of Site Files & Database**

All server providers (DigitalOcean, Linode, etc) offer automated backups of your entire server for a fee. These services are great and we highly recommend having backups of your whole server. But what happens if some media or data was deleted by accident from your WordPress site? You’re not going to restore your entire server just to get that data back. That’s where site backups come in. Site backups are full backups of your site files (media, themes, and plugins) and database. They allow you to easily restore a single site or just some files or data from a single site. With SpinupWP’s site backups, you choose your preferred provider to stash your backups whether that’s Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. You plug in your account details and SpinupWP will send your site backups there in an easy-to-see format.

**Teamwork Makes the Dream Work**

Create a new team account, invite a member of your team, and allow them to spin up their own servers. Or just only allow them to add sites, the permissions you give them is up to you.

= Features =

* Page cache purging
* Persistent object caching
* Ensures debug.log files aren’t saved in a publicly-accessible location

== Changelog ==

= 1.6 (2024-01-31) =
* New: Add "Purge this URL" option to our Cache menu in the WordPress nav bar.
* New: Cache key customization. /props quimcastella
* Improvement: Increase default cache purge timeout limit from 1 to 5 seconds.
* Improvement: PHP 8.1 compatibility. /props afragen
* Bug Fix: Page cache not cleared when clearing object cache fails.

= 1.5.1 (2022-11-05) =
* Ensure SpinupWP page caching is correctly detected in Site Health

= 1.5 (2022-08-23) =
* Purge the page cache on core, plugin, and theme update
* Add `wp_cache_*_multiple` functions to object-cache.php
* Fix "PHP Deprecated: trim(): Passing null to parameter" on PHP 8.1

= 1.4.2 (2022-06-13) =
* Plugin author updated

= 1.4.1 (2022-02-09) =
* Don't overwrite Object Cache Pro object-cache.php drop-in

= 1.4 (2022-02-02) =
* PHP 8.1 compatibility fixes

= 1.3 (2021-01-06) =
* Added compatibility with Elementor Website Builder.
* Show the "Cache" Admin Bar menu item on mobile devices.

= 1.2 (2020-08-17) =
* Added support for WP 5.5 `wp_cache_get_multi()` function.
* Changed Admin Bar menu title from "SpinupWP" to "Cache"

= 1.1.2 (2019-07-26) =
* Only purge the page cache when a public post type is updated.
* Fix "Trying to get property 'comment_post_ID' of non-object" error on post delete.

= 1.1.1 (2019-07-24) =
* Fix object-cache.php file deletion on Redis Object Cache plugin uninstall.

= 1.1 (2019-07-09) =
* `wp spinupwp status` and `wp spinupwp update-object-cache-dropin` WP-CLI commands added.
* WP-CLI cache related commands moved to new `cache` subcommand, e.g. `wp spinupwp cache purge-site`.
* Don't report "Your site is set to log errors to a potentially public file" issue in site health tool.
* Automatically update object-cache.php drop-in when a new version is available.
* Adhere to WordPress coding standards.

= 1.0.3 (2019-07-08) =
* Fix "The plugin does not have a valid header" error.
* Deprecate `WP_CACHE_KEY_SALT` and `WP_REDIS_SELECTIVE_FLUSH` constants.

= 1.0.2 (2019-06-20) =
* Fix missing assets directory.

= 1.0.1 (2019-05-22) =
* Ensure cache purge functionality is available on legacy SpinupWP sites.

= 1.0 (2019-04-22) =
* Initial release.
