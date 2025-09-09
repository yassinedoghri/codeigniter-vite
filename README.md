<div align="center">
  <img width="180" src="./src/logo.svg" alt="CodeIgniter Vite logo" />

# CodeIgniter Vite 🔥⚡

[![Latest Stable Version](https://poser.pugx.org/yassinedoghri/codeigniter-vite/v)](https://packagist.org/packages/yassinedoghri/codeigniter-vite)
[![Total Downloads](https://poser.pugx.org/yassinedoghri/codeigniter-vite/downloads)](https://packagist.org/packages/yassinedoghri/codeigniter-vite)
[![License](https://img.shields.io/github/license/yassinedoghri/codeigniter-vite?color=green)](https://packagist.org/packages/yassinedoghri/codeigniter-vite)
[![PHP Version Require](https://poser.pugx.org/yassinedoghri/codeigniter-vite/require/php)](https://packagist.org/packages/yassinedoghri/codeigniter-vite)

An opinionated [Vite](https://vite.dev/) integration for
[CodeIgniter4](https://codeigniter.com/) projects, just so that you don't have
to think about it!

</div>

---

Easily manage and bundle JavaScript / TypeScript and CSS files within a
`resources` folder in the root of your CodeIgniter project:

```sh
resources/
├── js # your JavaScript and/or TypeScript files
├── static # static files you want to copy as is
│   ├── fonts
│   └── images
└── styles # your CSS files
```

- [🚀 Getting started](#-getting-started)
  - [0. Prerequisites](#0-prerequisites)
  - [1. Installation](#1-installation)
  - [2. Initial setup](#2-initial-setup)
  - [3. Working with Vite's dev server](#3-working-with-vites-dev-server)
- [📦 Bundle for production](#-bundle-for-production)
- [⚙️ Config reference](#️-config-reference)
  - [Routes/Assets mapping](#routesassets-mapping)
  - [Vite Environment](#vite-environment)
  - [Other properties](#other-properties)
- [❤️ Acknowledgments](#️-acknowledgments)
- [📜 License](#-license)

## 🚀 Getting started

### 0. Prerequisites

1. Install [Node.js](https://nodejs.org/)\* with one of the following package
   managers:
   - `npm` (should be included with Node install)
   - `pnpm` (recommended),
   - `yarn`

   \*You may want to use [Deno](https://deno.com/) or [Bun](https://bun.sh/)
   instead.

2. Create a `package.json` file:

   ```sh
   # using npm
   npm init

   # using pnpm
   pnpm init

   # using yarn
   yarn init
   ```

### 1. Installation

1. Install `codeigniter-vite` using composer:

   ```sh
   composer require yassinedoghri/codeigniter-vite
   ```

2. Install Vite with
   [vite-plugin-codeigniter](https://github.com/yassinedoghri/vite-plugin-codeigniter):

   ```bash
   # using npm
   npm install --save-dev vite vite-plugin-codeigniter

   # using pnpm
   pnpm add -D vite vite-plugin-codeigniter

   # using yarn
   yarn add -D vite vite-plugin-codeigniter
   ```

### 2. Initial setup

Run the following command. This command handles steps 1-4 of
[Manual Setup](#manual-setup) for you.

```sh
php spark vite:setup
```

#### Manual Setup

> [!NOTE]  
> You may skip this if you've used the setup command above.

1. Copy the `Vite.php` file from
   `vendor/yassinedoghri/codeigniter-vite/src/Config` into your project's config
   folder, update the namespace to `Config` and have the class extend the
   original class like so:

   ```php
   <?php
   // app/Config/Vite.php

   declare(strict_types=1);

   namespace Config;

   use CodeIgniterVite\Config\Vite as CodeIgniterViteConfig;

   class Vite extends CodeIgniterViteConfig
   {
      // ...
   }
   ```

2. Create your `vite.config.js` file in your project's root and add the
   [vite-plugin-codeigniter](https://github.com/yassinedoghri/vite-plugin-codeigniter)
   plugin:

   ```js
   // vite.config.js
   import { defineConfig } from "vite";
   import codeigniter from "vite-plugin-codeigniter";

   export default defineConfig(() => ({
     server: {
       port: 5173,
       strictPort: true, // prevents port from changing to something other than 5173
     },
     plugins: [codeigniter()],
   }));
   ```

3. Add Vite's scripts to your `package.json`:

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

4. Create the `resources` folder in the root of your project:

   ```sh
   resources/
   ├── js # your JavaScript and/or TypeScript files
   ├── static # files you want to copy as is
   │   ├── fonts
   │   └── images
   └── styles # your CSS files
   ```

### 3. Working with Vite's dev server

1. Set Vite environment to `development` in your `.env`:

   ```ini
   # .env
   vite.environment="development"
   ```

2. Run Vite's dev server with:

   ```sh
   # using npm
   npm run dev

   # using pnpm
   pnpm run dev

   # using yarn
   yarn run dev
   ```

   By default, the server will launch @http://localhost:5173.

3. **Work your magic!** 🪄

> [!IMPORTANT]  
> Add your JS/TS, and CSS files in the `resources` directory and inject them
> into your pages by using the [routes/assets mapping](#routesassets-mapping) in
> your `app/Config/Vite.php` file.

## 📦 Bundle for production

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

> [!NOTE]  
> You can use the `*` wildcard to match any other applicable characters in the
> routes.

### Vite Environment

Vite environment is set to `production` by default, meaning it's looking for
assets in the `public/assets` folder.\
By setting it to `development` in your `.env`, it will instead point to Vite's
dev server.

```ini
# .env
vite.environment="development"
```

### Other properties

You can tweak CodeIgniterVite's config if needed:

```php
// app/Config/Vite.php

// ...
public string $serverOrigin = 'http://localhost:5173';

public string $resourcesDir = 'resources';

public string $assetsDir = 'assets';

public string $manifest = '.vite/manifest.json';

public string $manifestCacheName = 'vite-manifest';
// ...
```

> [!IMPORTANT]  
> These defaults are in sync with `vite-plugin-codeigniter`'s defaults. If you
> edit these, make sure you set the same values for
> [`vite-plugin-codeigniter`'s options](https://github.com/yassinedoghri/vite-plugin-codeigniter/blob/main/README.md#%EF%B8%8F-options)
> in your `vite.config.js` file.

## ❤️ Acknowledgments

This wouldn't have been possible without the amazing work of the
[CodeIgniter](https://codeigniter.com/) team.

Inspired by
[codeigniter-vitejs](https://github.com/mihatorikei/codeigniter-vitejs) &
[codeigniter4/shield](https://github.com/codeigniter4/shield/).

## 📜 License

Code released under the [MIT License](https://choosealicense.com/licenses/mit/).

Copyright (c) 2025-present, Yassine Doghri
([@yassinedoghri](https://yassinedoghri.com/)).
