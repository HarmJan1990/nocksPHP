# PHP wrapper class for Nocks API
This class is a wrapper for the Nocks API, for the API docs please visit: https://docs.nocks.com/.
You can use this class to check market values, make trades, get deposit addresses, create withdrawals or make trading bots.

This is the first class i made and the first time i use Github so if you have questions or suggestions please don't hesitate to contact me! I'm planning to add more functions to this class.


# Requirements
* Nocks account (https://www.nocks.com).
* Access to the Nocks API.


# Usage

```
use HarmJan1990\nocksPHP\Client;
  
$bearer = ''; // use your bearer token.
  
$n = new Client($bearer);
  
$list = $n->GetOrders();
```

  
# Documentation

I documented every function in the Client.php file.
