import { defineConfig } from "vite";
import codeigniter from "vite-plugin-codeigniter";

export default defineConfig(() => ({
  server: {
    port: 5173,
    strictPort: true,
  },
  plugins: [codeigniter()],
}));
