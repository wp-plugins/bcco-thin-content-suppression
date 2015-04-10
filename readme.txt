=== BCCO Thin Content Suppression ===
Contributors: BCCO 
Donate link: https://thebc.co/thin-content-suppression/
Tags: SEO, thin content, thin content suppression, thin content penalty, search engine penalty, search engine penalty recovery
Requires at least: 3.0
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Find and suppress pages with thin content in seconds! Great for SEOs, webmasters, and web developers.

== Description ==

Find And Suppress Pages With Thin Content In Seconds!

Google’s Panda Algorithm looks for many low quality indicators. One of those being thin content. Unfortunately, established websites that do not generally break search engine guidelines fall victim to penalties.

We developed this plugin to help anyone that has an out of control thin content issue on their WordPress based website.

Who would best benefit from this WordPress plugin? A webmaster that has a large thin content issue and needs a way to suppress all of the thin pages on a website in a timely manner.

How To Use

1. Paste the custom tag we note in the settings of the plugin in the head section of your website.
2. Pick between NOINDEX, FOLLOW or NOINDEX, NOFOLLOW.
3. Filter pages by adding a word count. Any page that has less than that word count will populate in the results. Note: This plugin counts all of the words on a page, not just the words in the WordPress text editor.
4. Tick the box beside any pages that you want to exclude from the NOINDEX processing.
5. Click the Copy URLs button and paste the URLs into a document for your records. You can supply your team with a list of pages to add content to.
6. Process the pages. After the plugin processes, your specified NOINDEX tag will appear in the head section of ALL pages that were less than ______ words on a page (except for the pages you purposefully excluded).
7. Every time you fix a batch of pages and add more content, reprocess the plugin and the NOINDEX tag will fall off of the pages you have fixed.

[BCCO](https://thebc.co/ "Your favorite software") is a search marketing, design, and development company. Thank you for using our plugin. If you have any questions or comments, please contact us at support@thebc.co.

== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Paste `<?php echo do_shortcode( '[META_TAGS page="'.get_the_ID().'"]' ); ?>` into the head section of your theme.

== Frequently Asked Questions ==

= What themes are compatible with the plugin? =

We have tested it with many mainstream theme vendors like StudioPress, Bootstrap, etc. It has functioned 100% of the time. The only cases we noticed the plugin breaking is with highly modified themes.

== Screenshots ==
1. This screen shot description corresponds to `/assets/bcco-thin-content-suppression-screenshot.png.`

== Changelog ==

= 1.0 =

== Upgrade Notice ==

= 1.0 =
No upgrades at this time.