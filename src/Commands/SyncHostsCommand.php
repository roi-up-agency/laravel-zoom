<?php

namespace RoiUp\Zoom\Commands;

use Illuminate\Console\Command;
use RoiUp\Zoom\Models\Eloquent\Host;

class SyncHostsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:vendor:sync-hosts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download all the users and save in zoom_hosts table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $zoom =  app('Zoom');

        $users = $zoom->user->list();

        foreach($users as $user){

            $host = Host::whereHostId($user->id)->first();

            if(empty($host)){
                $host = new Host();
                $host->host_id = $user->id;
            }

            $host->email = $user->email;
            $host->first_name = $user->first_name;
            $host->last_name = $user->last_name;
            $host->type = $user->type;

            $host->save();
        }
    }
}
