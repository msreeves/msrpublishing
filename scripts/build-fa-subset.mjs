#!/usr/bin/env node
/**
 * Build subset Font Awesome webfonts for Atlas Briefing (P44).
 *
 * Usage: node scripts/build-fa-subset.mjs
 */
import { readFileSync, writeFileSync, mkdirSync, unlinkSync, existsSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import subsetFontPkg from 'subset-font';

const subsetFont = subsetFontPkg.default || subsetFontPkg;

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const themeRoot = resolve( __dirname, '..' );
const configPath = resolve( themeRoot, 'config/msr-publishing-fa-icons.json' );
const iconsYml = resolve(
	themeRoot,
	'node_modules/@fortawesome/fontawesome-free/metadata/icons.yml'
);
const outDir = resolve( themeRoot, 'src/webfonts' );

const config = JSON.parse( readFileSync( configPath, 'utf8' ) );
const yml = readFileSync( iconsYml, 'utf8' );

function getUnicode( iconName ) {
	const block = yml.split( new RegExp( `^${iconName}:`, 'm' ) )[1];
	const match = block?.match( /^\s+unicode:\s+([0-9a-f]+)/m );
	return match ? match[1] : null;
}

function unicodesToText( names ) {
	const chars = [];
	for ( const name of names ) {
		const hex = getUnicode( name );
		if ( ! hex ) {
			throw new Error( `build-fa-subset: missing unicode for icon "${name}"` );
		}
		chars.push( String.fromCodePoint( parseInt( hex, 16 ) ) );
	}
	return chars.join( '' );
}

async function subsetOne( sourceName, outName, text ) {
	const source = resolve(
		themeRoot,
		`node_modules/@fortawesome/fontawesome-free/webfonts/${sourceName}`
	);
	const input = readFileSync( source );
	const output = await subsetFont( input, text, { targetFormat: 'woff2' } );
	writeFileSync( resolve( outDir, outName ), output );
}

mkdirSync( outDir, { recursive: true } );

const solidText = unicodesToText( config.solid );
const brandsText = unicodesToText( config.brands );

await subsetOne( 'fa-solid-900.woff2', 'fa-solid-subset.woff2', solidText );
await subsetOne( 'fa-brands-400.woff2', 'fa-brands-subset.woff2', brandsText );

for ( const legacy of [
	'fa-solid-900.woff2',
	'fa-solid-900.ttf',
	'fa-brands-400.woff2',
	'fa-brands-400.ttf',
] ) {
	const legacyPath = resolve( themeRoot, 'dist', legacy );
	if ( existsSync( legacyPath ) ) {
		unlinkSync( legacyPath );
	}
}

console.log(
	`build-fa-subset: PASS solid=${config.solid.length} brands=${config.brands.length} → src/webfonts/`
);
