<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Player;
use GuzzleHttp\Client as Guzzle;
use Cache;

class SlackUserSync extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slack:usersync';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:usersync {updateIfExists=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users from Slack. .';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $token = Cache::store('file')->get('slackAPIToken');

        if( empty($token) )
        {
            $this->error('No access token. You need to authorize this app before you use the Slack features');
        }

        $updateIfExists = $this->argument('updateIfExists');


        // https://api.slack.com/methods/users.list
        $client = new Guzzle();
        $res = $client->request('GET', 'https://slack.com/api/users.list?token='.$token);

        if( $res->getStatusCode() == 200 )
        {
            $data = json_decode( $res->getBody(), true );

            if( $data && $data['ok'] === true )
            {

                foreach( $data['members'] AS $user )
                {
                    if( $user['is_bot'] || !isset( $user['profile']['email'] ) )
                    {
                        continue;
                    }

                    $playerData = [
                        'email'         => $user['profile']['email'],
                        'avatar_url'    => $user['profile']['image_512'],
                        'slack_id'      => $user['id'],
                        'slack_name'    => $user['name'],
                        'rating'        => 1000,
                        'added_from'    => 'slack'
                    ];

                    if( isset( $user['profile']['first_name'] ))
                    {
                        $playerData['name'] = $user['profile']['first_name'];

                        if( isset($user['profile']['last_name']) )
                        {
                            $playerData['name'] .= ' '.$user['profile']['last_name'];
                        }
                    }

                    $search = [
                        'slack_id' => $user['id']
                    ];

                    $player = Player::getByAny( $search );

                    if( $player ) {

                        if( $updateIfExists == 1 )
                        {
                            $player->update($playerData);
                            $player->save();
                            $this->info( 'Player '.$player->name.' (#'.$player->id.') updated.');
                        }
                        else
                        {
                            $this->info( 'Player '.$player->name.' (#'.$player->id.') exists. Skipping.');
                        }
                        continue;
                    }

                    $player = Player::create($playerData);
                    $this->info( 'Player '.$player->name.' (#'.$player->id.') created.');

                }
            }
            else
            {
                $this->error('Error in slack response: '.$res->getBody());
            }
        }
        else
        {
            $this->error('Could not get data from Slack. Http status: '.$res->getStatusCode());
        }

        $this->info('done!');
    }


}
