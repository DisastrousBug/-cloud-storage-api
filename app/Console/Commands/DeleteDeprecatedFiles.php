<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;

class DeleteDeprecatedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:deprecatedFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command deletes deprecated files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $files = File::where('delete_at', '<', now())->cursor();

        foreach ($files as $file){
            $file->delete();
        }
        return Command::SUCCESS;
    }
}
