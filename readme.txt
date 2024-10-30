=== Chili Piper ===

Contributors: chilippiper
Donate link: http://www.chilipiper.com
Tags: revenue, chili piper, sales, inbound, outbound, Demand conversion, pipeline, booking calendar, web form, forms, appointment booking, appointments, booking system, forms, marketing, appointment scheduling, demo request, book demo, Salesforce
Requires at least: 4.3
Tested up to: 6.5
Stable tag: 1.0.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Deploy Chili Piper on your website!

== Description ==

Chili Piper helps sales teams qualify, route, and schedule meetings from any inbound or outbound channel, instantly turning inbound leads into qualified meetings.

With the Chili Piper plug-in, you can set up Chili Piper’s Concierge product to easily deploy your Concierge Router to your web forms.

== Set Up ==

Set-up is easy - simply fill out your tenant (your subdomain from your Chili Piper instance), your router ID (the one you can see in the embed tab), and your form type, and we will inject the Concierge script into your website.

== Frequently Asked Questions ==

= How can I get the information from my concierge router? =
You should go to your flow and access the deployment tab. There you will see the tenant and router information.

= How can I get more details from the script that is injected to my website? =

Our Customer Love team is happy to help you on what the script does and answer any of your questions about it.

With this plugin, your script is loaded without the need of copy pasting the snippet that is set on the embed tab (the ones you can see in our help article [here](https://help.chilipiper.com/hc/en-us/articles/29434325732755-Demand-Conversion-Platform-s-Concierge-Router-Deployment)),
 but as you'll be adding the script to your website, you should still look for the CSP considerations in [this article](https://help.chilipiper.com/hc/en-us/articles/21485355246355-Content-Security-Policy-CSP-Considerations)

If you need more information about how the script works, you can check the GitHub Repo [here](https://github.com/Chili-Piper/concierge)

== Screenshots ==

1. You should fill out the options for concierge here
2. This is the entire page preview with the concierge info widget on the right. You are able to set concierge up on any page or post

== Usage of 3rd party services ==

The Concierge script is hosted on your tenant instance, and the script will load from whatever you add to the tenant field. For example, if your tenant is calendar, it means the URL you use to sign in to Chili Piper is https://calendar.chilipiper.com, and the Concierge Script URL is https://calendar.chilipiper.com/concierge-js/cjs/concierge.js.
We use your tenant as the sub domain, and the script is the same regardless of the subdomain. The script will call Chili Piper services from your instance - there’s no need to enable multiple URLs on your security policies.

If you have any concerns regarding our security, you can check more details on our [Trust Center](https://security.chilipiper.com/)

== Changelog ==

= v1.0.13 =
* Fixes on validating tenant

= v1.0.12 =
* Supports different form types

= v1.0.1 =
* Fixes form not being detected

= v1.0.0 =
* Initial release