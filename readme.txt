=== WP Show Posts ===
Contributors: edge22
Donate link: https://wpshowposts.com
Tags: show posts, display posts shortcode, portfolio, gallery, post columns
Requires at least: 4.5
Tested up to: 4.9
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add posts to your website from any post type using a simple shortcode.

== Description ==

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

= 0.9 =
* Fix bug where terms weren't saving
* Strip oembed URLs from excerpt

= 0.8 =
* Strip shortcodes from excerpts
* Add ellipses after excerpts
* Fix some broken text domains for translations

= 0.7 =
* Prevent direct access to files
* Add prefix to all column classes to avoid conflicts
* Use wp_trim_words() function for excerpts
* Fix conflict with Maintenance plugin
* Make columns full width on mobile
* Allow more tag usage when excerpt is set
* Add blank option in widget to fix bug in Customizer/Elementor

= 0.6 =
* Add height: auto to images to prevent image stretching in Beaver Builder
* Prevent horizontal scrolling when posts are in columns
* Change "More query args" section name to "More settings"
* Allow multiple IDs in "Post ID" option
* Add "Exclude IDs" option
* Add "No results message" option
* Add "WP Show Posts" widget to add posts in widget areas

= 0.5 =
* Fix conflict with Yoast SEO causing taxonomy and terms fields to be blank
* Add support for translations

= 0.4 =
* Fix column width issue when content is disabled
* Fix pagination issue when post list is on the front page
* Disable pagination in single posts
* Fix saving of taxonomy and terms fields
* Force no underline on read more buttons

= 0.3 =
* Remove attachment post type from list for now
* Don't show pagination if there's no posts
* Move wpsp_before_title hook into the <header> element
* New hook: wpsp_before_wrapper
* New hook: wpsp_before_header

= 0.2 =
* Fix issue with posts showing up in wrong area on page
* Remove read more link if the <!-- more --> tag is used
* Wrap read more button in div: .wpsp-read-more

= 0.1 =
* Initial release

== Upgrade Notice ==

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

= 0.9 =
* Fix bug where terms weren't saving
* Strip oembed URLs from excerpt

= 0.8 =
* Strip shortcodes from excerpts
* Add ellipses after excerpts
* Fix some broken text domains for translations

= 0.7 =
* Prevent direct access to files
* Add prefix to all column classes to avoid conflicts
* Use wp_trim_words() function for excerpts
* Fix conflict with Maintenance plugin
* Make columns full width on mobile
* Allow more tag usage when excerpt is set
* Add blank option in widget to fix bug in Customizer/Elementor

= 0.6 =
* Add height: auto to images to prevent image stretching in Beaver Builder
* Prevent horizontal scrolling when posts are in columns
* Change "More query args" section name to "More settings"
* Allow multiple IDs in "Post ID" option
* Add "Exclude IDs" option
* Add "No results message" option
* Add "WP Show Posts" widget to add posts in widget areas

= 0.5 =
* Fix conflict with Yoast SEO causing taxonomy and terms fields to be blank
* Add support for translations

= 0.4 =
* Fix column width issue when content is disabled
* Fix pagination issue when post list is on the front page
* Disable pagination in single posts
* Fix saving of taxonomy and terms fields
* Force no underline on read more buttons

= 0.3 =
* Remove attachment post type from list for now
* Don't show pagination if there's no posts
* Move wpsp_before_title hook into the <header> element
* New hook: wpsp_before_wrapper
* New hook: wpsp_before_header

= 0.2 =
* Fix issue with posts showing up in wrong area on page
* Remove read more link if the <!-- more --> tag is used
* Wrap read more button in div: .wpsp-read-more

= 0.1 =
* Initial release
