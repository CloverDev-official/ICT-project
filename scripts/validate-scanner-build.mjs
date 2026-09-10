import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import { parseAst } from 'rollup/parseAst';

const buildRoot = new URL('../public/build/', import.meta.url);
const manifest = JSON.parse(
    await readFile(new URL('manifest.json', buildRoot), 'utf8'),
);
const scanner = manifest['resources/js/scanner.js'];
assert.ok(scanner?.isEntry, 'Scanner must be a production entry');
const ast = parseAst(
    await readFile(new URL(scanner.file, buildRoot), 'utf8'),
);
const exports = ast.body
    .filter((node) => node.type === 'ExportNamedDeclaration')
    .flatMap((node) => node.specifiers.map((item) => item.exported.name));
for (const name of ['createScanAudio', 'createScanResultController']) {
    assert.ok(exports.includes(name), `Production scanner is missing ${name}`);
}
console.log('Production scanner exports validated');
