#!/usr/bin/env node
/**
 * Atlas Briefing CSS budget gate (audit closure).
 *
 * Usage: node scripts/check-css-budget.mjs
 * Env: MSR_PUBLISHING_CSS_BUDGET_BYTES (default 322000)
 */
import { readFileSync, statSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname( fileURLToPath( import.meta.url ) );
const cssPath = resolve( __dirname, '../dist/app.css' );
const maxBytes = Number( process.env.MSR_PUBLISHING_CSS_BUDGET_BYTES || 322000 );

let size;
try {
	size = statSync( cssPath ).size;
} catch {
	console.error( `check-css-budget: missing ${cssPath} — run npm run production first` );
	process.exit( 1 );
}

const kb = ( size / 1024 ).toFixed( 1 );
const maxKb = ( maxBytes / 1024 ).toFixed( 1 );

if ( size > maxBytes ) {
	console.error( `check-css-budget: FAIL dist/app.css ${kb} KiB exceeds budget ${maxKb} KiB` );
	process.exit( 1 );
}

const css = readFileSync( cssPath, 'utf8' );
if ( /fonts\.googleapis\.com|cdn\.jsdelivr\.net|kit\.fontawesome\.com/.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css still references external CDN hosts' );
	process.exit( 1 );
}

if ( /url\(\/dm-sans|url\(\/fa-solid-900/.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css uses root-absolute font URLs — set base: "./" in vite.config.js' );
	process.exit( 1 );
}

if ( ! /Font Awesome 6 Free/.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css missing publishing FA subset' );
	process.exit( 1 );
}

if ( /fa-solid-900\.woff2|fa-brands-400\.woff2/.test( css ) ) {
	console.error( 'check-css-budget: FAIL dist/app.css still references full Font Awesome webfonts' );
	process.exit( 1 );
}

console.log( `check-css-budget: PASS dist/app.css ${kb} KiB (budget ${maxKb} KiB)` );
process.exit( 0 );
