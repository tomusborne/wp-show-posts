=== WP Show Posts ===
Contributors: edge22
Donate link: https://wpshowposts.com
Tags: show posts, display posts shortcode, portfolio, gallery, post columns
Requires at least: 4.5
Tested up to: 6.1
Stable tag: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add posts to your website from any post type using a simple shortcode.

== Description ==

= Note =

This plugin is only receiving security updates at this time. Check out our [GenerateBlocks](https://generateblocks.com/) plugin for a more modern solution.

https://vimeo.com/175638957

WP Show Posts allows you to display posts anywhere on your website using an easy to use shortcode.

You can pull posts from any post type like WooCommerce, Easy Digital Downloads etc..

This plugin works with any theme.

Here are the features in the free version:

= Posts =

* Post type
* Taxonomy
* Terms
* Posts per page
* Pagination

= Columns =

* Columns
* Columns gutter

= Images =

* Show images
* Image width
* Image height
* Image alignment
* Image location

= Content =

* Content type (excerpt or full post)
* Excerpt length
* Include title
* Read more text
* Read more button class

= Meta =

* Include author
* Author location
* Include date
* Date location
* Include terms
* Terms location

= More settings =

* Author ID
* Exclude current
* Post ID
* Exclude post ID
* Ignore sticky posts
* Offset
* Order
* Order by
* Status
* Meta key
* Meta value
* Tax operator
* No results message

= Our *Pro* version has these features =

https://vimeo.com/175660953

[Check out Pro](https://wpshowposts.com/ "Check out Pro")

= Posts =

* AJAX pagination

= Columns =

* Masonry
* Featured post
* Background color
* Background color hover
* Border color
* Border color hover

= Images =

* Image overlay color
* Image overlay icon
* Image hover effect
* Image lightbox
* Image lightbox gallery

= Content =

* Read more style
* Read more color
* Content link color
* Content link color hover
* Content text color
* Title color
* Title color hover

= Meta =

* Meta color
* Meta color hover

= Social =

* Twitter
* Twitter color + hover
* Facebook
* Facebook color + hover
* Google+
* Google+ color + hover
* Pinterest
* Pinterest color + hover
* Love it
* Alignment

Check out GeneratePress, our awesome WordPress theme! (http://wordpress.org/themes/generatepress)

== Installation ==

There's two ways to install WP Show Posts.

1. Go to "Plugins > Add New" in your Dashboard and search for: WP Show Posts
2. Download the .zip from WordPress.org, and upload the folder to the `/wp-content/plugins/` directory via FTP.

In most cases, #1 will work fine and is way easier.

== Frequently Asked Questions ==

= How do I create a post list? =

* Make sure WP Show Posts is activated.
* Navigate to "WP Show Posts > Add New" and configure your list.
* Copy the shortcode provided for you when adding your new list.
* Add your shortcode to your desired page or post.

== Screenshots ==

1. All of your created post lists.
2. The "Posts" settings tab.
3. The "Columns" settings tab.
4. The "Images" settings tab.
5. The "Content" settings tab.
6. The "Meta" settings tab.
7. The "More query ars" settings tab.

== Changelog ==

= 1.1.5 =
* Security: Add user capability check for post status

= 1.1.4 =
* Security: Improve escaping of settings that display HTML
* Tweak: Add wpsp_query_args filter

= 1.1.3 =
* New: Button class option
* Fix: Duplicate post classes
* Fix: Post classes PHP notice in some themes
* Fix: PHP 7.2 PHP warning while editing lists
* Tweak: Pass $settings to wpsp_wrapper_atts filter
* Tweak: Remove font-size and line-height CSS (allow themes to handle it)

= 1.1.2 =
* Fix: Performance issue dealing with lots of terms in list admin
* Fix: Post class clashes with GP Premium
* Tweak: Allow name in shortcode instead of ID
* Tweak: Use theme defined font size for title elements (removes the default 30px)

= 1.1.1 =
* Fix: Fix image hover effects in WPSP Pro

= 1.1 =
* New: Allow multiple taxonomy terms to be selected
* New: Choose the title HTML element
* New: wpsp_disable_title_link filter
* New: wpsp_disable_image_link filter
* New: wpsp_read_more_output filter
* New: wpsp_inside_wrapper hook
* New: wpsp_image_attributes filter
* New: wpsp_term_separator filter
* New: Option to add comments number/link in post meta
* New: Allow override of settings within shortcode parameter
* New: Add standard post classes to each post
* Tweak: Remove many function_exists() wrappers - check your custom functions!
* Tweak: Pass list settings through hooks instead of using global
* Tweak: Clean up code considerably
* Tweak: Use the_excerpt() instead of custom function
* Tweak: Remove border radius from read more buttons
* Fix: Broken author setting
* Fix: Remove image float on mobile
* Fix: Missing color labels in WP 4.9

= 1.0 =
* Add new hook inside image container: wpsp_inside_image_container
* Fix issue with pagination and random post ordering
* Clean up defaults to only include free options
* Add margin to the top of pagination
* Use manual excerpt if it's set

== Upgrade Notice ==

= 1.1.3 =
* New: Button class option
* Fix: Duplicate post classes
* Fix: Post classes PHP notice in some themes
* Fix: PHP 7.2 PHP warning while editing lists
* Tweak: Pass $settings to wpsp_wrapper_atts filter
* Tweak: Remove font-size and line-height CSS (allow themes to handle it)

= 1.1.2 =
* Fix: Performance issue dealing with lots of terms in list admin
* Fix: Post class clashes with GP Premium
* Tweak: Allow name in shortcode instead of ID
* Tweak: Use theme defined font size for title elements (removes the default 30px)

= 1.1.1 =
* Fix: Fix image hover effects in WPSP Pro

= 1.1 =
* New: Allow multiple taxonomy terms to be selected
* New: Choose the title HTML element
* New: wpsp_disable_title_link filter
* New: wpsp_disable_image_link filter
* New: wpsp_read_more_output filter
* New: wpsp_inside_wrapper hook
* New: wpsp_image_attributes filter
* New: wpsp_term_separator filter
* New: Option to add comments number/link in post meta
* New: Allow override of settings within shortcode parameter
* New: Add standard post classes to each post
* Tweak: Remove many function_exists() wrappers - check your custom functions!
* Tweak: Pass list settings through hooks instead of using global
* Tweak: Clean up code considerably
* Tweak: Use the_excerpt() instead of custom function
* Tweak: Remove border radius from read more buttons
* Fix: Broken author setting
* Fix: Remove image float on mobile
* Fix: Missing color labels in WP 4.9

= 1.0 =
* Add new hook inside image container: wpsp_inside_image_container
* Fix issue with pagination and random post ordering
* Clean up defaults to only include free options
* Add margin to the top of pagination
* Use manual excerpt if it's set
