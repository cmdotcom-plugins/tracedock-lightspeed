# Introduction


Due to an increase in adblockers, browsers with tracking prevention and cookie legislation, ecommerce websites miss 
up to 30% of their marketing attribution data. TraceDock is a first party data collection service working in parallel to 
Google Analytics and connecting your conversions to the Facebook Conversion API to improve the data that you 
measure from your website visitors.

The goal of this package is to simplify the configuration of serverside transaction tracking for customers with Lightspeed.

The package includes a Lightspeed module and will:
1. Setup the identify event based on the quoteId of the checkout.
2. Forward invoice data to the TraceDock endpoint to connect serverside transactions using a webhook

https://docs.tracedock.com/configuration/server-side-transaction-tracking/ for docs on the serverside transaction tracking.

Note: we assume that customers have implemented the basic setup of TraceDock, including adding a DNS record and adding the 
TraceDock code to the template of the website, as found in https://docs.tracedock.com//installation/start

This readme contains a step-by-step description of how you can install the module in your Lightspeed environment.

## 1. Setting up the client side identify event

First step is to add the client side identify event on all checkout pages. This event will allow TraceDock to stitch
the transaction to the browser session.

The code of a Lighspeed shop can be modified by navigating to Design > Theme editor > Advanced > Adjust code.
In the side bar you can see all the files you can modify in the template. 
The <head> can be found either in the file 'head.rain' in the folder 'snippets' or 'fixed.rain' under the tab 'layout'.

![Adjusting the <head> text found in the file 'head.rain'](https://github.com/cmdotcom/tracedock-lightspeed/blob/master/static/step1.png?raw=true)

In this file you can add the dataLayer event within the tags <head> and </head>. 

```
{% if checkout %}
    <script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({userId: "{{ checkout.quote.id }}"})
    </script>
{% endif %}
```

Note: Lightspeed does not contain a default userId,  as such we use the quoteId to stitch with the browser session. 
For compatibility with the default templates in TraceDock we will forward this field as `userId`.


## 2a - Setup server side API call

This file initiates the curl command to post the transaction data from Lightspeed to TraceDock. See the file `src/1_tracedock_api.php` in the code.

In this file 3 variables above the page (line 6 to 8) must be updated with your shop specific data:

![Adjusting variables in `tracedock.php`](https://github.com/cmdotcom/tracedock-lightspeed/blob/master/static/step2a_tracedock.png?raw=true)

* __$url__ - This is the url of the TraceDock endpoint that can be found in the TraceDock portal under the transaction event.
* __$key__ - This is the Lightspeed API key, accessible by the owner via Settings > Developers.
* __$secret__ - This is the Lightspeed API secret, accessible by the owner via Settings > Developers.



## 2b - Setup server side API call

This file initiates the webhook to make the API after the transaction is completed. See the file `src/2_createwebhook.php` in the code.

In this file 3 variables above the page (line 4 to 6) must be updated with your shop specific data:

![Adjusting variables in `tracedock.php`](https://github.com/cmdotcom/tracedock-lightspeed/blob/master/static/step2b_createwebhook.png?raw=true)

* __$url__ - This is the URL of the file you created under step _2a_, called `tracedock.php`.
* __$key__ - This is the Lightspeed API key, accessible by the owner via Settings > Developers.
* __$secret__ - This is the Lightspeed API secret, accessible by the owner via Settings > Developers.


## 3 - Test events in the portal

After these files are initiates, all orders with the status `paid` will be automatically forwarded to TraceDock.

You can debug these events within the TraceDock portal.
Go to [serverside_events](https://portal.tracedock.com/serverside_events) tab in the TraceDock portal.
Press on the three dots (...) after the event, and you will automatically filter the transactions in the live event view.

## Questions or support?

If you have any questions, please contact us on [support@tracedock.com](mailto:support@tracedock.com). We love to help.

## Known issues
No known issues.

## Changelog
See the [Changelog](CHANGELOG.md)

### Contribute

See the [Contribution Guidelines](CONTRIBUTE.md)