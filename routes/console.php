<?php

use App\Console\Commands\ScrapeNewsCommand;

Schedule::command(ScrapeNewsCommand::class)->daily()->runInBackground();
