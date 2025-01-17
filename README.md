# CodeIgniter Vite

An opinionated Vite integration for [CodeIgniter4](https://codeigniter.com/)
projects.

---

Easily manage and bundle JavaScript, TypeScript and CSS files with a `Resources`
folder in your CodeIgniter app:

```sh
app/Resources/
├── js # your JavaScript and/or TypeScript files
├── static # static files you want to copy as is
│   ├── fonts
│   └── images
└── styles # your CSS files
```

- [🚀 Getting started](#-getting-started)
  - [0. Prerequisites](#0-prerequisites)
  - [1. Setup](#1-setup)
  - [2. Run the dev environment](#2-run-the-dev-environment)
  - [3. Bundle for production](#3-bundle-for-production)
- [⚙️ Config reference](#️-config-reference)
  - [Routes/Assets mapping](#routesassets-mapping)
  - [Environment Variables](#environment-variables)
- [❤️ Acknowledgments](#️-acknowledgments)
- [📜 License](#-license)

## 🚀 Getting started

### 0. Prerequisites

1. Install [Node.js](https://nodejs.org/)\* with one of the following package
   managers:

   - `npm` (should be included with Node install)
   - `pnpm` (recommended),
   - `yarn`

2. create a `package.json` file:

   ```sh
   # using npm
   npm init

   # using pnpm
   pnpm init

   # using yarn
   yarn init
   ```

\*You may want to use [Deno](https://deno.com/) or [Bun](https://bun.sh/)
instead.

### 1. Setup

1. install `codeigniter-vite` using composer:

   ```sh
   composer require yassinedoghri/codeigniter-vite
   ```

2. install Vite with `@rollup/plugin-multi-entry`:

   ```bash
   # using npm
   npm install --save-dev vite @rollup/plugin-multi-entry

   # using pnpm
   pnpm add -D vite @rollup/plugin-multi-entry

   # using yarn
   yarn add -D vite @rollup/plugin-multi-entry
   ```

3. copy the following `vite.config.js` file in the root of your CodeIgniter
   project:

   ```js
   // vite.config.js
   import multi from "@rollup/plugin-multi-entry";
   import path from "path";
   import { defineConfig, loadEnv } from "vite";

   export default defineConfig(({ mode }) => {
     const env = Object.assign(process.env, loadEnv(mode, process.cwd()));

     return {
       root: path.resolve(__dirname, env.VITE_RESOURCES_DIR ?? "app/Resources"),
       publicDir: "./static",
       build: {
         outDir: path.resolve(
           __dirname,
           `public/${env.VITE_ASSETS_DIR ?? "assets"}`,
         ),
         assetsDir: "",
         manifest: env.VITE_MANIFEST ?? ".vite/manifest.json",
         rollupOptions: {
           input: [
             path.resolve(
               __dirname,
               `${env.VITE_RESOURCES_DIR ?? "app/Resources"}/js/**/*.{js,ts}`,
             ),
             path.resolve(
               __dirname,
               `./${env.VITE_RESOURCES_DIR ?? "app/Resources"}/styles/**/*.css`,
             ),
           ],
           preserveEntrySignatures: true,
           output: [
             {
               dir: path.resolve(
                 __dirname,
                 `public/${env.VITE_ASSETS_DIR ?? "assets"}`,
               ),
               preserveModules: true,
             },
           ],
         },
       },
       server: {
         host: env.VITE_SERVER_HOST ?? "localhost",
         port: env.VITE_SERVER_PORT ?? 5173,
         strictPort: true,
       },
       plugins: [multi({ preserveModules: true })],
     };
   });
   ```

4. Add Vite's scripts to your `package.json`:

   ```json
   {
     //...
     "scripts": {
       "dev": "vite",
       "build": "vite build"
     }
     //...
   }
   ```

5. Create the `Resources` folder in your CodeIgniter app folder:

   ```sh
   app/Resources/
   ├── js # your JavaScript and/or TypeScript files
   ├── static # files you want to copy as is
   │   ├── fonts
   │   └── images
   └── styles # your CSS files
   ```

6. Edit your `app/Config/Vite.php` config file to inject your styles and scripts
   in your routes:

   ```php
    <?php
    // app/Config/Vite.php

    declare(strict_types=1);

    namespace Config;

    use CodeIgniterVite\Config\Vite as ViteConfig;

    class Vite extends ViteConfig
    {
        public array $routesAssets = [
            [
                'routes' => ['*'],
                'assets'  => ['styles/index.css'],
            ],
        ];
    }
   ```

7. That's all! Your assets will get automatically linked in the `<head>` of your
   routes depending on the mapping you define.

### 2. Run the dev environment

Run Vite's dev server with:

```sh
# using npm
npm run dev

# using pnpm
pnpm run dev

# using yarn
yarn run dev
```

By default, the server will launch @http://localhost:5173. See the
[config reference](#️-config-reference) to change the host or port.

### 3. Bundle for production

For production, run the build command:

```sh
# using npm
npm run build

# using pnpm
pnpm run build

# using yarn
yarn run build
```

This will create an `assets` folder in your public directory including all of
your bundled css and js files + a `manifest.json` file under a `.vite/`
directory.

## ⚙️ Config reference

### Routes/Assets mapping

For assets to be injected in your routes `<head>`, you must define the
`$routesAssets` property in your `app/Config/Vite.php` file.

The `$routesAssets` property takes in an array of routes/assets mappings:

```php
public array $routesAssets = [
  [
    'routes' => ['*'], // include these assets on all routes
    'assets' => ['styles/index.css', 'js/main.js'],
  ],
  [
    // include the map.js file in the /map route
    'routes' => ['/map'],
    'assets' => ['js/map.js'],
  ],
  [
    'routes' => ['admin*'], // only include these assets in Admin routes
    'assets' => ['js/admin.js'],
  ]
];
```

### Environment Variables

You can tweak the following environment variables to your liking:

```ini
VITE_ENVIRONMENT="production"

VITE_SERVER_HOST="localhost"
VITE_SERVER_PORT=5173
VITE_SERVER_ORIGIN="http://localhost:5173"

VITE_RESOURCES_DIR="app/Resources"
VITE_ASSETS_DIR="assets"
VITE_MANIFEST=".vite/manifest.json"
```

Note that you can use the `*` wildcard to match any other applicable characters
in the routes.

## ❤️ Acknowledgments

This wouldn't have been possible without the amazing work of the
[CodeIgniter](https://codeigniter.com/) team.

Inspired by
[codeigniter-vitejs](https://github.com/mihatorikei/codeigniter-vitejs).

## 📜 License

Code released under the [MIT License](https://choosealicense.com/licenses/mit/).

Copyright (c) 2025-present, Yassine Doghri
([@yassinedoghri](https://yassinedoghri.com/)).
