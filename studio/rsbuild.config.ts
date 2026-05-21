import { defineConfig } from "@rsbuild/core";
import { pluginReact } from "@rsbuild/plugin-react";
import { pluginModuleFederation } from "@module-federation/rsbuild-plugin";
import { pluginGenerateEntrypoints } from "@pimcore/studio-ui-bundle/rsbuild/plugins";
import { createDynamicRemote } from "@pimcore/studio-ui-bundle/rsbuild/utils";
import path from "path";
import fs from "fs";
import packages from "./package.json";

const buildPath = path.resolve(__dirname, "..", "src", "Resources", "public", "build");

const devBuildPath = path.resolve(buildPath, "development");
if (!fs.existsSync(buildPath)) {
    fs.mkdirSync(buildPath, { recursive: true });
}

const prodBuildPath = path.resolve(buildPath, "production");

const inDevelopment = process.env.NODE_ENV === "development";

export default defineConfig({
    mode: inDevelopment ? "development" : "production",
    server: {
        port: 3034,
    },
    dev: {
        client: {
            host: "localhost",
            port: 3034,
            protocol: "ws",
        },
    },
    source: {
        entry: {
            main: "./src/main.ts",
        },
        decorators: {
            version: "legacy",
        },
    },
    output: {
        manifest: true,
        assetPrefix: "/bundles/torqitdataimporterextensions/build/" + (inDevelopment ? "development" : "production"),
        cleanDistPath: true,
        distPath: inDevelopment ? devBuildPath : prodBuildPath,
    },
    tools: {
        bundlerChain: (chain) => {
            chain.output.uniqueName("torqit_data_importer_extensions_bundle");
        },
    },
    plugins: [
        pluginGenerateEntrypoints(),
        pluginReact(),
        pluginModuleFederation({
            name: "torqit_data_importer_extensions_bundle",
            filename: "static/js/remoteEntry.js",
            exposes: {
                ".": "./src/main.ts",
            },
            dts: false,
            remotes: {
                "@pimcore/studio-ui-bundle": createDynamicRemote("pimcore_studio_ui_bundle"),
                "@pimcore/data-importer": createDynamicRemote("pimcore_dataimporter_bundle"),
            },
            shared: {
                ...packages.dependencies,
                react: {
                    singleton: true,
                    eager: true,
                    requiredVersion: false,
                },
                "react-dom": {
                    singleton: true,
                    eager: true,
                    requiredVersion: false,
                },
                antd: {
                    singleton: true,
                    eager: true,
                    requiredVersion: false,
                },
            },
        }),
    ],
});
