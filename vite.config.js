import { defineConfig } from "vite";
import laravel, { refreshPaths } from "laravel-vite-plugin";
import browserSync from "browser-sync";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: [...refreshPaths, "app/Livewire/**", "app/Filament/**"],
        }),
        {
            name: "browser-sync",
            configureServer(server) {
                browserSync({
                    proxy: "http://127.0.0.1:8000", // Adjust to your Laravel local URL
                    files: [
                        "app/**/*.php",
                        "resources/views/**/*.php",
                        "routes/**/*.php",
                        "resources/js/**/*.js",
                        "resources/css/**/*.css",
                    ],
                    open: false,
                });
            },
        },
    ],
});
