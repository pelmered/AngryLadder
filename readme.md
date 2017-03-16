# Angryladder

Built with Lumen

Use together with our slack bot




## API endpoints

```
/v1/matches/ (get)
/v1/matches/{id} (get)
/v1/matches/ (post)
/v1/matches/{id} (put)

/v1/players/ (get)
/v1/players/{id} (get)
/v1/players/{id}/stats (get)
/v1/players/ (post)
/v1/players/{id} (put)

/v1/top (get)
/v1/top/mostgames (get)

/v1/stats (get)
```

### Postman collection

Download [Postman collection](https://github.com/Angrycreative/AngryLadder/blob/master/AngryLadder.postman_collection.json).
Save it, import it into your Postman client and set up `url` as an environment variable to test all the endpoints.


## Setup

1. git clone
1. Run `composer install`
1. Copy .env.sample to .env
	1. Edit `APP_ENV`(local, stage or production) and `APP_DEBUG` according to what environment you are seting up
	1. Set `APP_KEY`. Should be a random string of at exactly 32 characters
	1. Set the database options in `.env`
	1. Add `SLACK_CLIENT_ID` and `SLACK_CLIENT_SECRET` if you want to be able to sync players from Slack.
1. Run migrations: `php artisan migrate`
	1. (optional) If you want test data, seed the database: `php artisan db:seed`
1. (not yet needed) Start work server `php artisan queue:listen`. More options for more stable work server can be found [here](https://laravel.com/docs/5.2/queues#running-the-queue-listener) ([supervisor](https://laravel.com/docs/5.2/queues#supervisor-configuration) is recomended).


## TODO
 * Some kind of web UI (separate project using the API. React?)
 * Update to latest versions of Lumen and libs (Maybe wait for the 5.5 LTS in August)
 * ~~Implement new Glicko2 ranking calculator~~
 * Implement support for dynamic ladder periods (in progress)
 * Fix bugs related to all time vs weekly rankings (in progress)
 * Unit tests
 * Setup CI tools (TravisCI?)
 * Setup code check tools (Scrutinizer?)
