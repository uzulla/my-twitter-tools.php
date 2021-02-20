# My twitter tools.

## setup

1. `composer install`
2. [prepare twitter api key](https://developer.twitter.com/en/apps). `cp .env.sample .env` and edit `.env`.

## `get_following.php`

get user screen_name list by some user following.

> https://developer.twitter.com/en/docs/twitter-api/v1/accounts-and-users/follow-search-get-users/api-reference/get-friends-list

> INFO: `get/friend` api is very restricted. Rate Requests / 15-min window is 15

```
$  php ./get_following.php -u SOME_USER_SCREEN_NAME -d > output
-1
.
1667641309640710583
.
1564552656607252027
.
1481766761135615661
.
done
$
```
