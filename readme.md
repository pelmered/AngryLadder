# Angryladder

Built with Lumen






## API endpoints

```
/v1/games/ (get)
/v1/games/{id} (get)
/v1/games/ (post)
/v1/games/{id} (put)

/v1/players/ (get)
/v1/players/{id} (get)
/v1/players/top (get)
/v1/players/top/mostgames (get)
/v1/players/ (post)
/v1/players/{id} (put)
```


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
1. Start work server `php artisan queue:listen`. More options for more stable work server can be found [here](https://laravel.com/docs/5.2/queues#running-the-queue-listener) ([supervisor](https://laravel.com/docs/5.2/queues#supervisor-configuration) is recomended).


## TODO
 * Update to latest versions of Lumen and libs (Maybe wait for the 5.5 LTS in August)
 * Implement new Glicko2 ranking calculator
 * Implement support for dynamic ladder periods
 * Fix bugs related to all time vs weekly rankings
 * Unit tests
 * Setup CI tools (TravisCI?)
 * Setup code check tools (Scrutinizer?)
