<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CategoryTreeService;

class WarmCategoryTreeCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:warm-category-tree-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CategoryTreeService $categoryTreeService): int
    {
        $tree = $categoryTreeService->getTree();

        if (empty($tree)) {
            $this->warn('No categories found. Cache was not warmed.');

            return self::SUCCESS;
        }

        $this->info('Category tree cache warmed successfully.');

        return self::SUCCESS;
    }
}
