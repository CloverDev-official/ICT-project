import fs from 'fs/promises';
import path from 'path';
import { fileURLToPath } from 'url';

const projectRoot = path.dirname(fileURLToPath(import.meta.url));

async function collectJavaScriptFiles(directory) {
    const entries = await fs.readdir(directory, { withFileTypes: true });
    const files = await Promise.all(entries.map(async (entry) => {
        const entryPath = path.join(directory, entry.name);

        if (entry.isDirectory()) {
            return collectJavaScriptFiles(entryPath);
        }

        return entry.isFile() && entry.name.endsWith('.js') ? [entryPath] : [];
    }));

    return files.flat();
}

/**
 * Return JS entry points from enabled Laravel modules for the application's
 * single Vite build. This avoids separate module manifests that Laravel's
 * default Vite facade cannot resolve.
 */
export default async function collectModuleAssetsPaths(paths, modulesPath) {
    const modulesDirectory = path.resolve(projectRoot, modulesPath);
    const statusesPath = path.join(projectRoot, 'modules_statuses.json');
    const entries = new Set(paths);

    try {
        const statuses = JSON.parse(await fs.readFile(statusesPath, 'utf8'));
        const modules = await fs.readdir(modulesDirectory, { withFileTypes: true });

        for (const module of modules) {
            if (! module.isDirectory() || statuses[module.name] !== true) {
                continue;
            }

            const assetsDirectory = path.join(
                modulesDirectory,
                module.name,
                'resources',
                'assets',
                'js',
            );

            try {
                const files = await collectJavaScriptFiles(assetsDirectory);

                files.forEach((file) => {
                    entries.add(path.relative(projectRoot, file).split(path.sep).join('/'));
                });
            } catch (error) {
                if (error.code !== 'ENOENT') {
                    throw error;
                }
            }
        }
    } catch (error) {
        console.error(`Unable to collect Laravel module Vite assets: ${error.message}`);
    }

    return [...entries];
}
