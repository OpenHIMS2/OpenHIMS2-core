<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishAssets extends Command
{
    protected $signature   = 'app:publish-assets';
    protected $description = 'Copy Bootstrap CSS/JS and Bootstrap Icons from node_modules to public/vendor/';

    public function handle(): int
    {
        $this->copyBootstrap();
        $this->copyBootstrapIcons();
        $this->copyChartJs();
        $this->newLine();
        $this->info('All assets published to public/vendor/');

        return self::SUCCESS;
    }

    private function copyBootstrap(): void
    {
        $srcCss = base_path('node_modules/bootstrap/dist/css/bootstrap.min.css');
        $srcJs  = base_path('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js');

        if (!file_exists($srcCss)) {
            $this->error('Bootstrap not found in node_modules. Run: npm install bootstrap');
            return;
        }

        $cssDir = public_path('vendor/bootstrap/css');
        $jsDir  = public_path('vendor/bootstrap/js');

        File::ensureDirectoryExists($cssDir);
        File::ensureDirectoryExists($jsDir);

        File::copy($srcCss, $cssDir . '/bootstrap.min.css');
        File::copy($srcJs,  $jsDir  . '/bootstrap.bundle.min.js');

        $this->line('  <info>✓</info> Bootstrap CSS and JS copied.');
    }

    private function copyBootstrapIcons(): void
    {
        $srcCss   = base_path('node_modules/bootstrap-icons/font/bootstrap-icons.css');
        $srcFonts = base_path('node_modules/bootstrap-icons/font/fonts');

        if (!file_exists($srcCss)) {
            $this->error('Bootstrap Icons not found in node_modules. Run: npm install bootstrap-icons');
            return;
        }

        $destDir   = public_path('vendor/bootstrap-icons');
        $destFonts = $destDir . '/fonts';

        File::ensureDirectoryExists($destDir);
        File::copy($srcCss, $destDir . '/bootstrap-icons.css');

        if (is_dir($srcFonts)) {
            File::ensureDirectoryExists($destFonts);
            File::copyDirectory($srcFonts, $destFonts);
        }

        $this->line('  <info>✓</info> Bootstrap Icons CSS and fonts copied.');
    }

    private function copyChartJs(): void
    {
        $src = base_path('node_modules/chart.js/dist/chart.umd.js');

        if (!file_exists($src)) {
            $this->error('Chart.js not found in node_modules. Run: npm install chart.js');
            return;
        }

        $destDir = public_path('vendor/chartjs');
        File::ensureDirectoryExists($destDir);
        File::copy($src, $destDir . '/chart.umd.js');

        $this->line('  <info>✓</info> Chart.js copied.');
    }
}
