import { defineConfig } from 'unocss'

export default defineConfig({
  content: {
    filesystem: [
      '**/*.{html,vue,js,ts,jsx,tsx,php}'
    ]
  }
})