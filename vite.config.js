import fs from "node:fs";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";

const root = fs.realpathSync.native(path.dirname(fileURLToPath(import.meta.url)));

export default defineConfig({
  root,
  plugins: [react(), tailwindcss()],
});
