import gulp from "gulp";
import gulpSass from "gulp-sass";
import * as sass from "sass";
import sourcemaps from "gulp-sourcemaps";
import cleanCSS from "gulp-clean-css";
import rename from "gulp-rename";
import { deleteAsync } from "del";
import esbuild from "esbuild";
import path from "node:path";
import typescript from "typescript";

const { src, dest, series, parallel, watch } = gulp;

const scss = gulpSass(sass);

const paths = {
    styles: "src/App.scss",
    stylesWatch: "src/**/*.scss",
    scripts: "src/App.ts",
    scriptsWatch: "src/**/*.ts",
    dist: "assets",
    js_dist: "assets/js",
    css_dist: "assets/css",
    types: "assets/js/types"
};


/**
 * Clean distribution directory.
 */
function clean() {
    return deleteAsync([paths.dist]);
}


/**
 * Build unminified CSS.
 */
function css() {
    return src(paths.styles)
        .pipe(sourcemaps.init())
        .pipe(scss().on("error", scss.logError))
        .pipe(sourcemaps.write("."))
        .pipe(dest(paths.css_dist));
}


/**
 * Build minified CSS.
 */
function cssMin() {
    return src(paths.styles)
        .pipe(sourcemaps.init())
        .pipe(scss().on("error", scss.logError))
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: ".min"
        }))
        .pipe(sourcemaps.write("."))
        .pipe(dest(paths.css_dist));
}


/**
 * Build unminified ES module bundle.
 */
function js() {
    return esbuild.build({
        entryPoints: [paths.scripts],
        outfile: path.join(paths.js_dist, "App.esm.js"),
        bundle: true,
        format: "esm",
        platform: "browser",
        target: "es2020",
        sourcemap: true,
        minify: false,
        tsconfig: "tsconfig.json",
        logLevel: "warning"
    });
}


/**
 * Build minified ES module bundle.
 */
function jsMin() {
    return esbuild.build({
        entryPoints: [paths.scripts],
        outfile: path.join(paths.js_dist, "App.esm.min.js"),
        bundle: true,
        format: "esm",
        platform: "browser",
        target: "es2020",
        sourcemap: true,
        minify: true,
        tsconfig: "tsconfig.json",
        logLevel: "warning"
    });
}


/**
 * Build unminified global browser script.
 */
function jsGlobal() {
    return esbuild.build({
        entryPoints: [paths.scripts],
        outfile: path.join(paths.js_dist, "App.js"),
        bundle: true,
        format: "iife",
        globalName: "Nex",
        platform: "browser",
        target: "es2020",
        sourcemap: true,
        minify: false,
        tsconfig: "tsconfig.json",
        logLevel: "warning"
    });
}


/**
 * Build minified global browser script.
 */
function jsGlobalMin() {
    return esbuild.build({
        entryPoints: [paths.scripts],
        outfile: path.join(paths.js_dist, "App.min.js"),
        bundle: true,
        format: "iife",
        globalName: "Nex",
        platform: "browser",
        target: "es2020",
        sourcemap: true,
        minify: true,
        tsconfig: "tsconfig.json",
        logLevel: "warning"
    });
}


/**
 * Build TypeScript declaration files.
 */
async function types() {
    const configPath = path.resolve(process.cwd(), "tsconfig.json");

    const configFile = typescript.readConfigFile(
        configPath,
        typescript.sys.readFile
    );

    if (configFile.error) {
        throw new Error(
            typescript.flattenDiagnosticMessageText(
                configFile.error.messageText,
                "\n"
            )
        );
    }

    const parsedConfig = typescript.parseJsonConfigFileContent(
        configFile.config,
        typescript.sys,
        path.dirname(configPath)
    );

    const options = {
        ...parsedConfig.options,
        rootDir: path.resolve(process.cwd(), "src"),
        outDir: path.resolve(process.cwd(), paths.types),
        declarationDir: path.resolve(process.cwd(), paths.types),
        declaration: true,
        declarationMap: false,
        emitDeclarationOnly: true,
        noEmit: false,
        sourceMap: false
    };

    const program = typescript.createProgram(
        parsedConfig.fileNames,
        options
    );

    const emitResult = program.emit();

    const diagnostics = typescript
        .getPreEmitDiagnostics(program)
        .concat(emitResult.diagnostics);

    if (diagnostics.length) {
        const host = {
            getCanonicalFileName: (fileName) => fileName,
            getCurrentDirectory: () => process.cwd(),
            getNewLine: () => "\n"
        };

        throw new Error(
            typescript.formatDiagnostics(diagnostics, host)
        );
    }
}


/**
 * Build Nex.
 */
const build = series(
    clean,
    parallel(
        css,
        cssMin,
        js,
        jsMin,
        jsGlobal,
        jsGlobalMin,
        types
    )
);


/**
 * Watch source files.
 */
function watchFiles() {
    watch(
        paths.stylesWatch,
        parallel(css, cssMin)
    );

    watch(
        paths.scriptsWatch,
        parallel(
            js,
            jsMin,
            jsGlobal,
            jsGlobalMin,
            types
        )
    );
}


/**
 * Development mode.
 */
const dev = series(
    build,
    watchFiles
);


export {
    clean,
    css,
    cssMin,
    js,
    jsMin,
    jsGlobal,
    jsGlobalMin,
    types,
    build,
    watchFiles as watch,
    dev
};


export default build;