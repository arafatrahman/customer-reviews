=== Customer Reviews ===

Contributors: Artiosmedia, steveneray, arafatrahmanbd
Donate link: [https://www.zeffy.com/en-US/donation-form/your-donation-makes-a-difference-6](https://www.zeffy.com/en-US/donation-form/your-donation-makes-a-difference-6)
Tags: customer Reviews, wp customer review, customer review plugin, product reviews, service reviews, review management, review form, review list, woocommerce reviews
Requires at least: 4.6
Tested up to: 6.8.1
Version: 1.0.0
Stable tag: 1.0.0
Requires PHP: 7.4.33
License: GPLv3 or later
License URI: [http://www.gnu.org/licenses/gpl-3.0.html](http://www.gnu.org/licenses/gpl-3.0.html)

The Customer Reviews plugin allows you to manage and display customer-submitted reviews for products and services. A shortcode can be added to any page, post, or custom post type to embed review forms and lists.

== Description ==

The Customer Reviews plugin is a robust and user-friendly tool designed for collecting, managing, and showcasing customer feedback on your WordPress site. It provides a comprehensive system for handling reviews from submission to display, enhancing trust and credibility for your products or services.

**Key Features:**

* **Review Submission:**
    * Allow customers to submit reviews directly on your website via a customizable form.
    * Configurable review form fields (Name, Email, Website, Phone, City, State, Title, Comment, Rating) with options to show/hide and set as required.
    * Adjustable font sizes and weights for review author names and comments.
    * Customizable star rating color.
* **Review Display:**
    * Display reviews on any page, post, or custom post type using simple shortcodes `[wp_ctrw_form]` and `[wp_ctrw_lists]`.
    * Option to automatically append review shortcodes to specific post types.
    * Configurable display settings for the review list, including reviews per page, date format (MM/DD/YYYY, DD/MM/YYYY, YYYY/MM/DD), and option to include time.
    * Ability to show or hide City and State in the review list.
    * Option to enable or disable the display of review titles.
    * Displays average rating and total review count on single product pages (if WooCommerce integration is enabled).
* **Review Management:**
    * Moderate and manage all submitted reviews from the WordPress admin dashboard.
    * Admin interface for listing reviews with filters for status (All, Approved, Reject, Pending, Trash).
    * Bulk actions for approving, rejecting, moving to trash, or permanently deleting reviews.
    * Ability to reply to customer reviews from the admin panel.
    * Edit existing customer reviews, including all submitted details, status, and rating.
    * Supports pagination and column visibility settings in the admin review list.
* **Notifications:**
    * Email notification to the admin for new pending reviews.
    * Optional email notification to customers about their review status (pending/approved).
* **WooCommerce Integration:**
    * Option to replace WooCommerce's default review system with the Customer Reviews plugin.
    * Disables default WooCommerce review settings when enabled.

== Installation ==

1.  **Upload:** Upload the `customer-reviews` folder to the `/wp-content/plugins/` directory.
2.  **Activate:** Activate the plugin through the 'Plugins' menu in WordPress.
3.  **Configure:** Navigate to 'Reviews' -> 'Review Settings' in your WordPress admin menu to configure plugin options, including form fields, display settings, and email notifications.
4.  **Shortcodes:** Use the shortcodes `[wp_ctrw_form]` to display the review submission form and `[wp_ctrw_lists]` to display the list of approved reviews on any page, post, or custom post type.

== Screenshots ==

(No screenshots available at this time.)

== Frequently Asked Questions ==

**Q: How do I add the review form and list to my pages?**
A: You can use the shortcodes `[wp_ctrw_form]` for the review submission form and `[wp_ctrw_lists]` for the review list. Simply paste these shortcodes into any page, post, or custom post type content.

**Q: Can I approve reviews automatically?**
A: Yes, you can enable automatic review approval in the plugin settings under the "General" tab.

**Q: How do I reply to a customer review?**
A: In the WordPress admin dashboard, go to 'Reviews' -> 'Reviews'. You will find a "Reply" button next to each review. Clicking it will open a popup where you can compose and send your reply.

**Q: Can I edit an existing review?**
A: Yes, from the 'Reviews' -> 'Reviews' page in the admin area, you can click the "Edit Review" button next to any review to modify its details.

**Q: Does this plugin integrate with WooCommerce?**
A: Yes, the plugin includes an option to replace the default WooCommerce review system with its own. This setting can be found under "Advanced Settings" in the Review Settings page.

== Changelog ==

= 1.0.0 =
* Initial Release.
* Core functionality for submitting, displaying, and managing customer reviews.
* Admin dashboard for review moderation, including bulk actions, replies, and edits.
* Customizable review form fields and display options.
* Email notifications for admin and customers.
* WooCommerce integration to replace default reviews.
* Shortcode support for embedding review forms and lists.

== Upgrade Notice ==

= 1.0.0 =
This is the initial stable release of the Customer Reviews plugin.