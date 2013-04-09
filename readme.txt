=== Google Analytics Multitrack ===
Contributors: mfkelly
Tags: plugin, google, analytics, multisite, buddypress, tracking, stats, multitrack
Requires at least: 3.3
Tested up to: 3.5
Stable tag: 0.1

== Description ==
Wordpress plugin.
In multisite installations of Wordpress, or in Buddypress, you may wish to track stats for the whole network, and for individual sites too.
This simple plugin allows the network superadmin to set details for one network-wide Google Analytics account, and separate GA account details
for individual sites in the network. It embeds the appropriate multitracker GA script where a site account is specified, along with the network-wide 
tracker script. Otherwise, it embeds the network-wide tracker script only.

Settings are currently hidden from end users.

== Installation ==

1. Copy the Google Analytics Multitrack folder to your web server's wp-content/plugins folder.
2. Activate the plugin for the network.
3. Set the GA account details for the Wordpress network in the Network Admin Dashboard, under Settings -> Google Analytics Multitrack.
4. For any individual sites in your network which require their own GA account, go to that site's Dashboard, and add its own GA account details under Settings -> Google Analytics Multitrack.

== Changelog ==

*0.1 Initial Release*