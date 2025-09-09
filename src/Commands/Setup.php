<?php

declare(strict_types=1);

namespace CodeIgniterVite\Commands;

use CodeIgniter\CLI\CLI;

/**
 * Command file copied and adapted from CodeIgniter\Shield (https://github.com/codeigniter4/shield/blob/develop/src/Commands/Setup.php)
 */
class Setup extends BaseCommand
{
    /**
     * The Command's name
     *
     * @var string
     */
    protected $name = 'vite:setup';

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description = 'Initial setup for CodeIgniter Vite.';

    /**
     * the Command's usage
     *
     * @var string
     */
    protected $usage = 'vite:setup';

    /**
     * the Command's Arguments
     *
     * @var array<string, string>
     */
    protected $arguments = [
        'resourcesDir' => 'Path to the resources directory, defaults to `resources`.',
    ];

    /**
     * the Command's Options
     *
     * @var array<string, string>
     */
    protected $options = [
        '-f' => 'Force overwrite ALL existing files in destination.',
    ];

    /**
     * The path to `CodeIgniterVite` src directory.
     */
    protected string $sourcePath;

    protected string $distPath = APPPATH;

    protected string $rootPath = ROOTPATH;

    private string $defaultResourcesDir = 'resources';

    private string $resourcesDir;

    /**
     * Displays the help for the spark cli script itself.
     */
    public function run(array $params): void
    {
        $this->sourcePath = __DIR__ . '/../';

        $resourcesDirOption = (string) CLI::getOption('resourcesDir');
        if ($resourcesDirOption !== '') {
            // sanitize passed resourcesDir argument
            $resourcesDirOptionPath = $this->getAbsolutePath($resourcesDirOption);
            $this->resourcesDir = preg_replace(
                '/^' . preg_quote(ROOTPATH, '/') . '/',
                '',
                $resourcesDirOptionPath
            ) ?? $this->defaultResourcesDir;
        } else {
            $this->resourcesDir = $this->defaultResourcesDir;
        }

        $this->publishConfig();
    }

    /**
     * @param array<string,string>  $replaces [search => replace]
     */
    protected function copyAndReplace(string $file, array $replaces, ?string $outputFile = null): void
    {
        $path = "{$this->sourcePath}/{$file}";

        $content = (string) file_get_contents($path);

        $content = $this->replace($content, $replaces);

        $this->writeFile($outputFile ?? $file, $content);
    }

    /**
     * Write a file, catching any exceptions and showing a
     * nicely formatted error.
     *
     * @param string $file Relative file path like 'Config/Auth.php'.
     */
    protected function writeFile(string $file, string $content): void
    {
        $path = $this->distPath . $file;
        $cleanPath = clean_path($path);

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($path)) {
            $overwrite = (bool) CLI::getOption('f');

            if (
                ! $overwrite
                && $this->prompt("  File '{$cleanPath}' already exists in destination. Overwrite?", ['n', 'y']) === 'n'
            ) {
                $this->error(
                    "  Skipped {$cleanPath}. If you wish to overwrite, please use the '-f' option or reply 'y' to the prompt."
                );

                return;
            }
        }

