=== Wigzo – All-in-one platform to convert, retain and grow ===
Contributors: wigzo
Tags: wigzo, browser push, exit intent, personalization, behavioural automation
Requires at least: 3.5.0
Requires PHP: 7.2
Tested up to: 6.5.3
Stable tag: 3.1.6
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart marketing automation and personalization to focus on customer engagement and growth.

== Description ==

Wigzo is a Customer Data Platform offering customer-centric solutions to boost engagement, conversions and retention for your eCommerce business.

### Analytics & Insights
Leverage first-party customer data and discover real-time insights with Wigzo’s advanced customer analytics.

### Automated User Segmentation
Segregate customers based on behavior, preferences and more, and create 360o customer profiles to launch targeted campaigns.

### Omnichannel Engagement
Engage customers on Email, SMS, WhatsApp, Onsite and Push, and streamline all interactions under one platform.

### Event-based Journey Builder
Create automated communication workflows using drag-n-drop builder and build personalised customer journeys.


== Features eCommerce Brands Love ==

### Behavioral Automation
Trigger communication based on user activity and engage customers at the right time on the right channel.

### Hyper-Personalisation
Personalise marketing campaigns based on user needs and preferences to boost customer engagement and conversions.

### Cart Recovery Automation
Build cart recovery workflows to remind customers to complete the checkout process and recover otherwise lost sales.

### Post-purchase Automation
Deliver an outstanding customer experience by engaging customers post-purchase and build a loyal customer base.

### Repeat Purchase Automation
Send personalised recommendations, upsell and cross-sell to garner more repeat orders from existing customers.


== Installation ==

Before proceeding please make sure that you are running WordPress 3.5 or above.

Please refer to the WordPress documentation on how to get the plugin to appear in your installation admin section.

Once the plugin appears in your installation, you must activate it. Navigate to the "Plugins" section and locate the "Wigzo" plugin. The activation is done simply by clicking the "Activate" link next to the plugin name in the list.

Once you have activated the plugin and added the necessary actions, you need to configure the plugin. The plugins configuration page can be found under the "Settings > Wigzo".

Once the plugin is activated campaigns and there respective reports can be managed via http://app.wigzo.com/


The configuration page consists of two settings:

* Browser Push
	* Http / Https (choose https if your website have a valid SSL certificate running on HTTPS. If you don't own any certificate, you can select HTTP.)
* On-Site Push Notification : Enable or disable to activate the same

All of the above settings are needed for the plugin to work. You do not need to add Wigzo javascript manually to your store.


The plugin uses the WordPress Action API to add content to the website. However, there are a few actions that will have to be added to the wordpress theme in order for the plugin to function to its full extent.

* Tracking/sending event can be done using our API
	* wigzo.track(eventName, eventValue);
* Users can be mapped using our API
	* wigzo.identify(userInfo, force, callback);
* Indexing product using our API. It's recommended to use this API only if you are following SPA(Single page architecture).
	* wigzo.index(productInfo);
	
For brief info do visit Wigzo Integrations Page i.e https://app.wigzo.com/integration/apicodeintegration	


== Changelog ==

= 3.1.5 =
* Additionally, monitor post-purchase events (Order Dispatched, Out for Delivery, Order Delivered, Order Shipped, Order Pickup, Order Pickup Scheduled).

= 3.0 =
* Release New 3.0 Version with New APIs Addition

= 1.0.1 =
* Performance optimizations