        if (write_file($path, $content)) {
            $this->write(CLI::color('  Created: ', 'green') . $cleanPath);
        } else {
            $this->error("  Error creating {$cleanPath}.");
        }
    }

    private function publishConfig(): void
    {
        $this->publishCodeIgniterViteConfig();
        $this->publishViteConfig();
        $this->addViteScripts();
        $this->createResourcesDir();
    }

    private function publishCodeIgniterViteConfig(): void
    {
        $file = 'Config/Vite.php';
        $replaces = [
            'namespace CodeIgniterVite\Config'             => 'namespace Config',
            'use CodeIgniter\\Config\\BaseConfig;'         => 'use CodeIgniterVite\\Config\\Vite as CodeIgniterViteConfig;',
            'extends BaseConfig'                           => 'extends CodeIgniterViteConfig',
            'public string $resourcesDir = \'resources\';' => "public string \$resourcesDir = '{$this->resourcesDir}';",
        ];

        $this->copyAndReplace($file, $replaces);
    }

    private function publishViteConfig(): void
    {
        $file = 'Templates/vite.config.js';

        $replaces = [];
        if ($this->resourcesDir !== $this->defaultResourcesDir) {
            $replaces = [
                'plugins: [codeigniter()],' => "plugins: [
    codeigniter({
      resourcesDir: \"{$this->resourcesDir}\",
    }),
  ],",
            ];
        }

        $this->copyAndReplace($file, $replaces, '../vite.config.js');
    }

    private function addViteScripts(): void
    {
        $file = "{$this->rootPath}/package.json";
        $cleanPath = clean_path($file);

        $content = (string) file_get_contents($file);

        /** @var array<mixed>|null $jsonContent */
        $jsonContent = json_decode($content, true);

        if (! is_array($jsonContent)) {
            $this->error(
                "  Error when reading {$cleanPath}. Have you initialized the `package.json` file in the root of your project?"
            );

            return;
        }

        if (! array_key_exists('scripts', $jsonContent)) {
            $jsonContent['scripts'] = [];
        }

        $viteScripts = [
            'dev'   => 'vite',
            'build' => 'vite build',
        ];

        $jsonContent['scripts'] = [...$viteScripts, ...$jsonContent['scripts']];

        $this->writeFile('../package.json', (string) json_encode($jsonContent, JSON_PRETTY_PRINT));
    }

    private function createResourcesDir(): void
    {
        $resourcesPath = $this->rootPath . $this->resourcesDir;
        $cleanPath = clean_path($resourcesPath);

        if (is_dir($resourcesPath)) {
            $overwrite = (bool) CLI::getOption('f');

            if (
                ! $overwrite
                && $this->prompt(
                    "  Folder '{$cleanPath}' already exists in destination. Overwrite?",
                    ['n', 'y']
                ) === 'n'
            ) {
                $this->error(
                    "  Skipped {$cleanPath}. If you wish to overwrite, please use the '-f' option or reply 'y' to the prompt."
                );

                return;
            }

            // overwriting resources folder, delete it
            delete_files($resourcesPath, delDir: true);
            rmdir($resourcesPath);
        }

        if ($this->xcopy("{$this->sourcePath}Templates/resources", $resourcesPath)) {
            $this->write(CLI::color('  Created: ', 'green') . $cleanPath);
        } else {
            $this->error("  Error creating {$cleanPath}.");
        }
    }

    /**
     * Get absolute path from a given relative path even if file or folder doesn't exist.
     * This is an improvement on php realpath function.
     *
     * @link adapted from https://stackoverflow.com/a/39796579
     */
    private function getAbsolutePath(string $path): string
    {
        $root = ($path[0] === '/') ? '/' : '';

        $pathParts = [];
        foreach (explode(DIRECTORY_SEPARATOR, $path) as $part) {
            // ignore parts that have no value
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part !== '..') {
                // cool, we found a new part
                $pathParts[] = $part;
            } elseif (count($pathParts) > 0) {
                // going back up? sure
                array_pop($pathParts);
            } else {
                // now, here we don't like
                throw new \Exception('Climbing above the root is not permitted.');
            }
        }

        return $root . implode('/', $pathParts);
    }

    /**
     * Recursively copies all contents of a source folder into a destination folder
     *
     * @link adapted from @https://stackoverflow.com/a/2050965
     */
    private function xcopy(string $src, string $dest): bool
    {
        $srcPath = realpath($src);

        if (! $srcPath) {
            // src folder or file does not exist
            return false;
        }

        // check that destination folder exists, create it otherwise
        if (! file_exists($dest) && ! mkdir($dest, 0777, true)) {
            return false;
        }

        foreach (scandir($srcPath) as $file) {
            if (! is_readable($srcPath . '/' . $file)) {
                continue;
            }

            if (is_dir($srcPath . '/' . $file) && ($file !== '.') && ($file !== '..')) {
                if (! is_dir($dest . '/' . $file) && ! mkdir($dest . '/' . $file)) {
                    return false;
                }

                $this->xcopy($srcPath . '/' . $file, $dest . '/' . $file);
            } elseif (($file !== '.') && ($file !== '..')) {
                if (! copy($srcPath . '/' . $file, $dest . '/' . $file)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Replace content
     *
     * @param array<string,string> $replaces [search => replace]
     */
    private function replace(string $content, array $replaces): string
    {
        return strtr($content, $replaces);
    }
}
